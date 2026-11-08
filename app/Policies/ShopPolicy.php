<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

/**
 * Only admins change shop data. Public visitors read through the directory, not through policies.
 */
class ShopPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Shop $shop): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Shop $shop): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Shop $shop): bool
    {
        return $user->isAdmin();
    }
}
