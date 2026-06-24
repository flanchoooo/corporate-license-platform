<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Delivery Orders</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3">Order</th><th class="px-6 py-3">Vehicle</th><th class="px-6 py-3">Address</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Dispatch</th><th class="px-6 py-3">State</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($orders as $order)
                            <tr>
                                <td class="px-6 py-3 font-medium">#{{ $order->id }}<div class="text-gray-500">{{ $order->quote->quote_number }}</div></td>
                                <td class="px-6 py-3">{{ $order->vehicle->number_plate }}</td>
                                <td class="px-6 py-3">{{ $order->delivery_address }}<div class="text-gray-500">{{ $order->contact_mobile }}</div></td>
                                <td class="px-6 py-3 capitalize">{{ str_replace('_', ' ', $order->status) }}</td>
                                <td class="px-6 py-3">
                                    <form method="POST" action="{{ route('admin.delivery-orders.assign', $order) }}" class="flex gap-2">
                                        @csrf
                                        <select name="rider_user_id" class="w-40 rounded-md border-gray-300 text-sm">
                                            @foreach($riders as $rider)
                                                <option value="{{ $rider->id }}" @selected($order->rider_user_id === $rider->id)>{{ $rider->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded bg-gray-900 px-3 py-2 text-xs font-semibold text-white">Dispatch</button>
                                    </form>
                                </td>
                                <td class="px-6 py-3">
                                    <form method="POST" action="{{ route('admin.delivery-orders.status', $order) }}" class="flex gap-2">
                                        @csrf
                                        <select name="status" class="w-36 rounded-md border-gray-300 text-sm">
                                            @foreach(['pending', 'in_transit', 'delivered', 'failed'] as $status)
                                                <option value="{{ $status }}" @selected($order->status === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700">Update</button>
                                    </form>
                                    @if($order->status !== 'delivered')
                                        <form method="POST" action="{{ route('admin.delivery-orders.status', $order) }}" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="status" value="delivered">
                                            <button class="rounded bg-emerald-700 px-3 py-2 text-xs font-semibold text-white">Mark Delivered</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('deliveries.show', $order) }}" class="mt-2 inline-block text-xs font-semibold text-indigo-700">View map</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-6 text-gray-500">No delivery orders.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
