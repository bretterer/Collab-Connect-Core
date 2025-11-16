<?php

namespace App\Jobs;

use App\Mail\EmailSequenceMail;
use App\Models\EmailSequenceSend;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmailSequenceEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public EmailSequenceSend $send
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Skip if already sent or cancelled
        if (! $this->send->isPending()) {
            return;
        }

        // Skip if subscriber is no longer active
        if (! $this->send->subscriber->isActive()) {
            $this->send->update(['status' => 'cancelled']);

            return;
        }

        try {
            $email = $this->send->email;
            $subscriber = $this->send->subscriber;

            // Process merge tags in body
            $processedBody = $this->processMergeTags($email->body, $subscriber);

            // Process merge tags in subject
            $processedSubject = $this->processMergeTags($email->subject, $subscriber);

            // Process merge tags in preview text
            $processedPreviewText = $email->preview_text
                ? $this->processMergeTags($email->preview_text, $subscriber)
                : null;

            // Generate unsubscribe URL
            $unsubscribeUrl = route('email-sequence.unsubscribe', [
                'subscriber' => $subscriber->id,
                'token' => hash_hmac('sha256', $subscriber->id, config('app.key')),
            ]);

            // Send using Laravel Markdown Mailable
            Mail::to($subscriber->email, $subscriber->full_name)
                ->send(new EmailSequenceMail(
                    $email,
                    $subscriber,
                    $processedBody,
                    $processedSubject,
                    $processedPreviewText,
                    $unsubscribeUrl,
                    $this->send->id
                ));

            // Mark as sent
            $this->send->markAsSent();
            $email->incrementSentCount();
        } catch (\Exception $e) {
            // Mark as failed
            $this->send->markAsFailed($e->getMessage());

            // Re-throw to retry if needed
            throw $e;
        }
    }

    /**
     * Process merge tags in content (body, subject, preview text)
     */
    protected function processMergeTags(string $content, $subscriber): string
    {
        // Start with default replacements
        $replacements = [
            '{email}' => $subscriber->email,
        ];

        // Add replacements from subscriber metadata (form field data)
        if (is_array($subscriber->metadata) && ! empty($subscriber->metadata)) {
            foreach ($subscriber->metadata as $key => $value) {
                // Skip email as it's already handled
                if ($key === 'email') {
                    continue;
                }

                // Convert value to string (handle arrays, objects, etc.)
                if (is_array($value)) {
                    $value = implode(', ', $value);
                } elseif (is_object($value)) {
                    $value = json_encode($value);
                } elseif (is_bool($value)) {
                    $value = $value ? 'Yes' : 'No';
                } elseif (is_null($value)) {
                    $value = '';
                }

                // Add to replacements with curly braces
                $replacements['{'.$key.'}'] = $value;
            }
        }

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
