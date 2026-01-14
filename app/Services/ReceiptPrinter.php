<?php

namespace App\Services;

use App\Models\Order;

class ReceiptPrinter
{
    /**
     * Dispatch a cashier receipt print job.
     * In browser-print mode, this event is consumed by the SPA to open a print window.
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
