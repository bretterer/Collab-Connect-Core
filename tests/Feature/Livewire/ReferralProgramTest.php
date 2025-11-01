<?php

namespace Tests\Feature\Livewire;

use App\Enums\AccountType;
use App\Livewire\Profile\Referrals;
use App\Models\BusinessUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class ReferralProgramTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function business_member_sees_owner_not_enrolled_state_when_owner_hasnt_enrolled()
    {
        // Create business owner
        $owner = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $owner->currentBusiness;

        // Create non-owner business member
        $member = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        // Add member to business
        BusinessUser::create([
            'business_id' => $business->id,
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        $member->setCurrentBusiness($business);

        // Act as the member
        $this->actingAs($member);

        // Test - should see owner not enrolled state
        Livewire::test(Referrals::class)
            ->assertStatus(200)
            ->assertSee('Referral Program Available')
            ->assertSee('Your business is eligible for the referral program, but it hasn\'t been set up yet by the business owner')
            ->assertSee('Action Required')
            ->assertSee('contact your business owner to enroll');
    }

    #[Test]
    public function business_member_sees_referral_link_when_owner_has_enrolled()
    {
        // Create business owner who is enrolled
        $owner = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $owner->currentBusiness;

        // Create non-owner business member
        $member = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        // Add member to business
        BusinessUser::create([
            'business_id' => $business->id,
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        $member->setCurrentBusiness($business);

        // Act as the member
        $this->actingAs($member);

        // Test - should see business member state with referral link
        Livewire::test(Referrals::class)
            ->assertStatus(200)
            ->assertSee('Business Referral Code')
            ->assertSee('Share your business\'s referral link')
            ->assertSee('All referral earnings go to the business owner')
            ->assertSee('Copy');
    }

    #[Test]
    public function not_eligible_state_shown_when_user_has_no_subscription()
    {
        // Create user without subscription
        $user = User::factory()->influencer()->withProfile()->create();

        $this->actingAs($user);

        // Test - should see not eligible state
        Livewire::test(Referrals::class)
            ->assertStatus(200)
            ->assertSee('Join Our Referral Program')
            ->assertSee('You\'re not quite eligible yet')
            ->assertSee('Active subscription (Business or Influencer plan)')
            ->assertSee('At least one completed subscription payment')
            ->assertSee('View Subscription Options');
    }

    #[Test]
    public function enrollment_state_shown_when_user_is_eligible_but_not_enrolled()
    {
        // Create subscribed user who hasn't enrolled
        $user = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($user);

        // Test - should see enrollment state
        Livewire::test(Referrals::class)
            ->assertStatus(200)
            ->assertSee('You\'re Eligible!')
            ->assertSee('Earn 10% of every subscription payment')
            ->assertSee('Share Your Link')
            ->assertSee('They Subscribe')
            ->assertSee('You Earn 10%')
            ->assertSee('Enroll in Referral Program');
    }

    #[Test]
    public function active_dashboard_shown_when_user_is_enrolled()
    {
        // Create subscribed and enrolled user
        $user = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($user);

        // Test - should see active dashboard
        Livewire::test(Referrals::class)
            ->set('isEnrolled', true) // Simulate enrolled state
            ->assertStatus(200)
            ->assertSee('Your Referral Link')
            ->assertSee('Pending Referrals')
            ->assertSee('Active Referrals')
            ->assertSee('Total Referrals')
            ->assertSee('Pending Payout')
            ->assertSee('Lifetime Earnings')
            ->assertSee('Program Details');
    }

    #[Test]
    public function business_owner_sees_enrollment_state_when_eligible_but_not_enrolled()
    {
        // Create business owner with subscription
        $owner = User::factory()->business()->withProfile()->subscribed()->create();

        $this->actingAs($owner);

        // Test - should see enrollment state (same as influencer)
        Livewire::test(Referrals::class)
            ->assertStatus(200)
            ->assertSee('You\'re Eligible!')
            ->assertSee('Enroll in Referral Program');
    }

    #[Test]
    public function business_owner_sees_active_dashboard_when_enrolled()
    {
        // Create business owner with subscription who is enrolled
        $owner = User::factory()->business()->withProfile()->subscribed()->create();

        $this->actingAs($owner);

        // Test - should see active dashboard with full stats
        Livewire::test(Referrals::class)
            ->set('isEnrolled', true)
            ->assertStatus(200)
            ->assertSee('Your Referral Link')
            ->assertSee('Pending Payout')
            ->assertSee('Lifetime Earnings')
            ->assertDontSee('contact your business owner'); // Should not see member messaging
    }

    #[Test]
    public function enroll_in_program_action_works()
    {
        // Create eligible user
        $user = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($user);

        // Test - enrollment action should work
        Livewire::test(Referrals::class)
            ->assertSee('Enroll in Referral Program')
            ->call('enrollInProgram')
            ->assertSet('isEnrolled', true);
    }

    #[Test]
    public function copy_referral_link_action_works()
    {
        // Create enrolled user
        $user = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($user);

        // Test - copy action should work
        Livewire::test(Referrals::class)
            ->set('isEnrolled', true)
            ->assertSet('copied', false)
            ->call('copyReferralLink')
            ->assertSet('copied', true)
            ->assertDispatched('link-copied');
    }

    #[Test]
    public function stats_are_displayed_correctly_in_active_dashboard()
    {
        // Create enrolled user
        $user = User::factory()->influencer()->withProfile()->subscribed()->create();

        $this->actingAs($user);

        $stats = [
            'pending_count' => 5,
            'active_count' => 15,
            'total_count' => 25,
            'pending_payout' => 125.50,
            'lifetime_earnings' => 1500.75,
        ];

        // Test - stats should be visible
        Livewire::test(Referrals::class)
            ->set('isEnrolled', true)
            ->set('stats', $stats)
            ->assertSee('5') // pending count
            ->assertSee('15') // active count
            ->assertSee('25') // total count
            ->assertSee('125.50') // pending payout
            ->assertSee('1,500.75'); // lifetime earnings (formatted with comma)
    }
}
