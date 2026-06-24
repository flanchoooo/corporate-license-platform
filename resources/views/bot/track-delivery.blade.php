<x-guest-layout>
    <div class="space-y-5">
        <div>
            <h1 class="text-xl font-semibold text-gray-950">Track Mutero Delivery</h1>
            <p class="mt-2 text-sm text-gray-600">Enter a Mutero reference, delivery order number, license disk reference, plate number, or contact mobile.</p>
        </div>

        <form method="POST" action="{{ route('bot.delivery.track.submit') }}" class="space-y-4">
            @csrf
            <div>
                <x-input-label for="tracking_reference" value="Tracking Reference" />
                <x-text-input id="tracking_reference" name="tracking_reference" class="mt-1 block w-full" value="{{ old('tracking_reference') }}" placeholder="MUTERO-000001" required />
                <x-input-error :messages="$errors->get('tracking_reference')" class="mt-2" />
            </div>

            <div class="flex gap-3">
                <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white">Track Delivery</button>
                <a href="{{ route('bot.menu') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Back</a>
            </div>
        </form>

        @isset($delivery)
            <div class="rounded-lg border border-gray-200 p-4 text-sm space-y-2">
                <div><span class="text-gray-500">Mutero Reference:</span> MUTERO-{{ str_pad((string) $delivery->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div><span class="text-gray-500">Delivery Order:</span> #{{ $delivery->id }}</div>
                <div><span class="text-gray-500">Vehicle:</span> {{ $delivery->vehicle?->number_plate }}</div>
                <div><span class="text-gray-500">Status:</span> {{ ucwords(str_replace('_', ' ', $delivery->status)) }}</div>
                <div><span class="text-gray-500">Address:</span> {{ $delivery->delivery_address }}</div>
                <div><span class="text-gray-500">Contact:</span> {{ $delivery->contact_mobile }}</div>
                @if ($delivery->rider)
                    <div><span class="text-gray-500">Rider:</span> {{ $delivery->rider->name }}</div>
                @endif
                @if ($delivery->assigned_at)
                    <div><span class="text-gray-500">Assigned:</span> {{ $delivery->assigned_at->format('d M Y H:i') }}</div>
                @endif
                @if ($delivery->delivered_at)
                    <div><span class="text-gray-500">Delivered:</span> {{ $delivery->delivered_at->format('d M Y H:i') }}</div>
                @endif
                @if ($delivery->failed_at)
                    <div><span class="text-gray-500">Failed:</span> {{ $delivery->failed_at->format('d M Y H:i') }}</div>
                @endif
            </div>
            <x-delivery-map :delivery="$delivery" />
        @endisset
    </div>
</x-guest-layout>
