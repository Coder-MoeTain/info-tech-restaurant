<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $q = Order::whereDate('paid_at', $date)->where('status', 'paid');
        $orderIds = $q->pluck('id');

        return [
            'summary' => [
                'orders' => $q->count(),
                'gross' => $q->sum('grand_total'),
                'tax' => $q->sum('tax_total'),
                'service' => $q->sum('service_charge_total'),
            ],
            'by_payment' => Payment::select('method', DB::raw('SUM(amount) as total'))
                ->whereIn('order_id', $orderIds)
                ->groupBy('method')
                ->get(),
            'by_dish' => OrderItem::select('item_id', DB::raw('SUM(qty) as qty'), DB::raw('SUM(line_total) as total'))
                ->whereIn('order_id', $orderIds)
                ->groupBy('item_id')
                ->with('item:id,name')
                ->get(),
        ];
    }

    public function range(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);
        $q = Order::whereBetween('paid_at', [$request->start, $request->end])->where('status', 'paid');
        return [
            'orders' => $q->count(),
            'gross' => $q->sum('grand_total'),
        ];
    }

    public function export(Request $request)
    {
        return Excel::download(new SalesExport($request->start, $request->end), 'sales.xlsx');
    }

    public function dashboard(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $q = Order::whereDate('paid_at', $date)->where('status', 'paid');
        $orderIds = $q->pluck('id');
        $avgTicket = $q->count() ? $q->sum('grand_total') / $q->count() : 0;

        return [
            'summary' => [
                'orders' => $q->count(),
                'gross' => $q->sum('grand_total'),
                'avg_ticket' => round($avgTicket, 2),
                'items_sold' => OrderItem::whereIn('order_id', $orderIds)->sum('qty'),
            ],
            'payment_mix' => Payment::select('method', DB::raw('SUM(amount) as total'))
                ->whereIn('order_id', $orderIds)->groupBy('method')->get(),
            'voids' => OrderItem::where('is_voided', true)->whereIn('order_id', $orderIds)->count(),
            'refunds' => Order::whereIn('id', $orderIds)->where('status', 'cancelled')->count(),
        ];
    }
}
