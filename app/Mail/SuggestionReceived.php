<?php

namespace App\Mail;

use App\Models\ShopSuggestion;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Thanks the visitor right after they send a suggestion and says it now waits for an admin.
 */
class SuggestionReceived extends Mailable
{
    public function __construct(public ShopSuggestion $suggestion) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Usulanmu sudah kami terima');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.suggestion-received');
    }
}
