<?php

namespace App\Services\Accounts;

use App\Models\ContactMessage;
use App\Models\ShopSuggestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Deletes an account together with the suggestions and contact messages sent from its email address. Both are
 * tied to the email, not to the user row, so they would otherwise stay behind. Reports and print checks follow the user
 * through their foreign keys.
 */
class AccountDeleter
{
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            ShopSuggestion::query()->where('contact_email', $user->email)->delete();
            ContactMessage::query()->where('email', $user->email)->delete();

            $user->delete();
        });
    }
}
