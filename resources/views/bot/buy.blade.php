@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-guest-layout>
    <form method="POST" action="{{ route('bot.buy', $quote) }}" class="space-y-5" data-checkout-form>
        @csrf
        <div>
            <h1 class="text-xl font-semibold text-gray-950">Delivery and Payment</h1>
            <p class="mt-2 text-sm text-gray-600">{{ $quote->vehicle->number_plate }} total: <span class="font-semibold">{{ $money($quote->total_cents) }}</span></p>
        </div>

        <div>
            <x-input-label for="delivery_address" value="Delivery Address" />
            <x-text-input id="delivery_address" name="delivery_address" class="mt-1 block w-full" value="{{ old('delivery_address') }}" required />
            <x-input-error :messages="$errors->get('delivery_address')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="contact_mobile" value="Contact Mobile" />
            <x-text-input id="contact_mobile" name="contact_mobile" class="mt-1 block w-full" value="{{ old('contact_mobile') }}" required />
            <x-input-error :messages="$errors->get('contact_mobile')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="landmark" value="Landmark" />
            <x-text-input id="landmark" name="landmark" class="mt-1 block w-full" value="{{ old('landmark') }}" />
            <x-input-error :messages="$errors->get('landmark')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="payment_method" value="Payment Method" />
            <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="mobile_money">Mobile Money</option>
                <option value="zimswitch">Zimswitch Card</option>
                <option value="visa">Visa</option>
                <option value="mastercard">Mastercard</option>
            </select>
            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
        </div>

        <div class="flex justify-between">
            <a href="{{ route('bot.menu') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
            <x-primary-button>Pay and Generate Disk</x-primary-button>
        </div>
    </form>
    <x-checkout-modal />
</x-guest-layout>
