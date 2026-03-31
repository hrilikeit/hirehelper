<?php

namespace App\Mail;

use App\Models\ProjectOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractActiveMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProjectOffer $offer,
        public string $userName,
        public string $projectUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your contract is now active — HireHelper',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-active',
        );
    }
}
