<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    public function view(User $user, Quote $quote): bool
    {
        return $user->isSuperAdmin() || $user->corporate_id === $quote->corporate_id;
    }

    public function purchase(User $user, Quote $quote): bool
    {
        return $quote->status === 'pending'
            && $user->canWriteCorporateData()
            && ($user->isSuperAdmin() || $user->corporate_id === $quote->corporate_id);
    }
}
