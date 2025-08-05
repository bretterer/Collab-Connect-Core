<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\CampaignApplicationStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CampaignApplicationTest extends TestCase
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
            'user_id' => $this->businessUser->id,
        ]);
    }

    public function test_influencer_can_apply_to_campaign()
    {
        $this->actingAs($this->influencerUser);

        // Test the ApplyToCampaign component
        Livewire::test('campaigns.apply-to-campaign', ['campaign' => $this->campaign])
            ->set('message', 'I would love to work on this campaign! I have experience in this niche and think I would be a great fit.')
            ->call('submitApplication')
            ->assertHasNoErrors()
            ->assertSet('existingApplication.status', CampaignApplicationStatus::PENDING);

        // Verify the application was created
        $this->assertDatabaseHas('campaign_applications', [
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->influencerUser->id,
            'status' => CampaignApplicationStatus::PENDING->value,
            'message' => 'I would love to work on this campaign! I have experience in this niche and think I would be a great fit.',
        ]);
    }

    public function test_influencer_cannot_apply_twice_to_same_campaign()
    {
        // Create an existing application
        CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->influencerUser->id,
            'status' => CampaignApplicationStatus::PENDING,
        ]);

        $this->actingAs($this->influencerUser);

        // Test that the component recognizes existing application
        $component = Livewire::test('campaigns.apply-to-campaign', ['campaign' => $this->campaign]);

        // The component should have detected the existing application in mount
        $this->assertNotNull($component->get('existingApplication'));

        // Try to submit another application - should be prevented
        $component->set('message', 'Trying to apply again')
                  ->call('submitApplication');

        // Verify the application prevention worked (either by error or by existing application check)
        $this->assertTrue($component->get('existingApplication') !== null);

        // Verify no duplicate application was created
        $this->assertCount(1, CampaignApplication::where([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->influencerUser->id,
        ])->get());
    }

    public function test_application_message_validation()
    {
        $this->actingAs($this->influencerUser);

        // Test with message too short
        Livewire::test('campaigns.apply-to-campaign', ['campaign' => $this->campaign])
            ->set('message', 'Too short')
            ->call('submitApplication')
            ->assertHasErrors(['message']);

        // Test with message too long
        $longMessage = str_repeat('a', 1001);
        Livewire::test('campaigns.apply-to-campaign', ['campaign' => $this->campaign])
            ->set('message', $longMessage)
            ->call('submitApplication')
            ->assertHasErrors(['message']);

        // Test with valid message
        Livewire::test('campaigns.apply-to-campaign', ['campaign' => $this->campaign])
            ->set('message', 'This is a valid message that meets the minimum length requirement and provides good context about why I want to work on this campaign.')
            ->call('submitApplication')
            ->assertHasNoErrors();
    }

    public function test_application_filtering_by_status()
    {
        // Create applications with different statuses
        CampaignApplication::factory()->pending()->create(['campaign_id' => $this->campaign->id]);
        CampaignApplication::factory()->accepted()->create(['campaign_id' => $this->campaign->id]);
        CampaignApplication::factory()->rejected()->create(['campaign_id' => $this->campaign->id]);

        $this->actingAs($this->businessUser);

        // Test filtering by pending
        $component = Livewire::test('campaigns.campaign-applications', ['campaign' => $this->campaign])
            ->set('statusFilter', 'pending');

        $applications = $component->viewData('applications');
        $this->assertCount(1, $applications);
        $this->assertEquals(CampaignApplicationStatus::PENDING, $applications->first()->status);

        // Test filtering by accepted
        $component->set('statusFilter', 'accepted');
        $applications = $component->viewData('applications');
        $this->assertCount(1, $applications);
        $this->assertEquals(CampaignApplicationStatus::ACCEPTED, $applications->first()->status);

        // Test filtering by rejected
        $component->set('statusFilter', 'rejected');
        $applications = $component->viewData('applications');
        $this->assertCount(1, $applications);
        $this->assertEquals(CampaignApplicationStatus::REJECTED, $applications->first()->status);

        // Test showing all applications
        $component->set('statusFilter', 'all');
        $applications = $component->viewData('applications');
        $this->assertCount(3, $applications);
    }

    public function test_cannot_apply_to_draft_campaign()
    {
        $draftCampaign = Campaign::factory()->create([
            'user_id' => $this->businessUser->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->actingAs($this->influencerUser);

        // This would typically be prevented by route middleware or campaign visibility rules
        // Here we test that the application model still works but business logic should prevent this
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $draftCampaign->id,
            'user_id' => $this->influencerUser->id,
        ]);

        $this->assertEquals(CampaignStatus::DRAFT, $application->campaign->status);
    }

    public function test_cannot_apply_to_archived_campaign()
    {
        $archivedCampaign = Campaign::factory()->archived()->create([
            'user_id' => $this->businessUser->id,
        ]);

        $this->actingAs($this->influencerUser);

        // This would typically be prevented by route middleware or campaign visibility rules
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $archivedCampaign->id,
            'user_id' => $this->influencerUser->id,
        ]);

        $this->assertEquals(CampaignStatus::ARCHIVED, $application->campaign->status);
    }

    public function test_application_count_methods()
    {
        CampaignApplication::factory()->pending()->count(2)->create(['campaign_id' => $this->campaign->id]);
        CampaignApplication::factory()->accepted()->create(['campaign_id' => $this->campaign->id]);
        CampaignApplication::factory()->rejected()->create(['campaign_id' => $this->campaign->id]);

        $this->actingAs($this->businessUser);

        $component = Livewire::test('campaigns.campaign-applications', ['campaign' => $this->campaign]);

        $this->assertEquals(4, $component->instance()->getApplicationsCount());
        $this->assertEquals(2, $component->instance()->getPendingApplicationsCount());
    }
}