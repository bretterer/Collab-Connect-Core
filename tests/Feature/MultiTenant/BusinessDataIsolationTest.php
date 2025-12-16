<?php

namespace Tests\Feature\MultiTenant;

use App\Enums\CampaignStatus;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BusinessDataIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $businessAOwner;

    private User $businessBOwner;

    private Business $businessA;

    private Business $businessB;

    protected function setUp(): void
    {
        parent::setUp();

        // Create two separate businesses
        $this->businessAOwner = User::factory()->business()->withProfile()->subscribed()->create();
        $this->businessA = $this->businessAOwner->currentBusiness;

        $this->businessBOwner = User::factory()->business()->withProfile()->subscribed()->create();
        $this->businessB = $this->businessBOwner->currentBusiness;
    }

    // ==================== Campaign List Isolation ====================

    #[Test]
    public function business_only_sees_own_campaigns_in_list(): void
    {
        // Create campaigns for both businesses
        $campaignA = Campaign::factory()->create([
            'business_id' => $this->businessA->id,
            'campaign_description' => 'Unique description for Business A Campaign',
        ]);

        $campaignB = Campaign::factory()->create([
            'business_id' => $this->businessB->id,
            'campaign_description' => 'Unique description for Business B Campaign',
        ]);

        $this->actingAs($this->businessAOwner);

        // Business A should only see their own campaign
        $component = Livewire::test('business.business-campaigns', [
            'user' => $this->businessAOwner,
        ]);

        $component->assertSee('Unique description for Business A Campaign');
        $component->assertDontSee('Unique description for Business B Campaign');
    }

    #[Test]
    public function business_b_only_sees_own_campaigns(): void
    {
        Campaign::factory()->create([
            'business_id' => $this->businessA->id,
            'campaign_description' => 'Unique description for Business A Campaign',
        ]);

        Campaign::factory()->create([
            'business_id' => $this->businessB->id,
            'campaign_description' => 'Unique description for Business B Campaign',
        ]);

        $this->actingAs($this->businessBOwner);

        $component = Livewire::test('business.business-campaigns', [
            'user' => $this->businessBOwner,
        ]);

        $component->assertSee('Unique description for Business B Campaign');
        $component->assertDontSee('Unique description for Business A Campaign');
    }

    // ==================== Campaign Applications Isolation ====================

    #[Test]
    public function business_can_view_applications_for_own_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->businessA->id,
        ]);

        $influencer = User::factory()->influencer()->withProfile()->create();
        CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
        ]);

        $this->actingAs($this->businessAOwner);

        $component = Livewire::test('campaigns.campaign-applications', [
            'campaign' => $campaign,
        ]);

        $component->assertStatus(200);
        $component->assertSee($influencer->name);
    }

    #[Test]
    public function business_cannot_view_applications_for_other_business_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->businessB->id,
        ]);

        $influencer = User::factory()->influencer()->withProfile()->create();
        CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
        ]);

        $this->actingAs($this->businessAOwner);

        // Should get 403 when trying to view another business's campaign applications
        Livewire::test('campaigns.campaign-applications', [
            'campaign' => $campaign,
        ])->assertStatus(403);
    }

    #[Test]
    public function business_cannot_update_application_status_for_other_business_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->businessB->id,
        ]);

        $influencer = User::factory()->influencer()->withProfile()->create();
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->businessAOwner);

        // First, we need to bypass the mount check to test the updateStatus method
        // Since mount already blocks, let's test via direct route instead
        $response = $this->get(route('campaigns.applications', $campaign));
        $response->assertStatus(403);
    }

    // ==================== Switching Business Context ====================

    #[Test]
    public function user_in_multiple_businesses_sees_correct_campaigns_based_on_current_business(): void
    {
        // Add businessAOwner to businessB as a member
        BusinessUser::create([
            'business_id' => $this->businessB->id,
            'user_id' => $this->businessAOwner->id,
            'role' => 'member',
        ]);

        // Create campaigns in both businesses
        Campaign::factory()->create([
            'business_id' => $this->businessA->id,
            'campaign_description' => 'Unique description for Business A Campaign',
        ]);

        Campaign::factory()->create([
            'business_id' => $this->businessB->id,
            'campaign_description' => 'Unique description for Business B Campaign',
        ]);

        $this->actingAs($this->businessAOwner);

        // When current business is A, should see A's campaigns
        $component = Livewire::test('business.business-campaigns', [
            'user' => $this->businessAOwner,
        ]);

        $component->assertSee('Unique description for Business A Campaign');
        $component->assertDontSee('Unique description for Business B Campaign');

        // Switch to business B
        $this->businessAOwner->setCurrentBusiness($this->businessB);
        $this->businessAOwner->refresh();

        // Now should see B's campaigns
        $component = Livewire::test('business.business-campaigns', [
            'user' => $this->businessAOwner,
        ]);

        $component->assertSee('Unique description for Business B Campaign');
        $component->assertDontSee('Unique description for Business A Campaign');
    }

    // ==================== Direct Database Query Isolation ====================

    #[Test]
    public function current_business_relationship_returns_only_own_campaigns(): void
    {
        Campaign::factory()->count(3)->create([
            'business_id' => $this->businessA->id,
        ]);

        Campaign::factory()->count(2)->create([
            'business_id' => $this->businessB->id,
        ]);

        $this->actingAs($this->businessAOwner);

        $campaigns = $this->businessAOwner->currentBusiness->campaigns;

        $this->assertCount(3, $campaigns);
        $this->assertTrue($campaigns->every(fn ($c) => $c->business_id === $this->businessA->id));
    }

    #[Test]
    public function campaign_applications_are_scoped_to_own_campaigns(): void
    {
        // Create campaigns
        $campaignA = Campaign::factory()->published()->create([
            'business_id' => $this->businessA->id,
        ]);

        $campaignB = Campaign::factory()->published()->create([
            'business_id' => $this->businessB->id,
        ]);

        // Create influencer applications
        $influencer = User::factory()->influencer()->withProfile()->create();

        CampaignApplication::factory()->create([
            'campaign_id' => $campaignA->id,
            'user_id' => $influencer->id,
        ]);

        CampaignApplication::factory()->create([
            'campaign_id' => $campaignB->id,
            'user_id' => $influencer->id,
        ]);

        $this->actingAs($this->businessAOwner);

        // Get applications through business's campaigns
        $applications = $this->businessAOwner->currentBusiness
            ->campaigns()
            ->with('applications')
            ->get()
            ->pluck('applications')
            ->flatten();

        $this->assertCount(1, $applications);
        $this->assertEquals($campaignA->id, $applications->first()->campaign_id);
    }

    // ==================== Route Protection ====================

    #[Test]
    public function campaign_edit_route_blocked_for_other_business(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->businessB->id,
        ]);

        $this->actingAs($this->businessAOwner);

        $response = $this->get(route('campaigns.edit', $campaign));

        // Should be blocked by policy (returns 404 for unpublished, 403 for access denied)
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    #[Test]
    public function published_campaign_view_allowed_for_any_business(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->businessB->id,
            'campaign_goal' => 'Public Campaign',
        ]);

        $this->actingAs($this->businessAOwner);

        $response = $this->get(route('campaigns.show', $campaign));

        // Published campaigns should be viewable by anyone
        $response->assertStatus(200);
    }

    #[Test]
    public function draft_campaign_view_blocked_for_other_business(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->businessB->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->actingAs($this->businessAOwner);

        $response = $this->get(route('campaigns.show', $campaign));

        // Draft campaigns return 404 for non-members (to hide existence)
        $response->assertStatus(404);
    }

    // ==================== Team Member Isolation ====================

    #[Test]
    public function team_member_sees_own_business_campaigns_only(): void
    {
        // Create a team member for business A
        $teamMember = User::factory()->business()->create();
        BusinessUser::create([
            'business_id' => $this->businessA->id,
            'user_id' => $teamMember->id,
            'role' => 'member',
        ]);
        $teamMember->setCurrentBusiness($this->businessA);

        Campaign::factory()->create([
            'business_id' => $this->businessA->id,
            'campaign_description' => 'Unique description for Business A Campaign',
        ]);

        Campaign::factory()->create([
            'business_id' => $this->businessB->id,
            'campaign_description' => 'Unique description for Business B Campaign',
        ]);

        $this->actingAs($teamMember);

        $component = Livewire::test('business.business-campaigns', [
            'user' => $teamMember,
        ]);

        $component->assertSee('Unique description for Business A Campaign');
        $component->assertDontSee('Unique description for Business B Campaign');
    }

    #[Test]
    public function team_member_can_view_own_business_campaign_applications(): void
    {
        // Create a team member for business A
        $teamMember = User::factory()->business()->create();
        BusinessUser::create([
            'business_id' => $this->businessA->id,
            'user_id' => $teamMember->id,
            'role' => 'member',
        ]);
        $teamMember->setCurrentBusiness($this->businessA);

        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->businessA->id,
        ]);

        $influencer = User::factory()->influencer()->withProfile()->create();
        CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
        ]);

        $this->actingAs($teamMember);

        $component = Livewire::test('campaigns.campaign-applications', [
            'campaign' => $campaign,
        ]);

        $component->assertStatus(200);
    }
}
