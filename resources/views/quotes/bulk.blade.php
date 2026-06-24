<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Bulk Quote Generation</h2></x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('quotes.bulk.store') }}" class="bg-white shadow-sm rounded-lg overflow-hidden">
                @csrf
                <div class="p-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                        <label class="flex items-center gap-2 pb-2 text-sm text-gray-700">
                            <input type="checkbox" name="include_carbon_tax" value="1" checked class="rounded border-gray-300">
                            Carbon tax
                        </label>
                        <div>
                            <x-input-label for="insurance_type" value="Insurance Type" />
                            <select id="insurance_type" name="insurance_type" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="third_party" @selected(old('insurance_type', 'third_party') === 'third_party')>Third Party</option>
                                <option value="full_cover" @selected(old('insurance_type') === 'full_cover')>Full Cover</option>
                            </select>
                            <x-input-error :messages="$errors->get('insurance_type')" class="mt-2" />
                        </div>
                    </div>
                    <x-primary-button>Generate Selected</x-primary-button>
                </div>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3"></th><th class="px-6 py-3">Plate</th><th class="px-6 py-3">Vehicle</th><th class="px-6 py-3">CC</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($vehicles as $vehicle)
                            <tr>
                                <td class="px-6 py-3"><input type="checkbox" name="vehicle_ids[]" value="{{ $vehicle->id }}" class="rounded border-gray-300"></td>
                                <td class="px-6 py-3 font-medium">{{ $vehicle->number_plate }}</td>
                                <td class="px-6 py-3">{{ $vehicle->make }} {{ $vehicle->model }}</td>
                                <td class="px-6 py-3">{{ number_format($vehicle->engine_capacity) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <x-input-error :messages="$errors->get('vehicle_ids')" class="p-6" />
            </form>
        </div>
    </div>
</x-app-layout>
