<?php

namespace App\Services;

use App\Models\CreditApplication;
use App\Models\DeliveryOrder;
use App\Models\LicenseDisk;
use App\Models\Quote;

class DeliveryService
{
    public function createForQuote(Quote $quote, array $payload, ?LicenseDisk $disk = null, ?CreditApplication $application = null): DeliveryOrder
    {
        return DeliveryOrder::create([
            'quote_id' => $quote->id,
            'vehicle_id' => $quote->vehicle_id,
            'license_disk_id' => $disk?->id,
            'credit_application_id' => $application?->id,
            'delivery_address' => $payload['delivery_address'],
            'contact_mobile' => $payload['contact_mobile'] ?? $payload['delivery_mobile'],
            'landmark' => $payload['landmark'] ?? $payload['delivery_landmark'] ?? null,
            'status' => 'pending',
        ]);
    }

    public function assign(DeliveryOrder $order, int $riderUserId): DeliveryOrder
    {
        $order->update([
            'rider_user_id' => $riderUserId,
            'status' => 'in_transit',
            'assigned_at' => now(),
        ]);

        return $order->fresh();
    }

    public function updateStatus(DeliveryOrder $order, string $status): DeliveryOrder
    {
        $timestamps = match ($status) {
            'in_transit' => ['assigned_at' => $order->assigned_at ?? now()],
            'delivered' => ['delivered_at' => now()],
            'failed' => ['failed_at' => now()],
            default => [],
        };

        $order->update(['status' => $status] + $timestamps);

        return $order->fresh();
    }
}
