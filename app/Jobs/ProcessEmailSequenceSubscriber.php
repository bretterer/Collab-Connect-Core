<?php

namespace App\Jobs;

use App\Enums\EmailSendStatus;
use App\Models\EmailSequenceSend;
use App\Models\EmailSequenceSubscriber;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessEmailSequenceSubscriber implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public EmailSequenceSubscriber $subscriber
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Only process active subscribers
        if (! $this->subscriber->isActive()) {
            return;
        }

        $emailSequence = $this->subscriber->emailSequence;

        // Schedule welcome email if enabled
        $this->scheduleWelcomeEmail($emailSequence);

        // Get all emails in the sequence
        $emails = $emailSequence->emails()->orderBy('order')->get();

        foreach ($emails as $email) {
            // Check if this email has already been scheduled for this subscriber
            $existingSend = EmailSequenceSend::where('email_sequence_email_id', $email->id)
                ->where('subscriber_id', $this->subscriber->id)
                ->exists();

            if ($existingSend) {
                continue;
            }

            // Calculate when this email should be sent
            $scheduledAt = $this->calculateScheduledTime($email);

            // Skip this email if scheduledAt is null (e.g., past anchor date emails)
            if ($scheduledAt === null) {
                continue;
            }

            // Create a send record
            EmailSequenceSend::create([
                'email_sequence_email_id' => $email->id,
                'subscriber_id' => $this->subscriber->id,
                'scheduled_at' => $scheduledAt,
                'status' => EmailSendStatus::PENDING,
            ]);
        }
    }

    /**
     * Calculate when an email should be sent based on sequence mode and delay settings
     */
    protected function calculateScheduledTime($email): ?Carbon
    {
        $emailSequence = $this->subscriber->emailSequence;

        if ($emailSequence->isBeforeAnchorMode()) {
            return $this->calculateAnchorBasedTime($email, $emailSequence);
        }

        return $this->calculateSubscriptionBasedTime($email);
    }

    /**
     * Calculate scheduled time for "after subscription" mode
     */
    protected function calculateSubscriptionBasedTime($email): Carbon
    {
        $subscribedAt = $this->subscriber->subscribed_at ?? now();

        // Add the delay days and hours
        $scheduledDate = $subscribedAt->copy()
            ->addDays($email->delay_days)
            ->addHours($email->delay_hours ?? 0);

        // Parse the send time
        $sendTime = Carbon::parse($email->send_time);

        // Set the time on the scheduled date
        $scheduledAt = $scheduledDate->setTimeFrom($sendTime);

        // Convert to the email's timezone if specified
        if ($email->timezone) {
            $scheduledAt->timezone($email->timezone);
        }

        // If the scheduled time has already passed, send it immediately
        if ($scheduledAt->isPast()) {
            return now();
        }

        return $scheduledAt;
    }

    /**
     * Calculate scheduled time for "before anchor date" mode (countdown)
     */
    protected function calculateAnchorBasedTime($email, $emailSequence): ?Carbon
    {
        if (! $emailSequence->anchor_datetime) {
            return null;
        }

        // Get the anchor datetime - ensure we're working with UTC first
        // The anchor_datetime is stored in UTC in the database
        $anchorTimezone = $emailSequence->anchor_timezone ?? 'UTC';

        // Create a fresh Carbon instance from the stored value to avoid timezone confusion
        // The database stores UTC, so parse it as UTC then convert to anchor timezone
        $anchorDateTime = Carbon::parse(
            $emailSequence->anchor_datetime->format('Y-m-d H:i:s'),
            'UTC'
        )->timezone($anchorTimezone);

        // Subtract the delay days and hours from the anchor date
        $scheduledAt = $anchorDateTime
            ->subDays($email->delay_days)
            ->subHours($email->delay_hours ?? 0);

        // If the scheduled time has already passed, return null to skip this email
        if ($scheduledAt->isPast()) {
            return null;
        }

        // Return in UTC for consistent database storage
        return $scheduledAt->utc();
    }

    /**
     * Schedule the welcome email if enabled for this sequence
     */
    protected function scheduleWelcomeEmail($emailSequence): void
    {
        if (! $emailSequence->send_welcome_email) {
            return;
        }

        // Check if welcome email has already been scheduled for this subscriber
        $existingSend = EmailSequenceSend::where('subscriber_id', $this->subscriber->id)
            ->whereNull('email_sequence_email_id')
            ->where('is_welcome_email', true)
            ->exists();

        if ($existingSend) {
            return;
        }

        // Schedule welcome email to send immediately
        EmailSequenceSend::create([
            'email_sequence_email_id' => null,
            'subscriber_id' => $this->subscriber->id,
            'scheduled_at' => now(),
            'status' => EmailSendStatus::PENDING,
            'is_welcome_email' => true,
        ]);
    }
}
