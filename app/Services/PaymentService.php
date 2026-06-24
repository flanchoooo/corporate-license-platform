<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Quote;
use Illuminate\Support\Str;

class PaymentService
{
    public function pay(Quote $quote, string $method, array $payload = []): Payment
    {
        $payment = Payment::create([
            'quote_id' => $quote->id,
            'corporate_id' => $quote->corporate_id,
            'method' => $method,
            'reference' => 'PAY-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
            'amount_cents' => $quote->total_cents,
            'status' => 'success',
            'provider_payload' => $payload,
            'paid_at' => now(),
        ]);

        $quote->update([
            'status' => 'paid',
            'purchased_at' => now(),
        ]);

        return $payment;
    }
}
