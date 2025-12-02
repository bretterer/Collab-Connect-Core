<?php

namespace App\Mail;

use App\Models\Collaboration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Collaboration $collaboration,
        public User $recipient,
        public User $otherParty,
        public string $recipientRole,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $campaignName = $this->collaboration->campaign->project_name
            ?? $this->collaboration->campaign->campaign_goal;

        return new Envelope(
            subject: "Share your experience: Review your collaboration on \"{$campaignName}\"",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reviews.request',
            with: [
                'collaboration' => $this->collaboration,
                'recipient' => $this->recipient,
                'otherParty' => $this->otherParty,
                'recipientRole' => $this->recipientRole,
                'campaignName' => $this->collaboration->campaign->project_name
                    ?? $this->collaboration->campaign->campaign_goal,
                'reviewUrl' => route('collaborations.review', $this->collaboration),
                'daysRemaining' => Collaboration::REVIEW_PERIOD_DAYS,
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
