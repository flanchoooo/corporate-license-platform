@props(['delivery'])

@php
    $origin = 'Marlven Corner Harare';
    $destination = trim($delivery->delivery_address);
    $destinationSearch = $destination !== '' ? $destination.', Harare, Zimbabwe' : 'Harare, Zimbabwe';
    $mapEmbedUrl = 'https://maps.google.com/maps?saddr='.rawurlencode($origin).'&daddr='.rawurlencode($destinationSearch).'&output=embed';
    $directionsUrl = 'https://www.google.com/maps/dir/?api=1&origin='.rawurlencode($origin).'&destination='.rawurlencode($destinationSearch).'&travelmode=driving';
    $status = $delivery->status;
    $progress = match ($status) {
        'pending' => 10,
        'assigned' => 38,
        'in_transit' => 62,
        'delivered' => 100,
        'failed' => 42,
        default => 10,
    };
    $label = match ($status) {
        'pending' => 'Awaiting dispatch',
        'assigned' => 'Rider assigned',
        'in_transit' => 'Bike in transit',
        'delivered' => 'Delivered',
        'failed' => 'Delivery issue',
        default => ucwords(str_replace('_', ' ', $status)),
    };
@endphp

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
    <div class="flex flex-col gap-3 border-b px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm font-semibold text-gray-950">Delivery Map</div>
            <div class="text-xs text-gray-500">From {{ $origin }} to {{ $delivery->delivery_address }}</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                Open in Google Maps
            </a>
            <div class="rounded-full bg-gray-900 px-3 py-1 text-xs font-semibold text-white">{{ $label }}</div>
        </div>
    </div>

    <div class="grid gap-0 lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,.65fr)]">
        <div class="relative min-h-80 bg-gray-100">
            <iframe
                title="Delivery route from {{ $origin }} to {{ $delivery->delivery_address }}"
                src="{{ $mapEmbedUrl }}"
                class="absolute inset-0 h-full w-full border-0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen
            ></iframe>
        </div>

        <div class="relative h-80 bg-emerald-50" style="background-image: linear-gradient(rgba(16, 185, 129, .16) 1px, transparent 1px), linear-gradient(90deg, rgba(16, 185, 129, .16) 1px, transparent 1px); background-size: 32px 32px;">
            <div class="absolute inset-x-10 top-1/2 h-2 -translate-y-1/2 rounded-full bg-gray-300">
                <div class="h-2 rounded-full bg-emerald-600" style="width: {{ $progress }}%;"></div>

                @if($status !== 'pending')
                    <div class="absolute top-1/2 -translate-x-1/2 -translate-y-1/2" style="left: {{ $progress }}%;">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full border-4 border-white bg-amber-400 text-xs font-black text-gray-950 shadow-lg">
                            BIKE
                        </div>
                    </div>
                @endif
            </div>

            <div class="absolute left-6 top-1/2 -translate-y-1/2">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-900 text-xs font-bold text-white shadow">HQ</div>
                <div class="mt-2 text-xs font-semibold text-gray-700">Dispatch</div>
            </div>

            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-right">
                <div class="ml-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-700 text-xs font-bold text-white shadow">TO</div>
                <div class="mt-2 text-xs font-semibold text-gray-700">Delivery</div>
            </div>

            @if($status === 'pending')
                <div class="absolute inset-x-6 bottom-6 rounded-md bg-white/90 px-4 py-3 text-sm text-gray-700 shadow-sm">This delivery is pending. The route is ready and the bike will move once admin dispatches a rider.</div>
            @elseif($status === 'delivered')
                <div class="absolute inset-x-6 bottom-6 rounded-md bg-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-sm">Delivered {{ optional($delivery->delivered_at)->format('d M Y H:i') }}</div>
            @else
                <div class="absolute inset-x-6 bottom-6 rounded-md bg-white/90 px-4 py-3 text-sm text-gray-700 shadow-sm">Rider is on the way from {{ $origin }}. Keep this page open or refresh for the latest status.</div>
            @endif
        </div>
    </div>
</div>
