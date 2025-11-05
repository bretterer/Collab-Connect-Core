<?php

namespace Tests\Feature\Livewire\Profile\Referrals;

use App\Enums\ReferralStatus;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPercentageHistory;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReferralEnrollmentStatesTest extends TestCase
{
    #[Test]
    public function a_business_owner_who_is_not_subscribed_sees_ineligible_state()
    {
        $businessUser = User::factory()->business()->withProfile()->create();

        $this->actingAs($businessUser);

        Livewire::test(\App\Livewire\Profile\Referrals::class)
            ->assertStatus(200)
            ->assertSeeText('Join Our Referral Program')
            ->assertSeeText('To join our referral program, you need to be an active subscriber')
            ->assertDontSeeText('Your Referral Link');
    }

    #[Test]
    public function a_business_member_sees_owner_not_enrolled_state_when_owner_hasnt_enrolled()
    {
        // Create business owner
        $owner = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $owner->currentBusiness;

        // Create non-owner business member
        $member = User::factory()->create([
            'account_type' => \App\Enums\AccountType::BUSINESS,
        ]);

        // Add member to business
        \App\Models\BusinessUser::create([
            'business_id' => $business->id,
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        $member->setCurrentBusiness($business);

        // Act as the member
        $this->actingAs($member);

        // Test - should see owner not enrolled state
        Livewire::test(\App\Livewire\Profile\Referrals::class)
            ->assertStatus(200)
            ->assertSee('Referral Program Available')
            ->assertSee('Your business is eligible for the referral program')
            ->assertSee('been set up yet by the business owner')
            ->assertSee('Action Required')
            ->assertSeeText('contact your business owner to enroll');
    }

    #[Test]
    public function an_influencer_who_is_not_subscribed_sees_ineligible_state()
    {
        $influencerUser = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($influencerUser);

        Livewire::test(\App\Livewire\Profile\Referrals::class)
            ->assertStatus(200)
            ->assertSeeText('Join Our Referral Program')
            ->assertSeeText('To join our referral program, you need to be an active subscriber')
            ->assertDontSeeText('Your Referral Link');
    }

    #[Test]
    public function an_influencer_who_is_subscribed_sees_enrollment_prompt()
    {
        $influencerUser = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($influencerUser);

        Livewire::test(\App\Livewire\Profile\Referrals::class)
            ->assertStatus(200)
            ->assertSeeText('You qualify for our referral program')
            ->assertSeeText('Enroll in Referral Program');
    }

    #[Test]
    public function an_influencer_who_is_subscribed_can_enroll()
    {
        $influencerUser = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($influencerUser);

        Livewire::test(\App\Livewire\Profile\Referrals::class)
            ->assertStatus(200)
            ->assertSeeText('You qualify for our referral program')
            ->assertSeeText('Enroll in Referral Program')
            ->call('enrollInProgram')
            ->assertDontSeeText('Enroll in Referral Program')
            ->assertSeeText('Your Referral Link');
    }

    #[Test]
    public function referral_page_shows_correct_stats()
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
        \App\Models\Referral::factory()->count(3)->create([
            'referrer_user_id' => $influencerUser->id,
            'status' => ReferralStatus::PENDING,
        ]);

        \App\Models\Referral::factory()->count(2)->create([
            'referrer_user_id' => $influencerUser->id,
            'status' => ReferralStatus::ACTIVE,
        ]);

        \App\Models\Referral::factory()->count(1)->create([
            'referrer_user_id' => $influencerUser->id,
            'status' => ReferralStatus::CHURNED,
        ]);

        Livewire::test(\App\Livewire\Profile\Referrals::class)
            ->assertStatus(200)
            ->assertSet('stats.pending_count', 3)
            ->assertSet('stats.total_count', 5)
            ->assertSet('stats.active_count', 2);
    }

    #[Test]
    public function pending_payout_is_reported_correctly() {}
}
