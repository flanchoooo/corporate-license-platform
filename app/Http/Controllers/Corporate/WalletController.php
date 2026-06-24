<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\CbzDirectPayment;
use App\Models\EcoCashTopUp;
use App\Models\Quote;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(Request $request): View
    {
        $corporate = $request->user()->corporate;
        $totalSpentCents = Quote::where('corporate_id', $corporate?->id)->where('status', 'paid')->sum('total_cents');

        $walletTransactions = WalletTransaction::where('corporate_id', $corporate?->id)
            ->latest()
            ->limit(20)
            ->get()
            ->toBase()
            ->map(fn (WalletTransaction $transaction) => [
                'reference' => $transaction->reference,
                'description' => $transaction->description,
                'type' => $transaction->type,
                'status' => 'posted',
                'amount_cents' => $transaction->amount_cents,
                'created_at' => $transaction->created_at,
                'approve_url' => null,
            ]);

        $pendingEcoCashTopUps = EcoCashTopUp::where('corporate_id', $corporate?->id)
            ->where('status', '!=', 'success')
            ->latest()
            ->limit(20)
            ->get()
            ->toBase()
            ->map(fn (EcoCashTopUp $topUp) => [
                'reference' => $topUp->transaction_reference,
                'description' => 'EcoCash top-up from '.$topUp->mobile_number,
                'type' => 'top_up',
                'status' => $topUp->status,
                'amount_cents' => $topUp->amount_cents,
                'created_at' => $topUp->created_at,
                'approve_url' => $topUp->status === 'pending' ? route('wallet.ecocash.approve', $topUp) : null,
            ]);

        $pendingCbzPayments = CbzDirectPayment::where('corporate_id', $corporate?->id)
            ->where('status', '!=', 'success')
            ->latest()
            ->limit(20)
            ->get()
            ->toBase()
            ->map(fn (CbzDirectPayment $payment) => [
                'reference' => $payment->payment_reference,
                'description' => 'CBZ Direct payment from '.$payment->payer_name,
                'type' => 'cbz_direct',
                'status' => $payment->status,
                'amount_cents' => $payment->amount_cents,
                'created_at' => $payment->created_at,
                'approve_url' => $payment->status === 'pending' ? route('wallet.cbz-direct.approve', $payment) : null,
            ]);

        $recentTransactions = collect()
            ->merge($walletTransactions->all())
            ->merge($pendingEcoCashTopUps->all())
            ->merge($pendingCbzPayments->all())
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        return view('wallet.index', compact('corporate', 'recentTransactions', 'totalSpentCents'));
    }
}
