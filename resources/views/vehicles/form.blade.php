<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $vehicle->exists ? 'Edit Vehicle' : 'Add Vehicle' }}</h2></x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ $vehicle->exists ? route('vehicles.update', $vehicle) : route('vehicles.store') }}" class="bg-white shadow-sm rounded-lg p-6 grid gap-4 md:grid-cols-2">
                @csrf
                @if($vehicle->exists) @method('PUT') @endif

                @foreach([
                    'number_plate' => 'Number Plate',
                    'engine_capacity' => 'Engine Capacity (CC)',
                    'make' => 'Make',
                    'model' => 'Model',
                    'year' => 'Year',
                    'vehicle_type' => 'Vehicle Type',
                    'chassis_number' => 'Chassis Number',
                    'vin' => 'VIN',
                    'fuel_type' => 'Fuel Type',
                    'owner_name' => 'Company Owner',
                ] as $field => $label)
                    <div>
                        <x-input-label :for="$field" :value="$label" />
                        <x-text-input :id="$field" class="block mt-1 w-full" type="{{ in_array($field, ['engine_capacity', 'year']) ? 'number' : 'text' }}" :name="$field" :value="old($field, $vehicle->{$field})" />
                        <x-input-error :messages="$errors->get($field)" class="mt-2" />
                    </div>
                @endforeach

                <div>
                    <x-input-label for="last_license_expires_at" value="Last License Expires" />
                    <x-text-input id="last_license_expires_at" class="block mt-1 w-full" type="date" name="last_license_expires_at" :value="old('last_license_expires_at', optional($vehicle->last_license_expires_at)->format('Y-m-d'))" />
                    <x-input-error :messages="$errors->get('last_license_expires_at')" class="mt-2" />
                </div>

                <div class="md:col-span-2 flex justify-end gap-3">
                    <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest text-gray-700">Cancel</a>
                    <x-primary-button>Save</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
