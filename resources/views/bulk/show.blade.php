<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Import Result</h2></x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            <div class="grid gap-4 md:grid-cols-4">
                <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Rows</div><div class="text-2xl font-semibold">{{ $import->total_rows }}</div></div>
                <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Imported</div><div class="text-2xl font-semibold">{{ $import->imported_rows }}</div></div>
                <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Failed</div><div class="text-2xl font-semibold">{{ $import->failed_rows }}</div></div>
                <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Status</div><div class="text-lg font-semibold">{{ str_replace('_', ' ', $import->status) }}</div></div>
            </div>
            @if($import->errors->isNotEmpty())
                <div class="flex justify-end"><a href="{{ route('bulk.imports.errors', $import) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest text-gray-700">Download Errors</a></div>
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-500"><tr><th class="px-6 py-3">Row</th><th class="px-6 py-3">Plate</th><th class="px-6 py-3">Error</th></tr></thead>
                        <tbody class="divide-y">
                            @foreach($import->errors as $error)
                                <tr><td class="px-6 py-3">{{ $error->row_number }}</td><td class="px-6 py-3">{{ $error->number_plate }}</td><td class="px-6 py-3">{{ $error->message }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
