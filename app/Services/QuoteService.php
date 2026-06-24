<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteService
{
    public function __construct(
        private readonly PricingService $pricing,
        private readonly PaymentService $payments,
        private readonly LicenseDiskService $licenseDisks,
    ) {
    }

    public function createForVehicle(Vehicle $vehicle, User $user, bool $includeCarbonTax = true, string $insuranceType = 'third_party'): Quote
    {
        return DB::transaction(function () use ($vehicle, $user, $includeCarbonTax, $insuranceType) {
            $items = $this->pricing->quoteItems($vehicle, $includeCarbonTax, $insuranceType);
            $total = $items->sum('amountCents');

            $quote = Quote::create([
                'corporate_id' => $vehicle->corporate_id,
                'vehicle_id' => $vehicle->id,
                'created_by' => $user->id,
                'quote_number' => 'QTE-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
                'status' => 'pending',
                'subtotal_cents' => $total,
                'total_cents' => $total,
                'expires_at' => now()->addDays(7),
            ]);

            foreach ($items as $item) {
                $quote->items()->create([
                    'description' => $item['description'],
                    'fee_type' => $item['feeType'],
                    'amount_cents' => $item['amountCents'],
                ]);
            }

            return $quote->load(['items', 'vehicle', 'corporate']);
        });
    }

    public function purchase(Quote $quote, string $paymentMethod = 'mobile_money', array $paymentPayload = []): Quote
    {
        return DB::transaction(function () use ($quote, $paymentMethod, $paymentPayload) {
            $quote = Quote::query()->whereKey($quote->id)->lockForUpdate()->firstOrFail();

            if ($quote->status === 'paid') {
                return $quote->load(['items', 'vehicle', 'corporate']);
            }

            $this->payments->pay($quote, $paymentMethod, $paymentPayload);
            $this->licenseDisks->issueFromQuote($quote->fresh(['items', 'vehicle', 'corporate']));

            return $quote->fresh(['items', 'vehicle', 'corporate']);
        });
    }
}
