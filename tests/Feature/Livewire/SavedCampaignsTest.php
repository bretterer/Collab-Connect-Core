<?php

namespace Tests\Feature\Livewire;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Livewire\Campaigns\InfluencerCampaigns;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SavedCampaignsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function influencer_can_save_campaign_for_later(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Test campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->call('toggleSaveCampaign', $campaign->id);

        $this->assertTrue($influencer->hasSavedCampaign($campaign));
    }

    #[Test]
    public function influencer_can_unsave_campaign(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Test campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $influencer->savedCampaigns()->attach($campaign->id);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->call('toggleSaveCampaign', $campaign->id);

        $this->assertFalse($influencer->fresh()->hasSavedCampaign($campaign));
    }

    #[Test]
    public function influencer_can_hide_campaign(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Test campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->call('hideCampaign', $campaign->id);

        $this->assertTrue($influencer->hasHiddenCampaign($campaign));
    }

    #[Test]
    public function influencer_can_unhide_campaign(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Test campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $influencer->hiddenCampaigns()->attach($campaign->id);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->call('unhideCampaign', $campaign->id);

        $this->assertFalse($influencer->fresh()->hasHiddenCampaign($campaign));
    }

    #[Test]
    public function hidden_campaigns_are_excluded_from_all_tab(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $visibleCampaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Visible campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $hiddenCampaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Hidden campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $influencer->hiddenCampaigns()->attach($hiddenCampaign->id);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->assertSee('Visible campaign')
            ->assertDontSee('Hidden campaign');
    }

    #[Test]
    public function saved_tab_shows_only_saved_campaigns(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $savedCampaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Saved campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $unsavedCampaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Unsaved campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $influencer->savedCampaigns()->attach($savedCampaign->id);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->set('activeTab', 'saved')
            ->assertSee('Saved campaign')
            ->assertDontSee('Unsaved campaign');
    }

    #[Test]
    public function hidden_tab_shows_only_hidden_campaigns(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $hiddenCampaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Hidden campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $visibleCampaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Visible campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $influencer->hiddenCampaigns()->attach($hiddenCampaign->id);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->set('activeTab', 'hidden')
            ->assertSee('Hidden campaign')
            ->assertDontSee('Visible campaign');
    }

    #[Test]
    public function hiding_campaign_removes_it_from_saved(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Test campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $influencer->savedCampaigns()->attach($campaign->id);
        $this->assertTrue($influencer->hasSavedCampaign($campaign));

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->call('hideCampaign', $campaign->id);

        // Fetch fresh instance from database to check state
        $freshInfluencer = User::find($influencer->id);
        $this->assertFalse($freshInfluencer->hasSavedCampaign($campaign));
        $this->assertTrue($freshInfluencer->hasHiddenCampaign($campaign));
    }

    #[Test]
    public function can_switch_between_tabs(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->assertSet('activeTab', 'all')
            ->call('setActiveTab', 'saved')
            ->assertSet('activeTab', 'saved')
            ->call('setActiveTab', 'hidden')
            ->assertSet('activeTab', 'hidden')
            ->call('setActiveTab', 'all')
            ->assertSet('activeTab', 'all');
    }

    #[Test]
    public function quick_view_opens_with_campaign_data(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Test campaign for quick view',
            'application_deadline' => now()->addDays(30),
        ]);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->call('openQuickView', $campaign->id)
            ->assertSet('quickViewCampaignId', $campaign->id)
            ->assertSet('showQuickViewModal', true);
    }

    #[Test]
    public function quick_view_closes_properly(): void
    {
        $influencer = User::factory()->influencer()->withProfile()->create();
        $business = User::factory()->business()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'campaign_type' => CampaignType::SPONSORED_POSTS,
            'campaign_goal' => 'Test campaign',
            'application_deadline' => now()->addDays(30),
        ]);

        $this->actingAs($influencer);

        Livewire::test(InfluencerCampaigns::class)
            ->call('openQuickView', $campaign->id)
            ->assertSet('quickViewCampaignId', $campaign->id)
            ->assertSet('showQuickViewModal', true)
            ->call('closeQuickView')
            ->assertSet('quickViewCampaignId', null)
            ->assertSet('showQuickViewModal', false);
    }
}
