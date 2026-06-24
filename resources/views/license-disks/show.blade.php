@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $licenseDisk->reference_number }}</h2></x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-8 space-y-6">
                <div class="flex justify-between border-b pb-4">
                    <div><div class="text-xl font-semibold">{{ $licenseDisk->vehicle->number_plate }}</div><div class="text-sm text-gray-500">{{ $licenseDisk->vehicle->make }} {{ $licenseDisk->vehicle->model }}</div></div>
                    <div class="text-right"><div class="text-sm text-gray-500">Valid until</div><div class="font-semibold">{{ $licenseDisk->valid_until->format('d M Y') }}</div></div>
                </div>
                <div class="grid gap-4 md:grid-cols-2 text-sm">
                    <div><span class="text-gray-500">Company:</span> {{ $licenseDisk->corporate->company_name }}</div>
                    <div><span class="text-gray-500">Reference:</span> {{ $licenseDisk->reference_number }}</div>
                    <div><span class="text-gray-500">Radio:</span> {{ $money($licenseDisk->radio_license_fee_cents) }}</div>
                    <div><span class="text-gray-500">Insurance:</span> {{ $money($licenseDisk->insurance_fee_cents) }}</div>
                    <div><span class="text-gray-500">ZINARA:</span> {{ $money($licenseDisk->zinara_fee_cents) }}</div>
                    <div><span class="text-gray-500">Total:</span> {{ $money($licenseDisk->total_paid_cents) }}</div>
                </div>
                <div class="flex justify-end"><a href="{{ route('license-disks.pdf', $licenseDisk) }}" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md text-xs font-semibold uppercase tracking-widest text-white">Download PDF</a></div>
            </div>
        </div>
    </div>
</x-app-layout>
