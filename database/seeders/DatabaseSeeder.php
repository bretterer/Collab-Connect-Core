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
    }
}
