<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\User as GoogleUser;

/**
 * Turns a Google account into a CariPrint user. Only Google accounts with a verified email are accepted, so
 * matching an existing user by email is safe. New accounts are plain users: admins are only made by the
 * cariprint:create-admin command, and public registration with a password stays closed.
 */
class GoogleLogin
{
    /**
     * @throws GoogleLoginRejected
     */
    public function resolve(GoogleUser $google): User
    {
        $email = $google->getEmail();

        if (blank($email) || ! filter_var($google->user['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            throw new GoogleLoginRejected('Email akun Google-mu belum terverifikasi.');
        }

        $googleId = (string) $google->getId();

        return User::where('google_id', $googleId)->first()
            ?? $this->claimByEmail($email, $googleId)
            ?? $this->register($google, $email, $googleId);
    }

    /**
     * Links the Google account to a user that already has this email, unless that user is tied to another Google account.
     */
    private function claimByEmail(string $email, string $googleId): ?User
    {
        $user = User::where('email', $email)->first();

        if ($user === null) {
            return null;
        }

        if ($user->google_id !== null) {
            throw new GoogleLoginRejected('Email ini sudah terhubung ke akun Google lain.');
        }

        $user->forceFill(['google_id' => $googleId, 'email_verified_at' => $user->email_verified_at ?? now()])->save();

        return $user;
    }

    private function register(GoogleUser $google, string $email, string $googleId): User
    {
        $user = new User;
        $user->forceFill([
            'name' => $google->getName() ?: $email,
            'email' => $email,
            'password' => Str::random(40),
            'google_id' => $googleId,
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }
}
