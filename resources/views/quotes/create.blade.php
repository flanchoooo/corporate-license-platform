<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Generate Quote</h2></x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('quotes.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf
                <div>
                    <x-input-label for="vehicle_id" value="Vehicle" />
                    <select id="vehicle_id" name="vehicle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->number_plate }} - {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->engine_capacity }} CC)</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="include_carbon_tax" value="1" checked class="rounded border-gray-300">
                    Carbon tax
                </label>
                <div>
                    <x-input-label for="insurance_type" value="Insurance Type" />
                    <select id="insurance_type" name="insurance_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="third_party" @selected(old('insurance_type', 'third_party') === 'third_party')>Third Party</option>
                        <option value="full_cover" @selected(old('insurance_type') === 'full_cover')>Full Cover</option>
                    </select>
                    <x-input-error :messages="$errors->get('insurance_type')" class="mt-2" />
                </div>
                <div class="flex justify-end"><x-primary-button>Generate</x-primary-button></div>
            </form>
        </div>
    </div>
</x-app-layout>
