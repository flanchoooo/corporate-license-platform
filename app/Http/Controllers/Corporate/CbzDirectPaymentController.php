<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CbzDirectPayment;
use App\Services\CbzDirectPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CbzDirectPaymentController extends Controller
{
    public function store(Request $request, CbzDirectPaymentService $payments): RedirectResponse
    {
        abort_unless($request->user()->canWriteCorporateData(), 403);

        $validated = $request->validate([
            'payer_name' => ['required', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:80'],
            'bank_reference' => ['nullable', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:1', 'max:100000'],
        ]);

        $payment = $payments->initiate($request->user()->corporate, $request->user(), [
            'payer_name' => $validated['payer_name'],
            'account_number' => $validated['account_number'] ?? null,
            'bank_reference' => $validated['bank_reference'] ?? null,
            'amount_cents' => (int) round(((float) $validated['amount']) * 100),
        ]);

        return redirect()->route('wallet.index')->with('status', 'CBZ Direct payment captured: '.$payment->payment_reference);
    }

    public function approve(Request $request, CbzDirectPayment $payment, CbzDirectPaymentService $payments): RedirectResponse
    {
        abort_unless(
            $request->user()->canWriteCorporateData()
                && ($request->user()->isSuperAdmin() || $request->user()->corporate_id === $payment->corporate_id),
            403
        );

        $payments->markSuccessful($payment, [
            'driver' => 'manual',
            'approved_by' => $request->user()->id,
            'approved_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('wallet.index')->with('status', 'CBZ Direct payment approved and wallet credited.');
    }

    public function callback(Request $request, CbzDirectPayment $payment, CbzDirectPaymentService $payments): RedirectResponse|JsonResponse
    {
        if ($request->input('status') === 'success') {
            $payments->markSuccessful($payment, $request->all());
        } else {
            $payments->markFailed($payment, $request->all());
        }

        if (! $request->user()) {
            return response()->json(['status' => 'processed']);
        }

        return redirect()->route('wallet.index')->with('status', 'CBZ Direct callback processed.');
    }
}
