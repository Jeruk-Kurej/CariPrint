<?php

namespace App\Observers;

use App\Enums\SuggestionStatus;
use App\Models\ShopSuggestion;
use App\Services\Suggestions\SuggestionMailer;

/**
 * When an admin moves a suggestion out of "pending", the visitor who sent it hears about the decision.
 */
class ShopSuggestionObserver
{
    public function __construct(private readonly SuggestionMailer $mailer) {}

    public function updated(ShopSuggestion $suggestion): void
    {
        if ($suggestion->wasChanged('status') && $suggestion->status !== SuggestionStatus::Pending) {
            $this->mailer->announceDecision($suggestion);
        }
    }
}
