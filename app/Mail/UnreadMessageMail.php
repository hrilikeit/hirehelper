<?php

namespace App\Mail;

use App\Models\ProjectMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnreadMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProjectMessage $projectMessage,
        public string $projectTitle,
        public string $messagesUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New message on "' . $this->projectTitle . '" — HireHelper',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.unread-message',
        );
    }
}
