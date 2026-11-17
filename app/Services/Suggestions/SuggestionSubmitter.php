<?php

namespace App\Services\Suggestions;

use App\Mail\NewShopSuggestion;
use App\Models\ShopSuggestion;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Saves a visitor's shop suggestion, then emails the admin. The row is saved first, so a mail failure
 * never loses the suggestion: the admin still finds it in the panel.
 */
class SuggestionSubmitter
{
    /**
     * @param  array<string, mixed>  $data  Validated fields from StoreShopSuggestionRequest.
     */
    public function submit(array $data): ShopSuggestion
    {
        $suggestion = ShopSuggestion::create($data);
        $adminEmail = config('cariprint.admin.email');

        if ($adminEmail === null) {
            return $suggestion;
        }

        try {
            Mail::to($adminEmail)->send(new NewShopSuggestion($suggestion));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $suggestion;
    }
}
