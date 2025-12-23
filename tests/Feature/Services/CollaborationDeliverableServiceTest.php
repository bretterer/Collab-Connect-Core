<?php

namespace Tests\Feature\Services;

use App\Enums\CampaignStatus;
use App\Enums\CollaborationDeliverableStatus;
use App\Enums\CollaborationStatus;
use App\Enums\DeliverableType;
use App\Models\Campaign;
use App\Models\Collaboration;
use App\Models\CollaborationDeliverable;
use App\Models\User;
use App\Services\CollaborationDeliverableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CollaborationDeliverableServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_initializes_deliverables_from_campaign(): void
    {
        $business = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $business->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
            'deliverables' => [
                DeliverableType::INSTAGRAM_POST->value,
                DeliverableType::INSTAGRAM_STORY->value,
                DeliverableType::TIKTOK_VIDEO->value,
            ],
        ]);

        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'business_id' => $business->currentBusiness->id,
            'influencer_id' => $influencer->id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        CollaborationDeliverableService::initializeDeliverablesFromCampaign($collaboration);

        $this->assertEquals(3, $collaboration->deliverables()->count());

        $deliverableTypes = $collaboration->deliverables->pluck('deliverable_type')->map(fn ($d) => $d->value)->toArray();
        $this->assertContains(DeliverableType::INSTAGRAM_POST->value, $deliverableTypes);
        $this->assertContains(DeliverableType::INSTAGRAM_STORY->value, $deliverableTypes);
        $this->assertContains(DeliverableType::TIKTOK_VIDEO->value, $deliverableTypes);
    }

    #[Test]
    public function it_marks_deliverable_as_in_progress(): void
    {
        $collaboration = $this->createCollaborationWithDeliverable(CollaborationDeliverableStatus::NOT_STARTED);
        $deliverable = $collaboration->deliverables->first();

        CollaborationDeliverableService::markInProgress($deliverable);

        $deliverable->refresh();
        $this->assertEquals(CollaborationDeliverableStatus::IN_PROGRESS, $deliverable->status);
    }

    #[Test]
    public function it_submits_deliverable(): void
    {
        $collaboration = $this->createCollaborationWithDeliverable(CollaborationDeliverableStatus::IN_PROGRESS);
        $deliverable = $collaboration->deliverables->first();
        $user = $collaboration->influencer;

        CollaborationDeliverableService::submit(
            $deliverable,
            $user,
            'https://instagram.com/p/123456',
            'Here is my submission'
        );

        $deliverable->refresh();
        $this->assertEquals(CollaborationDeliverableStatus::SUBMITTED, $deliverable->status);
        $this->assertEquals('https://instagram.com/p/123456', $deliverable->post_url);
        $this->assertEquals('Here is my submission', $deliverable->notes);
        $this->assertNotNull($deliverable->submitted_at);
    }

    #[Test]
    public function it_approves_deliverable(): void
    {
        $collaboration = $this->createCollaborationWithDeliverable(CollaborationDeliverableStatus::SUBMITTED);
        $deliverable = $collaboration->deliverables->first();

        // Get a business member
        $businessUser = $collaboration->business->users->first();

        CollaborationDeliverableService::approve($deliverable, $businessUser);

        $deliverable->refresh();
        $this->assertEquals(CollaborationDeliverableStatus::APPROVED, $deliverable->status);
        $this->assertNotNull($deliverable->approved_at);
    }

    #[Test]
    public function it_requests_revision(): void
    {
        $collaboration = $this->createCollaborationWithDeliverable(CollaborationDeliverableStatus::SUBMITTED);
        $deliverable = $collaboration->deliverables->first();

        // Get a business member
        $businessUser = $collaboration->business->users->first();

        CollaborationDeliverableService::requestRevision(
            $deliverable,
            $businessUser,
            'Please update the caption and add more hashtags'
        );

        $deliverable->refresh();
        $this->assertEquals(CollaborationDeliverableStatus::REVISION_REQUESTED, $deliverable->status);
        $this->assertEquals('Please update the caption and add more hashtags', $deliverable->revision_feedback);
    }

    #[Test]
    public function it_calculates_progress_stats(): void
    {
        $business = User::factory()->business()->withProfile()->create();
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

        // Create 5 deliverables: 2 approved, 1 submitted, 1 in_progress, 1 not_started
        CollaborationDeliverable::factory()->approved()->count(2)->create([
            'collaboration_id' => $collaboration->id,
        ]);
        CollaborationDeliverable::factory()->submitted()->create([
            'collaboration_id' => $collaboration->id,
        ]);
        CollaborationDeliverable::factory()->inProgress()->create([
            'collaboration_id' => $collaboration->id,
        ]);
        CollaborationDeliverable::factory()->notStarted()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        $stats = CollaborationDeliverableService::getProgressStats($collaboration);

        $this->assertEquals(5, $stats['total']);
        $this->assertEquals(2, $stats['approved']);
        $this->assertEquals(1, $stats['submitted']);
        $this->assertEquals(1, $stats['in_progress']);
        $this->assertEquals(3, $stats['pending']); // not_started + in_progress + submitted
        $this->assertEquals(40, $stats['percentage']); // 2/5 = 40%
    }

    #[Test]
    public function approving_all_deliverables_marks_all_as_approved(): void
    {
        $business = User::factory()->business()->withProfile()->create();
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

        // Create one deliverable that's already approved
        CollaborationDeliverable::factory()->approved()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        // Create one that's submitted and about to be approved
        $deliverable = CollaborationDeliverable::factory()->submitted()->create([
            'collaboration_id' => $collaboration->id,
        ]);

        // Before approving, not all deliverables are approved
        $this->assertFalse(CollaborationDeliverableService::areAllDeliverablesApproved($collaboration));

        // Approve the last deliverable using the business owner
        CollaborationDeliverableService::approve($deliverable, $business);

        // Collaboration should NOT auto-complete
        $collaboration->refresh();
        $this->assertEquals(CollaborationStatus::ACTIVE, $collaboration->status);
        $this->assertNull($collaboration->completed_at);

        // But now all deliverables should be approved
        $this->assertTrue(CollaborationDeliverableService::areAllDeliverablesApproved($collaboration));
    }

    #[Test]
    public function it_can_resubmit_after_revision_request(): void
    {
        $collaboration = $this->createCollaborationWithDeliverable(CollaborationDeliverableStatus::REVISION_REQUESTED);
        $deliverable = $collaboration->deliverables->first();
        $deliverable->update(['revision_feedback' => 'Please fix the caption']);

        $user = $collaboration->influencer;

        CollaborationDeliverableService::submit(
            $deliverable,
            $user,
            'https://instagram.com/p/updated123',
            'Updated submission with new caption'
        );

        $deliverable->refresh();
        $this->assertEquals(CollaborationDeliverableStatus::SUBMITTED, $deliverable->status);
        $this->assertEquals('https://instagram.com/p/updated123', $deliverable->post_url);
    }

    private function createCollaborationWithDeliverable(CollaborationDeliverableStatus $status): Collaboration
    {
        $businessUser = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();

        $campaign = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'status' => CampaignStatus::PUBLISHED,
        ]);

        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'business_id' => $businessUser->currentBusiness->id,
            'influencer_id' => $influencer->id,
            'status' => CollaborationStatus::ACTIVE,
        ]);

        $deliverable = CollaborationDeliverable::factory()->create([
            'collaboration_id' => $collaboration->id,
            'status' => $status,
        ]);

        $collaboration->load('deliverables', 'influencer', 'business.users');

        return $collaboration;
    }
}
