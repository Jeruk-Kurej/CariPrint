<?php

namespace App\Services\Suggestions;

use App\Models\ShopSuggestion;

/**
 * Saves a visitor's shop suggestion first, then sends the emails. The row is stored before any mail goes out,
 * so a mail failure never loses the suggestion: the admin still finds it in the panel.
 */
class SuggestionSubmitter
{
    public function __construct(private readonly SuggestionMailer $mailer) {}

    /**
     * @param  array<string, mixed>  $data  Validated fields from StoreShopSuggestionRequest.
     */
    public function submit(array $data): ShopSuggestion
    {
        $suggestion = ShopSuggestion::create($data);

        $this->mailer->notifyAdmins($suggestion);
        $this->mailer->confirmReceipt($suggestion);

        return $suggestion;
    }
}
