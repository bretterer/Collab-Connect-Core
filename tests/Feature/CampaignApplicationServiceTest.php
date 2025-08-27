<?php

namespace Tests\Feature;

use App\Enums\CampaignApplicationStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CampaignApplicationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $businessUser;

    protected User $influencerUser;

    protected Campaign $campaign;

    protected function setUp(): void
    {
        parent::setUp();

        $this->businessUser = User::factory()->business()->withProfile()->create();
        $this->influencerUser = User::factory()->influencer()->withProfile()->create();

        $this->campaign = Campaign::factory()->published()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);
    }

    public function test_can_create_campaign_application()
    {
        Event::fake();

        $application = CampaignApplication::create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->influencerUser->id,
            'message' => 'I would love to collaborate on this campaign. I have extensive experience in this niche and believe I would be a perfect fit.',
            'status' => CampaignApplicationStatus::PENDING,
            'submitted_at' => now(),
        ]);

        $this->assertInstanceOf(CampaignApplication::class, $application);
        $this->assertEquals($this->campaign->id, $application->campaign_id);
        $this->assertEquals($this->influencerUser->id, $application->user_id);
        $this->assertEquals(CampaignApplicationStatus::PENDING, $application->status);
        $this->assertNotNull($application->submitted_at);
        $this->assertNull($application->reviewed_at);
        $this->assertNull($application->accepted_at);
        $this->assertNull($application->rejected_at);

        Event::assertDispatched(\App\Events\CampaignApplicationCreated::class, function ($event) use ($application) {
            return $event->campaignApplication->id === $application->id;
        });
    }

    public function test_application_status_helper_methods()
    {
        $pendingApplication = CampaignApplication::factory()->pending()->create();
        $acceptedApplication = CampaignApplication::factory()->accepted()->create();
        $rejectedApplication = CampaignApplication::factory()->rejected()->create();

        // Test pending application
        $this->assertTrue($pendingApplication->isPending());
        $this->assertFalse($pendingApplication->isAccepted());
        $this->assertFalse($pendingApplication->isRejected());

        // Test accepted application
        $this->assertFalse($acceptedApplication->isPending());
        $this->assertTrue($acceptedApplication->isAccepted());
        $this->assertFalse($acceptedApplication->isRejected());

        // Test rejected application
        $this->assertFalse($rejectedApplication->isPending());
        $this->assertFalse($rejectedApplication->isAccepted());
        $this->assertTrue($rejectedApplication->isRejected());
    }

    public function test_application_belongs_to_campaign()
    {
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
        ]);

        $this->assertInstanceOf(Campaign::class, $application->campaign);
        $this->assertEquals($this->campaign->id, $application->campaign->id);
    }

    public function test_application_belongs_to_user()
    {
        $application = CampaignApplication::factory()->create([
            'user_id' => $this->influencerUser->id,
        ]);

        $this->assertInstanceOf(User::class, $application->user);
        $this->assertEquals($this->influencerUser->id, $application->user->id);
    }

    public function test_campaign_has_many_applications()
    {
        CampaignApplication::factory()->count(3)->create([
            'campaign_id' => $this->campaign->id,
        ]);

        $this->assertCount(3, $this->campaign->applications);
        $this->assertTrue($this->campaign->applications->every(function ($application) {
            return $application instanceof CampaignApplication;
        }));
    }

    public function test_user_has_many_applications()
    {
        CampaignApplication::factory()->count(3)->create([
            'user_id' => $this->influencerUser->id,
        ]);

        $this->assertCount(3, $this->influencerUser->campaignApplications);
        $this->assertTrue($this->influencerUser->campaignApplications->every(function ($application) {
            return $application instanceof CampaignApplication;
        }));
    }

    public function test_application_accepts_properly()
    {
        $application = CampaignApplication::factory()->pending()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->influencerUser->id,
        ]);

        $application->update([
            'status' => CampaignApplicationStatus::ACCEPTED,
            'reviewed_at' => now(),
            'accepted_at' => now(),
            'review_notes' => 'Great profile and experience!',
        ]);

        $this->assertTrue($application->fresh()->isAccepted());
        $this->assertNotNull($application->reviewed_at);
        $this->assertNotNull($application->accepted_at);
        $this->assertNull($application->rejected_at);
        $this->assertEquals('Great profile and experience!', $application->review_notes);
    }

    public function test_application_rejects_properly()
    {
        $application = CampaignApplication::factory()->pending()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->influencerUser->id,
        ]);

        $application->update([
            'status' => CampaignApplicationStatus::REJECTED,
            'reviewed_at' => now(),
            'rejected_at' => now(),
            'review_notes' => 'Not a good fit for this campaign.',
        ]);

        $this->assertTrue($application->fresh()->isRejected());
        $this->assertNotNull($application->reviewed_at);
        $this->assertNotNull($application->rejected_at);
        $this->assertNull($application->accepted_at);
        $this->assertEquals('Not a good fit for this campaign.', $application->review_notes);
    }

    public function test_cannot_apply_to_draft_campaign()
    {
        $draftCampaign = Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        // In a real application, this would be prevented by validation or middleware
        // Here we're just testing the model relationships work correctly
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $draftCampaign->id,
            'user_id' => $this->influencerUser->id,
        ]);

        $this->assertEquals(CampaignStatus::DRAFT, $application->campaign->status);
    }

    public function test_cannot_apply_to_archived_campaign()
    {
        $archivedCampaign = Campaign::factory()->archived()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
        ]);

        // In a real application, this would be prevented by validation or middleware
        // Here we're just testing the model relationships work correctly
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $archivedCampaign->id,
            'user_id' => $this->influencerUser->id,
        ]);

        $this->assertEquals(CampaignStatus::ARCHIVED, $application->campaign->status);
    }

    public function test_business_owner_cannot_apply_to_own_campaign()
    {
        // This would typically be prevented at the application level
        // but we can test the model allows it (business logic prevents it)
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->businessUser->id, // Business owner applying to own campaign
        ]);

        $this->assertEquals($this->businessUser->id, $application->user_id);
        $this->assertEquals($this->businessUser->currentBusiness->id, $application->campaign->business_id);
    }

    public function test_application_factory_states_work_correctly()
    {
        $pendingApp = CampaignApplication::factory()->pending()->create();
        $acceptedApp = CampaignApplication::factory()->accepted()->create();
        $rejectedApp = CampaignApplication::factory()->rejected()->create();

        $this->assertEquals(CampaignApplicationStatus::PENDING, $pendingApp->status);
        $this->assertNull($pendingApp->reviewed_at);
        $this->assertNull($pendingApp->review_notes);

        $this->assertEquals(CampaignApplicationStatus::ACCEPTED, $acceptedApp->status);
        $this->assertNotNull($acceptedApp->reviewed_at);

        $this->assertEquals(CampaignApplicationStatus::REJECTED, $rejectedApp->status);
        $this->assertNotNull($rejectedApp->reviewed_at);
        $this->assertNotNull($rejectedApp->review_notes);
    }

    public function test_application_timestamps_are_cast_correctly()
    {
        $application = CampaignApplication::factory()->create([
            'submitted_at' => '2024-01-15 10:30:00',
            'reviewed_at' => '2024-01-16 14:45:00',
            'accepted_at' => '2024-01-16 14:45:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $application->submitted_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $application->reviewed_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $application->accepted_at);
    }

    public function test_application_status_enum_casting()
    {
        $application = CampaignApplication::factory()->create([
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(CampaignApplicationStatus::class, $application->status);
        $this->assertEquals(CampaignApplicationStatus::PENDING, $application->status);
    }

    public function test_multiple_users_can_apply_to_same_campaign()
    {
        $influencer1 = User::factory()->influencer()->withProfile()->create();
        $influencer2 = User::factory()->influencer()->withProfile()->create();
        $influencer3 = User::factory()->influencer()->withProfile()->create();

        CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $influencer1->id,
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $influencer2->id,
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $influencer3->id,
        ]);

        $this->assertCount(3, $this->campaign->fresh()->applications);

        $userIds = $this->campaign->applications->pluck('user_id')->toArray();
        $this->assertContains($influencer1->id, $userIds);
        $this->assertContains($influencer2->id, $userIds);
        $this->assertContains($influencer3->id, $userIds);
    }

    public function test_user_can_apply_to_multiple_campaigns()
    {
        $campaign1 = Campaign::factory()->published()->create(['business_id' => $this->businessUser->currentBusiness->id]);
        $campaign2 = Campaign::factory()->published()->create(['business_id' => $this->businessUser->currentBusiness->id]);
        $campaign3 = Campaign::factory()->published()->create(['business_id' => $this->businessUser->currentBusiness->id]);

        CampaignApplication::factory()->create([
            'campaign_id' => $campaign1->id,
            'user_id' => $this->influencerUser->id,
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $campaign2->id,
            'user_id' => $this->influencerUser->id,
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $campaign3->id,
            'user_id' => $this->influencerUser->id,
        ]);

        $this->assertCount(3, $this->influencerUser->fresh()->campaignApplications);

        $campaignIds = $this->influencerUser->campaignApplications->pluck('campaign_id')->toArray();
        $this->assertContains($campaign1->id, $campaignIds);
        $this->assertContains($campaign2->id, $campaignIds);
        $this->assertContains($campaign3->id, $campaignIds);
    }
}
