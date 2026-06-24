<?php

namespace App\Services;

use App\Models\Arrear;
use App\Models\Vehicle;

class ArrearsService
{
    public function amountDue(Vehicle $vehicle): int
    {
        return (int) Arrear::where('vehicle_id', $vehicle->id)
            ->where('status', 'open')
            ->sum('amount_cents');
    }
}
