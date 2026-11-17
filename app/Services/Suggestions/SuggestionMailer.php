<?php

namespace App\Services\Suggestions;

use App\Mail\NewShopSuggestion;
use App\Mail\SuggestionReceived;
use App\Mail\SuggestionReviewed;
use App\Models\ShopSuggestion;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Sends the three emails of the suggestion flow: to the admins when a suggestion arrives, to the visitor
 * as a receipt, and to the visitor again when an admin decides. A failed email is reported but never
 * blocks the visitor's request or the admin's save, because the suggestion itself is already stored.
 */
class SuggestionMailer
{
    public function notifyAdmins(ShopSuggestion $suggestion): void
    {
        $recipients = config('cariprint.admin.notify_emails');

        if ($recipients !== []) {
            $this->send($recipients, new NewShopSuggestion($suggestion));
        }
    }

    public function confirmReceipt(ShopSuggestion $suggestion): void
    {
        if ($suggestion->contact_email !== null) {
            $this->send($suggestion->contact_email, new SuggestionReceived($suggestion));
        }
    }

    public function announceDecision(ShopSuggestion $suggestion): void
    {
        if ($suggestion->contact_email !== null) {
            $this->send($suggestion->contact_email, new SuggestionReviewed($suggestion));
        }
    }

    /**
     * @param  string|list<string>  $to
     */
    private function send(string|array $to, Mailable $mail): void
    {
        try {
            Mail::to($to)->send($mail);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
