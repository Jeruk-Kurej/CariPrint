<?php

namespace App\Services\Contact;

use App\Models\ContactMessage;
use App\Models\User;

/**
 * Saves a message from the "Hubungi kami" form, then tells the admins. The row is stored first, so a mail failure
 * never loses the message: the admin still finds it in the panel.
 */
class ContactSubmitter
{
    public function __construct(private readonly ContactMailer $mailer) {}

    /**
     * @param  array<string, mixed>  $data  Validated category and message.
     */
    public function submit(array $data, string $email, ?User $user): ContactMessage
    {
        $message = new ContactMessage([...$data, 'email' => $email]);
        $message->user_id = $user?->id;
        $message->save();

        $this->mailer->notifyAdmins($message);

        return $message;
    }
}
