@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))
<!doctype html>
<html>
<head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;color:#111827}.disk{border:3px solid #111827;border-radius:12px;padding:24px}.muted{color:#6b7280}.grid{width:100%}.grid td{padding:8px;border-bottom:1px solid #e5e7eb}.qr{float:right;width:120px;height:120px}.plate{font-size:28px;font-weight:bold}</style></head>
<body>
    <div class="disk">
        <img class="qr" src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR">
        <div class="muted">Corporate Vehicle License Disk</div>
        <div class="plate">{{ $licenseDisk->vehicle->number_plate }}</div>
        <p>{{ $licenseDisk->corporate->company_name }}</p>
        <table class="grid">
            <tr><td>Reference</td><td>{{ $licenseDisk->reference_number }}</td></tr>
            <tr><td>Vehicle</td><td>{{ $licenseDisk->vehicle->make }} {{ $licenseDisk->vehicle->model }} {{ $licenseDisk->vehicle->year }}</td></tr>
            <tr><td>Radio License</td><td>{{ $money($licenseDisk->radio_license_fee_cents) }}</td></tr>
            <tr><td>Insurance</td><td>{{ $money($licenseDisk->insurance_fee_cents) }}</td></tr>
            <tr><td>ZINARA</td><td>{{ $money($licenseDisk->zinara_fee_cents) }}</td></tr>
            <tr><td>Arrears</td><td>{{ $money($licenseDisk->arrears_cents) }}</td></tr>
            <tr><td>Total Paid</td><td>{{ $money($licenseDisk->total_paid_cents) }}</td></tr>
            <tr><td>Validity</td><td>{{ $licenseDisk->valid_from->format('d M Y') }} - {{ $licenseDisk->valid_until->format('d M Y') }}</td></tr>
        </table>
    </div>
</body>
</html>
