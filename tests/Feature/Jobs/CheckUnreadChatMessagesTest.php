<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CheckUnreadChatMessages;
use App\Models\Business;
use App\Models\Campaign;
use App\Models\Chat;
use App\Models\Influencer;
use App\Models\Message;
use App\Models\User;
use App\Notifications\UnreadMessagesReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckUnreadChatMessagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
        Notification::fake();
        Cache::flush();
    }

    #[Test]
    public function it_notifies_users_about_messages_within_notification_window(): void
    {
        // Create business with owner
        $business = Business::factory()->create();
        $businessOwner = User::factory()->create();
        $business->users()->attach($businessOwner->id, ['role' => 'owner']);

        // Create influencer
        $influencerUser = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $influencerUser->id]);
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Create chat
        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
            'status' => 'active',
        ]);

        // Create message from business to influencer that's 1 day old (within window)
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $businessOwner->id,
            'body' => 'Hello influencer!',
            'is_system_message' => false,
            'created_at' => now()->subDay(),
        ]);

        // Run the job
        $job = new CheckUnreadChatMessages;
        $job->handle();

        // Influencer should be notified
        Notification::assertSentTo($influencerUser, UnreadMessagesReminderNotification::class);
    }

    #[Test]
    public function it_does_not_notify_about_messages_older_than_max_threshold(): void
    {
        // Create business with owner
        $business = Business::factory()->create();
        $businessOwner = User::factory()->create();
        $business->users()->attach($businessOwner->id, ['role' => 'owner']);

        // Create influencer
        $influencerUser = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $influencerUser->id]);
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Create chat
        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
            'status' => 'active',
        ]);

        // Create message that's 10 days old (outside the 7-day window)
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $businessOwner->id,
            'body' => 'Old message',
            'is_system_message' => false,
            'created_at' => now()->subDays(10),
        ]);

        // Run the job
        $job = new CheckUnreadChatMessages;
        $job->handle();

        // No notifications should be sent for old messages
        Notification::assertNothingSent();
    }

    #[Test]
    public function it_does_not_notify_about_messages_newer_than_min_threshold(): void
    {
        // Create business with owner
        $business = Business::factory()->create();
        $businessOwner = User::factory()->create();
        $business->users()->attach($businessOwner->id, ['role' => 'owner']);

        // Create influencer
        $influencerUser = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $influencerUser->id]);
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Create chat
        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
            'status' => 'active',
        ]);

        // Create message that's only 1 hour old (less than 4-hour minimum)
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $businessOwner->id,
            'body' => 'Recent message',
            'is_system_message' => false,
            'created_at' => now()->subHour(),
        ]);

        // Run the job
        $job = new CheckUnreadChatMessages;
        $job->handle();

        // No notifications should be sent for recent messages
        Notification::assertNothingSent();
    }

    #[Test]
    public function it_does_not_notify_about_already_read_messages(): void
    {
        // Create business with owner
        $business = Business::factory()->create();
        $businessOwner = User::factory()->create();
        $business->users()->attach($businessOwner->id, ['role' => 'owner']);

        // Create influencer
        $influencerUser = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $influencerUser->id]);
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Create chat
        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
            'status' => 'active',
        ]);

        // Create message within window
        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $businessOwner->id,
            'body' => 'Hello!',
            'is_system_message' => false,
            'created_at' => now()->subDay(),
        ]);

        // Mark it as read by the influencer
        $message->markAsReadBy($influencerUser);

        // Run the job
        $job = new CheckUnreadChatMessages;
        $job->handle();

        // No notifications since message is read
        Notification::assertNothingSent();
    }

    #[Test]
    public function it_does_not_notify_about_archived_chats(): void
    {
        // Create business with owner
        $business = Business::factory()->create();
        $businessOwner = User::factory()->create();
        $business->users()->attach($businessOwner->id, ['role' => 'owner']);

        // Create influencer
        $influencerUser = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $influencerUser->id]);
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Create archived chat
        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
            'status' => 'archived',
        ]);

        // Create message within window
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $businessOwner->id,
            'body' => 'Message in archived chat',
            'is_system_message' => false,
            'created_at' => now()->subDay(),
        ]);

        // Run the job
        $job = new CheckUnreadChatMessages;
        $job->handle();

        // No notifications for archived chats
        Notification::assertNothingSent();
    }

    #[Test]
    public function it_uses_cache_to_prevent_spam(): void
    {
        // Create business with owner
        $business = Business::factory()->create();
        $businessOwner = User::factory()->create();
        $business->users()->attach($businessOwner->id, ['role' => 'owner']);

        // Create influencer
        $influencerUser = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $influencerUser->id]);
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Create chat
        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
            'status' => 'active',
        ]);

        // Create message within window
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $businessOwner->id,
            'body' => 'Hello!',
            'is_system_message' => false,
            'created_at' => now()->subDay(),
        ]);

        // Run the job twice
        $job1 = new CheckUnreadChatMessages;
        $job1->handle();

        $job2 = new CheckUnreadChatMessages;
        $job2->handle();

        // Should only be notified once (second run is cached)
        Notification::assertSentToTimes($influencerUser, UnreadMessagesReminderNotification::class, 1);
    }

    #[Test]
    public function it_does_not_notify_users_about_their_own_messages(): void
    {
        // Create business with owner
        $business = Business::factory()->create();
        $businessOwner = User::factory()->create();
        $business->users()->attach($businessOwner->id, ['role' => 'owner']);

        // Create influencer
        $influencerUser = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $influencerUser->id]);
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Create chat
        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
            'status' => 'active',
        ]);

        // Create message FROM influencer (so business should be notified, not influencer)
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $influencerUser->id,
            'body' => 'Message from influencer',
            'is_system_message' => false,
            'created_at' => now()->subDay(),
        ]);

        // Run the job
        $job = new CheckUnreadChatMessages;
        $job->handle();

        // Business owner should be notified, not influencer
        Notification::assertSentTo($businessOwner, UnreadMessagesReminderNotification::class);
        Notification::assertNotSentTo($influencerUser, UnreadMessagesReminderNotification::class);
    }
}
