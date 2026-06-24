<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Bulk CSV Upload</h2></x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="font-semibold text-gray-900">Sample vehicle CSV</div>
                    <div class="mt-1 text-sm text-gray-500">Download the template, fill in your vehicles, then upload it below.</div>
                </div>
                <a href="{{ asset('samples/sample-vehicles.csv') }}" download class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Download Sample CSV
                </a>
            </div>

            <form method="POST" action="{{ route('bulk.upload.store') }}" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf
                <div>
                    <x-input-label for="file" value="CSV or Excel file" />
                    <input id="file" type="file" name="file" class="mt-1 block w-full text-sm" required>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>
                <x-primary-button>Import Vehicles</x-primary-button>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3">File</th><th class="px-6 py-3">Imported</th><th class="px-6 py-3">Failed</th><th class="px-6 py-3">Status</th><th class="px-6 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($imports as $import)
                            <tr>
                                <td class="px-6 py-3">{{ $import->filename }}</td>
                                <td class="px-6 py-3">{{ $import->imported_rows }}</td>
                                <td class="px-6 py-3">{{ $import->failed_rows }}</td>
                                <td class="px-6 py-3">{{ str_replace('_', ' ', $import->status) }}</td>
                                <td class="px-6 py-3 text-right"><a class="text-indigo-700 font-medium" href="{{ route('bulk.imports.show', $import) }}">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-gray-500">No imports yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
