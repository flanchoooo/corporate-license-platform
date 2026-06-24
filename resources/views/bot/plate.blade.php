@php($titles = ['buy' => 'Buy License', 'credit' => 'Buy License on Credit', 'details' => 'View Vehicle Details'])

<x-guest-layout>
    <form method="POST" action="{{ route('bot.quote') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="flow" value="{{ $flow }}">

        <div>
            <h1 class="text-xl font-semibold text-gray-950">{{ $titles[$flow] }}</h1>
            <p class="mt-2 text-sm text-gray-600">Enter the vehicle number plate. The system will fetch vehicle details and auto-generate the quote.</p>
        </div>

        <div>
            <x-input-label for="number_plate" value="Number Plate" />
            <x-text-input id="number_plate" name="number_plate" class="mt-1 block w-full uppercase" value="{{ old('number_plate') }}" required autofocus />
            <x-input-error :messages="$errors->get('number_plate')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="insurance_type" value="Insurance Type" />
            <select id="insurance_type" name="insurance_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="third_party" @selected(old('insurance_type', 'third_party') === 'third_party')>Third Party</option>
                <option value="full_cover" @selected(old('insurance_type') === 'full_cover')>Full Cover</option>
            </select>
            <x-input-error :messages="$errors->get('insurance_type')" class="mt-2" />
        </div>

        <div class="flex justify-between">
            <a href="{{ route('bot.menu') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
            <x-primary-button>Continue</x-primary-button>
        </div>
    </form>
</x-guest-layout>
