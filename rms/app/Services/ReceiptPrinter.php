<?php

namespace App\Services;

use App\Models\Order;

class ReceiptPrinter
{
    /**
     * Dispatch a cashier receipt print job.
     */
    public static function dispatch(Order $order): void
    {
        event(new \App\Events\PrintJobCreated([
            'type' => 'cashier',
            'order_id' => $order->id,
            'html' => view('print.receipt', ['order' => $order])->render(),
        ]));
    }
}
