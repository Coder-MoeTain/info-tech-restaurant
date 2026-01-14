<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Services\KitchenPrinter;
use App\Services\ReceiptPrinter;
use App\Services\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $q = Order::with('items.item', 'table', 'waiter');
        if (request('status')) {
            $q->where('status', request('status'));
        }
        if (request('start')) {
            $q->whereDate('created_at', '>=', request('start'));
        }
        if (request('end')) {
            $q->whereDate('created_at', '<=', request('end'));
        }
        if (request('search')) {
            $term = request('search');
            $q->where(function ($sub) use ($term) {
                $sub->where('order_number', 'like', "%$term%")
                    ->orWhereHas('table', fn($t)=>$t->where('name', 'like', "%$term%"))
                    ->orWhereHas('waiter', fn($w)=>$w->where('name', 'like', "%$term%"));
            });
        }
        return $q->latest()->paginate();
    }

    public function open()
    {
        return Order::with('table')->whereIn('status', ['open', 'sent_to_kitchen', 'ready'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'table_id' => 'nullable|exists:tables,id',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'items' => 'array',
            'items.*.item_id' => 'required|exists:menu_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.note' => 'nullable|string',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $order = Order::create([
                'order_number' => $this->nextOrderNumber(),
                'table_id' => $data['table_id'] ?? null,
                'order_type' => $data['order_type'],
                'waiter_id' => $request->user()->id ?? null,
                'status' => 'open',
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
            ]);
            $this->syncItems($order, $data['items'] ?? []);
            Audit::log($request->user(), 'order_create', $order);
            return $order->load('items.item');
        });
    }

    public function addItems(Request $request, Order $order)
    {
        $data = $request->validate([
            'items' => 'array',
            'items.*.item_id' => 'required|exists:menu_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.note' => 'nullable|string',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
        ]);

        $newItems = [];
        DB::transaction(function () use (&$newItems, $order, $data) {
            $newItems = $this->syncItems($order, $data['items'] ?? [], true);
            $order->update(['sent_to_kitchen_at' => now()]);
        });

        KitchenPrinter::dispatch($order->fresh('items.item'), $newItems);
        Audit::log($request->user(), 'order_add_items', $order);

        return $order->fresh('items.item');
    }

    public function sendToKitchen(Order $order)
    {
        $order->update(['status' => 'sent_to_kitchen', 'sent_to_kitchen_at' => now()]);
        KitchenPrinter::dispatch($order->fresh('items.item'), $order->items);
        Audit::log(request()->user(), 'order_send_kitchen', $order);
        return response()->json(['ok' => true]);
    }

    public function pay(Request $request, Order $order)
    {
        $data = $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|in:cash,card,mobile',
            'payments.*.amount' => 'required|numeric|min:0',
            'discount_total' => 'nullable|numeric',
            'tax_total' => 'nullable|numeric',
            'service_charge_total' => 'nullable|numeric',
            'grand_total' => 'required|numeric|min:0',
            'tip_amount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($order, $data, $request) {
            foreach ($data['payments'] as $p) {
                $order->payments()->create($p);
            }
            $order->update([
                'cashier_id' => $request->user()->id ?? null,
                'status' => 'paid',
                'paid_at' => now(),
                'discount_total' => $data['discount_total'] ?? 0,
                'tax_total' => $data['tax_total'] ?? 0,
                'service_charge_total' => $data['service_charge_total'] ?? 0,
                'tip_amount' => $data['tip_amount'] ?? 0,
                'grand_total' => $data['grand_total'],
            ]);
            ReceiptPrinter::dispatch($order->fresh('items.item', 'payments'));
            Audit::log($request->user(), 'order_pay', $order, ['payments' => $data['payments']]);
            return $order->load('items.item', 'payments');
        });
    }

    public function voidItem(Request $request, Order $order)
    {
        $request->validate([
            'item_id' => 'required|exists:order_items,id',
            'reason' => 'nullable|string',
        ]);
        $item = $order->items()->findOrFail($request->item_id);
        $item->update(['is_voided' => true, 'void_reason' => $request->reason]);
        Audit::log($request->user(), 'void_item', $item, ['reason' => $request->reason]);
        return response()->json(['ok' => true]);
    }

    public function refund(Request $request, Order $order)
    {
        $request->validate(['reason' => 'nullable|string']);
        $order->update(['status' => 'cancelled']);
        Audit::log($request->user(), 'refund_order', $order, ['reason' => $request->reason]);
        return response()->json(['ok' => true]);
    }

    public function reprint(Order $order)
    {
        ReceiptPrinter::dispatch($order->load('items.item', 'payments'));
        Audit::log(request()->user(), 'order_reprint', $order);
        return response()->json(['ok' => true]);
    }

    public function transfer(Request $request, Order $order)
    {
        $data = $request->validate(['table_id' => 'required|exists:tables,id']);
        $order->update(['table_id' => $data['table_id']]);
        Audit::log($request->user(), 'order_transfer', $order, ['table_id' => $data['table_id']]);
        return response()->json(['ok' => true]);
    }

    private function syncItems(Order $order, array $items, bool $append = false)
    {
        $created = [];
        $subtotal = $append ? $order->subtotal : 0;
        foreach ($items as $item) {
            $discount = $item['discount_amount'] ?? 0;
            $unit = max(0, $item['unit_price'] - $discount);
            $line = $item['qty'] * $unit;
            $orderItem = $order->items()->create([
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'discount_amount' => $discount,
                'line_total' => $line,
                'note' => $item['note'] ?? null,
                'sent_to_kitchen_qty' => $append ? 0 : $item['qty'],
            ]);
            $created[] = $orderItem;
            $subtotal += $line;

            // decrement inventory
            $menu = MenuItem::find($item['item_id']);
            if ($menu && $menu->stock !== null) {
                $newStock = max(0, $menu->stock - $item['qty']);
                $menu->update(['stock' => $newStock]);
            }
        }
        $order->update(['subtotal' => $subtotal, 'grand_total' => $subtotal]);
        return $created;
    }

    private function nextOrderNumber(): string
    {
        $prefix = now()->format('Ymd');
        $last = Order::whereDate('created_at', now())->max('id') + 1;
        return $prefix . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
