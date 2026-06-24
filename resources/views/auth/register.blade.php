<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="company_name" :value="__('Company Name')" />
            <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autofocus />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="registration_number" :value="__('Registration Number')" />
            <x-text-input id="registration_number" class="block mt-1 w-full" type="text" name="registration_number" :value="old('registration_number')" required />
            <x-input-error :messages="$errors->get('registration_number')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="tax_number" :value="__('Tax Number')" />
            <x-text-input id="tax_number" class="block mt-1 w-full" type="text" name="tax_number" :value="old('tax_number')" />
            <x-input-error :messages="$errors->get('tax_number')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="physical_address" :value="__('Physical Address')" />
            <x-text-input id="physical_address" class="block mt-1 w-full" type="text" name="physical_address" :value="old('physical_address')" required />
            <x-input-error :messages="$errors->get('physical_address')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="contact_person" :value="__('Contact Person')" />
            <x-text-input id="contact_person" class="block mt-1 w-full" type="text" name="contact_person" :value="old('contact_person')" required />
            <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('Phone Number')" />
            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
