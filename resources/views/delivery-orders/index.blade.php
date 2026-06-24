<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Deliveries</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3">Order</th><th class="px-6 py-3">Vehicle</th><th class="px-6 py-3">Address</th><th class="px-6 py-3">Status</th><th class="px-6 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($deliveryOrders as $order)
                            <tr>
                                <td class="px-6 py-3 font-medium">MUTERO-{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}<div class="text-gray-500">#{{ $order->id }}</div></td>
                                <td class="px-6 py-3">{{ $order->vehicle?->number_plate }}</td>
                                <td class="px-6 py-3">{{ $order->delivery_address }}<div class="text-gray-500">{{ $order->contact_mobile }}</div></td>
                                <td class="px-6 py-3 capitalize">{{ str_replace('_', ' ', $order->status) }}</td>
                                <td class="px-6 py-3 text-right"><a class="text-indigo-700 font-medium" href="{{ route('deliveries.show', $order) }}">Track map</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-gray-500">No deliveries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $deliveryOrders->links() }}
        </div>
    </div>
</x-app-layout>
