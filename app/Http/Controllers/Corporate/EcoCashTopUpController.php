<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopUpRequest;
use App\Models\EcoCashTopUp;
use App\Services\EcoCashService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EcoCashTopUpController extends Controller
{
    public function store(TopUpRequest $request, EcoCashService $ecocash): RedirectResponse
    {
        $amountCents = (int) round(((float) $request->input('amount')) * 100);

        $topUp = $ecocash->initiate(
            $request->user()->corporate,
            $request->user(),
            $request->input('mobile_number'),
            $amountCents
        );

        return redirect()->route('wallet.index')->with('status', 'EcoCash top-up initiated: '.$topUp->transaction_reference);
    }

    public function callback(Request $request, EcoCashTopUp $topUp, EcoCashService $ecocash): RedirectResponse|JsonResponse
    {
        if ($request->input('status') === 'success') {
            $ecocash->markSuccessful($topUp, $request->all());
        } else {
            $ecocash->markFailed($topUp, $request->all());
        }

        if (! $request->user()) {
            return response()->json(['status' => 'processed']);
        }

        return redirect()->route('wallet.index')->with('status', 'EcoCash callback processed.');
    }

    public function approve(Request $request, EcoCashTopUp $topUp, EcoCashService $ecocash): RedirectResponse
    {
        abort_unless(
            $request->user()->canWriteCorporateData()
                && ($request->user()->isSuperAdmin() || $request->user()->corporate_id === $topUp->corporate_id),
            403
        );

        $ecocash->markSuccessful($topUp, [
            'driver' => 'manual',
            'approved_by' => $request->user()->id,
            'approved_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('wallet.index')->with('status', 'EcoCash top-up approved and wallet credited.');
    }
}
