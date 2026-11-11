<?php

namespace App\Policies;

use App\Models\PriceList;
use App\Models\User;

class PriceListPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, PriceList $priceList): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, PriceList $priceList): bool
    {
        return $user->isAdmin();
    }
}
