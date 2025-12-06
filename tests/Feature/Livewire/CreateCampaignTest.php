<?php

namespace Tests\Feature\Livewire;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Models\Campaign;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateCampaignTest extends TestCase
{
    use RefreshDatabase;

    protected User $businessUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a business user with completed profile and active subscription
        $this->businessUser = User::factory()->business()->withProfile()->subscribed()->create();
    }

    #[Test]
    public function business_user_can_access_create_campaign_page(): void
    {
        $this->actingAs($this->businessUser);

        $response = $this->get(route('campaigns.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('campaigns.edit-campaign');
    }

    #[Test]
    public function influencer_cannot_access_create_campaign_page(): void
    {
        $influencerUser = User::factory()->influencer()->withProfile()->create();
        $this->actingAs($influencerUser);

        $response = $this->get(route('campaigns.create'));

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_access_create_campaign_page(): void
    {
        $response = $this->get(route('campaigns.create'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function create_campaign_component_initializes_correctly(): void
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.edit-campaign')
            ->assertSet('currentStep', 1)
            ->assertSet('compensationType', 'monetary')
            ->assertSet('influencerCount', 1)
            ->assertSet('publishAction', 'publish')
            ->assertSee('Campaign Goal');
    }

    #[Test]
    public function wizard_has_four_steps(): void
    {
        $this->actingAs($this->businessUser);

        $component = Livewire::test('campaigns.edit-campaign');

        $this->assertEquals(4, $component->instance()->getTotalSteps());
    }

    #[Test]
    public function step_1_validation_works(): void
    {
        $this->actingAs($this->businessUser);

        // Clear any auto-filled values to test validation
        Livewire::test('campaigns.edit-campaign')
            ->set('targetZipCode', '') // Clear auto-filled zip code to test validation
            ->call('nextStep')
            ->assertHasErrors(['campaignGoal', 'campaignType', 'targetZipCode']);
    }

    #[Test]
    public function step_1_can_be_completed_with_valid_data(): void
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.edit-campaign')
            ->set('campaignGoal', 'Promote our new coffee blend to local coffee enthusiasts')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->assertHasNoErrors();
    }

    #[Test]
    public function step_2_validation_works_for_brand_and_briefing(): void
    {
        $this->actingAs($this->businessUser);

        $component = Livewire::test('campaigns.edit-campaign')
            ->set('campaignGoal', 'Promote our new coffee blend to local coffee enthusiasts')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->call('nextStep');

        // Step 2 now validates briefing fields
        $component->call('nextStep')
            ->assertHasErrors(['campaignObjective', 'keyInsights', 'fanMotivator']);
    }

    #[Test]
    public function step_2_can_be_completed_with_valid_data(): void
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.edit-campaign')
            ->set('campaignGoal', 'Promote our new coffee blend to local coffee enthusiasts')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->set('currentStep', 2)
            // Briefing
            ->set('campaignObjective', 'Increase brand awareness for our new coffee blend and drive foot traffic to our local coffee shops.')
            ->set('keyInsights', 'Coffee enthusiasts value authenticity and local connections. They prefer to support businesses that align with their values.')
            ->set('fanMotivator', 'The opportunity to discover a unique local coffee blend and be part of a community of coffee lovers.')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->assertHasNoErrors();
    }

    #[Test]
    public function step_3_validation_works_for_deliverables_and_settings(): void
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupComponentToStep3();

        // Step 3 now validates deliverables AND settings together
        $component->call('nextStep')
            ->assertHasErrors(['compensationDescription', 'applicationDeadline', 'campaignStartDate', 'campaignCompletionDate']);
    }

    #[Test]
    public function step_3_can_be_completed_with_valid_data(): void
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupComponentToStep3();

        $applicationDeadline = Carbon::now()->addDays(14)->format('Y-m-d');
        $startDate = Carbon::now()->addDays(21)->format('Y-m-d');
        $completionDate = Carbon::now()->addDays(45)->format('Y-m-d');

        $component
            ->set('targetPlatforms', ['instagram', 'tiktok'])
            ->set('deliverables', ['post', 'story'])
            ->set('successMetrics', ['engagement_rate', 'reach'])
            ->set('compensationType', 'free_product')
            ->set('compensationDescription', 'Free coffee products worth $50 plus additional merchandise')
            ->set('influencerCount', 5)
            ->set('applicationDeadline', $applicationDeadline)
            ->set('campaignStartDate', $startDate)
            ->set('campaignCompletionDate', $completionDate)
            ->call('nextStep')
            ->assertSet('currentStep', 4)
            ->assertHasNoErrors();
    }

    #[Test]
    public function can_navigate_to_review_step(): void
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupCompleteComponent();

        $component->assertSet('currentStep', 4)
            ->assertSee('Review & Publish')
            ->assertSee('Promote our new coffee blend');
    }

    #[Test]
    public function can_publish_campaign_immediately(): void
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupCompleteComponent();

        $component
            ->set('publishAction', 'publish')
            ->call('publishCampaign')
            ->assertRedirectContains('/campaigns/');

        // Verify campaign was created and published
        $this->assertDatabaseHas('campaigns', [
            'business_id' => $this->businessUser->currentBusiness->id,
            'campaign_goal' => 'Promote our new coffee blend to local coffee enthusiasts',
            'status' => CampaignStatus::PUBLISHED->value,
        ]);
    }

    #[Test]
    public function can_schedule_campaign_for_later(): void
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupCompleteComponent();
        $scheduledDate = Carbon::now()->addDays(7)->format('Y-m-d');

        $component
            ->set('publishAction', 'schedule')
            ->set('scheduledDate', $scheduledDate)
            ->call('publishCampaign')
            ->assertRedirectContains('/campaigns/');

        // Verify campaign was created and scheduled
        $this->assertDatabaseHas('campaigns', [
            'business_id' => $this->businessUser->currentBusiness->id,
            'campaign_goal' => 'Promote our new coffee blend to local coffee enthusiasts',
            'status' => CampaignStatus::SCHEDULED->value,
            'scheduled_date' => $scheduledDate.' 00:00:00',
        ]);
    }

    #[Test]
    public function can_save_draft(): void
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.edit-campaign')
            ->set('campaignGoal', 'Test campaign goal')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->call('saveDraft');

        // Verify draft was saved
        $this->assertDatabaseHas('campaigns', [
            'business_id' => $this->businessUser->currentBusiness->id,
            'campaign_goal' => 'Test campaign goal',
            'status' => CampaignStatus::DRAFT->value,
        ]);
    }

    #[Test]
    public function can_save_and_exit(): void
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.edit-campaign')
            ->set('campaignGoal', 'Test campaign goal')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->call('saveAndExit')
            ->assertRedirect(route('campaigns.index'));
    }

    #[Test]
    public function auto_save_functionality(): void
    {
        $this->actingAs($this->businessUser);

        $component = Livewire::test('campaigns.edit-campaign')
            ->set('campaignGoal', 'Test campaign for auto-save')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503');

        // Trigger auto-save by calling it directly
        $component->call('autoSave');

        // Verify data was auto-saved
        $this->assertDatabaseHas('campaigns', [
            'business_id' => $this->businessUser->currentBusiness->id,
            'campaign_goal' => 'Test campaign for auto-save',
            'status' => CampaignStatus::DRAFT->value,
        ]);
    }

    #[Test]
    public function can_edit_existing_campaign(): void
    {
        $this->actingAs($this->businessUser);

        // Create an existing campaign
        $campaign = Campaign::factory()->create([
            'business_id' => $this->businessUser->currentBusiness->id,
            'campaign_goal' => 'Original campaign goal',
            'campaign_type' => ['user_generated'],
            'target_zip_code' => '49503',
            'status' => CampaignStatus::DRAFT,
        ]);

        // Test editing the campaign
        Livewire::test('campaigns.edit-campaign', ['campaign' => $campaign->id])
            ->assertSet('campaignId', $campaign->id)
            ->assertSet('campaignGoal', 'Original campaign goal')
            ->assertSet('campaignType', [CampaignType::USER_GENERATED->value])
            ->assertSet('targetZipCode', '49503')
            ->set('campaignGoal', 'Updated campaign goal')
            ->call('autoSave');

        // Verify the campaign was updated
        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'campaign_goal' => 'Updated campaign goal',
        ]);
    }

    #[Test]
    public function step_navigation_works(): void
    {
        $this->actingAs($this->businessUser);

        $component = Livewire::test('campaigns.edit-campaign')
            ->set('campaignGoal', 'Test navigation')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->set('currentStep', 2)
            ->assertSet('currentStep', 2);

        // Test going back
        $component->call('previousStep')
            ->assertSet('currentStep', 1);

        // Test direct navigation to specific step
        $component->call('goToStep', 2)
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function schedule_validation_requires_future_date(): void
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupCompleteComponent();
        $pastDate = Carbon::now()->subDays(1)->format('Y-m-d');

        $component
            ->set('publishAction', 'schedule')
            ->set('scheduledDate', $pastDate)
            ->call('publishCampaign')
            ->assertHasErrors(['scheduledDate']);
    }

    #[Test]
    public function business_defaults_are_applied_for_new_campaigns(): void
    {
        // Set up business with campaign defaults
        $this->businessUser->currentBusiness->update([
            'postal_code' => '12345',
            'campaign_defaults' => [
                'brand_overview' => 'Default brand overview text that is long enough.',
                'brand_story' => 'Default brand story',
                'default_key_insights' => 'Default key insights about our audience.',
            ],
        ]);

        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.edit-campaign')
            ->assertSet('targetZipCode', '12345')
            ->assertSet('brandOverview', 'Default brand overview text that is long enough.')
            ->assertSet('brandStory', 'Default brand story')
            ->assertSet('keyInsights', 'Default key insights about our audience.')
            ->assertSet('hasAppliedDefaults', true)
            ->assertSet('canSkipBrandStep', true);
    }

    #[Test]
    public function can_skip_brand_step_when_defaults_exist(): void
    {
        // Set up business with campaign defaults
        $this->businessUser->currentBusiness->update([
            'campaign_defaults' => [
                'brand_overview' => 'Default brand overview text that is long enough.',
            ],
        ]);

        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.edit-campaign')
            ->set('campaignGoal', 'Test campaign goal for skipping')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->call('skipStep')
            ->assertSet('currentStep', 3);
    }

    // Helper methods for setting up components at different steps

    private function setupComponentToStep3()
    {
        return Livewire::test('campaigns.edit-campaign')
            ->set('campaignGoal', 'Promote our new coffee blend to local coffee enthusiasts')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->set('currentStep', 2)
            // Briefing
            ->set('campaignObjective', 'Increase brand awareness for our new coffee blend and drive foot traffic to our local coffee shops.')
            ->set('keyInsights', 'Coffee enthusiasts value authenticity and local connections.')
            ->set('fanMotivator', 'The opportunity to discover a unique local coffee blend and be part of a community of coffee lovers.')
            ->set('currentStep', 3);
    }

    private function setupCompleteComponent()
    {
        $applicationDeadline = Carbon::now()->addDays(14)->format('Y-m-d');
        $startDate = Carbon::now()->addDays(21)->format('Y-m-d');
        $completionDate = Carbon::now()->addDays(45)->format('Y-m-d');

        return $this->setupComponentToStep3()
            ->set('targetPlatforms', ['instagram', 'tiktok'])
            ->set('deliverables', ['post', 'story'])
            ->set('successMetrics', ['engagement_rate', 'reach'])
            ->set('compensationType', 'free_product')
            ->set('compensationDescription', 'Free coffee products worth $50 plus additional merchandise')
            ->set('influencerCount', 5)
            ->set('applicationDeadline', $applicationDeadline)
            ->set('campaignStartDate', $startDate)
            ->set('campaignCompletionDate', $completionDate)
            ->set('currentStep', 4);
    }
}
