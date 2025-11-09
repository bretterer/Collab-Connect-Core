<?php

namespace Tests\Unit\Unit\Models;

use App\Enums\PayoutStatus;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayout;
use App\Models\ReferralPayoutItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReferralEnrollmentPendingPayoutsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_calculates_pending_payout_amount_from_items_only(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        // Create unprocessed payout items
        // Note: Factory will recalculate amount based on subscription_amount and referral_percentage
        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_payout_id' => null, // Not yet processed
            'status' => PayoutStatus::DRAFT,
            'subscription_amount' => 1000.00,
            'referral_percentage' => 10,
        ]);

        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_payout_id' => null,
            'status' => PayoutStatus::PENDING,
            'subscription_amount' => 1500.00,
            'referral_percentage' => 10,
        ]);

        $this->assertEquals(250.00, $enrollment->getPendingPayoutAmount());
    }

    #[Test]
    public function it_calculates_pending_payout_amount_from_payouts_only(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 200.00,
        ]);

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 300.00,
        ]);

        $this->assertEquals(500.00, $enrollment->getPendingPayoutAmount());
    }

    #[Test]
    public function it_calculates_pending_payout_amount_from_both_items_and_payouts(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        // Unprocessed items
        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_payout_id' => null,
            'status' => PayoutStatus::DRAFT,
            'subscription_amount' => 1000.00,
            'referral_percentage' => 10,
        ]);

        // Pending payouts
        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 200.00,
        ]);

        $this->assertEquals(300.00, $enrollment->getPendingPayoutAmount());
    }

    #[Test]
    public function it_excludes_processed_items_from_pending_amount(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();
        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PAID,
            'amount' => 100.00,
        ]);

        // This item is already processed (has referral_payout_id)
        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_payout_id' => $payout->id,
            'status' => PayoutStatus::PAID,
            'subscription_amount' => 1000.00,
            'referral_percentage' => 10,
        ]);

        // Only unprocessed item should count
        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_payout_id' => null,
            'status' => PayoutStatus::PENDING,
            'subscription_amount' => 500.00,
            'referral_percentage' => 10,
        ]);

        $this->assertEquals(50.00, $enrollment->getPendingPayoutAmount());
    }

    #[Test]
    public function it_excludes_non_pending_payouts_from_amount(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PAID,
            'amount' => 200.00,
        ]);

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::FAILED,
            'amount' => 150.00,
        ]);

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        $this->assertEquals(100.00, $enrollment->getPendingPayoutAmount());
    }

    #[Test]
    public function it_detects_pending_payouts_from_items(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        $this->assertFalse($enrollment->hasPendingPayouts());

        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'referral_payout_id' => null,
            'status' => PayoutStatus::DRAFT,
            'subscription_amount' => 500.00,
            'referral_percentage' => 10,
        ]);

        $this->assertTrue($enrollment->hasPendingPayouts());
    }

    #[Test]
    public function it_detects_pending_payouts_from_payouts(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        $this->assertFalse($enrollment->hasPendingPayouts());

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        $this->assertTrue($enrollment->hasPendingPayouts());
    }

    #[Test]
    public function get_pending_payout_returns_payout_for_current_month(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        $this->assertEquals($payout->id, $enrollment->getPendingPayout()->id);
    }

    #[Test]
    public function get_pending_payout_returns_null_for_different_month(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'month' => now()->subMonth()->month,
            'year' => now()->year,
        ]);

        $this->assertNull($enrollment->getPendingPayout());
    }

    #[Test]
    public function get_pending_payout_returns_null_for_non_pending_status(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PAID,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        $this->assertNull($enrollment->getPendingPayout());
    }
}
