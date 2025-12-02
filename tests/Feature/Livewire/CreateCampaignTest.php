<?php

namespace Tests\Feature\Livewire;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Models\Campaign;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_business_user_can_access_create_campaign_page()
    {
        $this->actingAs($this->businessUser);

        $response = $this->get(route('campaigns.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('campaigns.create-campaign');
    }

    public function test_influencer_cannot_access_create_campaign_page()
    {
        $influencerUser = User::factory()->influencer()->withProfile()->create();
        $this->actingAs($influencerUser);

        $response = $this->get(route('campaigns.create'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_create_campaign_page()
    {
        $response = $this->get(route('campaigns.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_create_campaign_component_initializes_correctly()
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.create-campaign')
            ->assertSet('currentStep', 1)
            ->assertSet('compensationType', 'monetary')
            ->assertSet('influencerCount', 1)
            ->assertSet('publishAction', 'publish')
            ->assertSee('Campaign Goal');
    }

    public function test_step_1_validation_works()
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.create-campaign')
            ->call('nextStep')
            ->assertHasErrors(['campaignGoal', 'campaignType', 'targetZipCode']);
    }

    public function test_step_1_can_be_completed_with_valid_data()
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.create-campaign')
            ->set('campaignGoal', 'Promote our new coffee blend to local coffee enthusiasts')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->set('targetArea', 'Grand Rapids, MI')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->assertHasNoErrors();
    }

    public function test_step_2_validation_works()
    {
        $this->actingAs($this->businessUser);

        $component = Livewire::test('campaigns.create-campaign')
            ->set('campaignGoal', 'Promote our new coffee blend to local coffee enthusiasts')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->call('nextStep');

        $component->call('nextStep')
            ->assertHasErrors(['brandOverview']);
    }

    public function test_step_2_can_be_completed_with_valid_data()
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.create-campaign')
            ->set('campaignGoal', 'Promote our new coffee blend to local coffee enthusiasts')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->set('currentStep', 2) // Skip autoSave by directly setting step
            ->set('brandOverview', 'We are a local coffee roaster with over 15 years of experience in the Grand Rapids area. Our mission is to bring exceptional coffee experiences to our community while supporting sustainable farming practices.')
            ->set('brandStory', 'Founded in 2008 by coffee enthusiasts who wanted to share their passion for quality coffee.')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->assertHasNoErrors();
    }

    public function test_step_3_validation_works()
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupComponentToStep3();

        $component->call('nextStep')
            ->assertHasErrors(['campaignObjective', 'keyInsights', 'fanMotivator', 'creativeConnection']);
    }

    public function test_step_3_can_be_completed_with_valid_data()
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupComponentToStep3();

        $component
            ->set('campaignObjective', 'Increase brand awareness for our new coffee blend and drive foot traffic to our local coffee shops.')
            ->set('keyInsights', 'Coffee enthusiasts value authenticity and local connections. They prefer to support businesses that align with their values.')
            ->set('fanMotivator', 'The opportunity to discover a unique local coffee blend and be part of a community of coffee lovers.')
            ->set('creativeConnection', 'Show authentic moments of enjoying our coffee in your daily routine - whether it\'s your morning ritual or afternoon pick-me-up.')
            ->call('nextStep')
            ->assertSet('currentStep', 4)
            ->assertHasNoErrors();
    }

    public function test_step_4_deliverables_and_metrics()
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupComponentToStep4();

        $component
            ->set('targetPlatforms', ['instagram', 'tiktok'])
            ->set('deliverables', ['post', 'story'])
            ->set('successMetrics', ['engagement_rate', 'reach'])
            ->set('timingDetails', 'Post within 2 weeks of receiving product')
            ->call('nextStep')
            ->assertSet('currentStep', 5)
            ->assertHasNoErrors();
    }

    public function test_step_5_validation_works()
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupComponentToStep5();

        $component->call('nextStep')
            ->assertHasErrors(['compensationDescription', 'applicationDeadline', 'campaignStartDate', 'campaignCompletionDate']);
    }

    public function test_step_5_can_be_completed_with_valid_data()
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupComponentToStep5();

        $applicationDeadline = Carbon::now()->addDays(14)->format('Y-m-d');
        $startDate = Carbon::now()->addDays(21)->format('Y-m-d');
        $completionDate = Carbon::now()->addDays(45)->format('Y-m-d');

        $component
            ->set('compensationType', 'barter')
            ->set('compensationDescription', 'Free coffee products worth $50 plus additional merchandise')
            ->set('influencerCount', 5)
            ->set('applicationDeadline', $applicationDeadline)
            ->set('campaignStartDate', $startDate)
            ->set('campaignCompletionDate', $completionDate)
            ->set('additionalRequirements', 'Must be located within 25 miles of Grand Rapids')
            ->call('nextStep')
            ->assertSet('currentStep', 6)
            ->assertHasNoErrors();
    }

    public function test_can_navigate_to_review_step()
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupCompleteComponent();

        $component->assertSet('currentStep', 6)
            ->assertSee('Review & Publish')
            ->assertSee('Promote our new coffee blend');
    }

    public function test_can_publish_campaign_immediately()
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupCompleteComponent();

        $component
            ->set('publishAction', 'publish')
            ->call('publishCampaign')
            ->assertRedirect(route('dashboard'));

        // Verify campaign was created and published
        $this->assertDatabaseHas('campaigns', [
            'business_id' => $this->businessUser->currentBusiness->id,
            'campaign_goal' => 'Promote our new coffee blend to local coffee enthusiasts',
            'status' => CampaignStatus::PUBLISHED->value,
        ]);
    }

    public function test_can_schedule_campaign_for_later()
    {
        $this->actingAs($this->businessUser);

        $component = $this->setupCompleteComponent();
        $scheduledDate = Carbon::now()->addDays(7)->format('Y-m-d');

        $component
            ->set('publishAction', 'schedule')
            ->set('scheduledDate', $scheduledDate)
            ->call('publishCampaign')
            ->assertRedirect(route('dashboard'));

        // Verify campaign was created and scheduled
        $this->assertDatabaseHas('campaigns', [
            'business_id' => $this->businessUser->currentBusiness->id,
            'campaign_goal' => 'Promote our new coffee blend to local coffee enthusiasts',
            'status' => CampaignStatus::SCHEDULED->value,
            'scheduled_date' => $scheduledDate.' 00:00:00',
        ]);
    }

    public function test_can_save_draft()
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.create-campaign')
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

    public function test_can_save_and_exit()
    {
        $this->actingAs($this->businessUser);

        Livewire::test('campaigns.create-campaign')
            ->set('campaignGoal', 'Test campaign goal')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->call('saveAndExit')
            ->assertRedirect(route('campaigns.index'));
    }

    public function test_auto_save_functionality()
    {
        $this->actingAs($this->businessUser);

        $component = Livewire::test('campaigns.create-campaign')
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

    public function test_can_edit_existing_campaign()
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
        Livewire::test('campaigns.create-campaign', ['campaignId' => $campaign->id])
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

    public function test_step_navigation_works()
    {
        $this->actingAs($this->businessUser);

        $component = Livewire::test('campaigns.create-campaign')
            ->set('campaignGoal', 'Test navigation')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->set('currentStep', 2) // Skip autoSave by directly setting step
            ->assertSet('currentStep', 2);

        // Test going back
        $component->call('previousStep')
            ->assertSet('currentStep', 1);

        // Test direct navigation to specific step
        $component->call('goToStep', 2)
            ->assertSet('currentStep', 2);
    }

    public function test_schedule_validation_requires_future_date()
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

    // Helper methods for setting up components at different steps

    private function setupComponentToStep3()
    {
        return Livewire::test('campaigns.create-campaign')
            ->set('campaignGoal', 'Promote our new coffee blend to local coffee enthusiasts')
            ->set('campaignType', ['user_generated'])
            ->set('targetZipCode', '49503')
            ->set('currentStep', 2) // Skip autoSave by directly setting step
            ->set('brandOverview', 'We are a local coffee roaster with over 15 years of experience in the Grand Rapids area.')
            ->set('currentStep', 3); // Skip autoSave by directly setting step
    }

    private function setupComponentToStep4()
    {
        return $this->setupComponentToStep3()
            ->set('campaignObjective', 'Increase brand awareness for our new coffee blend and drive foot traffic to our local coffee shops.')
            ->set('keyInsights', 'Coffee enthusiasts value authenticity and local connections.')
            ->set('fanMotivator', 'The opportunity to discover a unique local coffee blend and be part of a community of coffee lovers.')
            ->set('creativeConnection', 'Show authentic moments of enjoying our coffee in your daily routine.')
            ->set('currentStep', 4); // Skip autoSave by directly setting step
    }

    private function setupComponentToStep5()
    {
        return $this->setupComponentToStep4()
            ->set('targetPlatforms', ['instagram', 'tiktok'])
            ->set('deliverables', ['post', 'story'])
            ->set('successMetrics', ['engagement_rate', 'reach'])
            ->set('currentStep', 5); // Skip autoSave by directly setting step
    }

    private function setupCompleteComponent()
    {
        $applicationDeadline = Carbon::now()->addDays(14)->format('Y-m-d');
        $startDate = Carbon::now()->addDays(21)->format('Y-m-d');
        $completionDate = Carbon::now()->addDays(45)->format('Y-m-d');

        return $this->setupComponentToStep5()
            ->set('compensationType', 'barter')
            ->set('compensationDescription', 'Free coffee products worth $50 plus additional merchandise')
            ->set('influencerCount', 5)
            ->set('applicationDeadline', $applicationDeadline)
            ->set('campaignStartDate', $startDate)
            ->set('campaignCompletionDate', $completionDate)
            ->set('currentStep', 6); // Skip autoSave by directly setting step
    }
}
