<?php

namespace App\Services;

use App\Models\Order;

class KitchenPrinter
{
    /**
     * Dispatch KOT print job (full or delta items).
     *
     * @param \Illuminate\Support\Collection|array $items
     */
    public static function dispatch(Order $order, $items): void
    {
        $items = collect($items)->map(function ($item) {
            return [
                'name' => $item->item->name ?? $item['name'] ?? '',
                'qty' => $item->qty ?? $item['qty'] ?? 0,
                'note' => $item->note ?? $item['note'] ?? null,
            ];
        });

        event(new \App\Events\PrintJobCreated([
            'type' => 'kitchen',
            'order_id' => $order->id,
            'html' => view('print.kot', ['order' => $order, 'items' => $items])->render(),
        ]));
    }
}
