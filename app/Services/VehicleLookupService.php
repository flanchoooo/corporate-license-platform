<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class VehicleLookupService
{
    public function findByNumberPlate(string $numberPlate): Vehicle
    {
        $plate = Str::upper(trim($numberPlate));

        return Vehicle::with('corporate')
            ->where('number_plate', $plate)
            ->firstOrFail();
    }
}
