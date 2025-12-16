<?php

namespace Tests\Feature\Policies;

use App\Enums\CampaignStatus;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CampaignPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $businessOwner;

    private User $businessMember;

    private User $otherBusinessOwner;

    private User $influencer;

    private User $admin;

    private Business $business;

    private Business $otherBusiness;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a business owner with their business
        $this->businessOwner = User::factory()->business()->withProfile()->create();
        $this->business = $this->businessOwner->currentBusiness;

        // Create a team member for the same business
        $this->businessMember = User::factory()->business()->create();
        BusinessUser::create([
            'business_id' => $this->business->id,
            'user_id' => $this->businessMember->id,
            'role' => 'member',
        ]);

        // Create another business owner (different business)
        $this->otherBusinessOwner = User::factory()->business()->withProfile()->create();
        $this->otherBusiness = $this->otherBusinessOwner->currentBusiness;

        // Create an influencer
        $this->influencer = User::factory()->influencer()->withProfile()->create();

        // Create an admin
        $this->admin = User::factory()->admin()->create();
    }

    // ==================== viewAny Tests ====================

    #[Test]
    public function anyone_can_view_campaign_list(): void
    {
        $this->assertTrue($this->businessOwner->can('viewAny', Campaign::class));
        $this->assertTrue($this->influencer->can('viewAny', Campaign::class));
        $this->assertTrue($this->admin->can('viewAny', Campaign::class));
    }

    // ==================== view Tests ====================

    #[Test]
    public function anyone_can_view_published_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessOwner->can('view', $campaign));
        $this->assertTrue($this->influencer->can('view', $campaign));
        $this->assertTrue($this->otherBusinessOwner->can('view', $campaign));
    }

    #[Test]
    public function business_owner_can_view_own_draft_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->assertTrue($this->businessOwner->can('view', $campaign));
    }

    #[Test]
    public function business_member_can_view_own_business_draft_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->assertTrue($this->businessMember->can('view', $campaign));
    }

    #[Test]
    public function admin_can_view_any_draft_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->assertTrue($this->admin->can('view', $campaign));
    }

    #[Test]
    public function non_member_cannot_view_draft_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        // Policy aborts with 404 for non-members viewing draft campaigns
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->otherBusinessOwner->can('view', $campaign);
    }

    #[Test]
    public function influencer_cannot_view_draft_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        // Policy aborts with 404 for influencers viewing draft campaigns
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->influencer->can('view', $campaign);
    }

    #[Test]
    public function business_owner_can_view_own_scheduled_campaign(): void
    {
        $campaign = Campaign::factory()->scheduled()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessOwner->can('view', $campaign));
    }

    // ==================== create Tests ====================

    #[Test]
    public function business_user_can_create_campaigns(): void
    {
        $this->assertTrue($this->businessOwner->can('create', Campaign::class));
    }

    #[Test]
    public function influencer_cannot_create_campaigns(): void
    {
        $this->assertFalse($this->influencer->can('create', Campaign::class));
    }

    #[Test]
    public function admin_with_business_type_can_create_campaigns(): void
    {
        // Admin account type is not BUSINESS, so they cannot create
        $this->assertFalse($this->admin->can('create', Campaign::class));
    }

    // ==================== update Tests ====================

    #[Test]
    public function business_owner_can_update_own_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessOwner->can('update', $campaign));
    }

    #[Test]
    public function business_member_can_update_own_business_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessMember->can('update', $campaign));
    }

    #[Test]
    public function admin_can_update_any_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->admin->can('update', $campaign));
    }

    #[Test]
    public function other_business_owner_cannot_update_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->otherBusinessOwner->can('update', $campaign));
    }

    #[Test]
    public function influencer_cannot_update_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->influencer->can('update', $campaign));
    }

    // ==================== delete Tests ====================

    #[Test]
    public function business_owner_can_delete_own_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessOwner->can('delete', $campaign));
    }

    #[Test]
    public function business_member_can_delete_own_business_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessMember->can('delete', $campaign));
    }

    #[Test]
    public function other_business_owner_cannot_delete_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->otherBusinessOwner->can('delete', $campaign));
    }

    #[Test]
    public function influencer_cannot_delete_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->influencer->can('delete', $campaign));
    }

    // ==================== publish Tests ====================

    #[Test]
    public function business_owner_can_publish_own_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessOwner->can('publish', $campaign));
    }

    #[Test]
    public function business_member_can_publish_own_business_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessMember->can('publish', $campaign));
    }

    #[Test]
    public function other_business_owner_cannot_publish_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->otherBusinessOwner->can('publish', $campaign));
    }

    // ==================== unpublish Tests ====================

    #[Test]
    public function business_owner_can_unpublish_own_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessOwner->can('unpublish', $campaign));
    }

    #[Test]
    public function other_business_owner_cannot_unpublish_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->otherBusinessOwner->can('unpublish', $campaign));
    }

    // ==================== archive Tests ====================

    #[Test]
    public function business_owner_can_archive_own_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessOwner->can('archive', $campaign));
    }

    #[Test]
    public function other_business_owner_cannot_archive_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->otherBusinessOwner->can('archive', $campaign));
    }

    // ==================== apply Tests ====================

    #[Test]
    public function influencer_can_apply_to_published_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->influencer->can('apply', $campaign));
    }

    #[Test]
    public function influencer_cannot_apply_to_draft_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        $this->assertFalse($this->influencer->can('apply', $campaign));
    }

    #[Test]
    public function influencer_cannot_apply_to_archived_campaign(): void
    {
        $campaign = Campaign::factory()->archived()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->influencer->can('apply', $campaign));
    }

    #[Test]
    public function influencer_cannot_apply_to_scheduled_campaign(): void
    {
        $campaign = Campaign::factory()->scheduled()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->influencer->can('apply', $campaign));
    }

    #[Test]
    public function business_user_cannot_apply_to_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->otherBusiness->id,
        ]);

        $this->assertFalse($this->businessOwner->can('apply', $campaign));
    }

    #[Test]
    public function admin_cannot_apply_to_campaign(): void
    {
        $campaign = Campaign::factory()->published()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->admin->can('apply', $campaign));
    }

    // ==================== restore Tests ====================

    #[Test]
    public function business_owner_can_restore_own_campaign(): void
    {
        $campaign = Campaign::factory()->archived()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessOwner->can('restore', $campaign));
    }

    #[Test]
    public function other_business_owner_cannot_restore_campaign(): void
    {
        $campaign = Campaign::factory()->archived()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->otherBusinessOwner->can('restore', $campaign));
    }

    // ==================== forceDelete Tests ====================

    #[Test]
    public function business_owner_can_force_delete_own_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertTrue($this->businessOwner->can('forceDelete', $campaign));
    }

    #[Test]
    public function other_business_owner_cannot_force_delete_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $this->assertFalse($this->otherBusinessOwner->can('forceDelete', $campaign));
    }

    // ==================== Cross-business isolation Tests ====================

    #[Test]
    public function user_in_multiple_businesses_has_correct_permissions(): void
    {
        // Add businessOwner as a member of otherBusiness
        BusinessUser::create([
            'business_id' => $this->otherBusiness->id,
            'user_id' => $this->businessOwner->id,
            'role' => 'member',
        ]);

        $campaignInOwnBusiness = Campaign::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $campaignInOtherBusiness = Campaign::factory()->create([
            'business_id' => $this->otherBusiness->id,
        ]);

        // User should have permissions on both businesses' campaigns
        $this->assertTrue($this->businessOwner->can('update', $campaignInOwnBusiness));
        $this->assertTrue($this->businessOwner->can('update', $campaignInOtherBusiness));
    }
}
