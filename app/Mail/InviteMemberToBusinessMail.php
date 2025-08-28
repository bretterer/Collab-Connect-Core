<?php

namespace App\Mail;

use App\Models\BusinessMemberInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteMemberToBusinessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BusinessMemberInvite $invite,
        public string $signedUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'ve been invited to join ' . $this->invite->business->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.business-member-invite',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
