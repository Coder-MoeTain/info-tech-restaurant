<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\KitchenPrinter;
use App\Services\ReceiptPrinter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with('items.item', 'table')->latest()->paginate();
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
        ]);

        return DB::transaction(function () use ($data, $request) {
            $order = Order::create([
                'order_number' => $this->nextOrderNumber(),
                'table_id' => $data['table_id'] ?? null,
                'order_type' => $data['order_type'],
                'waiter_id' => $request->user()->id,
                'status' => 'open',
            ]);
            $this->syncItems($order, $data['items'] ?? []);
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
        ]);

        $newItems = [];
        DB::transaction(function () use (&$newItems, $order, $data) {
            $newItems = $this->syncItems($order, $data['items'] ?? [], true);
            $order->update(['sent_to_kitchen_at' => now()]);
        });

        KitchenPrinter::dispatch($order->fresh('items.item'), $newItems);

        return $order->fresh('items.item');
    }

    public function sendToKitchen(Order $order)
    {
        $order->update(['status' => 'sent_to_kitchen', 'sent_to_kitchen_at' => now()]);
        KitchenPrinter::dispatch($order->fresh('items.item'), $order->items);
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
        ]);

        return DB::transaction(function () use ($order, $data, $request) {
            foreach ($data['payments'] as $p) {
                $order->payments()->create($p);
            }
            $order->update([
                'cashier_id' => $request->user()->id,
                'status' => 'paid',
                'paid_at' => now(),
                'discount_total' => $data['discount_total'] ?? 0,
                'tax_total' => $data['tax_total'] ?? 0,
                'service_charge_total' => $data['service_charge_total'] ?? 0,
                'grand_total' => $data['grand_total'],
            ]);
            ReceiptPrinter::dispatch($order->fresh('items.item', 'payments'));
            return $order->load('items.item', 'payments');
        });
    }

    public function voidItem(Request $request, Order $order)
    {
        $request->validate(['item_id' => 'required|exists:order_items,id']);
        $item = $order->items()->findOrFail($request->item_id);
        $item->update(['is_voided' => true]);
        return response()->json(['ok' => true]);
    }

    public function refund(Request $request, Order $order)
    {
        $request->validate(['reason' => 'nullable|string']);
        $order->update(['status' => 'cancelled']);
        return response()->json(['ok' => true]);
    }

    public function reprint(Order $order)
    {
        ReceiptPrinter::dispatch($order->load('items.item', 'payments'));
        return response()->json(['ok' => true]);
    }

    private function syncItems(Order $order, array $items, bool $append = false)
    {
        $created = [];
        $subtotal = $append ? $order->subtotal : 0;
        foreach ($items as $item) {
            $line = $item['qty'] * $item['unit_price'];
            $orderItem = $order->items()->create([
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'line_total' => $line,
                'note' => $item['note'] ?? null,
                'sent_to_kitchen_qty' => $append ? 0 : $item['qty'],
            ]);
            $created[] = $orderItem;
            $subtotal += $line;
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
