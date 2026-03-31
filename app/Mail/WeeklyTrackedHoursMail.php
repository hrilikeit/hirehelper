<?php

namespace App\Mail;

use App\Models\ProjectOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyTrackedHoursMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProjectOffer $offer,
        public string $userName,
        public float $hoursTracked,
        public string $weekLabel,
        public string $reportsUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly hours report: ' . $this->weekLabel . ' — HireHelper',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-tracked-hours',
        );
    }
}
