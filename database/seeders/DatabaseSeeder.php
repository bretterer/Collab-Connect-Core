<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\InfluencerProfile;
use App\Models\SocialMediaAccount;
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
        $businessUser = User::factory()->withBusinessProfile()->create([
            'name' => 'Sarah Johnson',
            'email' => env('INIT_BUSINESS_EMAIL', 'business@example.com'),
            'password' => Hash::make('password'),
        ]);


        // Create influencer user with complete profile
        $influencerUser = User::factory()->withInfluencerProfile()->create([
            'name' => 'Alex Rivera',
            'email' => env('INIT_INFLUENCER_EMAIL', 'influencer@example.com'),
            'password' => Hash::make('password'),
        ]);

        // Create social media accounts for the influencer
        SocialMediaAccount::factory()->instagram()->primary()->create([
            'user_id' => $influencerUser->id,
            'username' => 'foodiegram',
            'follower_count' => 25000,
        ]);

        SocialMediaAccount::factory()->tiktok()->create([
            'user_id' => $influencerUser->id,
            'username' => 'foodiegram_alex',
            'follower_count' => 18000,
        ]);

        SocialMediaAccount::factory()->youtube()->create([
            'user_id' => $influencerUser->id,
            'username' => 'AlexFoodieReviews',
            'follower_count' => 5000,
        ]);

        // Create additional test users
        // $this->createAdditionalTestUsers();
    }

    /**
     * Create additional test users for development.
     */
    private function createAdditionalTestUsers(): void
    {
        // Create a few more business users
        for ($i = 0; $i < 3; $i++) {
            $businessUser = User::factory()->business()->create();
            BusinessProfile::factory()->create(['user_id' => $businessUser->id]);
        }

        // Create a franchise business
        $franchiseUser = User::factory()->business()->create([
            'name' => 'Michael Chen',
            'email' => 'michael@pizzapalace.com',
        ]);
        BusinessProfile::factory()->franchise()->create([
            'user_id' => $franchiseUser->id,
            'business_name' => 'Pizza Palace',
            'industry' => 'Restaurant',
            'location_count' => 8,
        ]);

        // Create several influencers with different niches and follower counts
        $influencerData = [
            [
                'name' => 'Emma Style',
                'email' => 'emma@stylista.com',
                'niche' => 'fashion',
                'factory_method' => 'midTierInfluencer',
                'accounts' => [
                    ['platform' => 'instagram', 'username' => 'emmastylista', 'followers' => 35000],
                    ['platform' => 'tiktok', 'username' => 'emmastyle', 'followers' => 28000],
                ],
            ],
            [
                'name' => 'Marcus Fit',
                'email' => 'marcus@fitlife.com',
                'niche' => 'fitness',
                'factory_method' => 'microInfluencer',
                'accounts' => [
                    ['platform' => 'instagram', 'username' => 'marcusfitlife', 'followers' => 8500],
                    ['platform' => 'youtube', 'username' => 'MarcusFitnessCoach', 'followers' => 3200],
                ],
            ],
            [
                'name' => 'Sofia Beauty',
                'email' => 'sofia@beautyworld.com',
                'niche' => 'beauty',
                'factory_method' => 'macroInfluencer',
                'accounts' => [
                    ['platform' => 'instagram', 'username' => 'sofiabeautyworld', 'followers' => 125000],
                    ['platform' => 'tiktok', 'username' => 'sofiabeauty', 'followers' => 89000],
                    ['platform' => 'youtube', 'username' => 'SofiaBeautyTutorials', 'followers' => 45000],
                ],
            ],
        ];

        foreach ($influencerData as $index => $data) {
            $influencer = User::factory()->influencer()->create([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            // Create influencer profile
            $profile = InfluencerProfile::factory()->{$data['factory_method']}()->create([
                'user_id' => $influencer->id,
                'creator_name' => $data['name'],
                'primary_niche' => $data['niche'],
            ]);

            // Create social media accounts
            foreach ($data['accounts'] as $accountIndex => $account) {
                SocialMediaAccount::factory()
                    ->{$account['platform']}()
                    ->when($accountIndex === 0, fn ($factory) => $factory->primary())
                    ->create([
                        'user_id' => $influencer->id,
                        'username' => $account['username'],
                        'follower_count' => $account['followers'],
                    ]);
            }
        }
    }
}
