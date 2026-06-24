<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Credit Applications</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3">Applicant</th><th class="px-6 py-3">Vehicle</th><th class="px-6 py-3">Quote</th><th class="px-6 py-3">Status</th><th class="px-6 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($applications as $application)
                            <tr>
                                <td class="px-6 py-3 font-medium">{{ $application->name }} {{ $application->surname }}<div class="text-gray-500">{{ $application->national_id }}</div></td>
                                <td class="px-6 py-3">{{ $application->vehicle->number_plate }}</td>
                                <td class="px-6 py-3">{{ $application->quote->quote_number }}</td>
                                <td class="px-6 py-3 capitalize">{{ $application->status }}</td>
                                <td class="px-6 py-3 text-right"><a class="font-medium text-indigo-700" href="{{ route('admin.credit-applications.show', $application) }}">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-gray-500">No credit applications.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $applications->links() }}
        </div>
    </div>
</x-app-layout>
