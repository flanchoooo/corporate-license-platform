@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">License Disks</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500"><tr><th class="px-6 py-3">Reference</th><th class="px-6 py-3">Vehicle</th><th class="px-6 py-3">Valid Until</th><th class="px-6 py-3">Paid</th><th class="px-6 py-3"></th></tr></thead>
                    <tbody class="divide-y">
                        @forelse($licenseDisks as $disk)
                            <tr>
                                <td class="px-6 py-3 font-medium">{{ $disk->reference_number }}</td>
                                <td class="px-6 py-3">{{ $disk->vehicle->number_plate }}</td>
                                <td class="px-6 py-3">{{ $disk->valid_until->format('d M Y') }}</td>
                                <td class="px-6 py-3">{{ $money($disk->total_paid_cents) }}</td>
                                <td class="px-6 py-3 text-right"><a class="text-indigo-700 font-medium" href="{{ route('license-disks.show', $disk) }}">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-gray-500">No license disks generated.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $licenseDisks->links() }}
        </div>
    </div>
</x-app-layout>
