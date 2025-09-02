<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Events\CampaignArchived;
use App\Events\CampaignEdited;
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

class CampaignServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $businessUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->businessUser = User::factory()->business()->withProfile()->create();
    }

    public function test_save_draft_creates_new_campaign()
    {
        $campaignData = [
            'campaign_goal' => 'Promote our new product line',
            'campaign_type' => CampaignType::USER_GENERATED->value,
            'target_zip_code' => '49503',
            'target_area' => 'Grand Rapids, MI',
            'campaign_description' => 'A comprehensive campaign to showcase our new products',
            'influencer_count' => 5,
            'application_deadline' => Carbon::now()->addDays(14)->toDateString(),
            'campaign_completion_date' => Carbon::now()->addDays(30)->toDateString(),
            'current_step' => 3,
        ];

        $campaign = CampaignService::saveDraft($this->businessUser, $campaignData);

        $this->assertInstanceOf(Campaign::class, $campaign);
        $this->assertEquals(CampaignStatus::DRAFT, $campaign->status);
        $this->assertEquals($this->businessUser->currentBusiness->id, $campaign->business_id);
        $this->assertEquals('Promote our new product line', $campaign->campaign_goal);
        $this->assertEquals(CampaignType::USER_GENERATED, $campaign->campaign_type);
        $this->assertEquals('49503', $campaign->target_zip_code);
        $this->assertEquals(5, $campaign->influencer_count);
        $this->assertEquals(3, $campaign->current_step);
    }

    public function test_save_draft_updates_existing_campaign()
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'campaign_goal' => 'Original goal',
            'status' => CampaignStatus::DRAFT,
        ]);

        $campaignData = [
            'campaign_id' => $campaign->id,
            'campaign_goal' => 'Updated goal',
            'campaign_type' => CampaignType::PRODUCT_REVIEWS->value,
            'target_zip_code' => '49504',
            'application_deadline' => Carbon::now()->addDays(14)->toDateString(),
            'campaign_completion_date' => Carbon::now()->addDays(30)->toDateString(),
            'current_step' => 2,
        ];

        $updatedCampaign = CampaignService::saveDraft($this->businessUser, $campaignData);

        $this->assertEquals($campaign->id, $updatedCampaign->id);
        $this->assertEquals('Updated goal', $updatedCampaign->campaign_goal);
        $this->assertEquals(CampaignType::PRODUCT_REVIEWS, $updatedCampaign->campaign_type);
        $this->assertEquals('49504', $updatedCampaign->target_zip_code);
        $this->assertEquals(2, $updatedCampaign->current_step);
    }

    public function test_save_draft_creates_campaign_relationships()
    {
        $campaignData = [
            'campaign_goal' => 'Test campaign',
            'campaign_type' => CampaignType::USER_GENERATED->value,
            'target_zip_code' => '49503',
            'application_deadline' => Carbon::now()->addDays(14)->toDateString(),
            'campaign_completion_date' => Carbon::now()->addDays(30)->toDateString(),
            // Brand data
            'brand_overview' => 'We are a local business focused on quality',
            'brand_story' => 'Founded in 2020 with a passion for excellence',
            'brand_guidelines' => 'Use our brand colors and maintain professional tone',
            // Brief data
            'project_name' => 'Summer Campaign 2024',
            'main_contact' => 'John Doe',
            'campaign_objective' => 'Increase brand awareness in local market',
            'key_insights' => 'Target audience values authenticity and local connections',
            'fan_motivator' => 'Exclusive access to new products',
            'creative_connection' => 'Show product in daily life scenarios',
            'target_audience' => 'Young professionals aged 25-35',
            // Requirements data
            'social_requirements' => ['instagram_posts' => 2, 'stories' => 3],
            'placement_requirements' => ['feed' => true, 'stories' => true],
            'target_platforms' => ['instagram', 'tiktok'],
            'deliverables' => ['instagram_post', 'instagram_story'],
            'success_metrics' => ['impressions', 'engagement_rate'],
            'content_guidelines' => 'Keep content authentic and engaging',
            // Compensation data
            'compensation_type' => CompensationType::MONETARY->value,
            'compensation_amount' => 500,
            'compensation_description' => '$500 payment for completed campaign',
            'compensation_details' => ['payment_terms' => 'Net 30'],
        ];

        $campaign = CampaignService::saveDraft($this->businessUser, $campaignData);

        // Check that all fields were saved directly on the campaign
        // Brand fields
        $this->assertEquals('We are a local business focused on quality', $campaign->brand_overview);
        $this->assertEquals('Founded in 2020 with a passion for excellence', $campaign->brand_story);

        // Brief fields
        $this->assertEquals('Summer Campaign 2024', $campaign->project_name);
        $this->assertEquals('John Doe', $campaign->main_contact);
        $this->assertEquals('Increase brand awareness in local market', $campaign->campaign_objective);

        // Requirements fields
        $this->assertEquals(['instagram_posts' => 2, 'stories' => 3], $campaign->social_requirements);
        $this->assertEquals(['instagram', 'tiktok'], $campaign->target_platforms);

        // Compensation fields
        $this->assertEquals(CompensationType::MONETARY, $campaign->compensation_type);
        $this->assertEquals(500, $campaign->compensation_amount);
        $this->assertEquals('$500 payment for completed campaign', $campaign->compensation_description);
    }

    public function test_publish_campaign_updates_status_and_fires_event()
    {
        Event::fake();

        $campaign = Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        $publishedCampaign = CampaignService::publishCampaign($campaign, $this->businessUser);

        $this->assertEquals(CampaignStatus::PUBLISHED, $publishedCampaign->status);
        $this->assertNotNull($publishedCampaign->published_at);

        Event::assertDispatched(CampaignPublished::class, function ($event) use ($campaign) {
            return $event->campaign->id === $campaign->id &&
                   $event->publisher->id === $this->businessUser->id;
        });
    }

    public function test_schedule_campaign_updates_status_and_fires_event()
    {
        Event::fake();

        $campaign = Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        $scheduledDate = Carbon::now()->addDays(7)->format('Y-m-d');
        $scheduledCampaign = CampaignService::scheduleCampaign($campaign, $scheduledDate, $this->businessUser);

        $this->assertEquals(CampaignStatus::SCHEDULED, $scheduledCampaign->status);
        $this->assertEquals($scheduledDate, $scheduledCampaign->scheduled_date->format('Y-m-d'));

        Event::assertDispatched(CampaignScheduled::class, function ($event) use ($campaign, $scheduledDate) {
            return $event->campaign->id === $campaign->id &&
                   $event->scheduler->id === $this->businessUser->id &&
                   $event->scheduledDate === $scheduledDate;
        });
    }

    public function test_archive_campaign_updates_status_and_fires_event()
    {
        Event::fake();

        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);

        $archivedCampaign = CampaignService::archiveCampaign($campaign, $this->businessUser);

        $this->assertEquals(CampaignStatus::ARCHIVED, $archivedCampaign->status);

        Event::assertDispatched(CampaignArchived::class, function ($event) use ($campaign) {
            return $event->campaign->id === $campaign->id &&
                   $event->archiver->id === $this->businessUser->id;
        });
    }

    public function test_unschedule_campaign_converts_to_draft_and_fires_event()
    {
        Event::fake();

        $campaign = Campaign::factory()->scheduled()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);

        $unscheduledCampaign = CampaignService::unscheduleCampaign($campaign, $this->businessUser);

        $this->assertEquals(CampaignStatus::DRAFT, $unscheduledCampaign->status);
        $this->assertNull($unscheduledCampaign->scheduled_date);

        Event::assertDispatched(CampaignUnpublished::class, function ($event) use ($campaign) {
            return $event->campaign->id === $campaign->id &&
                   $event->unpublisher->id === $this->businessUser->id;
        });
    }

    public function test_update_campaign_tracks_changes_and_fires_event()
    {
        Event::fake();

        $campaign = Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'campaign_goal' => 'Original goal',
            'target_zip_code' => '49503',
        ]);

        $updateData = [
            'campaign_goal' => 'Updated goal',
            'target_zip_code' => '49504',
            'campaign_description' => 'New description',
        ];

        $updatedCampaign = CampaignService::updateCampaign($campaign->id, $updateData, $this->businessUser);

        $this->assertEquals('Updated goal', $updatedCampaign->campaign_goal);
        $this->assertEquals('49504', $updatedCampaign->target_zip_code);
        $this->assertEquals('New description', $updatedCampaign->campaign_description);

        Event::assertDispatched(CampaignEdited::class, function ($event) use ($campaign) {
            return $event->campaign->id === $campaign->id &&
                   $event->editor->id === $this->businessUser->id &&
                   is_array($event->changes) &&
                   count($event->changes) > 0;
        });
    }

    public function test_get_user_drafts_returns_correct_campaigns()
    {
        // Create campaigns with different statuses
        Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'status' => CampaignStatus::DRAFT,
        ]);
        Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
        ]);
        Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        $drafts = CampaignService::getUserDrafts($this->businessUser);

        $this->assertCount(2, $drafts);
        $this->assertTrue($drafts->every(fn ($campaign) => $campaign->status === CampaignStatus::DRAFT));
    }

    public function test_get_user_published_returns_correct_campaigns()
    {
        Campaign::factory()->published()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);
        Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'status' => CampaignStatus::DRAFT,
        ]);
        Campaign::factory()->published()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);

        $published = CampaignService::getUserPublished($this->businessUser);

        $this->assertCount(2, $published);
        $this->assertTrue($published->every(fn ($campaign) => $campaign->status === CampaignStatus::PUBLISHED));
    }

    public function test_get_user_scheduled_returns_correct_campaigns()
    {
        Campaign::factory()->scheduled()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);
        Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'status' => CampaignStatus::DRAFT,
        ]);
        Campaign::factory()->scheduled()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);

        $scheduled = CampaignService::getUserScheduled($this->businessUser);

        $this->assertCount(2, $scheduled);
        $this->assertTrue($scheduled->every(fn ($campaign) => $campaign->status === CampaignStatus::SCHEDULED));
    }

    public function test_get_user_archived_returns_correct_campaigns()
    {
        Campaign::factory()->archived()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);
        Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'status' => CampaignStatus::DRAFT,
        ]);
        Campaign::factory()->archived()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);

        $archived = CampaignService::getUserArchived($this->businessUser);

        $this->assertCount(2, $archived);
        $this->assertTrue($archived->every(fn ($campaign) => $campaign->status === CampaignStatus::ARCHIVED));
    }

    public function test_campaign_status_helper_methods()
    {
        $draftCampaign = Campaign::factory()->create(['status' => CampaignStatus::DRAFT]);
        $publishedCampaign = Campaign::factory()->create(['status' => CampaignStatus::PUBLISHED]);
        $scheduledCampaign = Campaign::factory()->create(['status' => CampaignStatus::SCHEDULED]);
        $archivedCampaign = Campaign::factory()->create(['status' => CampaignStatus::ARCHIVED]);

        // Test draft campaign
        $this->assertTrue($draftCampaign->isDraft());
        $this->assertFalse($draftCampaign->isPublished());
        $this->assertFalse($draftCampaign->isScheduled());
        $this->assertFalse($draftCampaign->isArchived());

        // Test published campaign
        $this->assertFalse($publishedCampaign->isDraft());
        $this->assertTrue($publishedCampaign->isPublished());
        $this->assertFalse($publishedCampaign->isScheduled());
        $this->assertFalse($publishedCampaign->isArchived());

        // Test scheduled campaign
        $this->assertFalse($scheduledCampaign->isDraft());
        $this->assertFalse($scheduledCampaign->isPublished());
        $this->assertTrue($scheduledCampaign->isScheduled());
        $this->assertFalse($scheduledCampaign->isArchived());

        // Test archived campaign
        $this->assertFalse($archivedCampaign->isDraft());
        $this->assertFalse($archivedCampaign->isPublished());
        $this->assertFalse($archivedCampaign->isScheduled());
        $this->assertTrue($archivedCampaign->isArchived());
    }

    public function test_campaign_compensation_display_methods()
    {
        $campaign = Campaign::factory()->withFullDetails()->create();

        $this->assertIsString($campaign->getCompensationDisplayAttribute());
        $this->assertIsBool($campaign->isMonetaryCompensation());
    }

    public function test_campaign_scopes_work_correctly()
    {
        Campaign::factory()->create(['status' => CampaignStatus::DRAFT]);
        Campaign::factory()->create(['status' => CampaignStatus::PUBLISHED]);
        Campaign::factory()->create(['status' => CampaignStatus::SCHEDULED]);
        Campaign::factory()->create(['status' => CampaignStatus::ARCHIVED]);

        $this->assertCount(1, Campaign::drafts()->get());
        $this->assertCount(1, Campaign::published()->get());
        $this->assertCount(1, Campaign::scheduled()->get());
        $this->assertCount(1, Campaign::archived()->get());
    }
}
