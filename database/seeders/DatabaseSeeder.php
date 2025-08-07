<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
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
            'email' => env('INIT_BUSINESS_EMAIL', 'business@example.com'),
            'password' => Hash::make('password'),
        ]);

        // Create influencer user with complete profile
        $influencerUser = User::factory()->influencer()->withProfile()->create([
            'name' => 'Alex Rivera',
            'email' => env('INIT_INFLUENCER_EMAIL', 'influencer@example.com'),
            'password' => Hash::make('password'),
        ]);

        // Create social media accounts for the influencer
        \Database\Factories\SocialMediaAccountFactory::new()->instagram()->primary()->create([
            'user_id' => $influencerUser->id,
            'username' => 'foodiegram',
            'follower_count' => 25000,
        ]);

        \Database\Factories\SocialMediaAccountFactory::new()->tiktok()->create([
            'user_id' => $influencerUser->id,
            'username' => 'foodiegram_alex',
            'follower_count' => 18000,
        ]);

        \Database\Factories\SocialMediaAccountFactory::new()->youtube()->create([
            'user_id' => $influencerUser->id,
            'username' => 'AlexFoodieReviews',
            'follower_count' => 5000,
        ]);

        // Import postal codes for proximity search
        // $this->command->info('Importing postal codes...');
        // Artisan::call('collabconnect:import-postal-codes', [
        //     '--chunk' => 2000,
        // ]);
        // $this->command->info('Postal codes imported successfully.');

        // Create additional test users using AccountSeeder
        // $this->call(AccountSeeder::class);

        // Create sample campaigns for testing
        $this->call(CampaignSeeder::class);

        // Create BIGGBY campaign template
        $this->call(BiggbysCampaignTemplateSeeder::class);
    }
}
