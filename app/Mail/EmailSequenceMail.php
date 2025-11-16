<?php

namespace App\Mail;

use App\Models\EmailSequenceEmail;
use App\Models\EmailSequenceSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailSequenceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public EmailSequenceEmail $email,
        public EmailSequenceSubscriber $subscriber,
        public string $processedBody,
        public string $processedSubject,
        public ?string $processedPreviewText,
        public string $unsubscribeUrl,
        public int $sendId,
    ) {
        // Wrap links in the body with tracking URLs
        $this->processedBody = $this->wrapLinksWithTracking($processedBody, $sendId);
    }

    /**
     * Wrap all links in the email body with click tracking
     */
    protected function wrapLinksWithTracking(string $body, int $sendId): string
    {
        // Find all href attributes and wrap them with tracking URL
        return preg_replace_callback(
            '/<a\s+(?:[^>]*?\s+)?href=(["\'])(.*?)\1/i',
            function ($matches) use ($sendId) {
                $url = $matches[2];

                // Skip if it's already a tracking URL or anchor link
                if (str_starts_with($url, '#') || str_contains($url, '/email/click/')) {
                    return $matches[0];
                }

                // Create tracking URL
                $trackingUrl = route('email-sequence.click', ['send' => $sendId]).'?url='.urlencode($url);

                return '<a href="'.$trackingUrl.'"';
            },
            $body
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->processedSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sequence.message',
            with: [
                'email' => $this->email,
                'subscriber' => $this->subscriber,
                'body' => $this->processedBody,
                'subject' => $this->processedSubject,
                'previewText' => $this->processedPreviewText,
                'unsubscribeUrl' => $this->unsubscribeUrl,
                'sendId' => $this->sendId,
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
