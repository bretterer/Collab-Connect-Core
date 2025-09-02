<?php

namespace Tests\Feature\Livewire;

use App\Enums\AccountType;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_influencer_can_view_campaign_they_dont_own()
    {
        // Create business user and campaign
        $businessUser = User::factory()->withProfile()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $campaign = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_goal' => 'Test Campaign Goal',
            'project_name' => 'Test Campaign Goal',
        ]);

        // Create influencer user with completed onboarding
        $influencerUser = User::factory()->withProfile()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $this->actingAs($influencerUser);

        // Test the component loads
        $response = $this->get("/campaigns/{$campaign->id}");

        $response->assertStatus(200);
        $response->assertSee('Test Campaign Goal');
        $response->assertSee('Apply Now');
    }

    public function test_campaign_not_found_for_invalid_id()
    {
        // Create influencer user with completed onboarding
        $influencerUser = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($influencerUser);

        $response = $this->get('/campaigns/999999');

        $response->assertStatus(404);
    }

    public function test_campaign_not_accessible_if_not_published()
    {
        // Create business user and unpublished campaign
        $businessUser = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        // Create influencer user with completed onboarding
        $influencerUser = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($influencerUser);

        $response = $this->get("/campaigns/{$campaign->id}");

        $response->assertStatus(404);
    }
}
