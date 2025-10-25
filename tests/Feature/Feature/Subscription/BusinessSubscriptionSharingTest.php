<?php

namespace Tests\Feature\Feature\Subscription;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessSubscriptionSharingTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_follows_business_not_individual_users()
    {
        // 1: Create business user 1
        $businessUser1 = User::factory()->business()->withProfile()->create();
        $business1 = $businessUser1->currentBusiness;

        // 2: Create business user 2
        $businessUser2 = User::factory()->business()->withProfile()->create();

        // 3: Attach business user 2 to Business from business user 1
        $business1->users()->attach($businessUser2->id, ['role' => 'member']);

        // 4: Subscribe Business user 1 to a plan
        $business1->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.\Illuminate\Support\Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_'.\Illuminate\Support\Str::random(14),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        // 5: Validate profile is subscribed
        $this->assertTrue($businessUser1->profile->subscribed('default'));

        // 6: Log in Business User 2 (simulated by acting as them)
        $this->actingAs($businessUser2);

        // 7: setCurrentBusiness of user 2 to business 1
        $businessUser2->setCurrentBusiness($business1);

        // 8: Validate profile of business user 2 is subscribed
        $this->assertTrue($businessUser2->profile->subscribed('default'));
    }

    public function test_multiple_business_members_all_benefit_from_subscription()
    {
        // Create a business with owner
        $owner = User::factory()->business()->withProfile()->create();
        $business = $owner->currentBusiness;

        // Add three more members to the business
        $member1 = User::factory()->business()->withProfile()->create();
        $member2 = User::factory()->business()->withProfile()->create();
        $member3 = User::factory()->business()->withProfile()->create();

        $business->users()->attach($member1->id, ['role' => 'member']);
        $business->users()->attach($member2->id, ['role' => 'member']);
        $business->users()->attach($member3->id, ['role' => 'admin']);

        // Business should not be subscribed initially
        $this->assertFalse($business->subscribed('default'));
        $this->assertFalse($owner->profile->subscribed('default'));

        // Subscribe the business
        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.\Illuminate\Support\Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_'.\Illuminate\Support\Str::random(14),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        // Clear the owner's cached profile since we just added a subscription
        unset($owner->profile);

        // Set members' current business to the subscribed business
        $member1->setCurrentBusiness($business);
        $member2->setCurrentBusiness($business);
        $member3->setCurrentBusiness($business);

        // All users should now be subscribed via the business
        $this->assertTrue($owner->profile->subscribed('default'));
        $this->assertTrue($member1->profile->subscribed('default'));
        $this->assertTrue($member2->profile->subscribed('default'));
        $this->assertTrue($member3->profile->subscribed('default'));
    }

    public function test_user_loses_subscription_when_leaving_subscribed_business()
    {
        // Create a subscribed business
        $owner = User::factory()->business()->withProfile()->subscribed()->create();
        $business = $owner->currentBusiness;

        // Create a member
        $member = User::factory()->business()->withProfile()->create();
        $memberOriginalBusiness = $member->currentBusiness;

        $business->users()->attach($member->id, ['role' => 'member']);
        $member->setCurrentBusiness($business);

        // Member should be subscribed via business
        $this->assertTrue($member->profile->subscribed('default'));

        // Remove member from the business and set back to their original business
        $business->users()->detach($member->id);
        $member->setCurrentBusiness($memberOriginalBusiness);

        // Member should no longer be subscribed (their original business has no subscription)
        $this->assertFalse($member->profile->subscribed('default'));
    }

    public function test_user_gains_subscription_when_joining_subscribed_business()
    {
        // Create a user without subscription
        $user = User::factory()->business()->withProfile()->create();
        $originalBusiness = $user->currentBusiness;

        // Verify user is not subscribed
        $this->assertFalse($user->profile->subscribed('default'));

        // Create a different business that IS subscribed
        $subscribedBusinessOwner = User::factory()->business()->withProfile()->subscribed()->create();
        $subscribedBusiness = $subscribedBusinessOwner->currentBusiness;

        // Verify the subscribed business is subscribed
        $this->assertTrue($subscribedBusiness->subscribed('default'));

        // Remove user from their original business
        $originalBusiness->users()->detach($user->id);

        // Add the user to the subscribed business
        $subscribedBusiness->users()->attach($user->id, ['role' => 'member']);
        $user->setCurrentBusiness($subscribedBusiness);

        // User should now be subscribed via the new business
        $this->assertTrue($user->profile->subscribed('default'));
    }

    public function test_subscription_is_business_scoped_not_user_scoped()
    {
        // Create two separate businesses
        $business1Owner = User::factory()->business()->withProfile()->create();
        $business1 = $business1Owner->currentBusiness;

        $business2Owner = User::factory()->business()->withProfile()->create();
        $business2 = $business2Owner->currentBusiness;

        // Subscribe only business1
        $business1->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.\Illuminate\Support\Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_'.\Illuminate\Support\Str::random(14),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        $business1Owner->refresh();
        $business2Owner->refresh();
        $business1->refresh();
        $business2->refresh();

        // Verify business1 owner is subscribed
        $this->assertTrue($business1Owner->profile->subscribed('default'));

        // Verify business2 owner is NOT subscribed
        $this->assertFalse($business2Owner->profile->subscribed('default'));

        // Verify the businesses themselves have correct subscription status
        $this->assertTrue($business1->subscribed('default'));
        $this->assertFalse($business2->subscribed('default'));
    }

    public function test_influencer_subscriptions_are_user_scoped_not_business_scoped()
    {
        // Create two influencer users
        $influencer1 = User::factory()->influencer()->withProfile()->create();
        $influencer2 = User::factory()->influencer()->withProfile()->create();

        // Subscribe only influencer1
        $influencer1->influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.\Illuminate\Support\Str::random(14),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_'.\Illuminate\Support\Str::random(14),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        $influencer1->refresh();
        $influencer2->refresh();

        // Verify influencer1 is subscribed
        $this->assertTrue($influencer1->profile->subscribed('default'));

        // Verify influencer2 is NOT subscribed (different user = different subscription)
        $this->assertFalse($influencer2->profile->subscribed('default'));

        // Verify the influencer profiles themselves have correct subscription status
        $this->assertTrue($influencer1->influencer->subscribed('default'));
        $this->assertFalse($influencer2->influencer->subscribed('default'));
    }
}
