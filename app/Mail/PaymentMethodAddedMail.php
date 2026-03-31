<?php

namespace App\Mail;

use App\Models\ClientBillingMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentMethodAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClientBillingMethod $billingMethod,
        public string $userName,
        public string $dashboardUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment method added — HireHelper',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-method-added',
        );
    }
}
