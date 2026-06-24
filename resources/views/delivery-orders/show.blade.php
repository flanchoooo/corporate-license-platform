<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Delivery MUTERO-{{ str_pad((string) $deliveryOrder->id, 6, '0', STR_PAD_LEFT) }}</h2></x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Vehicle</div><div class="mt-1 font-semibold">{{ $deliveryOrder->vehicle?->number_plate }}</div></div>
                <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Status</div><div class="mt-1 font-semibold capitalize">{{ str_replace('_', ' ', $deliveryOrder->status) }}</div></div>
                <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Rider</div><div class="mt-1 font-semibold">{{ $deliveryOrder->rider?->name ?? 'Not dispatched' }}</div></div>
            </div>

            <x-delivery-map :delivery="$deliveryOrder" />

            <div class="bg-white p-5 shadow-sm rounded-lg text-sm space-y-2">
                <div><span class="text-gray-500">Address:</span> {{ $deliveryOrder->delivery_address }}</div>
                <div><span class="text-gray-500">Contact:</span> {{ $deliveryOrder->contact_mobile }}</div>
                <div><span class="text-gray-500">Landmark:</span> {{ $deliveryOrder->landmark ?: 'None' }}</div>
                @if($deliveryOrder->assigned_at)<div><span class="text-gray-500">Dispatched:</span> {{ $deliveryOrder->assigned_at->format('d M Y H:i') }}</div>@endif
                @if($deliveryOrder->delivered_at)<div><span class="text-gray-500">Delivered:</span> {{ $deliveryOrder->delivered_at->format('d M Y H:i') }}</div>@endif
            </div>
        </div>
    </div>
</x-app-layout>
