<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->corporate_id !== null;
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->isSuperAdmin() || $user->corporate_id === $vehicle->corporate_id;
    }

    public function create(User $user): bool
    {
        return $user->canWriteCorporateData();
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->canWriteCorporateData() && ($user->isSuperAdmin() || $user->corporate_id === $vehicle->corporate_id);
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $this->update($user, $vehicle);
    }
}
