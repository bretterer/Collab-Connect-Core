<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BetaSignupNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $userType,
        public ?string $businessName = null,
        public ?string $followerCount = null
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $userTypeLabel = $this->userType === 'business' ? 'Business' : 'Influencer';

        return new Envelope(
            subject: "[CollabConnect Beta] New {$userTypeLabel} Signup: {$this->name}",
            replyTo: [
                new Address($this->email, $this->name)
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.beta-signup-notification',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'userType' => $this->userType,
                'businessName' => $this->businessName,
                'followerCount' => $this->followerCount,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
