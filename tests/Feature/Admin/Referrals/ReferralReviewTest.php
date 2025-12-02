<?php

namespace Tests\Feature\Admin\Referrals;

use App\Enums\AccountType;
use App\Enums\PayoutStatus;
use App\Enums\ReferralStatus;
use App\Livewire\Admin\Referrals\ReferralReview;
use App\Models\Referral;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayoutItem;
use App\Models\ReferralPayoutItemNote;
use App\Models\ReferralPercentageHistory;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReferralReviewTest extends TestCase
{
    protected function createPayoutItemWithRelations(array $attributes = []): ReferralPayoutItem
    {
        // Create referrer user with enrollment
        $referrerUser = User::factory()->influencer()->withProfile()->create();
        $referralEnrollment = ReferralEnrollment::factory()->create([
            'user_id' => $referrerUser->id,
        ]);

        // Create referred user
        $referredUser = User::factory()->influencer()->withProfile()->create();

        // Create referral
        $referral = Referral::factory()->create([
            'referrer_user_id' => $referrerUser->id,
            'referred_user_id' => $referredUser->id,
            'status' => ReferralStatus::ACTIVE,
        ]);

        // Create percentage history
        $percentageHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'new_percentage' => 10,
        ]);

        // Create payout item
        return ReferralPayoutItem::factory()->create(array_merge([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral->id,
            'referral_percentage_history_id' => $percentageHistory->id,
            'scheduled_payout_date' => now()->day(15),
            'status' => PayoutStatus::DRAFT,
        ], $attributes));
    }

    #[Test]
    public function admin_can_view_payout_review_page()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);

        $this->actingAs($admin);

        Livewire::test(ReferralReview::class)
            ->assertStatus(200)
            ->assertSee('Review Monthly Payouts');
    }

    #[Test]
    public function displays_payout_items_for_selected_month()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $payoutItem = $this->createPayoutItemWithRelations(['subscription_amount' => 100.00]);

        Livewire::test(ReferralReview::class)
            ->assertSee($payoutItem->fresh()->enrollment->user->name)
            ->assertSee(number_format($payoutItem->fresh()->amount, 2));
    }

    #[Test]
    public function can_filter_by_status()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $draftItem = $this->createPayoutItemWithRelations([
            'status' => PayoutStatus::DRAFT,
        ]);

        $approvedItem = $this->createPayoutItemWithRelations([
            'status' => PayoutStatus::APPROVED,
        ]);

        Livewire::test(ReferralReview::class)
            ->set('statusFilter', PayoutStatus::DRAFT->value)
            ->assertSee($draftItem->fresh()->enrollment->user->name)
            ->assertDontSee($approvedItem->fresh()->enrollment->user->name);
    }

    #[Test]
    public function can_search_by_referrer_name()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $referrerUser1 = User::factory()->influencer()->withProfile()->create(['name' => 'John Doe']);
        $referrerUser2 = User::factory()->influencer()->withProfile()->create(['name' => 'Jane Smith']);

        $enrollment1 = ReferralEnrollment::factory()->create(['user_id' => $referrerUser1->id]);
        $enrollment2 = ReferralEnrollment::factory()->create(['user_id' => $referrerUser2->id]);

        $referredUser = User::factory()->influencer()->withProfile()->create();

        $referral1 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser1->id,
            'referred_user_id' => $referredUser->id,
        ]);

        $referral2 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser2->id,
            'referred_user_id' => $referredUser->id,
        ]);

        $percentageHistory1 = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
        ]);

        $percentageHistory2 = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
        ]);

        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'referral_id' => $referral1->id,
            'referral_percentage_history_id' => $percentageHistory1->id,
            'scheduled_payout_date' => now()->day(15),
        ]);

        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
            'referral_id' => $referral2->id,
            'referral_percentage_history_id' => $percentageHistory2->id,
            'scheduled_payout_date' => now()->day(15),
        ]);

        Livewire::test(ReferralReview::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    #[Test]
    public function can_approve_individual_payout_item()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $payoutItem = $this->createPayoutItemWithRelations(['status' => PayoutStatus::DRAFT]);

        Livewire::test(ReferralReview::class)
            ->call('approveItem', $payoutItem->id);

        $this->assertEquals(PayoutStatus::APPROVED, $payoutItem->fresh()->status);
    }

    #[Test]
    public function can_deny_individual_payout_item()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $payoutItem = $this->createPayoutItemWithRelations(['status' => PayoutStatus::DRAFT]);

        Livewire::test(ReferralReview::class)
            ->call('denyItem', $payoutItem->id);

        $this->assertEquals(PayoutStatus::CANCELLED, $payoutItem->fresh()->status);
    }

    #[Test]
    public function cannot_approve_already_processed_items()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $payoutItem = $this->createPayoutItemWithRelations(['status' => PayoutStatus::PAID]);

        Livewire::test(ReferralReview::class)
            ->call('approveItem', $payoutItem->id);

        // Status should remain PAID
        $this->assertEquals(PayoutStatus::PAID, $payoutItem->fresh()->status);
    }

    #[Test]
    public function can_approve_all_pending_items_for_enrollment()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        // Create referrer user with enrollment
        $referrerUser = User::factory()->influencer()->withProfile()->create();
        $referralEnrollment = ReferralEnrollment::factory()->create([
            'user_id' => $referrerUser->id,
        ]);

        $percentageHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'new_percentage' => 10,
        ]);

        // Create multiple payout items for the same enrollment
        $referredUser1 = User::factory()->influencer()->withProfile()->create();
        $referredUser2 = User::factory()->influencer()->withProfile()->create();
        $referredUser3 = User::factory()->influencer()->withProfile()->create();

        $referral1 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser->id,
            'referred_user_id' => $referredUser1->id,
        ]);

        $referral2 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser->id,
            'referred_user_id' => $referredUser2->id,
        ]);

        $referral3 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser->id,
            'referred_user_id' => $referredUser3->id,
        ]);

        // Create items with different statuses
        $draftItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral1->id,
            'referral_percentage_history_id' => $percentageHistory->id,
            'scheduled_payout_date' => now()->day(15),
            'status' => PayoutStatus::DRAFT,
        ]);

        $pendingItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral2->id,
            'referral_percentage_history_id' => $percentageHistory->id,
            'scheduled_payout_date' => now()->day(15),
            'status' => PayoutStatus::PENDING,
        ]);

        $approvedItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral3->id,
            'referral_percentage_history_id' => $percentageHistory->id,
            'scheduled_payout_date' => now()->day(15),
            'status' => PayoutStatus::APPROVED,
        ]);

        Livewire::test(ReferralReview::class)
            ->call('approveAllForEnrollment', $referralEnrollment->id);

        // Verify pending items were approved
        $this->assertEquals(PayoutStatus::APPROVED, $draftItem->fresh()->status);
        $this->assertEquals(PayoutStatus::APPROVED, $pendingItem->fresh()->status);

        // Verify already approved item remains approved
        $this->assertEquals(PayoutStatus::APPROVED, $approvedItem->fresh()->status);
    }

    #[Test]
    public function can_deny_all_pending_items_for_enrollment()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        // Create referrer user with enrollment
        $referrerUser = User::factory()->influencer()->withProfile()->create();
        $referralEnrollment = ReferralEnrollment::factory()->create([
            'user_id' => $referrerUser->id,
        ]);

        $percentageHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'new_percentage' => 10,
        ]);

        // Create multiple payout items for the same enrollment
        $referredUser1 = User::factory()->influencer()->withProfile()->create();
        $referredUser2 = User::factory()->influencer()->withProfile()->create();
        $referredUser3 = User::factory()->influencer()->withProfile()->create();

        $referral1 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser->id,
            'referred_user_id' => $referredUser1->id,
        ]);

        $referral2 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser->id,
            'referred_user_id' => $referredUser2->id,
        ]);

        $referral3 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser->id,
            'referred_user_id' => $referredUser3->id,
        ]);

        // Create items with different statuses
        $draftItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral1->id,
            'referral_percentage_history_id' => $percentageHistory->id,
            'scheduled_payout_date' => now()->day(15),
            'status' => PayoutStatus::DRAFT,
        ]);

        $pendingItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral2->id,
            'referral_percentage_history_id' => $percentageHistory->id,
            'scheduled_payout_date' => now()->day(15),
            'status' => PayoutStatus::PENDING,
        ]);

        $approvedItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral3->id,
            'referral_percentage_history_id' => $percentageHistory->id,
            'scheduled_payout_date' => now()->day(15),
            'status' => PayoutStatus::APPROVED,
        ]);

        Livewire::test(ReferralReview::class)
            ->call('denyAllForEnrollment', $referralEnrollment->id);

        // Verify pending items were denied
        $this->assertEquals(PayoutStatus::CANCELLED, $draftItem->fresh()->status);
        $this->assertEquals(PayoutStatus::CANCELLED, $pendingItem->fresh()->status);

        // Verify already approved item remains approved
        $this->assertEquals(PayoutStatus::APPROVED, $approvedItem->fresh()->status);
    }

    #[Test]
    public function can_bulk_approve_payout_items()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $item1 = $this->createPayoutItemWithRelations(['status' => PayoutStatus::DRAFT]);
        $item2 = $this->createPayoutItemWithRelations(['status' => PayoutStatus::PENDING]);

        Livewire::test(ReferralReview::class)
            ->set('selectedItems', [$item1->id, $item2->id])
            ->call('bulkApprove');

        $this->assertEquals(PayoutStatus::APPROVED, $item1->fresh()->status);
        $this->assertEquals(PayoutStatus::APPROVED, $item2->fresh()->status);
    }

    #[Test]
    public function can_bulk_deny_payout_items()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $item1 = $this->createPayoutItemWithRelations(['status' => PayoutStatus::DRAFT]);
        $item2 = $this->createPayoutItemWithRelations(['status' => PayoutStatus::PENDING]);

        Livewire::test(ReferralReview::class)
            ->set('selectedItems', [$item1->id, $item2->id])
            ->call('bulkDeny');

        $this->assertEquals(PayoutStatus::CANCELLED, $item1->fresh()->status);
        $this->assertEquals(PayoutStatus::CANCELLED, $item2->fresh()->status);
    }

    #[Test]
    public function displays_summary_statistics()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $item1 = $this->createPayoutItemWithRelations([
            'status' => PayoutStatus::DRAFT,
            'subscription_amount' => 100.00,
        ]);

        $item2 = $this->createPayoutItemWithRelations([
            'status' => PayoutStatus::APPROVED,
            'subscription_amount' => 200.00,
        ]);

        $totalAmount = $item1->fresh()->amount + $item2->fresh()->amount;

        Livewire::test(ReferralReview::class)
            ->assertSee('Total Items')
            ->assertSee('Total Amount')
            ->assertSee(number_format($totalAmount, 2))
            ->assertSee('Pending Review')
            ->assertSee('Approved');
    }

    #[Test]
    public function select_all_checkbox_selects_all_items_on_page()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $item1 = $this->createPayoutItemWithRelations();
        $item2 = $this->createPayoutItemWithRelations();

        $component = Livewire::test(ReferralReview::class)
            ->set('selectAll', true);

        // Check that both items are selected (order doesn't matter)
        $this->assertCount(2, $component->get('selectedItems'));
        $this->assertContains($item1->id, $component->get('selectedItems'));
        $this->assertContains($item2->id, $component->get('selectedItems'));
    }

    #[Test]
    public function can_filter_by_different_months()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $nextMonth = now()->addMonth();

        $currentMonthItem = $this->createPayoutItemWithRelations([
            'scheduled_payout_date' => now()->day(15),
        ]);

        $nextMonthItem = $this->createPayoutItemWithRelations([
            'scheduled_payout_date' => $nextMonth->copy()->day(15),
        ]);

        Livewire::test(ReferralReview::class)
            ->assertSee($currentMonthItem->fresh()->enrollment->user->name)
            ->assertDontSee($nextMonthItem->fresh()->enrollment->user->name)
            ->set('selectedYear', $nextMonth->year)
            ->set('selectedMonth', $nextMonth->month)
            ->assertDontSee($currentMonthItem->fresh()->enrollment->user->name)
            ->assertSee($nextMonthItem->fresh()->enrollment->user->name);
    }

    #[Test]
    public function can_expand_referrer_to_see_individual_payout_items()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        // Create one enrollment with multiple payout items
        $referrerUser = User::factory()->influencer()->withProfile()->create();
        $referralEnrollment = ReferralEnrollment::factory()->create([
            'user_id' => $referrerUser->id,
        ]);

        $percentageHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'new_percentage' => 10,
        ]);

        // Create two different referred users
        $referredUser1 = User::factory()->influencer()->withProfile()->create(['name' => 'Referred User 1']);
        $referredUser2 = User::factory()->influencer()->withProfile()->create(['name' => 'Referred User 2']);

        $referral1 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser->id,
            'referred_user_id' => $referredUser1->id,
            'status' => ReferralStatus::ACTIVE,
        ]);

        $referral2 = Referral::factory()->create([
            'referrer_user_id' => $referrerUser->id,
            'referred_user_id' => $referredUser2->id,
            'status' => ReferralStatus::ACTIVE,
        ]);

        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral1->id,
            'referral_percentage_history_id' => $percentageHistory->id,
            'scheduled_payout_date' => now()->day(15),
        ]);

        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral2->id,
            'referral_percentage_history_id' => $percentageHistory->id,
            'scheduled_payout_date' => now()->day(15),
        ]);

        // Initially, individual items should not be visible
        Livewire::test(ReferralReview::class)
            ->assertSee($referrerUser->name)
            ->assertSee('2 referrals')
            ->assertDontSee('Referred User 1')
            ->assertDontSee('Referred User 2')
            // Expand the enrollment
            ->call('toggleEnrollment', $referralEnrollment->id)
            ->assertSee('Referred User 1')
            ->assertSee('Referred User 2')
            // Collapse the enrollment
            ->call('toggleEnrollment', $referralEnrollment->id)
            ->assertDontSee('Referred User 1')
            ->assertDontSee('Referred User 2');
    }

    #[Test]
    public function can_add_note_to_payout_item()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $payoutItem = $this->createPayoutItemWithRelations();

        Livewire::test(ReferralReview::class)
            ->call('openNotesModal', $payoutItem->id)
            ->set('noteText', 'This is a test note')
            ->call('addNote');

        $this->assertDatabaseHas('referral_payout_item_notes', [
            'referral_payout_item_id' => $payoutItem->id,
            'user_id' => $admin->id,
            'note' => 'This is a test note',
        ]);
    }

    #[Test]
    public function cannot_add_empty_note()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $payoutItem = $this->createPayoutItemWithRelations();

        Livewire::test(ReferralReview::class)
            ->call('openNotesModal', $payoutItem->id)
            ->set('noteText', '')
            ->call('addNote');

        $this->assertEquals(0, ReferralPayoutItemNote::count());
    }

    #[Test]
    public function can_open_and_close_notes_modal()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $payoutItem = $this->createPayoutItemWithRelations();

        // Add a note to the payout item
        ReferralPayoutItemNote::create([
            'referral_payout_item_id' => $payoutItem->id,
            'user_id' => $admin->id,
            'note' => 'Existing note',
        ]);

        Livewire::test(ReferralReview::class)
            // Modal should not be open initially
            ->assertSet('notesModalItemId', null)
            // Open the modal
            ->call('openNotesModal', $payoutItem->id)
            ->assertSet('notesModalItemId', $payoutItem->id)
            ->assertSee('Existing note')
            // Close the modal
            ->call('closeNotesModal')
            ->assertSet('notesModalItemId', null);
    }

    #[Test]
    public function displays_note_count_when_notes_exist()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $payoutItem = $this->createPayoutItemWithRelations();

        // Add multiple notes
        ReferralPayoutItemNote::create([
            'referral_payout_item_id' => $payoutItem->id,
            'user_id' => $admin->id,
            'note' => 'First note',
        ]);

        ReferralPayoutItemNote::create([
            'referral_payout_item_id' => $payoutItem->id,
            'user_id' => $admin->id,
            'note' => 'Second note',
        ]);

        Livewire::test(ReferralReview::class)
            ->call('toggleEnrollment', $payoutItem->referral_enrollment_id)
            ->assertSee('Notes', false)
            ->assertSee('(2)', false);
    }

    #[Test]
    public function note_displays_author_and_timestamp()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN, 'name' => 'Admin User']);
        $this->actingAs($admin);

        $payoutItem = $this->createPayoutItemWithRelations();

        $note = ReferralPayoutItemNote::create([
            'referral_payout_item_id' => $payoutItem->id,
            'user_id' => $admin->id,
            'note' => 'Test note content',
        ]);

        Livewire::test(ReferralReview::class)
            ->call('openNotesModal', $payoutItem->id)
            ->assertSee('Test note content')
            ->assertSee('Admin User');
    }
}
