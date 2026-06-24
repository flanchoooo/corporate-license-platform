@php
    $money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2);
    $item = fn ($type) => $quote->items->firstWhere('fee_type', $type)?->amount_cents ?? 0;
    $insuranceItem = $quote->items->firstWhere('fee_type', 'motor_insurance');
@endphp

<x-guest-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-950">Auto Quote</h1>
            <p class="mt-2 text-sm text-gray-600">Review the vehicle details and amount due.</p>
        </div>

        <div class="rounded-lg border border-gray-200">
            <div class="grid gap-3 p-4 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Number Plate</span><span class="font-semibold">{{ $quote->vehicle->number_plate }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Owner</span><span>{{ $quote->vehicle->owner_name ?: $quote->corporate->company_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Vehicle</span><span>{{ $quote->vehicle->make }} {{ $quote->vehicle->model }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Engine Capacity</span><span>{{ number_format($quote->vehicle->engine_capacity) }} CC</span></div>
                <div class="flex justify-between"><span class="text-gray-500">License Expiry</span><span>{{ optional($quote->vehicle->last_license_expires_at)->format('d M Y') ?? 'Not set' }}</span></div>
            </div>
            <div class="border-t border-gray-200 p-4 text-sm">
                <div class="flex justify-between py-1"><span>ZINARA Fee</span><span>{{ $money($item('zinara_license')) }}</span></div>
                <div class="flex justify-between py-1"><span>Radio License Fee</span><span>{{ $money($item('radio_license')) }}</span></div>
                <div class="flex justify-between py-1"><span>{{ $insuranceItem?->description ?? 'Insurance Fee' }}</span><span>{{ $money($item('motor_insurance')) }}</span></div>
                <div class="flex justify-between py-1"><span>Arrears</span><span>{{ $money($item('arrears')) }}</span></div>
                <div class="flex justify-between py-1"><span>Delivery Fee</span><span>{{ $money($item('delivery_fee')) }}</span></div>
                <div class="mt-3 flex justify-between border-t border-gray-200 pt-3 text-base font-semibold"><span>Total</span><span>{{ $money($quote->total_cents) }}</span></div>
            </div>
        </div>

        <form method="POST" action="{{ route('bot.continue', $quote) }}" class="grid gap-3">
            @csrf
            <input type="hidden" name="flow" value="{{ $flow }}">
            <button name="choice" value="continue" class="rounded-md bg-gray-900 px-4 py-3 text-sm font-semibold text-white">1. Continue</button>
            <button name="choice" value="cancel" class="rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700">2. Cancel</button>
        </form>
    </div>
</x-guest-layout>
