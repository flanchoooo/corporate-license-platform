<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Vehicles</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            <div class="flex justify-between items-center">
                <div></div>
                @if(Auth::user()->canWriteCorporateData())
                    <div class="flex gap-2">
                        <a href="{{ route('bulk.upload') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest text-gray-700">Upload CSV</a>
                        <a href="{{ route('vehicles.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md text-xs font-semibold uppercase tracking-widest text-white">Add Vehicle</a>
                    </div>
                @endif
            </div>
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3">Plate</th><th class="px-6 py-3">Vehicle</th><th class="px-6 py-3">CC</th><th class="px-6 py-3">Type</th><th class="px-6 py-3">Expires</th><th class="px-6 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td class="px-6 py-4 font-semibold">{{ $vehicle->number_plate }}</td>
                                <td class="px-6 py-4">{{ $vehicle->make }} {{ $vehicle->model }}<div class="text-gray-500">{{ $vehicle->year }}</div></td>
                                <td class="px-6 py-4">{{ number_format($vehicle->engine_capacity) }}</td>
                                <td class="px-6 py-4">{{ $vehicle->vehicle_type }}</td>
                                <td class="px-6 py-4">{{ optional($vehicle->last_license_expires_at)->format('d M Y') ?? 'Not set' }}</td>
                                <td class="px-6 py-4 text-right"><a class="text-indigo-700 font-medium" href="{{ route('vehicles.show', $vehicle) }}">Open</a></td>
                            </tr>
                        @empty
                            <tr><td class="px-6 py-6 text-gray-500" colspan="6">No vehicles registered.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $vehicles->links() }}
        </div>
    </div>
</x-app-layout>
