<?php

namespace App\Services;

use App\Models\LicenseDisk;
use App\Models\Quote;
use Illuminate\Support\Str;

class LicenseDiskService
{
    public function issueFromQuote(Quote $quote): LicenseDisk
    {
        $quote->loadMissing(['items', 'vehicle', 'corporate']);

        $reference = 'LIC-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));
        $validFrom = now()->startOfDay();
        $validUntil = now()->addYear()->subDay()->startOfDay();
        $amount = fn (string $feeType) => (int) $quote->items->firstWhere('fee_type', $feeType)?->amount_cents;

        return LicenseDisk::firstOrCreate(
            ['quote_id' => $quote->id],
            [
                'corporate_id' => $quote->corporate_id,
                'vehicle_id' => $quote->vehicle_id,
                'reference_number' => $reference,
                'radio_license_fee_cents' => $amount('radio_license'),
                'insurance_fee_cents' => $amount('motor_insurance'),
                'zinara_fee_cents' => $amount('zinara_license'),
                'arrears_cents' => $amount('arrears'),
                'total_paid_cents' => $quote->total_cents,
                'valid_from' => $validFrom,
                'valid_until' => $validUntil,
                'qr_payload' => route('license-disks.verify', $reference, absolute: true),
                'issued_at' => now(),
            ]
        );
    }
}
