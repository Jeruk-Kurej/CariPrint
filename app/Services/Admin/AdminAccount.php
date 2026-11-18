<?php

namespace App\Services\Admin;

use App\Enums\UserRole;
use App\Models\User;

class AdminAccount
{
    /** Name given to accounts made by the create-admin command, replaced by the Google name on first Google sign-in. */
    public const DEFAULT_NAME = 'Admin CariPrint';

    /**
     * Create or update an admin. Role is set explicitly because it is not mass assignable.
     */
    public static function save(string $email, string $password): User
    {
        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => self::DEFAULT_NAME,
            'password' => $password,
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }
}
