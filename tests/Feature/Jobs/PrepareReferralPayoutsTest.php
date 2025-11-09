<?php

namespace Tests\Feature\Jobs;

use App\Enums\PayoutStatus;
use App\Jobs\PrepareReferralPayouts;
use App\Models\Referral;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayout;
use App\Models\ReferralPayoutItem;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PrepareReferralPayoutsTest extends TestCase
{
    #[Test]
    public function processes_draft_pending_and_approved_items()
    {
        // Create enrollment with items in different statuses
        $enrollment = ReferralEnrollment::factory()->create();

        // Create different referrals to avoid unique constraint violation
        $referral1 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);
        $referral2 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);
        $referral3 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);
        $referral4 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);

        // Create items in different statuses that should be processed
        $draftItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral1->id,
            'status' => PayoutStatus::DRAFT,
            'amount' => 10.00,
            'scheduled_payout_date' => now()->addDays(15),
        ]);

        $pendingItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral2->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 15.00,
            'scheduled_payout_date' => now()->addDays(15),
        ]);

        $approvedItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral3->id,
            'status' => PayoutStatus::APPROVED,
            'amount' => 20.00,
            'scheduled_payout_date' => now()->addDays(15),
        ]);

        // Create an item that should NOT be processed
        $processedItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral4->id,
            'status' => PayoutStatus::PROCESSED,
            'amount' => 5.00,
            'scheduled_payout_date' => now()->addDays(15),
        ]);

        // Run the job
        (new PrepareReferralPayouts)->handle();

        // Assert all draft/pending/approved items are now processed
        $this->assertDatabaseHas('referral_payout_items', [
            'id' => $draftItem->id,
            'status' => PayoutStatus::PROCESSED->value,
        ]);

        $this->assertDatabaseHas('referral_payout_items', [
            'id' => $pendingItem->id,
            'status' => PayoutStatus::PROCESSED->value,
        ]);

        $this->assertDatabaseHas('referral_payout_items', [
            'id' => $approvedItem->id,
            'status' => PayoutStatus::PROCESSED->value,
        ]);

        // Assert the already processed item remains unchanged
        $this->assertDatabaseHas('referral_payout_items', [
            'id' => $processedItem->id,
            'status' => PayoutStatus::PROCESSED->value,
        ]);
    }

    #[Test]
    public function creates_referral_payout_with_correct_totals()
    {
        $enrollment = ReferralEnrollment::factory()->create();
        $referral1 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);
        $referral2 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);

        // Create multiple items for the same enrollment
        $item1 = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral1->id,
            'status' => PayoutStatus::DRAFT,
            'scheduled_payout_date' => now()->month(3)->day(15)->year(2025),
        ]);

        $item2 = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral2->id,
            'status' => PayoutStatus::APPROVED,
            'scheduled_payout_date' => now()->month(3)->day(15)->year(2025),
        ]);

        $expectedTotal = $item1->amount + $item2->amount;

        // Run the job
        (new PrepareReferralPayouts)->handle();

        // Assert a ReferralPayout was created with correct totals
        $payout = ReferralPayout::where('referral_enrollment_id', $enrollment->id)->first();
        $this->assertNotNull($payout);
        $this->assertEqualsWithDelta($expectedTotal, (float) $payout->amount, 0.01);
        $this->assertEquals(PayoutStatus::PENDING, $payout->status);
        $this->assertEquals(3, $payout->month);
        $this->assertEquals(2025, $payout->year);
        $this->assertEquals(2, $payout->referral_count);
    }

    #[Test]
    public function links_payout_items_to_created_payout()
    {
        $enrollment = ReferralEnrollment::factory()->create();
        $referral1 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);
        $referral2 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);

        $item1 = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral1->id,
            'status' => PayoutStatus::DRAFT,
            'referral_payout_id' => null,
        ]);

        $item2 = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral2->id,
            'status' => PayoutStatus::PENDING,
            'referral_payout_id' => null,
        ]);

        // Run the job
        (new PrepareReferralPayouts)->handle();

        // Get the created payout
        $payout = ReferralPayout::where('referral_enrollment_id', $enrollment->id)->first();
        $this->assertNotNull($payout);

        // Assert both items are linked to the payout
        $this->assertDatabaseHas('referral_payout_items', [
            'id' => $item1->id,
            'referral_payout_id' => $payout->id,
            'status' => PayoutStatus::PROCESSED->value,
        ]);

        $this->assertDatabaseHas('referral_payout_items', [
            'id' => $item2->id,
            'referral_payout_id' => $payout->id,
            'status' => PayoutStatus::PROCESSED->value,
        ]);
    }

    #[Test]
    public function groups_items_by_enrollment_correctly()
    {
        // Create two different enrollments
        $enrollment1 = ReferralEnrollment::factory()->create();
        $enrollment2 = ReferralEnrollment::factory()->create();

        $referral1a = Referral::factory()->create(['referrer_user_id' => $enrollment1->user_id]);
        $referral1b = Referral::factory()->create(['referrer_user_id' => $enrollment1->user_id]);
        $referral2a = Referral::factory()->create(['referrer_user_id' => $enrollment2->user_id]);
        $referral2b = Referral::factory()->create(['referrer_user_id' => $enrollment2->user_id]);

        // Create items for enrollment 1
        $item1a = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'referral_id' => $referral1a->id,
            'status' => PayoutStatus::DRAFT,
        ]);

        $item1b = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'referral_id' => $referral1b->id,
            'status' => PayoutStatus::APPROVED,
        ]);

        // Create items for enrollment 2
        $item2a = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
            'referral_id' => $referral2a->id,
            'status' => PayoutStatus::DRAFT,
        ]);

        $item2b = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
            'referral_id' => $referral2b->id,
            'status' => PayoutStatus::PENDING,
        ]);

        $expectedTotal1 = $item1a->amount + $item1b->amount;
        $expectedTotal2 = $item2a->amount + $item2b->amount;

        // Run the job
        (new PrepareReferralPayouts)->handle();

        // Assert two separate payouts were created
        $payout1 = ReferralPayout::where('referral_enrollment_id', $enrollment1->id)->first();
        $payout2 = ReferralPayout::where('referral_enrollment_id', $enrollment2->id)->first();

        $this->assertNotNull($payout1);
        $this->assertNotNull($payout2);

        // Assert correct amounts
        $this->assertEquals($expectedTotal1, (float) $payout1->amount);
        $this->assertEquals($expectedTotal2, (float) $payout2->amount);

        // Assert correct counts
        $this->assertEquals(2, $payout1->referral_count);
        $this->assertEquals(2, $payout2->referral_count);
    }

    #[Test]
    public function handles_empty_items_gracefully()
    {
        // Ensure no items exist in processable states
        ReferralPayoutItem::query()->delete();

        // Run the job
        (new PrepareReferralPayouts)->handle();

        // Assert no payouts were created
        $this->assertEquals(0, ReferralPayout::count());
    }

    #[Test]
    public function uses_correct_month_and_year_from_scheduled_date()
    {
        $enrollment = ReferralEnrollment::factory()->create();
        $referral = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);

        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral->id,
            'status' => PayoutStatus::DRAFT,
            'amount' => 10.00,
            'scheduled_payout_date' => '2025-06-15',
        ]);

        // Run the job
        (new PrepareReferralPayouts)->handle();

        // Assert payout has correct month and year
        $this->assertDatabaseHas('referral_payouts', [
            'referral_enrollment_id' => $enrollment->id,
            'month' => 6,
            'year' => 2025,
        ]);
    }

    #[Test]
    public function sets_currency_from_payout_items()
    {
        $enrollment = ReferralEnrollment::factory()->create();
        $referral = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);

        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral->id,
            'status' => PayoutStatus::DRAFT,
            'amount' => 10.00,
            'currency' => 'CAD',
        ]);

        // Run the job
        (new PrepareReferralPayouts)->handle();

        // Assert payout has correct currency
        $this->assertDatabaseHas('referral_payouts', [
            'referral_enrollment_id' => $enrollment->id,
            'currency' => 'CAD',
        ]);
    }

    #[Test]
    public function does_not_process_cancelled_or_failed_items()
    {
        $enrollment = ReferralEnrollment::factory()->create();
        $referral1 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);
        $referral2 = Referral::factory()->create(['referrer_user_id' => $enrollment->user_id]);

        $cancelledItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral1->id,
            'status' => PayoutStatus::CANCELLED,
            'amount' => 10.00,
        ]);

        $failedItem = ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_id' => $referral2->id,
            'status' => PayoutStatus::FAILED,
            'amount' => 15.00,
        ]);

        // Run the job
        (new PrepareReferralPayouts)->handle();

        // Assert no payouts were created
        $this->assertEquals(0, ReferralPayout::count());

        // Assert items remain in their original status
        $this->assertDatabaseHas('referral_payout_items', [
            'id' => $cancelledItem->id,
            'status' => PayoutStatus::CANCELLED->value,
        ]);

        $this->assertDatabaseHas('referral_payout_items', [
            'id' => $failedItem->id,
            'status' => PayoutStatus::FAILED->value,
        ]);
    }
}
