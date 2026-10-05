<?php

namespace App\Mail;

use App\Models\BusinessExecutive;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExecutiveMagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BusinessExecutive $executive,
        public string $magicUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Magic Login Link — RHÁYỌ̀OGE Partner Desk',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.executive-magic-link',
        );
    }
}
