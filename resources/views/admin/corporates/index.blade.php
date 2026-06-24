<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Corporate approvals</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3">Company</th><th class="px-6 py-3">Registration</th><th class="px-6 py-3">Contact</th><th class="px-6 py-3">Status</th><th class="px-6 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($corporates as $corporate)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $corporate->company_name }}<div class="text-gray-500">{{ $corporate->email }}</div></td>
                                <td class="px-6 py-4">{{ $corporate->registration_number }}</td>
                                <td class="px-6 py-4">{{ $corporate->contact_person }}<div class="text-gray-500">{{ $corporate->phone_number }}</div></td>
                                <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $corporate->status) }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if($corporate->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.corporates.approve', $corporate) }}">
                                            @csrf @method('PATCH')
                                            <x-primary-button>Approve</x-primary-button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $corporates->links() }}</div>
        </div>
    </div>
</x-app-layout>
