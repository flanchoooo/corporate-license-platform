<?php

namespace App\Services;

use App\Models\CbzDirectPayment;
use App\Models\Corporate;
use App\Models\User;
use Illuminate\Support\Str;

class CbzDirectPaymentService
{
    public function __construct(private readonly WalletService $wallets)
    {
    }

    public function initiate(Corporate $corporate, User $user, array $payload): CbzDirectPayment
    {
        $wallet = $corporate->wallet()->firstOrCreate();

        return CbzDirectPayment::create([
            'corporate_id' => $corporate->id,
            'wallet_id' => $wallet->id,
            'initiated_by' => $user->id,
            'payment_reference' => 'CBZ-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
            'bank_reference' => $payload['bank_reference'] ?? null,
            'payer_name' => $payload['payer_name'],
            'account_number' => $payload['account_number'] ?? null,
            'amount_cents' => $payload['amount_cents'],
            'status' => 'pending',
            'provider_payload' => [
                'driver' => 'stub',
                'message' => 'Pending CBZ Direct payment confirmation.',
            ],
        ]);
    }

    public function markSuccessful(CbzDirectPayment $payment, array $payload = []): CbzDirectPayment
    {
        if ($payment->status === 'success') {
            return $payment;
        }

        $this->wallets->credit(
            $payment->corporate,
            $payment->amount_cents,
            'CBZ Direct payment '.$payment->payment_reference,
            $payload
        );

        $payment->update([
            'status' => 'success',
            'provider_payload' => array_merge($payment->provider_payload ?? [], $payload),
            'completed_at' => now(),
        ]);

        return $payment->fresh();
    }

    public function markFailed(CbzDirectPayment $payment, array $payload = []): CbzDirectPayment
    {
        $payment->update([
            'status' => 'failed',
            'provider_payload' => array_merge($payment->provider_payload ?? [], $payload),
        ]);

        return $payment->fresh();
    }
}
