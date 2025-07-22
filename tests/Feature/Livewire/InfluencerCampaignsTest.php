<?php

namespace Tests\Feature\Livewire;

use App\Enums\AccountType;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\Niche;
use App\Livewire\Campaigns\InfluencerCampaigns;
use App\Models\BusinessProfile;
use App\Models\Campaign;
use App\Models\InfluencerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InfluencerCampaignsTest extends TestCase
{
    use RefreshDatabase;

    public function test_influencer_can_view_campaign_discovery_page()
    {
        $influencer = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $influencerProfile = InfluencerProfile::factory()->create([
            'user_id' => $influencer->id,
            'primary_niche' => Niche::FASHION,
            'primary_zip_code' => '12345',
        ]);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->assertStatus(200)
            ->assertSee('Discover Campaigns')
            ->assertSee('Find the perfect collaboration opportunities');
    }

    public function test_campaigns_are_sorted_by_match_score()
    {
        // Create influencer
        $influencer = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $influencerProfile = InfluencerProfile::factory()->create([
            'user_id' => $influencer->id,
            'primary_niche' => Niche::FASHION,
            'primary_zip_code' => '12345',
        ]);

        // Create businesses and campaigns
        $business1 = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $businessProfile1 = BusinessProfile::factory()->create([
            'user_id' => $business1->id,
            'industry' => Niche::FASHION, // Perfect match
            'primary_zip_code' => '12345', // Same location
        ]);

        $business2 = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $businessProfile2 = BusinessProfile::factory()->create([
            'user_id' => $business2->id,
            'industry' => Niche::FOOD, // Different niche
            'primary_zip_code' => '67890', // Different location
        ]);

        // Create campaigns
        $campaign1 = Campaign::factory()->create([
            'user_id' => $business1->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'target_zip_code' => '12345',
            'campaign_goal' => 'Fashion campaign',
            'campaign_description' => 'Looking for fashion influencers',
            'compensation_amount' => 500,
            'influencer_count' => 5,
            'application_deadline' => now()->addDays(30),
            'campaign_completion_date' => now()->addDays(60),
        ]);

        $campaign2 = Campaign::factory()->create([
            'user_id' => $business2->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::PRODUCT_REVIEWS,
            'target_zip_code' => '67890',
            'campaign_goal' => 'Food campaign',
            'campaign_description' => 'Looking for food influencers',
            'compensation_amount' => 300,
            'influencer_count' => 3,
            'application_deadline' => now()->addDays(30),
            'campaign_completion_date' => now()->addDays(60),
        ]);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->assertSee('Fashion campaign')
            ->assertSee('Food campaign');
    }

        public function test_campaigns_can_be_filtered_by_search()
    {
        $influencer = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $influencerProfile = InfluencerProfile::factory()->create([
            'user_id' => $influencer->id,
        ]);

        $business = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $businessProfile = BusinessProfile::factory()->create([
            'user_id' => $business->id,
        ]);

        $campaign = Campaign::factory()->create([
            'user_id' => $business->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_goal' => 'Unique fashion campaign',
            'campaign_description' => 'Looking for fashion influencers',
            'published_at' => now(),
        ]);

        $this->actingAs($influencer);

        // Test that the component loads without errors
        Livewire::test(InfluencerCampaigns::class)
            ->assertStatus(200)
            ->assertSee('Discover Campaigns');
    }

    public function test_campaigns_can_be_filtered_by_niche()
    {
        $influencer = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $influencerProfile = InfluencerProfile::factory()->create([
            'user_id' => $influencer->id,
        ]);

        $business1 = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $businessProfile1 = BusinessProfile::factory()->create([
            'user_id' => $business1->id,
            'industry' => Niche::FASHION,
        ]);

        $business2 = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $businessProfile2 = BusinessProfile::factory()->create([
            'user_id' => $business2->id,
            'industry' => Niche::FOOD,
        ]);

        $campaign1 = Campaign::factory()->create([
            'user_id' => $business1->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_goal' => 'Fashion campaign',
        ]);

        $campaign2 = Campaign::factory()->create([
            'user_id' => $business2->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_goal' => 'Food campaign',
        ]);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->set('selectedNiches', [Niche::FASHION->value])
            ->assertSee('Fashion campaign')
            ->assertDontSee('Food campaign');
    }
}