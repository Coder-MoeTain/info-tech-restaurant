<!doctype html>
<html>
<head>
    <style>
        @page { size: 80mm auto; margin: 5mm; }
        body { font-family: 'Menlo', monospace; font-size: 12px; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
        .totals td { padding: 2px 0; }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <div>{{ config('app.name') }}</div>
        <div>{{ $order->table?->name ?? strtoupper($order->order_type) }}</div>
        <div>{{ $order->created_at }}</div>
    </div>
    <div class="line"></div>
    @foreach($order->items as $item)
        <div>{{ $item->qty }} x {{ $item->item->name ?? '' }} - {{ number_format($item->line_total, 2) }}</div>
        @if($item->note)<div>  * {{ $item->note }}</div>@endif
    @endforeach
    <div class="line"></div>
    <table class="totals" width="100%">
        <tr><td>Subtotal</td><td align="right">{{ number_format($order->subtotal,2) }}</td></tr>
        <tr><td>Discount</td><td align="right">{{ number_format($order->discount_total,2) }}</td></tr>
        <tr><td>Tax</td><td align="right">{{ number_format($order->tax_total,2) }}</td></tr>
        <tr><td>Service</td><td align="right">{{ number_format($order->service_charge_total,2) }}</td></tr>
        <tr><td>Tip</td><td align="right">{{ number_format($order->tip_amount,2) }}</td></tr>
        <tr><td><strong>Total</strong></td><td align="right"><strong>{{ number_format($order->grand_total,2) }}</strong></td></tr>
        <tr><td>Paid</td><td align="right">{{ number_format($order->payments->sum('amount'),2) }}</td></tr>
    </table>
    <div class="line"></div>
    <div class="center">Thank you!</div>
</body>
</html>
