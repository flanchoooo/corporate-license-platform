<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white shadow-sm rounded-lg p-6 grid gap-4 md:grid-cols-3">
                @csrf
                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" class="mt-1 block w-full" name="name" value="{{ old('name') }}" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="mt-1 block w-full" name="email" type="email" value="{{ old('email') }}" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="role" value="Role" />
                    <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}">{{ str_replace('_', ' ', $role->value) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="corporate_id" value="Corporate" />
                    <select id="corporate_id" name="corporate_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">None</option>
                        @foreach($corporates as $corporate)
                            <option value="{{ $corporate->id }}">{{ $corporate->company_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('corporate_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="mt-1 block w-full" name="password" type="password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" />
                    <x-text-input id="password_confirmation" class="mt-1 block w-full" name="password_confirmation" type="password" />
                </div>
                <div class="md:col-span-3 flex justify-end"><x-primary-button>Create User</x-primary-button></div>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500"><tr><th class="px-6 py-3">Name</th><th class="px-6 py-3">Email</th><th class="px-6 py-3">Role</th><th class="px-6 py-3">Corporate</th><th class="px-6 py-3">Approved</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-3 font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-3">{{ $user->email }}</td>
                                <td class="px-6 py-3">{{ str_replace('_', ' ', $user->role) }}</td>
                                <td class="px-6 py-3">{{ $user->corporate?->company_name ?? 'Platform' }}</td>
                                <td class="px-6 py-3">{{ $user->is_approved ? 'Yes' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
