<?php

namespace Tests\Feature\Livewire\Collaborations;

use App\Enums\CampaignStatus;
use App\Enums\CollaborationStatus;
use App\Livewire\Collaborations\Dashboard;
use App\Models\Campaign;
use App\Models\Collaboration;
use App\Models\CollaborationDeliverable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_collaboration_dashboard_for_influencer_participant(): void
    {
        $business = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->subscribed()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'project_name' => 'Test Campaign',
        ]);

        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'business_id' => $business->currentBusiness->id,
            'influencer_id' => $influencer->id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        $this->actingAs($influencer);

        Livewire::test(Dashboard::class, ['collaboration' => $collaboration])
            ->assertStatus(200)
            ->assertSee('Test Campaign')
            ->assertSet('collaboration.id', $collaboration->id);
    }

    #[Test]
    public function it_shows_collaboration_dashboard_for_business_participant(): void
    {
        $business = User::factory()->business()->withProfile()->subscribed()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'project_name' => 'Business Test Campaign',
        ]);

        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'business_id' => $business->currentBusiness->id,
            'influencer_id' => $influencer->id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        $this->actingAs($business);

        Livewire::test(Dashboard::class, ['collaboration' => $collaboration])
            ->assertStatus(200)
            ->assertSee('Business Test Campaign');
    }

    #[Test]
    public function it_denies_access_to_non_participants(): void
    {
        $business = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();
        $otherUser = User::factory()->influencer()->withProfile()->subscribed()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
        ]);

        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'business_id' => $business->currentBusiness->id,
            'influencer_id' => $influencer->id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        $this->actingAs($otherUser);

        Livewire::test(Dashboard::class, ['collaboration' => $collaboration])
            ->assertStatus(403);
    }

    #[Test]
    public function it_shows_progress_stats(): void
    {
        $business = User::factory()->business()->withProfile()->subscribed()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
        ]);

        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'business_id' => $business->currentBusiness->id,
            'influencer_id' => $influencer->id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        // Create deliverables
        CollaborationDeliverable::factory()
            ->count(2)
            ->approved()
            ->create(['collaboration_id' => $collaboration->id]);

        CollaborationDeliverable::factory()
            ->submitted()
            ->create(['collaboration_id' => $collaboration->id]);

        $this->actingAs($business);

        $component = Livewire::test(Dashboard::class, ['collaboration' => $collaboration]);

        $progressStats = $component->get('progressStats');

        $this->assertEquals(3, $progressStats['total']);
        $this->assertEquals(2, $progressStats['approved']);
    }

    #[Test]
    public function it_correctly_identifies_business_user(): void
    {
        $business = User::factory()->business()->withProfile()->subscribed()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
        ]);

        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'business_id' => $business->currentBusiness->id,
            'influencer_id' => $influencer->id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        $this->actingAs($business);

        Livewire::test(Dashboard::class, ['collaboration' => $collaboration])
            ->assertSet('isBusiness', true);
    }

    #[Test]
    public function it_correctly_identifies_influencer_user(): void
    {
        $business = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->subscribed()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
        ]);

        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'business_id' => $business->currentBusiness->id,
            'influencer_id' => $influencer->id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        $this->actingAs($influencer);

        Livewire::test(Dashboard::class, ['collaboration' => $collaboration])
            ->assertSet('isBusiness', false);
    }
}
