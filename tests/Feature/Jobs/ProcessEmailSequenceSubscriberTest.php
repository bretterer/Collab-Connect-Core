<?php

namespace Tests\Feature\Jobs;

use App\Enums\EmailSendStatus;
use App\Enums\SequenceMode;
use App\Enums\SubscriberStatus;
use App\Jobs\ProcessEmailSequenceSubscriber;
use App\Models\EmailSequence;
use App\Models\EmailSequenceEmail;
use App\Models\EmailSequenceSend;
use App\Models\EmailSequenceSubscriber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProcessEmailSequenceSubscriberTest extends TestCase
{
    use RefreshDatabase;

    private function createSequence(array $attributes = []): EmailSequence
    {
        $user = User::factory()->create();

        return EmailSequence::factory()->create(array_merge([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ], $attributes));
    }

    private function createSubscriber(EmailSequence $sequence, array $attributes = []): EmailSequenceSubscriber
    {
        return EmailSequenceSubscriber::create(array_merge([
            'email_sequence_id' => $sequence->id,
            'email' => 'test@example.com',
            'status' => SubscriberStatus::ACTIVE,
            'subscribed_at' => now(),
        ], $attributes));
    }

    private function createEmail(EmailSequence $sequence, array $attributes = []): EmailSequenceEmail
    {
        return EmailSequenceEmail::create(array_merge([
            'email_sequence_id' => $sequence->id,
            'name' => 'Test Email',
            'subject' => 'Test Subject',
            'body' => 'Test body content',
            'delay_days' => 0,
            'delay_hours' => 0,
            'send_time' => '08:00:00',
            'timezone' => 'America/New_York',
            'order' => 1,
        ], $attributes));
    }

    #[Test]
    public function it_schedules_emails_for_after_subscription_mode(): void
    {
        Carbon::setTestNow('2025-12-01 10:00:00');

        $sequence = $this->createSequence([
            'sequence_mode' => SequenceMode::AFTER_SUBSCRIPTION,
        ]);

        $this->createEmail($sequence, [
            'delay_days' => 0,
            'delay_hours' => 0,
            'send_time' => '08:00:00',
            'order' => 1,
        ]);

        $this->createEmail($sequence, [
            'delay_days' => 3,
            'delay_hours' => 0,
            'send_time' => '14:00:00',
            'order' => 2,
        ]);

        $subscriber = $this->createSubscriber($sequence, [
            'subscribed_at' => Carbon::parse('2025-12-01 10:00:00'),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->get();

        $this->assertCount(2, $sends);

        // First email: Day 0, 8 AM - but time has passed so should be sent immediately
        $firstSend = $sends->where('email_sequence_email_id', $sequence->emails[0]->id)->first();
        $this->assertEquals(EmailSendStatus::PENDING, $firstSend->status);

        // Second email: Day 3, 2 PM
        $secondSend = $sends->where('email_sequence_email_id', $sequence->emails[1]->id)->first();
        $this->assertEquals(EmailSendStatus::PENDING, $secondSend->status);
    }

    #[Test]
    public function it_schedules_emails_for_before_anchor_date_mode(): void
    {
        Carbon::setTestNow('2025-12-01 10:00:00');

        // Event is on December 25, 2025 at 2:00 PM Eastern
        $anchorDatetime = Carbon::parse('2025-12-25 14:00:00', 'America/New_York')->utc();

        $sequence = $this->createSequence([
            'sequence_mode' => SequenceMode::BEFORE_ANCHOR_DATE,
            'anchor_datetime' => $anchorDatetime,
            'anchor_timezone' => 'America/New_York',
        ]);

        // 20 days before
        $this->createEmail($sequence, [
            'delay_days' => 20,
            'delay_hours' => 0,
            'order' => 1,
        ]);

        // 10 days before
        $this->createEmail($sequence, [
            'delay_days' => 10,
            'delay_hours' => 0,
            'order' => 2,
        ]);

        // 1 hour before
        $this->createEmail($sequence, [
            'delay_days' => 0,
            'delay_hours' => 1,
            'order' => 3,
        ]);

        $subscriber = $this->createSubscriber($sequence, [
            'subscribed_at' => Carbon::parse('2025-12-01 10:00:00'),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->get();

        $this->assertCount(3, $sends);

        // 20 days before: Dec 25 - 20 days = Dec 5 at 2 PM Eastern
        $twentyDaysBefore = $sends->where('email_sequence_email_id', $sequence->emails[0]->id)->first();
        $this->assertEquals(
            '2025-12-05 14:00',
            $twentyDaysBefore->scheduled_at->timezone('America/New_York')->format('Y-m-d H:i')
        );

        // 10 days before: Dec 25 - 10 days = Dec 15 at 2 PM Eastern
        $tenDaysBefore = $sends->where('email_sequence_email_id', $sequence->emails[1]->id)->first();
        $this->assertEquals(
            '2025-12-15 14:00',
            $tenDaysBefore->scheduled_at->timezone('America/New_York')->format('Y-m-d H:i')
        );

        // 1 hour before: Dec 25 at 1 PM Eastern
        $oneHourBefore = $sends->where('email_sequence_email_id', $sequence->emails[2]->id)->first();
        $this->assertEquals(
            '2025-12-25 13:00',
            $oneHourBefore->scheduled_at->timezone('America/New_York')->format('Y-m-d H:i')
        );
    }

    #[Test]
    public function it_skips_past_emails_for_late_subscribers_in_anchor_mode(): void
    {
        // Subscriber signs up on Dec 14, missing the 20 days before email (Dec 5)
        Carbon::setTestNow('2025-12-14 10:00:00');

        $anchorDatetime = Carbon::parse('2025-12-25 14:00:00', 'America/New_York')->utc();

        $sequence = $this->createSequence([
            'sequence_mode' => SequenceMode::BEFORE_ANCHOR_DATE,
            'anchor_datetime' => $anchorDatetime,
            'anchor_timezone' => 'America/New_York',
        ]);

        // 20 days before (Dec 5) - this should be SKIPPED
        $this->createEmail($sequence, [
            'name' => '20 days before',
            'delay_days' => 20,
            'delay_hours' => 0,
            'order' => 1,
        ]);

        // 10 days before (Dec 15) - should be scheduled
        $this->createEmail($sequence, [
            'name' => '10 days before',
            'delay_days' => 10,
            'delay_hours' => 0,
            'order' => 2,
        ]);

        // 5 days before (Dec 20) - should be scheduled
        $this->createEmail($sequence, [
            'name' => '5 days before',
            'delay_days' => 5,
            'delay_hours' => 0,
            'order' => 3,
        ]);

        $subscriber = $this->createSubscriber($sequence, [
            'subscribed_at' => Carbon::parse('2025-12-14 10:00:00'),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->get();

        // Should only have 2 sends (10 days and 5 days before), skipping 20 days
        $this->assertCount(2, $sends);

        // Verify the 20 days before email was NOT scheduled
        $twentyDaysEmail = $sequence->emails->where('name', '20 days before')->first();
        $this->assertNull(
            $sends->where('email_sequence_email_id', $twentyDaysEmail->id)->first()
        );
    }

    #[Test]
    public function it_skips_multiple_past_emails_for_very_late_subscribers(): void
    {
        // Subscriber signs up on Dec 20, missing 20, 15, and 10 days before emails
        Carbon::setTestNow('2025-12-20 10:00:00');

        $anchorDatetime = Carbon::parse('2025-12-25 14:00:00', 'America/New_York')->utc();

        $sequence = $this->createSequence([
            'sequence_mode' => SequenceMode::BEFORE_ANCHOR_DATE,
            'anchor_datetime' => $anchorDatetime,
            'anchor_timezone' => 'America/New_York',
        ]);

        // 20 days before - SKIPPED
        $this->createEmail($sequence, [
            'name' => '20 days before',
            'delay_days' => 20,
            'delay_hours' => 0,
            'order' => 1,
        ]);

        // 15 days before - SKIPPED
        $this->createEmail($sequence, [
            'name' => '15 days before',
            'delay_days' => 15,
            'delay_hours' => 0,
            'order' => 2,
        ]);

        // 10 days before - SKIPPED
        $this->createEmail($sequence, [
            'name' => '10 days before',
            'delay_days' => 10,
            'delay_hours' => 0,
            'order' => 3,
        ]);

        // 5 days before - scheduled
        $this->createEmail($sequence, [
            'name' => '5 days before',
            'delay_days' => 5,
            'delay_hours' => 0,
            'order' => 4,
        ]);

        // 3 days before - scheduled
        $this->createEmail($sequence, [
            'name' => '3 days before',
            'delay_days' => 3,
            'delay_hours' => 0,
            'order' => 5,
        ]);

        // 1 day before - scheduled
        $this->createEmail($sequence, [
            'name' => '1 day before',
            'delay_days' => 1,
            'delay_hours' => 0,
            'order' => 6,
        ]);

        // 1 hour before - scheduled
        $this->createEmail($sequence, [
            'name' => '1 hour before',
            'delay_days' => 0,
            'delay_hours' => 1,
            'order' => 7,
        ]);

        $subscriber = $this->createSubscriber($sequence, [
            'subscribed_at' => Carbon::parse('2025-12-20 10:00:00'),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->get();

        // Should only have 4 sends (5 days, 3 days, 1 day, 1 hour before)
        $this->assertCount(4, $sends);
    }

    #[Test]
    public function it_does_not_schedule_for_inactive_subscribers(): void
    {
        $sequence = $this->createSequence([
            'sequence_mode' => SequenceMode::AFTER_SUBSCRIPTION,
        ]);

        $this->createEmail($sequence, [
            'delay_days' => 0,
            'order' => 1,
        ]);

        $subscriber = $this->createSubscriber($sequence, [
            'status' => SubscriberStatus::UNSUBSCRIBED,
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->count();

        $this->assertEquals(0, $sends);
    }

    #[Test]
    public function it_does_not_duplicate_sends_for_existing_schedules(): void
    {
        $sequence = $this->createSequence([
            'sequence_mode' => SequenceMode::AFTER_SUBSCRIPTION,
        ]);

        $email = $this->createEmail($sequence, [
            'delay_days' => 0,
            'order' => 1,
        ]);

        $subscriber = $this->createSubscriber($sequence);

        // Create existing send
        EmailSequenceSend::create([
            'email_sequence_email_id' => $email->id,
            'subscriber_id' => $subscriber->id,
            'scheduled_at' => now()->addDay(),
            'status' => EmailSendStatus::PENDING,
        ]);

        // Run job again
        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->count();

        $this->assertEquals(1, $sends);
    }

    #[Test]
    public function it_handles_hours_correctly_in_anchor_mode(): void
    {
        Carbon::setTestNow('2025-12-24 10:00:00');

        $anchorDatetime = Carbon::parse('2025-12-25 14:00:00', 'America/New_York')->utc();

        $sequence = $this->createSequence([
            'sequence_mode' => SequenceMode::BEFORE_ANCHOR_DATE,
            'anchor_datetime' => $anchorDatetime,
            'anchor_timezone' => 'America/New_York',
        ]);

        // 1 day and 2 hours before (Dec 24 at 12 PM Eastern)
        $this->createEmail($sequence, [
            'name' => '1 day 2 hours before',
            'delay_days' => 1,
            'delay_hours' => 2,
            'order' => 1,
        ]);

        $subscriber = $this->createSubscriber($sequence, [
            'subscribed_at' => Carbon::parse('2025-12-24 10:00:00'),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $send = EmailSequenceSend::where('subscriber_id', $subscriber->id)->first();

        // 1 day 2 hours before Dec 25 2PM = Dec 24 12 PM Eastern
        $this->assertEquals(
            '2025-12-24 12:00',
            $send->scheduled_at->timezone('America/New_York')->format('Y-m-d H:i')
        );
    }

    /**
     * Helper to create the standard event countdown sequence for testing.
     * Event: December 25, 2025 at 2:00 PM Eastern
     */
    private function createEventCountdownSequence(): EmailSequence
    {
        $anchorDatetime = Carbon::parse('2025-12-25 14:00:00', 'America/New_York')->utc();

        $sequence = $this->createSequence([
            'sequence_mode' => SequenceMode::BEFORE_ANCHOR_DATE,
            'anchor_datetime' => $anchorDatetime,
            'anchor_timezone' => 'America/New_York',
        ]);

        // 20 days before → Dec 5 at 2 PM
        $this->createEmail($sequence, [
            'name' => '20 days before',
            'delay_days' => 20,
            'delay_hours' => 0,
            'order' => 1,
        ]);

        // 15 days before → Dec 10 at 2 PM
        $this->createEmail($sequence, [
            'name' => '15 days before',
            'delay_days' => 15,
            'delay_hours' => 0,
            'order' => 2,
        ]);

        // 10 days before → Dec 15 at 2 PM
        $this->createEmail($sequence, [
            'name' => '10 days before',
            'delay_days' => 10,
            'delay_hours' => 0,
            'order' => 3,
        ]);

        // 5 days before → Dec 20 at 2 PM
        $this->createEmail($sequence, [
            'name' => '5 days before',
            'delay_days' => 5,
            'delay_hours' => 0,
            'order' => 4,
        ]);

        // 1 hour before → Dec 25 at 1 PM
        $this->createEmail($sequence, [
            'name' => '1 hour before',
            'delay_days' => 0,
            'delay_hours' => 1,
            'order' => 5,
        ]);

        return $sequence;
    }

    #[Test]
    public function person_a_signs_up_dec_1_gets_all_emails(): void
    {
        // Person A signs up on 12/1/2025 at 9 AM Eastern - should get ALL 5 emails
        Carbon::setTestNow(Carbon::parse('2025-12-01 09:00:00', 'America/New_York'));

        $sequence = $this->createEventCountdownSequence();

        $subscriber = $this->createSubscriber($sequence, [
            'email' => 'person.a@example.com',
            'subscribed_at' => Carbon::now(),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)
            ->orderBy('scheduled_at')
            ->get();

        // Person A should get ALL 5 emails
        $this->assertCount(5, $sends, 'Person A (Dec 1 signup) should receive all 5 emails');

        // Verify each email is scheduled for the correct date/time (stored as UTC)
        // Event: Dec 25 at 2 PM Eastern = Dec 25 at 19:00 UTC
        // 20 days before: Dec 5 at 2 PM Eastern = Dec 5 at 19:00 UTC
        $expectedScheduleUtc = [
            '20 days before' => '2025-12-05 19:00:00',
            '15 days before' => '2025-12-10 19:00:00',
            '10 days before' => '2025-12-15 19:00:00',
            '5 days before' => '2025-12-20 19:00:00',
            '1 hour before' => '2025-12-25 18:00:00', // 1 PM Eastern = 18:00 UTC
        ];

        foreach ($sends as $send) {
            $emailName = $send->email->name;
            // Check the raw database value to avoid timezone conversion issues in tests
            $scheduledTimeUtc = $send->getRawOriginal('scheduled_at');

            $this->assertArrayHasKey($emailName, $expectedScheduleUtc, "Unknown email: {$emailName}");
            $this->assertEquals(
                $expectedScheduleUtc[$emailName],
                $scheduledTimeUtc,
                "Email '{$emailName}' should be scheduled for {$expectedScheduleUtc[$emailName]} UTC, got {$scheduledTimeUtc}"
            );
        }
    }

    #[Test]
    public function person_b_signs_up_dec_14_skips_20_and_15_day_emails(): void
    {
        // Person B signs up on 12/14/2025 at 9 AM Eastern
        // 20-day email (Dec 5) and 15-day email (Dec 10) are already past
        // Should get: 10-day (Dec 15), 5-day (Dec 20), 1-hour (Dec 25 1PM)
        Carbon::setTestNow(Carbon::parse('2025-12-14 09:00:00', 'America/New_York'));

        $sequence = $this->createEventCountdownSequence();

        $subscriber = $this->createSubscriber($sequence, [
            'email' => 'person.b@example.com',
            'subscribed_at' => Carbon::now(),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)
            ->orderBy('scheduled_at')
            ->get();

        // Person B should get 3 emails (skipping 20-day and 15-day)
        $this->assertCount(3, $sends, 'Person B (Dec 14 signup) should receive 3 emails');

        // Verify skipped emails
        $emailNames = $sends->pluck('email.name')->toArray();
        $this->assertNotContains('20 days before', $emailNames, '20-day email should be skipped');
        $this->assertNotContains('15 days before', $emailNames, '15-day email should be skipped');

        // Verify the remaining emails are scheduled correctly (UTC times)
        // Dec 15 at 2 PM Eastern = 19:00 UTC, etc.
        $expectedScheduleUtc = [
            '10 days before' => '2025-12-15 19:00:00',
            '5 days before' => '2025-12-20 19:00:00',
            '1 hour before' => '2025-12-25 18:00:00',
        ];

        foreach ($sends as $send) {
            $emailName = $send->email->name;
            $scheduledTimeUtc = $send->getRawOriginal('scheduled_at');

            $this->assertEquals(
                $expectedScheduleUtc[$emailName],
                $scheduledTimeUtc,
                "Email '{$emailName}' should be scheduled for {$expectedScheduleUtc[$emailName]} UTC, got {$scheduledTimeUtc}"
            );
        }
    }

    #[Test]
    public function person_c_signs_up_dec_20_skips_20_15_10_day_emails(): void
    {
        // Person C signs up on 12/20/2025 at 9 AM Eastern - should skip 20, 15, and 10-day emails
        Carbon::setTestNow(Carbon::parse('2025-12-20 09:00:00', 'America/New_York'));

        $sequence = $this->createEventCountdownSequence();

        $subscriber = $this->createSubscriber($sequence, [
            'email' => 'person.c@example.com',
            'subscribed_at' => Carbon::now(),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)
            ->orderBy('scheduled_at')
            ->get();

        // Person C should get only 2 emails (5-day and 1-hour)
        $this->assertCount(2, $sends, 'Person C (Dec 20 signup) should receive 2 emails');

        // Verify skipped emails
        $emailNames = $sends->pluck('email.name')->toArray();
        $this->assertNotContains('20 days before', $emailNames, '20-day email should be skipped');
        $this->assertNotContains('15 days before', $emailNames, '15-day email should be skipped');
        $this->assertNotContains('10 days before', $emailNames, '10-day email should be skipped');

        // Verify scheduled emails
        $this->assertContains('5 days before', $emailNames);
        $this->assertContains('1 hour before', $emailNames);

        // Verify correct times (UTC)
        // Dec 20 at 2 PM Eastern = 19:00 UTC, Dec 25 at 1 PM Eastern = 18:00 UTC
        $fiveDaySend = $sends->firstWhere('email.name', '5 days before');
        $oneHourSend = $sends->firstWhere('email.name', '1 hour before');

        $this->assertEquals(
            '2025-12-20 19:00:00',
            $fiveDaySend->getRawOriginal('scheduled_at'),
            '5-day email should be scheduled for Dec 20 at 19:00 UTC'
        );

        $this->assertEquals(
            '2025-12-25 18:00:00',
            $oneHourSend->getRawOriginal('scheduled_at'),
            '1-hour email should be scheduled for Dec 25 at 18:00 UTC'
        );
    }

    #[Test]
    public function very_late_signup_on_event_day_only_gets_1_hour_email(): void
    {
        // Someone signs up on Dec 25 at 10 AM Eastern - should only get 1-hour before email (1 PM)
        Carbon::setTestNow(Carbon::parse('2025-12-25 10:00:00', 'America/New_York'));

        $sequence = $this->createEventCountdownSequence();

        $subscriber = $this->createSubscriber($sequence, [
            'email' => 'last.minute@example.com',
            'subscribed_at' => Carbon::now(),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->get();

        // Should only get the 1-hour before email (1 PM Eastern = 18:00 UTC)
        $this->assertCount(1, $sends, 'Dec 25 10 AM signup should only get 1 email');
        $this->assertEquals('1 hour before', $sends->first()->email->name);
        $this->assertEquals(
            '2025-12-25 18:00:00',
            $sends->first()->getRawOriginal('scheduled_at'),
            '1-hour email should be scheduled for Dec 25 at 18:00 UTC'
        );
    }

    #[Test]
    public function signup_after_event_gets_no_emails(): void
    {
        // Someone signs up after the event - should get NO emails
        // Event is Dec 25 at 2 PM Eastern, signup at 3 PM Eastern
        Carbon::setTestNow(Carbon::parse('2025-12-25 15:00:00', 'America/New_York'));

        $sequence = $this->createEventCountdownSequence();

        $subscriber = $this->createSubscriber($sequence, [
            'email' => 'too.late@example.com',
            'subscribed_at' => Carbon::now(),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->get();

        $this->assertCount(0, $sends, 'Post-event signup should receive no emails');
    }

    #[Test]
    public function it_schedules_welcome_email_immediately_when_enabled(): void
    {
        Carbon::setTestNow('2025-12-01 10:00:00');

        $sequence = $this->createSequence([
            'sequence_mode' => \App\Enums\SequenceMode::AFTER_SUBSCRIPTION,
            'send_welcome_email' => true,
            'welcome_email_subject' => 'Welcome to our list!',
            'welcome_email_body' => 'Thanks for signing up!',
        ]);

        $this->createEmail($sequence, [
            'delay_days' => 1,
            'order' => 1,
        ]);

        $subscriber = $this->createSubscriber($sequence, [
            'subscribed_at' => Carbon::now(),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->get();

        // Should have 2 sends: welcome email + scheduled email
        $this->assertCount(2, $sends);

        // Verify welcome email exists and is scheduled for now
        $welcomeSend = $sends->where('is_welcome_email', true)->first();
        $this->assertNotNull($welcomeSend, 'Welcome email should be scheduled');
        $this->assertNull($welcomeSend->email_sequence_email_id);
        $this->assertEquals(
            Carbon::now()->format('Y-m-d H:i'),
            $welcomeSend->scheduled_at->format('Y-m-d H:i'),
            'Welcome email should be scheduled for immediate delivery'
        );
    }

    #[Test]
    public function it_does_not_schedule_welcome_email_when_disabled(): void
    {
        Carbon::setTestNow('2025-12-01 10:00:00');

        $sequence = $this->createSequence([
            'sequence_mode' => \App\Enums\SequenceMode::AFTER_SUBSCRIPTION,
            'send_welcome_email' => false,
        ]);

        $this->createEmail($sequence, [
            'delay_days' => 1,
            'order' => 1,
        ]);

        $subscriber = $this->createSubscriber($sequence, [
            'subscribed_at' => Carbon::now(),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->get();

        // Should only have 1 send (the scheduled email, no welcome)
        $this->assertCount(1, $sends);
        $this->assertFalse($sends->first()->is_welcome_email);
    }

    #[Test]
    public function welcome_email_works_with_anchor_mode(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-12-01 09:00:00', 'America/New_York'));

        $anchorDatetime = Carbon::parse('2025-12-25 14:00:00', 'America/New_York')->utc();

        $sequence = $this->createSequence([
            'sequence_mode' => \App\Enums\SequenceMode::BEFORE_ANCHOR_DATE,
            'anchor_datetime' => $anchorDatetime,
            'anchor_timezone' => 'America/New_York',
            'send_welcome_email' => true,
            'welcome_email_subject' => 'Thanks for registering for our event!',
            'welcome_email_body' => 'See you on December 25th!',
        ]);

        $this->createEmail($sequence, [
            'name' => '10 days before',
            'delay_days' => 10,
            'order' => 1,
        ]);

        $subscriber = $this->createSubscriber($sequence, [
            'subscribed_at' => Carbon::now(),
        ]);

        $job = new ProcessEmailSequenceSubscriber($subscriber);
        $job->handle();

        $sends = EmailSequenceSend::where('subscriber_id', $subscriber->id)->get();

        // Should have 2 sends: welcome email + 10 days before
        $this->assertCount(2, $sends);

        $welcomeSend = $sends->where('is_welcome_email', true)->first();
        $this->assertNotNull($welcomeSend, 'Welcome email should be scheduled in anchor mode');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
