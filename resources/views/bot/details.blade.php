@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-guest-layout>
    <div class="space-y-5">
        <div>
            <h1 class="text-xl font-semibold text-gray-950">Vehicle Details</h1>
            <p class="mt-2 text-sm text-gray-600">Amount due is based on the auto-generated quote.</p>
        </div>
        <div class="rounded-lg border border-gray-200 p-4 text-sm space-y-2">
            <div><span class="text-gray-500">Number Plate:</span> {{ $quote->vehicle->number_plate }}</div>
            <div><span class="text-gray-500">Owner:</span> {{ $quote->vehicle->owner_name ?: $quote->corporate->company_name }}</div>
            <div><span class="text-gray-500">Vehicle:</span> {{ $quote->vehicle->make }} {{ $quote->vehicle->model }}</div>
            <div><span class="text-gray-500">Engine Capacity:</span> {{ number_format($quote->vehicle->engine_capacity) }} CC</div>
            <div><span class="text-gray-500">License Expiry:</span> {{ optional($quote->vehicle->last_license_expires_at)->format('d M Y') ?? 'Not set' }}</div>
            <div class="border-t border-gray-200 pt-2 font-semibold">Amount Due: {{ $money($quote->total_cents) }}</div>
        </div>
        <a href="{{ route('bot.menu') }}" class="block rounded-md bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white">Back to Menu</a>
    </div>
</x-guest-layout>
