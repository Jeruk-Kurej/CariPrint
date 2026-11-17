<?php

namespace App\Mail;

use App\Enums\SuggestionStatus;
use App\Models\ShopSuggestion;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Tells the visitor whether an admin accepted or rejected their suggestion.
 */
class SuggestionReviewed extends Mailable
{
    public function __construct(public ShopSuggestion $suggestion) {}

    public function envelope(): Envelope
    {
        $outcome = $this->suggestion->status === SuggestionStatus::Accepted
            ? 'diterima'
            : 'belum bisa kami tambahkan';

        return new Envelope(subject: "Usulan {$this->suggestion->name} {$outcome}");
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.suggestion-reviewed');
    }
}
