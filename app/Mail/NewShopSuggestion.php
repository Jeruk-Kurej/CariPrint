<?php

namespace App\Mail;

use App\Filament\Resources\ShopSuggestions\ShopSuggestionResource;
use App\Models\ShopSuggestion;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Tells the admin that a visitor asked for a print shop to be added.
 */
class NewShopSuggestion extends Mailable
{
    public function __construct(public ShopSuggestion $suggestion) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Usulan tempat baru: {$this->suggestion->name}");
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-shop-suggestion',
            with: ['reviewUrl' => ShopSuggestionResource::getUrl('edit', ['record' => $this->suggestion], panel: 'admin')],
        );
    }
}
