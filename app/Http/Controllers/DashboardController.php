<?php

namespace App\Http\Controllers;

use App\Models\Corporate;
use App\Models\CbzDirectPayment;
use App\Models\EcoCashTopUp;
use App\Models\Quote;
use App\Models\Vehicle;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $corporate = $user->corporate;

        if ($user->isSuperAdmin()) {
            $stats = [
                'corporates' => Corporate::count(),
                'pending_corporates' => Corporate::where('status', 'pending')->count(),
                'vehicles' => Vehicle::count(),
                'spend_cents' => Quote::where('status', 'paid')->sum('total_cents'),
            ];

            return view('dashboard', compact('stats'));
        }

        $stats = [
            'vehicles' => Vehicle::where('corporate_id', $corporate?->id)->count(),
            'expiring_soon' => Vehicle::where('corporate_id', $corporate?->id)->expiringSoon()->count(),
            'licenses_this_month' => Quote::where('corporate_id', $corporate?->id)->where('status', 'paid')->whereMonth('purchased_at', now()->month)->count(),
            'outstanding_arrears_cents' => Quote::where('corporate_id', $corporate?->id)->where('status', 'pending')->sum('total_cents'),
            'pending_quotes' => Quote::where('corporate_id', $corporate?->id)->where('status', 'pending')->count(),
            'total_spend_cents' => Quote::where('corporate_id', $corporate?->id)->where('status', 'paid')->sum('total_cents'),
        ];

        $walletTransactions = WalletTransaction::where('corporate_id', $corporate?->id)
            ->latest()
            ->limit(8)
            ->get()
            ->toBase()
            ->map(fn (WalletTransaction $transaction) => [
                'reference' => $transaction->reference,
                'type' => $transaction->type,
                'status' => 'posted',
                'amount_cents' => $transaction->amount_cents,
                'created_at' => $transaction->created_at,
            ]);

        $pendingEcoCashTopUps = EcoCashTopUp::where('corporate_id', $corporate?->id)
            ->where('status', '!=', 'success')
            ->latest()
            ->limit(8)
            ->get()
            ->toBase()
            ->map(fn (EcoCashTopUp $topUp) => [
                'reference' => $topUp->transaction_reference,
                'type' => 'top_up',
                'status' => $topUp->status,
                'amount_cents' => $topUp->amount_cents,
                'created_at' => $topUp->created_at,
            ]);

        $pendingCbzPayments = CbzDirectPayment::where('corporate_id', $corporate?->id)
            ->where('status', '!=', 'success')
            ->latest()
            ->limit(8)
            ->get()
            ->toBase()
            ->map(fn (CbzDirectPayment $payment) => [
                'reference' => $payment->payment_reference,
                'type' => 'cbz_direct',
                'status' => $payment->status,
                'amount_cents' => $payment->amount_cents,
                'created_at' => $payment->created_at,
            ]);

        $recentTransactions = collect()
            ->merge($walletTransactions->all())
            ->merge($pendingEcoCashTopUps->all())
            ->merge($pendingCbzPayments->all())
            ->sortByDesc('created_at')
            ->take(8)
            ->values();

        return view('dashboard', compact('corporate', 'stats', 'recentTransactions'));
    }
}
