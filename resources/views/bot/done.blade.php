@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-guest-layout>
    <div class="space-y-5">
        <h1 class="text-xl font-semibold text-gray-950">License Purchased</h1>
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
            Payment {{ $payment->reference }} was successful for {{ $money($payment->amount_cents) }}.
        </div>
        <div class="rounded-lg border border-gray-200 p-4 text-sm space-y-2">
            <div><span class="text-gray-500">License Disk:</span> {{ $disk->reference_number }}</div>
            <div><span class="text-gray-500">Mutero Delivery:</span> MUTERO-{{ str_pad((string) $delivery->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div><span class="text-gray-500">Delivery Order:</span> #{{ $delivery->id }}</div>
            <div><span class="text-gray-500">Delivery Status:</span> {{ ucfirst($delivery->status) }}</div>
        </div>
        <a href="{{ route('bot.menu') }}" class="block rounded-md bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white">Back to Menu</a>
    </div>
</x-guest-layout>
