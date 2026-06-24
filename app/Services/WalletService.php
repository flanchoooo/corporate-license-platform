<?php

namespace App\Services;

use App\Models\Corporate;
use App\Models\Quote;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class WalletService
{
    public function credit(Corporate $corporate, int $amountCents, string $description, array $meta = []): WalletTransaction
    {
        return $this->record($corporate, 'credit', $amountCents, $description, null, $meta);
    }

    public function debit(Corporate $corporate, int $amountCents, string $description, ?Quote $quote = null, array $meta = []): WalletTransaction
    {
        return $this->record($corporate, 'debit', -abs($amountCents), $description, $quote, $meta);
    }

    private function record(Corporate $corporate, string $type, int $signedAmountCents, string $description, ?Quote $quote, array $meta): WalletTransaction
    {
        return DB::transaction(function () use ($corporate, $type, $signedAmountCents, $description, $quote, $meta) {
            $wallet = Wallet::query()->where('corporate_id', $corporate->id)->lockForUpdate()->firstOrCreate([
                'corporate_id' => $corporate->id,
            ]);

            $newBalance = $wallet->balance_cents + $signedAmountCents;

            if ($newBalance < 0) {
                throw new RuntimeException('Insufficient funds for this purchase.');
            }

            $wallet->update(['balance_cents' => $newBalance]);

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'corporate_id' => $corporate->id,
                'quote_id' => $quote?->id,
                'reference' => 'WTX-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
                'type' => $type,
                'amount_cents' => $signedAmountCents,
                'running_balance_cents' => $newBalance,
                'description' => $description,
                'meta' => $meta,
            ]);
        });
    }
}
