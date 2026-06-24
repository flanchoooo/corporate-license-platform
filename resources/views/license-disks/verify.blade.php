@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-guest-layout>
    <div class="space-y-4">
        <div class="text-xl font-semibold text-gray-900">Verified License Disk</div>
        <div class="text-sm text-gray-600">{{ $licenseDisk->reference_number }}</div>
        <div class="border rounded-lg p-4 text-sm space-y-2">
            <div><span class="text-gray-500">Company:</span> {{ $licenseDisk->corporate->company_name }}</div>
            <div><span class="text-gray-500">Vehicle:</span> {{ $licenseDisk->vehicle->number_plate }} - {{ $licenseDisk->vehicle->make }} {{ $licenseDisk->vehicle->model }}</div>
            <div><span class="text-gray-500">Valid:</span> {{ $licenseDisk->valid_from->format('d M Y') }} to {{ $licenseDisk->valid_until->format('d M Y') }}</div>
            <div><span class="text-gray-500">Paid:</span> {{ $money($licenseDisk->total_paid_cents) }}</div>
        </div>
    </div>
</x-guest-layout>
