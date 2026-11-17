<?php

namespace App\Policies;

use App\Models\ShopSuggestion;
use App\Models\User;

/**
 * Only admins review suggestions. Visitors send them through the public form, which needs no policy.
 */
class ShopSuggestionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ShopSuggestion $suggestion): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ShopSuggestion $suggestion): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ShopSuggestion $suggestion): bool
    {
        return $user->isAdmin();
    }
}
