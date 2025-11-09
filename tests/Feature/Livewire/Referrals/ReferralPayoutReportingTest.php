<?php

namespace Tests\Feature\Livewire\Referrals;

use App\Enums\ReferralStatus;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPercentageHistory;
use App\Models\User;
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

        $referralEnrollment = ReferralEnrollment::factory()->create([
            'user_id' => $influencerUser->id,
        ]);

        $referralPercentageHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $referralEnrollment->id,
        ]);

        // Create referrals with different statuses
        $subscribedUser1 = User::factory()->influencer()->withProfile()->subscribed()->create();
        $subscribedUser2 = User::factory()->influencer()->withProfile()->subscribed()->create();

        \App\Models\Referral::factory()->create([
            'referrer_user_id' => $influencerUser->id,
            'referred_user_id' => $subscribedUser1->id,
            'status' => ReferralStatus::ACTIVE,
        ]);

        \App\Models\Referral::factory()->create([
            'referrer_user_id' => $influencerUser->id,
            'referred_user_id' => $subscribedUser2->id,
            'status' => ReferralStatus::PENDING,
        ]);

        $this->markTestIncomplete('Pending payout calculation logic needs to be implemented');

        Livewire::test(\App\Livewire\Referrals\Index::class)
            ->assertStatus(200)
            ->assertSet('stats.pending_payout', 123);
    }
}
