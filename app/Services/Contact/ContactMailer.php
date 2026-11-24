<?php

namespace App\Services\Contact;

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Emails the admins when a message arrives. A failed email is reported but never blocks the sender's request,
 * because the message itself is already stored.
 */
class ContactMailer
{
    public function notifyAdmins(ContactMessage $message): void
    {
        $recipients = config('cariprint.admin.notify_emails');

        if ($recipients === []) {
            return;
        }

        try {
            Mail::to($recipients)->send(new NewContactMessage($message));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
