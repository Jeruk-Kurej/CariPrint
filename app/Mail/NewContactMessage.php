<?php

namespace App\Mail;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Tells the admin that someone wrote through the contact form. Replying to this email answers the sender.
 */
class NewContactMessage extends Mailable
{
    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->contactMessage->email],
            subject: "Pesan baru: {$this->contactMessage->category->label()}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-contact-message',
            with: ['reviewUrl' => ContactMessageResource::getUrl('edit', ['record' => $this->contactMessage], panel: 'admin')],
        );
    }
}
