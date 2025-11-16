<?php

namespace Tests\Feature\Livewire\Referrals;

use App\Enums\PayoutStatus;
use App\Enums\ReferralStatus;
use App\Models\Referral;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayout;
use App\Models\ReferralPayoutItem;
use App\Models\ReferralPercentageHistory;
use App\Models\User;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReferralPayoutReportingTest extends TestCase
{
    #[Test]
    public function a_referrer_can_see_their_pending_payout_amount()
    {
        $influencerUser = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($influencerUser);
        Feature::activate('referral-program');

        $referralEnrollment = ReferralEnrollment::factory()->create([
            'user_id' => $influencerUser->id,
        ]);

        $referralPercentageHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
        ]);

        // Create referrals with different statuses
        $subscribedUser1 = User::factory()->influencer()->withProfile()->subscribed()->create();
        $subscribedUser2 = User::factory()->influencer()->withProfile()->subscribed()->create();

        $referral1 = Referral::factory()->create([
            'referrer_user_id' => $influencerUser->id,
            'referred_user_id' => $subscribedUser1->id,
            'status' => ReferralStatus::ACTIVE,
        ]);

        $referral2 = Referral::factory()->create([
            'referrer_user_id' => $influencerUser->id,
            'referred_user_id' => $subscribedUser2->id,
            'status' => ReferralStatus::ACTIVE,
        ]);

        // Create pending payout items (unprocessed)
        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral1->id,
            'referral_payout_id' => null, // Not yet processed
            'status' => PayoutStatus::DRAFT,
            'subscription_amount' => 1000.00,
            'referral_percentage' => 10,
            // amount will be calculated by factory: 1000 * 0.10 = 100.00
        ]);

        ReferralPayoutItem::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral2->id,
            'referral_payout_id' => null,
            'status' => PayoutStatus::PENDING,
            'subscription_amount' => 500.00,
            'referral_percentage' => 10,
            // amount will be calculated by factory: 500 * 0.10 = 50.00
        ]);

        // Create a pending payout (already aggregated but not yet processed)
        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 75.00,
        ]);

        // Expected total: 100.00 (item 1) + 50.00 (item 2) + 75.00 (payout) = 225.00
        $expectedPendingAmount = 225.00;

        Livewire::test(\App\Livewire\Referrals\Index::class)
            ->assertStatus(200)
            ->assertSet('stats.pending_payout', $expectedPendingAmount);
    }
}
