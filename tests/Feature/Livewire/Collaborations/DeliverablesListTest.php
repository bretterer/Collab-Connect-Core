<?php

namespace Tests\Feature\Livewire\Collaborations;

use App\Enums\CampaignStatus;
use App\Enums\CollaborationDeliverableStatus;
use App\Enums\CollaborationStatus;
use App\Livewire\Collaborations\DeliverablesList;
use App\Models\Campaign;
use App\Models\Collaboration;
use App\Models\CollaborationDeliverable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeliverablesListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_deliverables_list(): void
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

        CollaborationDeliverable::factory()->instagramPost()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        $this->actingAs($business);

        Livewire::test(DeliverablesList::class, ['collaboration' => $collaboration])
            ->assertStatus(200)
            ->assertSee('Instagram Post');
    }

    #[Test]
    public function influencer_can_see_submit_button_for_pending_deliverable(): void
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

        CollaborationDeliverable::factory()->notStarted()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        $this->actingAs($influencer);

        Livewire::test(DeliverablesList::class, ['collaboration' => $collaboration])
            ->assertSee('Submit');
    }

    #[Test]
    public function business_can_approve_submitted_deliverable(): void
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

        $deliverable = CollaborationDeliverable::factory()->submitted()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        $this->actingAs($business);

        Livewire::test(DeliverablesList::class, ['collaboration' => $collaboration])
            ->call('openApprovalModal', $deliverable->id)
            ->assertSet('showApprovalModal', true)
            ->assertSet('selectedDeliverableId', $deliverable->id)
            ->call('approveDeliverable');

        $deliverable->refresh();
        $this->assertEquals(CollaborationDeliverableStatus::APPROVED, $deliverable->status);
    }

    #[Test]
    public function business_can_request_revision(): void
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

        $deliverable = CollaborationDeliverable::factory()->submitted()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        $this->actingAs($business);

        Livewire::test(DeliverablesList::class, ['collaboration' => $collaboration])
            ->call('openRevisionModal', $deliverable->id)
            ->set('revisionFeedback', 'Please update the caption and re-upload')
            ->call('requestRevision');

        $deliverable->refresh();
        $this->assertEquals(CollaborationDeliverableStatus::REVISION_REQUESTED, $deliverable->status);
        $this->assertEquals('Please update the caption and re-upload', $deliverable->revision_feedback);
    }

    #[Test]
    public function influencer_cannot_approve_deliverables(): void
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

        $deliverable = CollaborationDeliverable::factory()->submitted()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        $this->actingAs($influencer);

        Livewire::test(DeliverablesList::class, ['collaboration' => $collaboration])
            ->call('openApprovalModal', $deliverable->id)
            ->call('approveDeliverable');

        $deliverable->refresh();
        // Status should remain SUBMITTED since influencer can't approve
        $this->assertEquals(CollaborationDeliverableStatus::SUBMITTED, $deliverable->status);
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

        CollaborationDeliverable::factory()->approved()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        CollaborationDeliverable::factory()->notStarted()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        $this->actingAs($business);

        $component = Livewire::test(DeliverablesList::class, ['collaboration' => $collaboration]);

        $progressStats = $component->get('progressStats');

        $this->assertEquals(2, $progressStats['total']);
        $this->assertEquals(1, $progressStats['approved']);
        $this->assertEquals(1, $progressStats['pending']);
    }

    #[Test]
    public function revision_feedback_is_required(): void
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

        $deliverable = CollaborationDeliverable::factory()->submitted()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        $this->actingAs($business);

        Livewire::test(DeliverablesList::class, ['collaboration' => $collaboration])
            ->call('openRevisionModal', $deliverable->id)
            ->set('revisionFeedback', '')
            ->call('requestRevision')
            ->assertHasErrors(['revisionFeedback' => 'required']);
    }

    #[Test]
    public function revision_feedback_must_be_at_least_10_characters(): void
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

        $deliverable = CollaborationDeliverable::factory()->submitted()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        $this->actingAs($business);

        Livewire::test(DeliverablesList::class, ['collaboration' => $collaboration])
            ->call('openRevisionModal', $deliverable->id)
            ->set('revisionFeedback', 'short')
            ->call('requestRevision')
            ->assertHasErrors(['revisionFeedback' => 'min']);
    }
}
