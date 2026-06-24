<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $vehicle->number_plate }}</h2></x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid gap-4 md:grid-cols-3 text-sm">
                    <div><div class="text-gray-500">Vehicle</div><div class="font-semibold">{{ $vehicle->make }} {{ $vehicle->model }}</div></div>
                    <div><div class="text-gray-500">Year</div><div class="font-semibold">{{ $vehicle->year }}</div></div>
                    <div><div class="text-gray-500">Engine Capacity</div><div class="font-semibold">{{ number_format($vehicle->engine_capacity) }} CC</div></div>
                    <div><div class="text-gray-500">Type</div><div class="font-semibold">{{ $vehicle->vehicle_type }}</div></div>
                    <div><div class="text-gray-500">Fuel</div><div class="font-semibold">{{ $vehicle->fuel_type ?: 'Not set' }}</div></div>
                    <div><div class="text-gray-500">Owner</div><div class="font-semibold">{{ $vehicle->owner_name ?: $vehicle->corporate->company_name }}</div></div>
                </div>
                @if(Auth::user()->canWriteCorporateData())
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end">
                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest text-gray-700">Edit</a>
                        <form method="POST" action="{{ route('quotes.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            @csrf
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                            <input type="hidden" name="include_carbon_tax" value="1">
                            <div>
                                <x-input-label for="insurance_type" value="Insurance Type" />
                                <select id="insurance_type" name="insurance_type" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm">
                                    <option value="third_party">Third Party</option>
                                    <option value="full_cover">Full Cover</option>
                                </select>
                            </div>
                            <x-primary-button>Generate Quote</x-primary-button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
