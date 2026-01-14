<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        return Order::with('items.item', 'table')
            ->whereIn('status', ['sent_to_kitchen', 'ready'])
            ->orderBy('sent_to_kitchen_at', 'desc')
            ->get();
    }

    public function updateStatus(Request $request, Order $ticket)
    {
        $data = $request->validate(['status' => 'required|in:preparing,ready']);
        $ticket->update(['status' => $data['status'] === 'ready' ? 'ready' : 'sent_to_kitchen']);
        return response()->json(['ok' => true]);
    }
}
