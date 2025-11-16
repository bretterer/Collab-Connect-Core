<?php

namespace App\Services;

use App\Enums\EmailSendStatus;
use App\Enums\SubscriberStatus;
use App\Jobs\ProcessEmailSequenceSubscriber;
use App\Jobs\SendEmailSequenceEmail;
use App\Models\EmailSequence;
use App\Models\EmailSequenceSend;
use App\Models\EmailSequenceSubscriber;

class EmailSequenceService
{
    /**
     * Subscribe a user to an email sequence
     */
    public function subscribe(
        EmailSequence $sequence,
        string $email,
        ?string $firstName = null,
        ?string $lastName = null,
        ?array $metadata = null,
        ?string $source = null
    ): EmailSequenceSubscriber {
        // Check if already subscribed
        $subscriber = EmailSequenceSubscriber::where('email_sequence_id', $sequence->id)
            ->where('email', $email)
            ->first();

        if ($subscriber) {
            // If they were unsubscribed, reactivate them
            if ($subscriber->status !== SubscriberStatus::ACTIVE) {
                $subscriber->update([
                    'status' => SubscriberStatus::ACTIVE,
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,
                    'unsubscribe_reason' => null,
                ]);
            }

            return $subscriber;
        }

        // Create new subscriber
        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'metadata' => $metadata,
            'status' => SubscriberStatus::ACTIVE,
            'subscribed_at' => now(),
            'source' => $source,
        ]);

        // Update sequence subscriber count
        $sequence->updateSubscriberCount();

        // Queue job to schedule all emails for this subscriber
        ProcessEmailSequenceSubscriber::dispatch($subscriber);

        return $subscriber;
    }

    /**
     * Unsubscribe a user from an email sequence
     */
    public function unsubscribe(EmailSequenceSubscriber $subscriber, ?string $reason = null): void
    {
        $subscriber->unsubscribe($reason);

        // Cancel any pending sends
        EmailSequenceSend::where('subscriber_id', $subscriber->id)
            ->where('status', EmailSendStatus::PENDING)
            ->update(['status' => EmailSendStatus::CANCELLED]);
    }

    /**
     * Process pending email sends that are ready to be sent
     */
    public function processPendingSends(): int
    {
        $pendingSends = EmailSequenceSend::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->with(['email', 'subscriber'])
            ->limit(100) // Process in batches
            ->get();

        foreach ($pendingSends as $send) {
            SendEmailSequenceEmail::dispatch($send);
        }

        return $pendingSends->count();
    }

    /**
     * Check if a form submission should trigger an email sequence
     */
    public function handleFormSubmission(int $formId, array $data): void
    {
        // Find all sequences triggered by this form
        $sequences = EmailSequence::whereNotNull('subscribe_triggers')
            ->get()
            ->filter(function ($sequence) use ($formId) {
                $triggers = $sequence->subscribe_triggers ?? [];
                foreach ($triggers as $trigger) {
                    if (
                        $trigger['type'] === 'form_submitted' &&
                        isset($trigger['form_id']) &&
                        $trigger['form_id'] == $formId
                    ) {
                        return true;
                    }
                }

                return false;
            });

        // Subscribe to each triggered sequence
        foreach ($sequences as $sequence) {
            $this->subscribe(
                sequence: $sequence,
                email: $data['email'] ?? null,
                firstName: $data['first_name'] ?? null,
                lastName: $data['last_name'] ?? null,
                metadata: $data,
                source: "form_{$formId}"
            );
        }
    }

    /**
     * Check if a landing page visit should trigger an email sequence
     */
    public function handleLandingPageVisit(int $landingPageId, string $email, ?array $data = null): void
    {
        // Find all sequences triggered by this landing page
        $sequences = EmailSequence::whereNotNull('subscribe_triggers')
            ->get()
            ->filter(function ($sequence) use ($landingPageId) {
                $triggers = $sequence->subscribe_triggers ?? [];
                foreach ($triggers as $trigger) {
                    if (
                        $trigger['type'] === 'landing_page_visited' &&
                        isset($trigger['landing_page_id']) &&
                        $trigger['landing_page_id'] == $landingPageId
                    ) {
                        return true;
                    }
                }

                return false;
            });

        // Subscribe to each triggered sequence
        foreach ($sequences as $sequence) {
            $this->subscribe(
                sequence: $sequence,
                email: $email,
                firstName: $data['first_name'] ?? null,
                lastName: $data['last_name'] ?? null,
                metadata: $data,
                source: "landing_page_{$landingPageId}"
            );
        }
    }
}
