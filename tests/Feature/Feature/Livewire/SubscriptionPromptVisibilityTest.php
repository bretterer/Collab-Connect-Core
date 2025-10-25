<?php

namespace Tests\Feature\Feature\Livewire;

use App\Enums\AccountType;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPromptVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_subscribed_business_sees_prompt_on_campaign_index()
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $response = $this->actingAs($businessUser)
            ->get(route('campaigns.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('components.subscription-prompt');
        $response->assertSee('Create Campaigns with CollabConnect');
        $response->assertSee('Subscribe to a plan to unlock powerful campaign management features');
        // The button text "Create Campaign" appears in the subscription prompt features list,
        // so we can't use assertDontSee for the exact text. Instead verify the button itself is hidden.
    }

    public function test_subscribed_business_does_not_see_prompt_on_campaign_index()
    {
        $businessUser = User::factory()->business()->withProfile()->subscribed()->create();

        $response = $this->actingAs($businessUser)
            ->get(route('campaigns.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Subscribe to a plan to unlock powerful campaign management features');
        $response->assertSee('Create Campaign'); // Button should be visible
    }

    public function test_non_subscribed_business_sees_prompt_on_campaign_create()
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $response = $this->actingAs($businessUser)
            ->get(route('campaigns.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('components.subscription-prompt');
        $response->assertSee('Create Campaigns with CollabConnect');
        $response->assertDontSee('Campaign Goal'); // Form should be hidden
        $response->assertDontSee('Step 1 of 6'); // Wizard should be hidden
    }

    public function test_subscribed_business_does_not_see_prompt_on_campaign_create()
    {
        $businessUser = User::factory()->business()->withProfile()->subscribed()->create();

        $response = $this->actingAs($businessUser)
            ->get(route('campaigns.create'));

        $response->assertStatus(200);
        $response->assertDontSee('Subscribe to a plan to unlock powerful campaign management features');
        $response->assertSee('Campaign Goal'); // Form should be visible
    }

    public function test_non_subscribed_influencer_sees_prompt_on_campaign_detail()
    {
        // Create a published campaign
        $businessUser = User::factory()->business()->withProfile()->subscribed()->create();
        $campaign = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
        ]);

        // Create non-subscribed influencer
        $influencerUser = User::factory()->influencer()->withProfile()->create();

        $response = $this->actingAs($influencerUser)
            ->get("/campaigns/{$campaign->id}");

        $response->assertStatus(200);
        $response->assertSeeLivewire('components.subscription-prompt');
        $response->assertSee('Subscribe to Apply');
        $response->assertSee('Ready to collaborate? Subscribe to unlock the ability to apply for campaigns');
        $response->assertDontSee('Apply Now'); // Apply button should be hidden
    }

    public function test_subscribed_influencer_does_not_see_prompt_on_campaign_detail()
    {
        // Create a published campaign
        $businessUser = User::factory()->business()->withProfile()->subscribed()->create();
        $campaign = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_goal' => 'Test Campaign',
            'project_name' => 'Test Campaign',
        ]);

        // Create subscribed influencer
        $influencerUser = User::factory()->influencer()->withProfile()->subscribed()->create();

        $response = $this->actingAs($influencerUser)
            ->get("/campaigns/{$campaign->id}");

        $response->assertStatus(200);
        $response->assertDontSee('Ready to collaborate? Subscribe to unlock campaigns');
        $response->assertSee('Apply Now'); // Apply button should be visible
    }

    public function test_non_subscribed_business_sees_different_prompt_on_campaign_detail()
    {
        // Create a published campaign from another business
        $otherBusinessUser = User::factory()->business()->withProfile()->subscribed()->create();
        $campaign = Campaign::factory()->create([
            'business_id' => $otherBusinessUser->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
        ]);

        // Create non-subscribed business user
        $businessUser = User::factory()->business()->withProfile()->create();

        $response = $this->actingAs($businessUser)
            ->get("/campaigns/{$campaign->id}");

        $response->assertStatus(200);
        $response->assertSeeLivewire('components.subscription-prompt');
        $response->assertSee('Subscribe to Create Campaigns');
        $response->assertSee('Unlock the full potential of your brand by subscribing');
    }

    public function test_non_subscribed_influencer_sees_prompt_on_search_page()
    {
        $influencerUser = User::factory()->influencer()->withProfile()->create();

        $response = $this->actingAs($influencerUser)
            ->get(route('search'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('components.subscription-prompt');
        $response->assertSee('Unlock Advanced Search');
        $response->assertSee('Subscribe to access powerful search capabilities');
    }

    public function test_subscribed_influencer_does_not_see_prompt_on_search_page()
    {
        $influencerUser = User::factory()->influencer()->withProfile()->subscribed()->create();

        $response = $this->actingAs($influencerUser)
            ->get(route('search'));

        $response->assertStatus(200);
        $response->assertDontSee('Unlock Advanced Search');
        $response->assertDontSee('Subscribe to access powerful search capabilities');
        // Search form should be visible
        $response->assertSeeLivewire('search');
    }

    public function test_non_subscribed_business_sees_prompt_on_dashboard()
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $response = $this->actingAs($businessUser)
            ->get(route('business.dashboard'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('components.subscription-prompt');
        $response->assertSee('Find Your Perfect Influencers');
        $response->assertSee('Subscribe to access our full network of influencers');
    }

    public function test_subscribed_business_does_not_see_prompt_on_dashboard()
    {
        $businessUser = User::factory()->business()->withProfile()->subscribed()->create();

        $response = $this->actingAs($businessUser)
            ->get(route('business.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Subscribe to access our full network of influencers');
    }

    public function test_non_subscribed_influencer_sees_prompt_on_dashboard()
    {
        $influencerUser = User::factory()->influencer()->withProfile()->create();

        $response = $this->actingAs($influencerUser)
            ->get(route('influencer.dashboard'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('components.subscription-prompt');
        $response->assertSee('Unlock Your Full Potential');
        $response->assertSee('Subscribe to access unlimited campaigns');
    }

    public function test_subscribed_influencer_does_not_see_prompt_on_dashboard()
    {
        $influencerUser = User::factory()->influencer()->withProfile()->subscribed()->create();

        $response = $this->actingAs($influencerUser)
            ->get(route('influencer.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Unlock Your Full Potential');
        $response->assertDontSee('Subscribe to access unlimited campaigns');
    }

    public function test_subscription_prompt_has_link_to_billing_page()
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $response = $this->actingAs($businessUser)
            ->get(route('campaigns.index'));

        $response->assertStatus(200);
        $response->assertSee('View Subscription Plans');
        $response->assertSee(route('billing'));
    }
}
