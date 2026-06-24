@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif

            @if(Auth::user()->isSuperAdmin())
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Corporates</div><div class="text-2xl font-semibold">{{ $stats['corporates'] }}</div></div>
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Pending approvals</div><div class="text-2xl font-semibold">{{ $stats['pending_corporates'] }}</div></div>
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Vehicles</div><div class="text-2xl font-semibold">{{ $stats['vehicles'] }}</div></div>
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Total spend</div><div class="text-2xl font-semibold">{{ $money($stats['spend_cents']) }}</div></div>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Total spent</div><div class="text-2xl font-semibold">{{ $money($stats['total_spend_cents']) }}</div></div>
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Vehicles</div><div class="text-2xl font-semibold">{{ $stats['vehicles'] }}</div></div>
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Expiring soon</div><div class="text-2xl font-semibold">{{ $stats['expiring_soon'] }}</div></div>
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Pending quotes</div><div class="text-2xl font-semibold">{{ $stats['pending_quotes'] }}</div></div>
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Licenses this month</div><div class="text-2xl font-semibold">{{ $stats['licenses_this_month'] }}</div></div>
                    <div class="bg-white p-5 shadow-sm rounded-lg"><div class="text-sm text-gray-500">Outstanding arrears</div><div class="text-2xl font-semibold">{{ $money($stats['outstanding_arrears_cents']) }}</div></div>
                </div>

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b font-semibold">Recent transactions</div>
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-500">
                            <tr><th class="px-6 py-3">Reference</th><th class="px-6 py-3">Type</th><th class="px-6 py-3">Amount</th><th class="px-6 py-3">Date</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td class="px-6 py-3">{{ $transaction['reference'] }}</td>
                                    <td class="px-6 py-3 capitalize">{{ str_replace('_', ' ', $transaction['status'] === 'posted' ? $transaction['type'] : $transaction['status'].' '.$transaction['type']) }}</td>
                                    <td class="px-6 py-3">{{ $money($transaction['amount_cents']) }}</td>
                                    <td class="px-6 py-3">{{ $transaction['created_at']->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td class="px-6 py-6 text-gray-500" colspan="4">No transactions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
