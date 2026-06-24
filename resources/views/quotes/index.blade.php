@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Quotes</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            <div class="flex justify-end gap-2">
                <a href="{{ route('quotes.export') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest text-gray-700">Export CSV</a>
                @if(Auth::user()->canWriteCorporateData())
                    <a href="{{ route('quotes.bulk.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest text-gray-700">Bulk Quote</a>
                    <a href="{{ route('quotes.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md text-xs font-semibold uppercase tracking-widest text-white">New Quote</a>
                @endif
            </div>
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('quotes.bulk.purchase') }}" class="bg-white shadow-sm rounded-lg overflow-hidden" data-checkout-form data-delivery-required="true">
                @csrf
                @if(Auth::user()->canWriteCorporateData())
                    <div class="p-4 flex flex-wrap justify-end gap-3">
                        <select name="payment_method" class="rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="mobile_money">Mobile Money</option>
                            <option value="zimswitch">Zimswitch Card</option>
                            <option value="visa">Visa</option>
                            <option value="mastercard">Mastercard</option>
                        </select>
                        <x-primary-button>Checkout Selected</x-primary-button>
                    </div>
                @endif
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3"></th><th class="px-6 py-3">Quote</th><th class="px-6 py-3">Vehicle</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Total</th><th class="px-6 py-3">Expires</th><th class="px-6 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($quotes as $quote)
                            <tr>
                                <td class="px-6 py-4">
                                    @if(Auth::user()->canWriteCorporateData() && $quote->status === 'pending')
                                        <input type="checkbox" name="quote_ids[]" value="{{ $quote->id }}" class="rounded border-gray-300">
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium">{{ $quote->quote_number }}</td>
                                <td class="px-6 py-4">{{ $quote->vehicle->number_plate }}<div class="text-gray-500">{{ $quote->vehicle->make }} {{ $quote->vehicle->model }}</div></td>
                                <td class="px-6 py-4 capitalize">{{ $quote->status }}</td>
                                <td class="px-6 py-4">{{ $money($quote->total_cents) }}</td>
                                <td class="px-6 py-4">{{ optional($quote->expires_at)->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right"><a class="text-indigo-700 font-medium" href="{{ route('quotes.show', $quote) }}">Open</a></td>
                            </tr>
                        @empty
                            <tr><td class="px-6 py-6 text-gray-500" colspan="7">No quotes found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
            {{ $quotes->links() }}
        </div>
    </div>
    <x-checkout-modal />
</x-app-layout>
