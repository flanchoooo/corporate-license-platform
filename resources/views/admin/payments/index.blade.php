@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Payments</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3">Reference</th><th class="px-6 py-3">Method</th><th class="px-6 py-3">Vehicle</th><th class="px-6 py-3">Amount</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Date</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($payments as $payment)
                            <tr>
                                <td class="px-6 py-3 font-medium">{{ $payment->reference }}</td>
                                <td class="px-6 py-3 capitalize">{{ str_replace('_', ' ', $payment->method) }}</td>
                                <td class="px-6 py-3">{{ $payment->quote->vehicle->number_plate }}</td>
                                <td class="px-6 py-3">{{ $money($payment->amount_cents) }}</td>
                                <td class="px-6 py-3 capitalize">{{ $payment->status }}</td>
                                <td class="px-6 py-3">{{ optional($payment->paid_at)->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-6 text-gray-500">No payments.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $payments->links() }}</div>
        </div>
    </div>
</x-app-layout>
