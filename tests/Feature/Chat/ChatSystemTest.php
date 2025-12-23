<?php

namespace Tests\Feature\Chat;

use App\Enums\ChatStatus;
use App\Enums\ReactionType;
use App\Enums\SystemMessageType;
use App\Models\Business;
use App\Models\Campaign;
use App\Models\Chat;
use App\Models\Influencer;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChatSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    #[Test]
    public function chat_has_correct_status_when_created(): void
    {
        $business = Business::factory()->create();
        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::findOrCreateForCampaign($business, $influencer, $campaign);

        $this->assertEquals(ChatStatus::Active, $chat->status);
        $this->assertTrue($chat->isActive());
        $this->assertFalse($chat->isArchived());
    }

    #[Test]
    public function chat_can_be_archived(): void
    {
        $business = Business::factory()->create();
        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::findOrCreateForCampaign($business, $influencer, $campaign);

        $chat->archive();

        $this->assertEquals(ChatStatus::Archived, $chat->status);
        $this->assertFalse($chat->isActive());
        $this->assertTrue($chat->isArchived());
        $this->assertNotNull($chat->archived_at);
    }

    #[Test]
    public function archived_chats_cannot_accept_messages(): void
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::findOrCreateForCampaign($business, $influencer, $campaign);
        $chat->archive();

        $this->assertFalse($chat->canSendMessage($member));
        $this->assertFalse($chat->canSendMessage($influencer->user));
    }

    #[Test]
    public function system_messages_can_be_created(): void
    {
        $business = Business::factory()->create();
        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        $message = ChatService::sendSystemMessage($chat, SystemMessageType::CampaignStarted);

        $this->assertTrue($message->is_system_message);
        $this->assertEquals(SystemMessageType::CampaignStarted, $message->system_message_type);
        $this->assertNull($message->user_id);
    }

    #[Test]
    public function message_reactions_can_be_toggled(): void
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $member->id,
            'body' => 'Test message',
        ]);

        // Add reaction
        $added = $message->toggleReaction($influencer->user, ReactionType::ThumbsUp);
        $this->assertTrue($added);
        $this->assertEquals(1, $message->reactions()->count());
        $this->assertTrue($message->hasReactionFrom($influencer->user, ReactionType::ThumbsUp));

        // Remove reaction
        $added = $message->toggleReaction($influencer->user, ReactionType::ThumbsUp);
        $this->assertFalse($added);
        $this->assertEquals(0, $message->reactions()->count());
        $this->assertFalse($message->hasReactionFrom($influencer->user, ReactionType::ThumbsUp));
    }

    #[Test]
    public function multiple_users_can_react_to_same_message(): void
    {
        $business = Business::factory()->create();
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();
        $business->users()->attach($member1->id, ['role' => 'owner']);
        $business->users()->attach($member2->id, ['role' => 'member']);

        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $influencer->user->id,
            'body' => 'Great work!',
        ]);

        $message->toggleReaction($member1, ReactionType::ThumbsUp);
        $message->toggleReaction($member2, ReactionType::ThumbsUp);
        $message->toggleReaction($member1, ReactionType::Heart);

        $counts = $message->getReactionCounts();

        $this->assertEquals(2, $counts['thumbs_up']);
        $this->assertEquals(1, $counts['heart']);
    }

    #[Test]
    public function message_read_tracking_works(): void
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $influencer->user->id,
            'body' => 'Hello business!',
        ]);

        // Initially unread
        $this->assertFalse($message->isReadBy($member));
        $this->assertEquals(1, $chat->getUnreadCountForUser($member));

        // Mark as read
        $message->markAsReadBy($member);

        $this->assertTrue($message->isReadBy($member));
        $this->assertEquals(0, $chat->getUnreadCountForUser($member));
    }

    #[Test]
    public function users_cannot_mark_their_own_messages_as_read(): void
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $member->id,
            'body' => 'My own message',
        ]);

        // Try to mark own message as read
        $result = $message->markAsReadBy($member);

        $this->assertNull($result);
        $this->assertEquals(0, MessageRead::count());
    }

    #[Test]
    public function chat_service_can_send_messages(): void
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
            'status' => ChatStatus::Active,
        ]);

        $message = ChatService::sendMessage($chat, $member, 'Hello via service!');

        $this->assertNotNull($message);
        $this->assertEquals('Hello via service!', $message->body);
        $this->assertEquals($member->id, $message->user_id);
        $this->assertFalse($message->is_system_message);
    }

    #[Test]
    public function chat_service_returns_null_for_archived_chat(): void
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
            'status' => ChatStatus::Archived,
        ]);

        $message = ChatService::sendMessage($chat, $member, 'This should not work');

        $this->assertNull($message);
    }

    #[Test]
    public function chat_service_returns_organized_chats(): void
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencer1 = Influencer::factory()->create();
        $influencer2 = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer1->id,
            'campaign_id' => $campaign->id,
            'status' => ChatStatus::Active,
        ]);

        Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer2->id,
            'campaign_id' => $campaign->id,
            'status' => ChatStatus::Archived,
        ]);

        $result = ChatService::getChatsForUser($member);

        $this->assertCount(1, $result['active']);
        $this->assertCount(1, $result['archived']);
    }

    #[Test]
    public function sender_role_is_correctly_identified(): void
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        $businessMessage = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $member->id,
            'body' => 'From business',
        ]);

        $influencerMessage = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $influencer->user->id,
            'body' => 'From influencer',
        ]);

        $this->assertEquals('business', $businessMessage->getSenderRole());
        $this->assertEquals('influencer', $influencerMessage->getSenderRole());
    }
}
