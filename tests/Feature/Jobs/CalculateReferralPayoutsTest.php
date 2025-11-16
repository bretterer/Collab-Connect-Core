<?php

namespace Tests\Feature\Jobs;

use App\Enums\PayoutStatus;
use App\Enums\ReferralStatus;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayoutItem;
use App\Models\ReferralPercentageHistory;
use App\Models\StripePrice;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalculateReferralPayoutsTest extends TestCase
{
    #[Test]
    public function can_calculate_referral_payouts_correctly()
    {
        // Create influencer user with subscription
        $influencerUser = User::factory()->influencer()->withProfile()->create();

        // Create referred user with subscription
        $referredUser = User::factory()->influencer()->withProfile()->create();

        // Create a Stripe price that will be used by the subscription
        $stripePrice = StripePrice::factory()->create([
            'stripe_id' => 'price_test_123',
            'unit_amount' => 1000, // $10.00 (1000 cents)
        ]);

        // Create subscription for the referred user with the stripe price
        $referredUser->influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $stripePrice->stripe_id,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        $this->actingAs($influencerUser);

        $referralEnrollment = ReferralEnrollment::factory()->create([
            'user_id' => $influencerUser->id,
        ]);

        $referralPercentageHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'new_percentage' => 10,
        ]);

        $referral = \App\Models\Referral::factory()->create([
            'referrer_user_id' => $influencerUser->id,
            'referred_user_id' => $referredUser->id,
            'status' => ReferralStatus::ACTIVE,
        ]);

        (new \App\Jobs\CalculateReferralPayouts)->handle();

        $expectedPayoutDate = now()->day >= 15
            ? now()->addMonth()->day(15)->toDateString()
            : now()->day(15)->toDateString();

        $this->assertDatabaseHas('referral_payout_items', [
            'referral_payout_id' => null,
            'referral_enrollment_id' => $referralEnrollment->id,
            'referral_id' => $referral->id,
            'referral_percentage_history_id' => $referralPercentageHistory->id,
            'subscription_amount' => '10.00', // 1000 cents = $10.00
            'referral_percentage' => 10,
            'amount' => '1.00', // $10.00 * 10% = $1.00
            'currency' => 'USD',
            'scheduled_payout_date' => $expectedPayoutDate,
            'status' => PayoutStatus::DRAFT->value,
        ]);
    }

    #[Test]
    public function does_not_create_duplicate_payout_items_when_run_multiple_times()
    {
        // Create influencer user with subscription
        $influencerUser = User::factory()->influencer()->withProfile()->create();

        // Create referred user with subscription
        $referredUser = User::factory()->influencer()->withProfile()->create();

        // Create a Stripe price that will be used by the subscription
        $stripePrice = StripePrice::factory()->create([
            'stripe_id' => 'price_test_456',
            'unit_amount' => 1000, // $10.00 (1000 cents)
        ]);

        // Create subscription for the referred user with the stripe price
        $referredUser->influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $stripePrice->stripe_id,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        $this->actingAs($influencerUser);

        $referralEnrollment = ReferralEnrollment::factory()->create([
            'user_id' => $influencerUser->id,
        ]);

        $referralPercentageHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
            'new_percentage' => 10,
        ]);

        $referral = \App\Models\Referral::factory()->create([
            'referrer_user_id' => $influencerUser->id,
            'referred_user_id' => $referredUser->id,
            'status' => ReferralStatus::ACTIVE,
        ]);

        // Run the job first time
        (new \App\Jobs\CalculateReferralPayouts)->handle();

        $initialCount = ReferralPayoutItem::count();
        $this->assertEquals(1, $initialCount, 'Expected 1 payout item after first run');

        // Run the job again
        (new \App\Jobs\CalculateReferralPayouts)->handle();

        $secondCount = ReferralPayoutItem::count();
        $this->assertEquals(1, $secondCount, 'Expected still only 1 payout item after second run');

        // Run the job a third time for good measure
        (new \App\Jobs\CalculateReferralPayouts)->handle();

        $thirdCount = ReferralPayoutItem::count();
        $this->assertEquals(1, $thirdCount, 'Expected still only 1 payout item after third run');
    }
}
