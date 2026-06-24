<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingRuleController extends Controller
{
    public function index(): View
    {
        $this->authorize('manage-platform');

        $pricingRules = PricingRule::orderBy('fee_type')->orderBy('min_cc')->get();

        return view('admin.pricing.index', compact('pricingRules'));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('manage-platform');

        $validated = $request->validate([
            'rules' => ['required', 'array'],
            'rules.*.amount' => ['required', 'numeric', 'min:0'],
            'rules.*.is_active' => ['nullable', 'boolean'],
        ]);

        foreach ($validated['rules'] as $id => $rule) {
            PricingRule::whereKey($id)->update([
                'amount_cents' => (int) round(((float) $rule['amount']) * 100),
                'is_active' => (bool) ($rule['is_active'] ?? false),
            ]);
        }

        return back()->with('status', 'Pricing updated.');
    }
}
