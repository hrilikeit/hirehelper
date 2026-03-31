<?php

namespace App\Mail;

use App\Models\ProjectOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProjectOffer $offer,
        public string $userName,
        public string $billingUrl,
        public string $amount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment failed — action required — HireHelper',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-failed',
        );
    }
}
