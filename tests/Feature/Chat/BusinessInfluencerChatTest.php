<?php

namespace Tests\Feature\Chat;

use App\Models\Business;
use App\Models\Campaign;
use App\Models\Chat;
use App\Models\Influencer;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BusinessInfluencerChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake events to prevent broadcasting errors in tests
        Event::fake();
    }

    #[Test]
    public function business_members_can_access_chats_for_their_business()
    {
        // Create business with two members
        $business = Business::factory()->create();
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();
        $business->users()->attach($member1->id, ['role' => 'owner']);
        $business->users()->attach($member2->id, ['role' => 'member']);

        // Create influencer
        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Create chat
        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        // Both business members should have access
        $this->assertTrue($chat->hasParticipant($member1));
        $this->assertTrue($chat->hasParticipant($member2));

        // Influencer should have access
        $this->assertTrue($chat->hasParticipant($influencer->user));
    }

    #[Test]
    public function non_business_members_cannot_access_business_chats()
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $nonMember = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        $this->assertTrue($chat->hasParticipant($member));
        $this->assertFalse($chat->hasParticipant($nonMember));
    }

    #[Test]
    public function all_business_members_can_send_messages_in_chat()
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

        // Member 1 sends a message
        $message1 = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $member1->id,
            'body' => 'Hello from member 1',
        ]);

        // Member 2 sends a message
        $message2 = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $member2->id,
            'body' => 'Hello from member 2',
        ]);

        // Influencer sends a message
        $message3 = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $influencer->user->id,
            'body' => 'Hello from influencer',
        ]);

        $this->assertCount(3, $chat->messages);
        $this->assertEquals('Hello from member 1', $chat->messages[0]->body);
        $this->assertEquals('Hello from member 2', $chat->messages[1]->body);
        $this->assertEquals('Hello from influencer', $chat->messages[2]->body);
    }

    #[Test]
    public function all_business_members_can_see_all_message_history()
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

        // Create messages from different users
        Message::create(['chat_id' => $chat->id, 'user_id' => $member1->id, 'body' => 'Message 1']);
        Message::create(['chat_id' => $chat->id, 'user_id' => $influencer->user->id, 'body' => 'Message 2']);
        Message::create(['chat_id' => $chat->id, 'user_id' => $member1->id, 'body' => 'Message 3']);

        // Both members should see all messages
        $this->assertCount(3, $chat->messages);
        $this->assertTrue($chat->hasParticipant($member1));
        $this->assertTrue($chat->hasParticipant($member2));
    }

    #[Test]
    public function chat_is_scoped_to_specific_campaign()
    {
        $business = Business::factory()->create();
        $influencer = Influencer::factory()->create();
        $campaign1 = Campaign::factory()->create(['business_id' => $business->id]);
        $campaign2 = Campaign::factory()->create(['business_id' => $business->id]);

        $chat1 = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign1->id,
        ]);

        $chat2 = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign2->id,
        ]);

        $this->assertEquals($campaign1->id, $chat1->campaign_id);
        $this->assertEquals($campaign2->id, $chat2->campaign_id);
        $this->assertNotEquals($chat1->id, $chat2->id);
    }

    #[Test]
    public function for_user_scope_returns_chats_for_business_members()
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

        $memberChats = Chat::forUser($member)->get();
        $this->assertCount(1, $memberChats);
        $this->assertEquals($chat->id, $memberChats->first()->id);
    }

    #[Test]
    public function for_user_scope_returns_chats_for_influencers()
    {
        $business = Business::factory()->create();
        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        $influencerChats = Chat::forUser($influencer->user)->get();
        $this->assertCount(1, $influencerChats);
        $this->assertEquals($chat->id, $influencerChats->first()->id);
    }

    #[Test]
    public function display_name_shows_correctly_for_business_and_influencer()
    {
        $business = Business::factory()->create(['name' => 'Test Business']);
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencerUser = User::factory()->create(['name' => 'Test Influencer']);
        $influencer = Influencer::factory()->create(['user_id' => $influencerUser->id]);
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        // Business member sees influencer name
        $this->assertEquals('Test Influencer', $chat->getDisplayNameFor($member));

        // Influencer sees business name
        $this->assertEquals('Test Business', $chat->getDisplayNameFor($influencerUser));
    }

    #[Test]
    public function user_can_get_unread_message_count()
    {
        $business = Business::factory()->create();
        $member = User::factory()->create();
        $business->users()->attach($member->id, ['role' => 'owner']);

        $influencerUser = User::factory()->create();
        $influencer = Influencer::factory()->create(['user_id' => $influencerUser->id]);
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        $chat = Chat::create([
            'business_id' => $business->id,
            'influencer_id' => $influencer->id,
            'campaign_id' => $campaign->id,
        ]);

        // Create some unread messages from influencer to business
        Message::create(['chat_id' => $chat->id, 'user_id' => $influencerUser->id, 'body' => 'Message 1']);
        Message::create(['chat_id' => $chat->id, 'user_id' => $influencerUser->id, 'body' => 'Message 2']);
        Message::create(['chat_id' => $chat->id, 'user_id' => $influencerUser->id, 'body' => 'Message 3']);

        // Business member should see 3 unread messages
        $this->assertEquals(3, $member->getUnreadMessageCount());

        // Influencer should see 0 unread messages (they sent them)
        $this->assertEquals(0, $influencerUser->getUnreadMessageCount());

        // Create a message from business to influencer
        Message::create(['chat_id' => $chat->id, 'user_id' => $member->id, 'body' => 'Reply from business']);

        // Influencer should now see 1 unread message (clear cache first since we modified data)
        $influencerUser->clearUnreadMessageCountCache();
        $this->assertEquals(1, $influencerUser->getUnreadMessageCount());
    }

    #[Test]
    public function find_or_create_for_campaign_creates_chat_with_correct_relationships()
    {
        $business = Business::factory()->create();
        $influencer = Influencer::factory()->create();
        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Ensure no chat exists initially
        $this->assertEquals(0, Chat::count());

        // Create chat
        $chat = Chat::findOrCreateForCampaign($business, $influencer, $campaign);

        // Verify chat was created
        $this->assertEquals(1, Chat::count());
        $this->assertEquals($business->id, $chat->business_id);
        $this->assertEquals($influencer->id, $chat->influencer_id);
        $this->assertEquals($campaign->id, $chat->campaign_id);

        // Calling again should return the same chat
        $chat2 = Chat::findOrCreateForCampaign($business, $influencer, $campaign);
        $this->assertEquals($chat->id, $chat2->id);
        $this->assertEquals(1, Chat::count());
    }
}
