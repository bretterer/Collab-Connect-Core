<?php

namespace Tests\Feature\Livewire;

use App\Enums\AccountType;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\CompensationType;
use App\Enums\Niche;
use App\Models\BusinessProfile;
use App\Models\Campaign;
use App\Models\InfluencerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewCampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_influencer_can_view_campaign_details()
    {
        // Create business user and campaign
        $businessUser = User::factory()->business()->withProfile()->subscribed()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_goal' => 'Promote our new fashion line',
            'project_name' => 'Promote our new fashion line',
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'compensation_type' => CompensationType::MONETARY,
            'compensation_amount' => 500,
            'influencer_count' => 5,
            'target_zip_code' => '12345',
        ]);

        // Create influencer user
        /** @var User $influencerUser */
        $influencerUser = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($influencerUser);

        // Test the component loads
        $response = $this->get("/campaigns/{$campaign->id}");

        $response->assertStatus(200);
        $response->assertSee('Promote our new fashion line');
        $response->assertSee('Apply Now');
    }

    public function test_campaign_not_found_for_invalid_id()
    {
        /** @var User $influencerUser */
        $influencerUser = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $this->actingAs($influencerUser);

        $response = $this->get('/campaigns/999999');

        $response->assertStatus(404);
    }

    public function test_back_to_discover_button_works()
    {
        // Create business user and campaign
        $businessUser = User::factory()->business()->withProfile()->subscribed()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
        ]);

        // Create influencer user
        /** @var User $influencerUser */
        $influencerUser = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($influencerUser);

        $response = $this->get("/campaigns/{$campaign->id}");

        $response->assertStatus(200);
        $response->assertSee('Back to Discover');
    }
}
