<?php

namespace App\Services\Admin;

use App\Enums\UserRole;
use App\Models\User;

class AdminAccount
{
    /**
     * Create or update an admin. Role is set explicitly because it is not mass assignable.
     */
    public static function save(string $email, string $password): User
    {
        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => 'Admin CariPrint',
            'password' => $password,
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }
}
