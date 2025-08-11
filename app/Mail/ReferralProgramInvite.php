<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralProgramInvite extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $name,
        public string $referralCode
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Earn $5 for Every Friend You Refer to CollabConnect!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.referral-program-invite',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}