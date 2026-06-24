<?php

namespace App\Services;

use App\Models\CreditApplication;
use App\Models\Quote;

class CreditApplicationService
{
    public function create(Quote $quote, array $payload): CreditApplication
    {
        return CreditApplication::create([
            'quote_id' => $quote->id,
            'vehicle_id' => $quote->vehicle_id,
            'corporate_id' => $quote->corporate_id,
            'name' => $payload['name'],
            'surname' => $payload['surname'],
            'national_id' => $payload['national_id'],
            'mobile_number' => $payload['mobile_number'],
            'address' => $payload['address'],
            'delivery_address' => $payload['delivery_address'],
            'delivery_mobile' => $payload['delivery_mobile'],
            'delivery_landmark' => $payload['delivery_landmark'] ?? null,
            'status' => 'pending',
        ]);
    }

    public function approve(CreditApplication $application, ?string $notes = null): CreditApplication
    {
        $application->update([
            'status' => 'approved',
            'admin_notes' => $notes,
            'approved_at' => now(),
        ]);

        $application->quote->update([
            'status' => 'approved_credit',
            'purchased_at' => now(),
        ]);

        return $application->fresh(['quote.items', 'vehicle', 'corporate']);
    }

    public function reject(CreditApplication $application, ?string $notes = null): CreditApplication
    {
        $application->update([
            'status' => 'rejected',
            'admin_notes' => $notes,
            'rejected_at' => now(),
        ]);

        return $application->fresh();
    }
}
