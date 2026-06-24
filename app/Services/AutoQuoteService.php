<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AutoQuoteService
{
    public function __construct(
        private readonly PricingService $pricing,
        private readonly ArrearsService $arrears,
    ) {
    }

    public function generate(Vehicle $vehicle, string $insuranceType = 'third_party'): Quote
    {
        return DB::transaction(function () use ($vehicle, $insuranceType) {
            $items = $this->pricing->quoteItems($vehicle, false, $insuranceType)
                ->reject(fn (array $item) => $item['feeType'] === 'administration_fee')
                ->values();

            $arrears = $this->arrears->amountDue($vehicle);
            $deliveryFee = $this->pricing->amountFor('delivery_fee', $vehicle, 500);

            $items = $items
                ->reject(fn (array $item) => $item['feeType'] === 'arrears')
                ->push([
                    'description' => 'Arrears',
                    'feeType' => 'arrears',
                    'amountCents' => $arrears,
                ])
                ->push([
                    'description' => 'Delivery Fee',
                    'feeType' => 'delivery_fee',
                    'amountCents' => $deliveryFee,
                ]);

            $total = $items->sum('amountCents');

            $quote = Quote::create([
                'corporate_id' => $vehicle->corporate_id,
                'vehicle_id' => $vehicle->id,
                'created_by' => null,
                'quote_number' => 'BOT-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
                'status' => 'pending',
                'subtotal_cents' => $total,
                'total_cents' => $total,
                'expires_at' => now()->addDays(3),
            ]);

            foreach ($items as $item) {
                $quote->items()->create([
                    'description' => $item['description'],
                    'fee_type' => $item['feeType'],
                    'amount_cents' => $item['amountCents'],
                ]);
            }

            return $quote->load(['items', 'vehicle.corporate', 'corporate']);
        });
    }
}
