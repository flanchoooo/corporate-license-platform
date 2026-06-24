@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Credit Application #{{ $application->id }}</h2></x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('status') }}</div>
            @endif
            <div class="grid gap-6 md:grid-cols-2">
                <div class="bg-white p-6 shadow-sm rounded-lg">
                    <h3 class="font-semibold">KYC</h3>
                    <div class="mt-4 space-y-2 text-sm">
                        <div><span class="text-gray-500">Name:</span> {{ $application->name }} {{ $application->surname }}</div>
                        <div><span class="text-gray-500">National ID:</span> {{ $application->national_id }}</div>
                        <div><span class="text-gray-500">Mobile:</span> {{ $application->mobile_number }}</div>
                        <div><span class="text-gray-500">Address:</span> {{ $application->address }}</div>
                    </div>
                </div>
                <div class="bg-white p-6 shadow-sm rounded-lg">
                    <h3 class="font-semibold">Vehicle and Quote</h3>
                    <div class="mt-4 space-y-2 text-sm">
                        <div><span class="text-gray-500">Plate:</span> {{ $application->vehicle->number_plate }}</div>
                        <div><span class="text-gray-500">Vehicle:</span> {{ $application->vehicle->make }} {{ $application->vehicle->model }}</div>
                        <div><span class="text-gray-500">Quote:</span> {{ $application->quote->quote_number }}</div>
                        <div><span class="text-gray-500">Total:</span> {{ $money($application->quote->total_cents) }}</div>
                        <div><span class="text-gray-500">Status:</span> {{ ucfirst($application->status) }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 shadow-sm rounded-lg">
                <h3 class="font-semibold">Delivery</h3>
                <div class="mt-4 space-y-2 text-sm">
                    <div><span class="text-gray-500">Address:</span> {{ $application->delivery_address }}</div>
                    <div><span class="text-gray-500">Mobile:</span> {{ $application->delivery_mobile }}</div>
                    <div><span class="text-gray-500">Landmark:</span> {{ $application->delivery_landmark ?: 'None' }}</div>
                </div>
            </div>

            @if($application->status === 'pending')
                <div class="bg-white p-6 shadow-sm rounded-lg">
                    <div class="grid gap-4 md:grid-cols-2">
                        <form method="POST" action="{{ route('admin.credit-applications.approve', $application) }}" class="space-y-3">
                            @csrf
                            <x-input-label for="approve_notes" value="Approval Notes" />
                            <textarea id="approve_notes" name="admin_notes" class="block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            <x-primary-button>Approve Credit</x-primary-button>
                        </form>
                        <form method="POST" action="{{ route('admin.credit-applications.reject', $application) }}" class="space-y-3">
                            @csrf
                            <x-input-label for="reject_notes" value="Rejection Notes" />
                            <textarea id="reject_notes" name="admin_notes" class="block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            <x-danger-button>Reject Credit</x-danger-button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
