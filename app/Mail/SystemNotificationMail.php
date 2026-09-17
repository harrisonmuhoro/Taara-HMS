<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $notificationText = '';

    public function __construct(public string $mailSubject, string $body, public ?string $reference = null)
    {
        $this->notificationText = $body;
    }

    public function envelope(): Envelope { return new Envelope(subject: $this->mailSubject); }

    public function content(): Content
    {
        return new Content(
            view: 'emails.system-notification',
            with: ['notificationBody' => $this->notificationText],
        );
    }
}
