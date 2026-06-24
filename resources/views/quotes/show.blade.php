@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $quote->quote_number }}</h2></x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">{{ $errors->first() }}</div>
            @endif
            <div class="bg-white shadow-sm rounded-lg p-8">
                <div class="flex justify-between border-b pb-6">
                    <div>
                        <div class="text-lg font-semibold">{{ $quote->corporate->company_name }}</div>
                        <div class="text-sm text-gray-500">{{ $quote->corporate->registration_number }}</div>
                        <div class="text-sm text-gray-500">{{ $quote->corporate->physical_address }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Quote</div>
                        <div class="font-semibold">{{ $quote->quote_number }}</div>
                        <div class="text-sm capitalize">{{ $quote->status }}</div>
                    </div>
                </div>
                <div class="py-6">
                    <div class="font-semibold">{{ $quote->vehicle->number_plate }}</div>
                    <div class="text-sm text-gray-500">{{ $quote->vehicle->make }} {{ $quote->vehicle->model }} - {{ number_format($quote->vehicle->engine_capacity) }} CC</div>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y">
                        @foreach($quote->items as $item)
                            <tr><td class="py-3">{{ $item->description }}</td><td class="py-3 text-right">{{ $money($item->amount_cents) }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td class="pt-5 text-lg font-semibold">Grand Total</td><td class="pt-5 text-right text-lg font-semibold">{{ $money($quote->total_cents) }}</td></tr>
                    </tfoot>
                </table>
                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('quotes.pdf', $quote) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest text-gray-700">PDF</a>
                    @can('purchase', $quote)
                        <form method="POST" action="{{ route('quotes.purchase', $quote) }}" class="flex items-center gap-3" data-checkout-form data-delivery-required="true">
                            @csrf
                            <select name="payment_method" class="rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="mobile_money">Mobile Money</option>
                                <option value="zimswitch">Zimswitch Card</option>
                                <option value="visa">Visa</option>
                                <option value="mastercard">Mastercard</option>
                            </select>
                            <x-primary-button>Checkout</x-primary-button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <x-checkout-modal />
</x-app-layout>
