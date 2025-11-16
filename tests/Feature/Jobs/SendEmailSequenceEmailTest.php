<?php

namespace Tests\Feature\Jobs;

use App\Enums\EmailSendStatus;
use App\Enums\SubscriberStatus;
use App\Jobs\SendEmailSequenceEmail;
use App\Models\EmailSequence;
use App\Models\EmailSequenceEmail;
use App\Models\EmailSequenceSend;
use App\Models\EmailSequenceSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendEmailSequenceEmailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_replaces_merge_tags_from_form_field_metadata(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $sequence = EmailSequence::factory()->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $sequenceEmail = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Test Email',
            'subject' => 'Welcome {first_name}!',
            'preview_text' => 'Hi {first_name}, thanks for signing up!',
            'body' => 'Hello {first_name} {last_name}! Your phone is {phone}.',
            'delay_days' => 0,
            'send_time' => '09:00:00',
            'timezone' => 'America/New_York',
            'order' => 1,
        ]);

        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'test@example.com',
            'metadata' => [
                'email' => 'test@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '555-1234',
                'company' => 'Acme Corp',
            ],
            'status' => SubscriberStatus::ACTIVE,
            'subscribed_at' => now(),
        ]);

        $send = EmailSequenceSend::create([
            'email_sequence_email_id' => $sequenceEmail->id,
            'subscriber_id' => $subscriber->id,
            'scheduled_at' => now()->subMinute(),
            'status' => EmailSendStatus::PENDING,
        ]);

        // Execute the job
        $job = new SendEmailSequenceEmail($send);
        $job->handle();

        // Verify email was sent
        Mail::assertSent(\App\Mail\EmailSequenceMail::class, function ($mail) {
            return $mail->processedSubject === 'Welcome John!' &&
                   $mail->processedPreviewText === 'Hi John, thanks for signing up!' &&
                   str_contains($mail->processedBody, 'Hello John Doe!') &&
                   str_contains($mail->processedBody, 'Your phone is 555-1234.');
        });
    }

    #[Test]
    public function it_handles_missing_metadata_fields_gracefully(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $sequence = EmailSequence::factory()->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $sequenceEmail = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Test Email',
            'subject' => 'Welcome {first_name}!',
            'body' => 'Hello {first_name} {last_name}!',
            'delay_days' => 0,
            'send_time' => '09:00:00',
            'timezone' => 'America/New_York',
            'order' => 1,
        ]);

        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'test@example.com',
            'metadata' => [
                'email' => 'test@example.com',
                'first_name' => 'Jane',
                // last_name is missing
            ],
            'status' => SubscriberStatus::ACTIVE,
            'subscribed_at' => now(),
        ]);

        $send = EmailSequenceSend::create([
            'email_sequence_email_id' => $sequenceEmail->id,
            'subscriber_id' => $subscriber->id,
            'scheduled_at' => now()->subMinute(),
            'status' => EmailSendStatus::PENDING,
        ]);

        // Execute the job
        $job = new SendEmailSequenceEmail($send);
        $job->handle();

        // Verify email was sent with unreplaced tags left in place
        Mail::assertSent(\App\Mail\EmailSequenceMail::class, function ($mail) {
            return $mail->processedSubject === 'Welcome Jane!' &&
                   str_contains($mail->processedBody, 'Hello Jane {last_name}!');
        });
    }

    #[Test]
    public function it_converts_array_values_to_comma_separated_strings(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $sequence = EmailSequence::factory()->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $sequenceEmail = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Test Email',
            'subject' => 'Your interests',
            'body' => 'You selected: {interests}',
            'delay_days' => 0,
            'send_time' => '09:00:00',
            'timezone' => 'America/New_York',
            'order' => 1,
        ]);

        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'test@example.com',
            'metadata' => [
                'email' => 'test@example.com',
                'interests' => ['Sports', 'Music', 'Travel'],
            ],
            'status' => SubscriberStatus::ACTIVE,
            'subscribed_at' => now(),
        ]);

        $send = EmailSequenceSend::create([
            'email_sequence_email_id' => $sequenceEmail->id,
            'subscriber_id' => $subscriber->id,
            'scheduled_at' => now()->subMinute(),
            'status' => EmailSendStatus::PENDING,
        ]);

        // Execute the job
        $job = new SendEmailSequenceEmail($send);
        $job->handle();

        // Verify array was converted to comma-separated string
        Mail::assertSent(\App\Mail\EmailSequenceMail::class, function ($mail) {
            return str_contains($mail->processedBody, 'You selected: Sports, Music, Travel');
        });
    }

    #[Test]
    public function it_converts_boolean_values_to_yes_no(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $sequence = EmailSequence::factory()->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $sequenceEmail = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Test Email',
            'subject' => 'Newsletter preference',
            'body' => 'Newsletter: {newsletter_opt_in}',
            'delay_days' => 0,
            'send_time' => '09:00:00',
            'timezone' => 'America/New_York',
            'order' => 1,
        ]);

        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'test@example.com',
            'metadata' => [
                'email' => 'test@example.com',
                'newsletter_opt_in' => true,
            ],
            'status' => SubscriberStatus::ACTIVE,
            'subscribed_at' => now(),
        ]);

        $send = EmailSequenceSend::create([
            'email_sequence_email_id' => $sequenceEmail->id,
            'subscriber_id' => $subscriber->id,
            'scheduled_at' => now()->subMinute(),
            'status' => EmailSendStatus::PENDING,
        ]);

        // Execute the job
        $job = new SendEmailSequenceEmail($send);
        $job->handle();

        // Verify boolean was converted to Yes
        Mail::assertSent(\App\Mail\EmailSequenceMail::class, function ($mail) {
            return str_contains($mail->processedBody, 'Newsletter: Yes');
        });
    }

    #[Test]
    public function it_always_replaces_email_merge_tag(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $sequence = EmailSequence::factory()->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $sequenceEmail = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Test Email',
            'subject' => 'Email confirmation',
            'body' => 'Your email is {email}',
            'delay_days' => 0,
            'send_time' => '09:00:00',
            'timezone' => 'America/New_York',
            'order' => 1,
        ]);

        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'subscriber@example.com',
            'metadata' => [],
            'status' => SubscriberStatus::ACTIVE,
            'subscribed_at' => now(),
        ]);

        $send = EmailSequenceSend::create([
            'email_sequence_email_id' => $sequenceEmail->id,
            'subscriber_id' => $subscriber->id,
            'scheduled_at' => now()->subMinute(),
            'status' => EmailSendStatus::PENDING,
        ]);

        // Execute the job
        $job = new SendEmailSequenceEmail($send);
        $job->handle();

        // Verify email tag is always replaced
        Mail::assertSent(\App\Mail\EmailSequenceMail::class, function ($mail) {
            return str_contains($mail->processedBody, 'Your email is subscriber@example.com');
        });
    }
}
