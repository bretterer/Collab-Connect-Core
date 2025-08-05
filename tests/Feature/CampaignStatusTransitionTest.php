<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Events\CampaignArchived;
use App\Events\CampaignPublished;
use App\Events\CampaignScheduled;
use App\Events\CampaignUnpublished;
use App\Models\Campaign;
use App\Models\User;
use App\Services\CampaignService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CampaignStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected User $businessUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->businessUser = User::factory()->business()->withProfile()->create();
    }

    public function test_draft_campaign_can_be_published()
    {
        Event::fake();

        $campaign = Campaign::factory()->create([
            'user_id' => $this->businessUser->id,
            'status' => CampaignStatus::DRAFT,
            'published_at' => null,
        ]);

        $this->assertTrue($campaign->isDraft());
        $this->assertFalse($campaign->isPublished());

        $publishedCampaign = CampaignService::publishCampaign($campaign);

        $this->assertTrue($publishedCampaign->isPublished());
        $this->assertFalse($publishedCampaign->isDraft());
        $this->assertNotNull($publishedCampaign->published_at);

        Event::assertDispatched(CampaignPublished::class);
    }

    public function test_draft_campaign_can_be_scheduled()
    {
        Event::fake();

        $campaign = Campaign::factory()->create([
            'user_id' => $this->businessUser->id,
            'status' => CampaignStatus::DRAFT,
            'scheduled_date' => null,
        ]);

        $this->assertTrue($campaign->isDraft());
        $this->assertFalse($campaign->isScheduled());

        $futureDate = Carbon::now()->addDays(7)->format('Y-m-d');
        $scheduledCampaign = CampaignService::scheduleCampaign($campaign, $futureDate);

        $this->assertTrue($scheduledCampaign->isScheduled());
        $this->assertFalse($scheduledCampaign->isDraft());
        $this->assertEquals($futureDate, $scheduledCampaign->scheduled_date->format('Y-m-d'));

        Event::assertDispatched(CampaignScheduled::class);
    }

    public function test_published_campaign_can_be_archived()
    {
        Event::fake();

        $campaign = Campaign::factory()->published()->create([
            'user_id' => $this->businessUser->id,
        ]);

        $this->assertTrue($campaign->isPublished());
        $this->assertFalse($campaign->isArchived());

        $archivedCampaign = CampaignService::archiveCampaign($campaign);

        $this->assertTrue($archivedCampaign->isArchived());
        $this->assertFalse($archivedCampaign->isPublished());

        Event::assertDispatched(CampaignArchived::class);
    }

    public function test_scheduled_campaign_can_be_unscheduled()
    {
        Event::fake();

        $campaign = Campaign::factory()->scheduled()->create([
            'user_id' => $this->businessUser->id,
        ]);

        $this->assertTrue($campaign->isScheduled());
        $this->assertNotNull($campaign->scheduled_date);

        $unscheduledCampaign = CampaignService::unscheduleCampaign($campaign);

        $this->assertTrue($unscheduledCampaign->isDraft());
        $this->assertFalse($unscheduledCampaign->isScheduled());
        $this->assertNull($unscheduledCampaign->scheduled_date);

        Event::assertDispatched(CampaignUnpublished::class);
    }

    public function test_scheduled_campaign_can_be_archived()
    {
        Event::fake();

        $campaign = Campaign::factory()->scheduled()->create([
            'user_id' => $this->businessUser->id,
        ]);

        $this->assertTrue($campaign->isScheduled());
        $this->assertFalse($campaign->isArchived());

        $archivedCampaign = CampaignService::archiveCampaign($campaign);

        $this->assertTrue($archivedCampaign->isArchived());
        $this->assertFalse($archivedCampaign->isScheduled());

        Event::assertDispatched(CampaignArchived::class);
    }

    public function test_all_campaign_status_helper_methods()
    {
        $draftCampaign = Campaign::factory()->create(['status' => CampaignStatus::DRAFT]);
        $publishedCampaign = Campaign::factory()->create(['status' => CampaignStatus::PUBLISHED]);
        $scheduledCampaign = Campaign::factory()->create(['status' => CampaignStatus::SCHEDULED]);
        $archivedCampaign = Campaign::factory()->create(['status' => CampaignStatus::ARCHIVED]);

        // Test draft status helpers
        $this->assertTrue($draftCampaign->isDraft());
        $this->assertFalse($draftCampaign->isPublished());
        $this->assertFalse($draftCampaign->isScheduled());
        $this->assertFalse($draftCampaign->isArchived());

        // Test published status helpers
        $this->assertFalse($publishedCampaign->isDraft());
        $this->assertTrue($publishedCampaign->isPublished());
        $this->assertFalse($publishedCampaign->isScheduled());
        $this->assertFalse($publishedCampaign->isArchived());

        // Test scheduled status helpers
        $this->assertFalse($scheduledCampaign->isDraft());
        $this->assertFalse($scheduledCampaign->isPublished());
        $this->assertTrue($scheduledCampaign->isScheduled());
        $this->assertFalse($scheduledCampaign->isArchived());

        // Test archived status helpers
        $this->assertFalse($archivedCampaign->isDraft());
        $this->assertFalse($archivedCampaign->isPublished());
        $this->assertFalse($archivedCampaign->isScheduled());
        $this->assertTrue($archivedCampaign->isArchived());
    }

    public function test_campaign_scopes_filter_by_status()
    {
        // Create campaigns with different statuses
        Campaign::factory()->create(['status' => CampaignStatus::DRAFT]);
        Campaign::factory()->create(['status' => CampaignStatus::DRAFT]);
        Campaign::factory()->create(['status' => CampaignStatus::PUBLISHED]);
        Campaign::factory()->create(['status' => CampaignStatus::SCHEDULED]);
        Campaign::factory()->create(['status' => CampaignStatus::ARCHIVED]);

        // Test draft scope
        $drafts = Campaign::drafts()->get();
        $this->assertCount(2, $drafts);
        $this->assertTrue($drafts->every(fn($campaign) => $campaign->isDraft()));

        // Test published scope
        $published = Campaign::published()->get();
        $this->assertCount(1, $published);
        $this->assertTrue($published->every(fn($campaign) => $campaign->isPublished()));

        // Test scheduled scope
        $scheduled = Campaign::scheduled()->get();
        $this->assertCount(1, $scheduled);
        $this->assertTrue($scheduled->every(fn($campaign) => $campaign->isScheduled()));

        // Test archived scope
        $archived = Campaign::archived()->get();
        $this->assertCount(1, $archived);
        $this->assertTrue($archived->every(fn($campaign) => $campaign->isArchived()));
    }

    public function test_campaign_service_user_filtering_methods()
    {
        $otherUser = User::factory()->business()->withProfile()->create();

        // Create campaigns for both users with different statuses
        Campaign::factory()->create(['user_id' => $this->businessUser->id, 'status' => CampaignStatus::DRAFT]);
        Campaign::factory()->create(['user_id' => $this->businessUser->id, 'status' => CampaignStatus::DRAFT]);
        Campaign::factory()->published()->create(['user_id' => $this->businessUser->id]);
        Campaign::factory()->scheduled()->create(['user_id' => $this->businessUser->id]);
        Campaign::factory()->archived()->create(['user_id' => $this->businessUser->id]);

        // Create campaigns for other user (should not be included)
        Campaign::factory()->create(['user_id' => $otherUser->id, 'status' => CampaignStatus::DRAFT]);
        Campaign::factory()->published()->create(['user_id' => $otherUser->id]);

        // Test user-specific filtering
        $userDrafts = CampaignService::getUserDrafts($this->businessUser);
        $this->assertCount(2, $userDrafts);
        $this->assertTrue($userDrafts->every(fn($c) => $c->user_id === $this->businessUser->id && $c->isDraft()));

        $userPublished = CampaignService::getUserPublished($this->businessUser);
        $this->assertCount(1, $userPublished);
        $this->assertTrue($userPublished->every(fn($c) => $c->user_id === $this->businessUser->id && $c->isPublished()));

        $userScheduled = CampaignService::getUserScheduled($this->businessUser);
        $this->assertCount(1, $userScheduled);
        $this->assertTrue($userScheduled->every(fn($c) => $c->user_id === $this->businessUser->id && $c->isScheduled()));

        $userArchived = CampaignService::getUserArchived($this->businessUser);
        $this->assertCount(1, $userArchived);
        $this->assertTrue($userArchived->every(fn($c) => $c->user_id === $this->businessUser->id && $c->isArchived()));
    }

    public function test_campaign_status_transitions_preserve_other_data()
    {
        $campaign = Campaign::factory()->withFullDetails()->create([
            'user_id' => $this->businessUser->id,
            'status' => CampaignStatus::DRAFT,
            'campaign_goal' => 'Original goal',
            'target_zip_code' => '49503',
        ]);

        // Ensure all relationships exist
        $this->assertNotNull($campaign->brief);
        $this->assertNotNull($campaign->brand);
        $this->assertNotNull($campaign->requirements);
        $this->assertNotNull($campaign->compensation);

        // Publish the campaign
        $publishedCampaign = CampaignService::publishCampaign($campaign);

        // Verify status changed but other data remains
        $this->assertEquals(CampaignStatus::PUBLISHED, $publishedCampaign->status);
        $this->assertEquals('Original goal', $publishedCampaign->campaign_goal);
        $this->assertEquals('49503', $publishedCampaign->target_zip_code);
        $this->assertNotNull($publishedCampaign->published_at);

        // Verify relationships still exist
        $this->assertNotNull($publishedCampaign->brief);
        $this->assertNotNull($publishedCampaign->brand);
        $this->assertNotNull($publishedCampaign->requirements);
        $this->assertNotNull($publishedCampaign->compensation);
    }

    public function test_campaign_status_enum_casting()
    {
        $campaign = Campaign::factory()->create([
            'status' => 'draft',
        ]);

        // Test that status is properly cast to enum
        $this->assertInstanceOf(CampaignStatus::class, $campaign->status);
        $this->assertEquals(CampaignStatus::DRAFT, $campaign->status);

        // Test updating status with string value
        $campaign->update(['status' => 'published']);
        $campaign->refresh();

        $this->assertEquals(CampaignStatus::PUBLISHED, $campaign->status);
        $this->assertTrue($campaign->isPublished());
    }

    public function test_campaign_published_at_timestamp()
    {
        $campaign = Campaign::factory()->create([
            'user_id' => $this->businessUser->id,
            'status' => CampaignStatus::DRAFT,
            'published_at' => null,
        ]);

        $this->assertNull($campaign->published_at);

        $beforePublish = Carbon::now()->subSecond(); // Give a little buffer
        $publishedCampaign = CampaignService::publishCampaign($campaign);
        $afterPublish = Carbon::now()->addSecond(); // Give a little buffer

        $this->assertNotNull($publishedCampaign->published_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $publishedCampaign->published_at);
        $this->assertTrue($publishedCampaign->published_at->between($beforePublish, $afterPublish));
    }

    public function test_campaign_scheduled_date_handling()
    {
        $campaign = Campaign::factory()->create([
            'user_id' => $this->businessUser->id,
            'status' => CampaignStatus::DRAFT,
            'scheduled_date' => null,
        ]);

        $futureDate = Carbon::now()->addDays(10)->format('Y-m-d');
        $scheduledCampaign = CampaignService::scheduleCampaign($campaign, $futureDate);

        $this->assertNotNull($scheduledCampaign->scheduled_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $scheduledCampaign->scheduled_date);
        $this->assertEquals($futureDate, $scheduledCampaign->scheduled_date->format('Y-m-d'));

        // Test unscheduling clears the date
        $unscheduledCampaign = CampaignService::unscheduleCampaign($scheduledCampaign);
        $this->assertNull($unscheduledCampaign->scheduled_date);
    }

    public function test_campaign_factory_status_states()
    {
        $draftCampaign = Campaign::factory()->create();
        $publishedCampaign = Campaign::factory()->published()->create();
        $scheduledCampaign = Campaign::factory()->scheduled()->create();
        $archivedCampaign = Campaign::factory()->archived()->create();

        $this->assertEquals(CampaignStatus::DRAFT, $draftCampaign->status);
        $this->assertEquals(CampaignStatus::PUBLISHED, $publishedCampaign->status);
        $this->assertEquals(CampaignStatus::SCHEDULED, $scheduledCampaign->status);
        $this->assertEquals(CampaignStatus::ARCHIVED, $archivedCampaign->status);

        // Test published campaign has published_at set
        $this->assertNotNull($publishedCampaign->published_at);

        // Test scheduled campaign has scheduled_date set
        $this->assertNotNull($scheduledCampaign->scheduled_date);
    }

    public function test_multiple_status_transitions()
    {
        Event::fake();

        $campaign = Campaign::factory()->create([
            'user_id' => $this->businessUser->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        // Draft -> Scheduled
        $scheduledCampaign = CampaignService::scheduleCampaign($campaign, Carbon::now()->addDays(5)->format('Y-m-d'));
        $this->assertTrue($scheduledCampaign->isScheduled());

        // Scheduled -> Draft (unschedule)
        $unscheduledCampaign = CampaignService::unscheduleCampaign($scheduledCampaign);
        $this->assertTrue($unscheduledCampaign->isDraft());

        // Draft -> Published
        $publishedCampaign = CampaignService::publishCampaign($unscheduledCampaign);
        $this->assertTrue($publishedCampaign->isPublished());

        // Published -> Archived
        $archivedCampaign = CampaignService::archiveCampaign($publishedCampaign);
        $this->assertTrue($archivedCampaign->isArchived());

        // Verify events were fired for each transition
        Event::assertDispatched(CampaignScheduled::class);
        Event::assertDispatched(CampaignUnpublished::class);
        Event::assertDispatched(CampaignPublished::class);
        Event::assertDispatched(CampaignArchived::class);
    }
}