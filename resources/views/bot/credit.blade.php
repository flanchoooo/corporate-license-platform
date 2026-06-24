<x-guest-layout>
    <form method="POST" action="{{ route('bot.credit', $quote) }}" class="space-y-5">
        @csrf
        <div>
            <h1 class="text-xl font-semibold text-gray-950">Credit Application KYC</h1>
            <p class="mt-2 text-sm text-gray-600">{{ $quote->vehicle->number_plate }} quote will be submitted for admin approval.</p>
        </div>

        @foreach([
            'name' => 'Name',
            'surname' => 'Surname',
            'national_id' => 'National ID',
            'mobile_number' => 'Mobile Number',
            'address' => 'Address',
            'delivery_address' => 'Delivery Address',
            'delivery_mobile' => 'Delivery Mobile',
            'delivery_landmark' => 'Delivery Landmark',
        ] as $field => $label)
            <div>
                <x-input-label :for="$field" :value="$label" />
                <x-text-input :id="$field" :name="$field" class="mt-1 block w-full" :value="old($field)" :required="$field !== 'delivery_landmark'" />
                <x-input-error :messages="$errors->get($field)" class="mt-2" />
            </div>
        @endforeach

        <div class="flex justify-between">
            <a href="{{ route('bot.menu') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
            <x-primary-button>Submit for Approval</x-primary-button>
        </div>
    </form>
</x-guest-layout>
