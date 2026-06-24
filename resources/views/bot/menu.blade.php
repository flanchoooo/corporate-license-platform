<x-guest-layout>
    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif

        <div>
            <h1 class="text-xl font-semibold text-gray-950">Vehicle Licensing Bot</h1>
            <p class="mt-2 text-sm text-gray-600">Choose an option to begin. You only need the vehicle number plate.</p>
        </div>

        <div class="grid gap-3">
            <a href="{{ route('bot.plate', 'buy') }}" class="rounded-md bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white">1. Buy License</a>
            <a href="{{ route('bot.plate', 'credit') }}" class="rounded-md border border-gray-300 bg-white px-4 py-3 text-center text-sm font-semibold text-gray-800">2. Buy License on Credit</a>
            <a href="{{ route('bot.plate', 'details') }}" class="rounded-md border border-gray-300 bg-white px-4 py-3 text-center text-sm font-semibold text-gray-800">3. View Vehicle Details</a>
            <a href="{{ route('bot.delivery.track') }}" class="rounded-md border border-gray-300 bg-white px-4 py-3 text-center text-sm font-semibold text-gray-800">4. Track Mutero Delivery</a>
        </div>
    </div>
</x-guest-layout>
