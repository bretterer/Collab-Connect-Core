<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BetaInviteInfluencer extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public object $invite,
        public string $signedUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re Invited to Join CollabConnect Beta - Influencers',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.beta-invite-influencer',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
