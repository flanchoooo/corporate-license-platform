<x-guest-layout>
    <div class="space-y-5">
        <h1 class="text-xl font-semibold text-gray-950">Credit Application Submitted</h1>
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            Application #{{ $application->id }} is pending admin approval.
        </div>
        <a href="{{ route('bot.menu') }}" class="block rounded-md bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white">Back to Menu</a>
    </div>
</x-guest-layout>
