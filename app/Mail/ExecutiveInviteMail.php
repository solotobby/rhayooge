<?php

namespace App\Mail;

use App\Models\BusinessExecutive;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExecutiveInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BusinessExecutive $executive,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation: Your RHÁYỌ̀OGE Business Executive Partner Portal',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.executive-invite',
        );
    }
}
