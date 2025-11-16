<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->admin()->create([
            'name' => env('INIT_USER_NAME', 'Admin User'),
            'email' => env('INIT_USER_EMAIL', 'admin@collabconnect.dev'),
            'password' => Hash::make(env('INIT_USER_PASSWORD', 'password')),
        ]);

        // Create business user with complete profile
        $businessUser = User::factory()->business()->withProfile()->create([
            'name' => 'Sarah Johnson',
            'email' => config('collabconnect.init_business_email', 'business@example.com'),
            'password' => Hash::make('password'),
        ]);

        // Create influencer user with complete profile
        $influencerUser = User::factory()->influencer()->withProfile()->create([
            'name' => 'Alex Rivera',
            'email' => env('INIT_INFLUENCER_EMAIL', 'influencer@example.com'),
            'password' => Hash::make('password'),
        ]);

        // Import postal codes for proximity search
        $this->call(PostalCodeSeeder::class);

        // Create additional test users using AccountSeeder
        $this->call(AccountSeeder::class);

        // Create sample campaigns for testing
        $this->call(CampaignSeeder::class);

        // Create campaign template
        $this->call(CampaignTemplateSeeder::class);

        // add some applications for testing
        $this->call(ApplicationSeeder::class);

        // Create sample messages for testing
        $this->call(MessageSeeder::class);

        // Pull in Stripe data for testing
        $this->call(StripeDataSeeder::class);

        // Create fake Stripe customers and subscriptions for testing
        // Business subscription
        $businessUser->currentBusiness->stripe_id = 'cus_test_business_'.uniqid();
        $businessUser->currentBusiness->pm_type = 'card';
        $businessUser->currentBusiness->pm_last_four = '4242';
        $businessUser->currentBusiness->save();

        $businessUser->currentBusiness->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_business_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => config('collabconnect.stripe.prices.business_pro'),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        // Influencer subscription
        $influencerUser->influencer->stripe_id = 'cus_test_influencer_'.uniqid();
        $influencerUser->influencer->pm_type = 'card';
        $influencerUser->influencer->pm_last_four = '4242';
        $influencerUser->influencer->save();

        $influencerUser->influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_influencer_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => config('collabconnect.stripe.prices.influencer_basic'),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        // Create referral enrollments and referrals for testing
        $this->call(ReferralSeeder::class, false, ['userId' => $businessUser->id, 'count' => 5, 'status' => 'active']);
        $this->call(ReferralSeeder::class, false, ['userId' => $businessUser->id, 'count' => 5, 'status' => 'pending']);
        $this->call(ReferralSeeder::class, false, ['userId' => $influencerUser->id, 'count' => 3, 'status' => 'active']);
        $this->call(ReferralSeeder::class, false, ['userId' => $influencerUser->id, 'count' => 3, 'status' => 'pending']);

    }
}
