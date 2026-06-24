<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Pricing</h2></x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.pricing.update') }}" class="bg-white shadow-sm rounded-lg overflow-hidden">
                @csrf @method('PUT')
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr><th class="px-6 py-3">Rule</th><th class="px-6 py-3">Fee Type</th><th class="px-6 py-3">CC Band</th><th class="px-6 py-3">Amount</th><th class="px-6 py-3">Active</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($pricingRules as $rule)
                            <tr>
                                <td class="px-6 py-3 font-medium">{{ $rule->name }}</td>
                                <td class="px-6 py-3">{{ str_replace('_', ' ', $rule->fee_type) }}</td>
                                <td class="px-6 py-3">{{ $rule->min_cc ?? '0' }} - {{ $rule->max_cc ?? 'above' }}</td>
                                <td class="px-6 py-3">
                                    <x-text-input class="w-32" type="number" step="0.01" name="rules[{{ $rule->id }}][amount]" value="{{ number_format($rule->amount_cents / 100, 2, '.', '') }}" />
                                </td>
                                <td class="px-6 py-3">
                                    <input type="checkbox" name="rules[{{ $rule->id }}][is_active]" value="1" @checked($rule->is_active) class="rounded border-gray-300">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-6 flex justify-end"><x-primary-button>Save Pricing</x-primary-button></div>
            </form>
        </div>
    </div>
</x-app-layout>
