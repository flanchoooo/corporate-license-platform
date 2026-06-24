@php($money = fn ($cents) => 'USD '.number_format(($cents ?? 0) / 100, 2))

<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Payments</h2></x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="bg-white p-6 shadow-sm rounded-lg">
                    <div class="text-sm text-gray-500">Total spent</div>
                    <div class="text-3xl font-semibold mt-2">{{ $money($totalSpentCents) }}</div>
                </div>
                @if(Auth::user()->canWriteCorporateData())
                    <form method="POST" action="{{ route('wallet.ecocash.store') }}" class="bg-white p-6 shadow-sm rounded-lg grid gap-4">
                        @csrf
                        <div class="font-semibold text-gray-900">EcoCash</div>
                        <div>
                            <x-input-label for="mobile_number" value="EcoCash Number" />
                            <x-text-input id="mobile_number" class="mt-1 block w-full" name="mobile_number" value="{{ old('mobile_number') }}" />
                            <x-input-error :messages="$errors->get('mobile_number')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="amount" value="Amount" />
                            <x-text-input id="amount" class="mt-1 block w-full" name="amount" type="number" step="0.01" value="{{ old('amount') }}" />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>
                        <div class="flex items-end"><x-primary-button>Top Up</x-primary-button></div>
                    </form>

                    <form method="POST" action="{{ route('wallet.cbz-direct.store') }}" class="bg-white p-6 shadow-sm rounded-lg grid gap-4">
                        @csrf
                        <div class="font-semibold text-gray-900">CBZ Direct Payment</div>
                        <div>
                            <x-input-label for="payer_name" value="Payer Name" />
                            <x-text-input id="payer_name" class="mt-1 block w-full" name="payer_name" value="{{ old('payer_name', $corporate?->company_name) }}" />
                            <x-input-error :messages="$errors->get('payer_name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="account_number" value="Account Number" />
                            <x-text-input id="account_number" class="mt-1 block w-full" name="account_number" value="{{ old('account_number') }}" />
                            <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="bank_reference" value="Bank Reference" />
                            <x-text-input id="bank_reference" class="mt-1 block w-full" name="bank_reference" value="{{ old('bank_reference') }}" />
                            <x-input-error :messages="$errors->get('bank_reference')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="cbz_amount" value="Amount" />
                            <x-text-input id="cbz_amount" class="mt-1 block w-full" name="amount" type="number" step="0.01" value="{{ old('amount') }}" />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>
                        <div class="flex items-end"><x-primary-button>Capture Payment</x-primary-button></div>
                    </form>
                @endif
            </div>
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b font-semibold">Recent payment activity</div>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500"><tr><th class="px-6 py-3">Reference</th><th class="px-6 py-3">Description</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Amount</th><th class="px-6 py-3">Date</th><th class="px-6 py-3"></th></tr></thead>
                    <tbody class="divide-y">
                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td class="px-6 py-3">{{ $transaction['reference'] }}</td>
                                <td class="px-6 py-3">{{ $transaction['description'] }}</td>
                                <td class="px-6 py-3 capitalize">{{ str_replace('_', ' ', $transaction['status']) }}</td>
                                <td class="px-6 py-3">{{ $money($transaction['amount_cents']) }}</td>
                                <td class="px-6 py-3">{{ $transaction['created_at']->format('d M Y H:i') }}</td>
                                <td class="px-6 py-3 text-right">
                                    @if($transaction['approve_url'])
                                        <form method="POST" action="{{ $transaction['approve_url'] }}">
                                            @csrf
                                            <x-primary-button>Approve</x-primary-button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-6 text-gray-500">No recent payment activity.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
