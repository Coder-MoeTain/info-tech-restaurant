<!doctype html>
<html>
<head>
    <style>
        @page { size: 80mm auto; margin: 5mm; }
        body { font-family: 'Menlo', monospace; font-size: 12px; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <strong>KITCHEN COPY</strong><br>
        Order #{{ $order->order_number }} | {{ $order->table?->name ?? strtoupper($order->order_type) }}<br>
        Waiter: {{ $order->waiter?->name ?? 'N/A' }}<br>
        Time: {{ now() }}
    </div>
    <div class="line"></div>
    @foreach($items as $item)
        <div>{{ $item['qty'] }} x {{ $item['name'] }}</div>
        @if(!empty($item['note']))<div>  * {{ $item['note'] }}</div>@endif
    @endforeach
    <div class="line"></div>
</body>
</html>
