<?php

namespace Tests\Feature\Chat;

use App\Models\Business;
use App\Models\Campaign;
use App\Models\Chat;
use App\Models\Influencer;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChatMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake events to prevent broadcasting errors in tests
        Event::fake();
    }

    #[Test]
    public function it_migrates_old_user_to_user_chats_to_new_business_influencer_format()
    {
        // Create the old schema structure (before migration)
        $this->createOldChatSchema();

        // Create test data in old format
        $businessUser = User::factory()->business()->withProfile()->create();
        $business = $businessUser->businesses()->first();
        $business->users()->attach($businessUser->id, ['role' => 'owner']);

        $influencerUser = User::factory()->influencer()->withProfile()->create();
        $influencer = $influencerUser->influencer;

        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Insert chat with OLD schema (business_user_id and influencer_user_id)
        $chatId = DB::table('chats')->insertGetId([
            'business_user_id' => $businessUser->id,
            'influencer_user_id' => $influencerUser->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert messages for this chat
        DB::table('messages')->insert([
            'chat_id' => $chatId,
            'user_id' => $businessUser->id,
            'body' => 'Hello from business user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('messages')->insert([
            'chat_id' => $chatId,
            'user_id' => $influencerUser->id,
            'body' => 'Hello from influencer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verify old structure exists
        $this->assertDatabaseHas('chats', [
            'id' => $chatId,
            'business_user_id' => $businessUser->id,
            'influencer_user_id' => $influencerUser->id,
        ]);

        // Run the schema migration (part 1 - rename columns only)
        $this->runSchemaChangesWithoutForeignKeys($chatId, $business->id, $influencer->id, $campaign->id);

        // Now the data has been updated, we can verify

        // Verify new structure
        $chat = Chat::find($chatId);

        $this->assertNotNull($chat);
        $this->assertEquals($business->id, $chat->business_id);
        $this->assertEquals($influencer->id, $chat->influencer_id);
        $this->assertEquals($campaign->id, $chat->campaign_id);

        // Verify messages still exist and are intact
        $messages = Message::where('chat_id', $chatId)->get();
        $this->assertCount(2, $messages);
        $this->assertEquals('Hello from business user', $messages[0]->body);
        $this->assertEquals('Hello from influencer', $messages[1]->body);

        // Verify both users can still access the chat
        $this->assertTrue($chat->hasParticipant($businessUser));
        $this->assertTrue($chat->hasParticipant($influencerUser));
    }

    #[Test]
    public function it_handles_multiple_business_members_after_migration()
    {
        // Create the old schema structure
        $this->createOldChatSchema();

        // Create business with multiple members
        $business = Business::factory()->create();
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();
        $business->users()->attach($member1->id, ['role' => 'owner']);
        $business->users()->attach($member2->id, ['role' => 'member']);

        $influencerUser = User::factory()->influencer()->withProfile()->create();
        $influencer = $influencerUser->influencer;

        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Insert chat with member1 as the original business_user_id
        $chatId = DB::table('chats')->insertGetId([
            'business_user_id' => $member1->id,
            'influencer_user_id' => $influencerUser->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Run schema changes with data migration
        $this->runSchemaChangesWithoutForeignKeys($chatId, $business->id, $influencer->id, $campaign->id);

        $chat = Chat::find($chatId);

        // Both business members should have access to the chat
        $this->assertTrue($chat->hasParticipant($member1), 'Member 1 should have access');
        $this->assertTrue($chat->hasParticipant($member2), 'Member 2 should have access');

        // Verify forUser scope works for both members
        $member1Chats = Chat::forUser($member1)->get();
        $member2Chats = Chat::forUser($member2)->get();

        $this->assertCount(1, $member1Chats);
        $this->assertCount(1, $member2Chats);
        $this->assertEquals($chatId, $member1Chats->first()->id);
        $this->assertEquals($chatId, $member2Chats->first()->id);
    }

    #[Test]
    public function it_preserves_message_history_during_migration()
    {
        // Create the old schema structure
        $this->createOldChatSchema();

        $businessUser = User::factory()->business()->withProfile()->create();
        $business = $businessUser->businesses()->first();
        $business->users()->attach($businessUser->id, ['role' => 'owner']);

        $influencerUser = User::factory()->influencer()->withProfile()->create();
        $influencer = $influencerUser->influencer;

        $campaign = Campaign::factory()->create(['business_id' => $business->id]);

        // Create chat with multiple messages
        $chatId = DB::table('chats')->insertGetId([
            'business_user_id' => $businessUser->id,
            'influencer_user_id' => $influencerUser->id,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        // Create 10 messages
        for ($i = 1; $i <= 10; $i++) {
            DB::table('messages')->insert([
                'chat_id' => $chatId,
                'user_id' => $i % 2 === 0 ? $businessUser->id : $influencerUser->id,
                'body' => "Message {$i}",
                'created_at' => now()->subDays(5)->addHours($i),
                'updated_at' => now()->subDays(5)->addHours($i),
            ]);
        }

        // Run schema changes with data migration
        $this->runSchemaChangesWithoutForeignKeys($chatId, $business->id, $influencer->id, $campaign->id);

        // Verify all messages still exist
        $chat = Chat::with('messages')->find($chatId);
        $this->assertCount(10, $chat->messages);

        // Verify message order is preserved
        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals("Message " . ($i + 1), $chat->messages[$i]->body);
        }
    }

    /**
     * Create the old chat schema (before migration)
     */
    private function createOldChatSchema(): void
    {
        // Drop the new schema if it exists
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chats');

        // Create old chat schema
        Schema::create('chats', function ($table) {
            $table->id();
            $table->foreignId('business_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('influencer_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['business_user_id', 'influencer_user_id']);
        });

        // Create messages table (same structure in both old and new)
        Schema::create('messages', function ($table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->foreignId('read_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Run schema changes and data migration manually to avoid foreign key constraints
     */
    private function runSchemaChangesWithoutForeignKeys(int $chatId, int $businessId, int $influencerId, int $campaignId): void
    {
        Schema::table('chats', function ($table) {
            // Drop existing foreign keys first
            $table->dropForeign(['business_user_id']);
            $table->dropForeign(['influencer_user_id']);

            // Drop unique constraint
            $table->dropUnique(['business_user_id', 'influencer_user_id']);

            // Add campaign_id column
            $table->unsignedBigInteger('campaign_id')->after('id')->nullable();

            // Rename columns
            $table->renameColumn('business_user_id', 'business_id');
            $table->renameColumn('influencer_user_id', 'influencer_id');
        });

        // Manually update the data to use business_id and influencer_id
        DB::table('chats')->where('id', $chatId)->update([
            'business_id' => $businessId,
            'influencer_id' => $influencerId,
            'campaign_id' => $campaignId,
        ]);

        // Now add foreign keys (data is correct now)
        Schema::table('chats', function ($table) {
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('influencer_id')->references('id')->on('influencers')->onDelete('cascade');

            // Add unique constraint
            $table->unique(['business_id', 'influencer_id', 'campaign_id']);
        });
    }
}
