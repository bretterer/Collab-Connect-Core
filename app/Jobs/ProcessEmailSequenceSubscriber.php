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
     * Calculate when an email should be sent based on delay and send time
     */
    protected function calculateScheduledTime($email): Carbon
    {
        $subscribedAt = $this->subscriber->subscribed_at ?? now();

        // Add the delay days
        $scheduledDate = $subscribedAt->copy()->addDays($email->delay_days);

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
}
