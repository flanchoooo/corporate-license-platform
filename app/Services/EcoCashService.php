<?php

namespace App\Services;

use App\Models\Corporate;
use App\Models\EcoCashTopUp;
use App\Models\User;
use Illuminate\Support\Str;

class EcoCashService
{
    public function __construct(private readonly WalletService $wallets)
    {
    }

    public function initiate(Corporate $corporate, User $user, string $mobileNumber, int $amountCents): EcoCashTopUp
    {
        $wallet = $corporate->wallet()->firstOrCreate();

        return EcoCashTopUp::create([
            'corporate_id' => $corporate->id,
            'wallet_id' => $wallet->id,
            'initiated_by' => $user->id,
            'transaction_reference' => 'ECO-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
            'mobile_number' => $mobileNumber,
            'amount_cents' => $amountCents,
            'status' => 'pending',
            'provider_payload' => [
                'driver' => 'stub',
                'message' => 'Pending customer authorization on handset.',
            ],
        ]);
    }

    public function markSuccessful(EcoCashTopUp $topUp, array $payload = []): EcoCashTopUp
    {
        if ($topUp->status === 'success') {
            return $topUp;
        }

        $this->wallets->credit($topUp->corporate, $topUp->amount_cents, 'EcoCash top-up '.$topUp->transaction_reference, $payload);

        $topUp->update([
            'status' => 'success',
            'provider_payload' => array_merge($topUp->provider_payload ?? [], $payload),
            'completed_at' => now(),
        ]);

        return $topUp->fresh();
    }

    public function markFailed(EcoCashTopUp $topUp, array $payload = []): EcoCashTopUp
    {
        $topUp->update([
            'status' => 'failed',
            'provider_payload' => array_merge($topUp->provider_payload ?? [], $payload),
        ]);

        return $topUp->fresh();
    }
}
