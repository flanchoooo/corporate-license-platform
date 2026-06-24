@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))
<!doctype html>
<html>
<head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;color:#111827}.muted{color:#6b7280}.right{text-align:right}table{width:100%;border-collapse:collapse}td,th{padding:10px;border-bottom:1px solid #e5e7eb}.total{font-size:18px;font-weight:bold}</style></head>
<body>
    <h1>Vehicle License Statement</h1>
    <p class="muted">{{ $quote->quote_number }}</p>
    <table>
        <tr><td>Company</td><td class="right">{{ $quote->corporate->company_name }}</td></tr>
        <tr><td>Vehicle</td><td class="right">{{ $quote->vehicle->number_plate }} - {{ $quote->vehicle->make }} {{ $quote->vehicle->model }}</td></tr>
        <tr><td>Engine Capacity</td><td class="right">{{ number_format($quote->vehicle->engine_capacity) }} CC</td></tr>
    </table>
    <h2>Charges</h2>
    <table>
        @foreach($quote->items as $item)
            <tr><td>{{ $item->description }}</td><td class="right">{{ $money($item->amount_cents) }}</td></tr>
        @endforeach
        <tr><td class="total">Grand Total</td><td class="right total">{{ $money($quote->total_cents) }}</td></tr>
    </table>
</body>
</html>
