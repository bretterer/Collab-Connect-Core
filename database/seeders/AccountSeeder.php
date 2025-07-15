<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\SocialPlatform;
use App\Models\SocialMediaAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 500 accounts randomly across business and influencer
        for ($i = 0; $i < 500; $i++) {
            $accountType = fake()->randomElement([AccountType::BUSINESS, AccountType::INFLUENCER]);

            if ($accountType == AccountType::BUSINESS) {
                /** @var User $user */
                $user = User::factory()->business()->withProfile()->create();
            } else {
                /** @var User $user */
                $user = User::factory()->influencer()->withProfile()->create();
                $this->createSocialMediaAccounts($user);
            }
        }
    }

    /**
     * Create random social media accounts for an influencer
     */
    private function createSocialMediaAccounts(User $user): void
    {
        // Determine follower tier (high, medium, low)
        $followerTier = fake()->randomElement(['low', 'medium', 'high']);

        // Get follower count ranges based on tier
        $followerRanges = [
            'low' => [1000, 10000],      // Micro-influencers
            'medium' => [10000, 100000], // Mid-tier influencers
            'high' => [100000, 1000000], // Macro-influencers
        ];

        [$minFollowers, $maxFollowers] = $followerRanges[$followerTier];

        // Get all available platforms
        $allPlatforms = SocialPlatform::cases();

        // Randomly select 1-4 platforms (no more than 1 of each type)
        $numPlatforms = fake()->numberBetween(1, 4);
        $selectedPlatforms = fake()->randomElements($allPlatforms, $numPlatforms);

        $isPrimarySet = false;

        foreach ($selectedPlatforms as $platform) {
            $username = $this->generateUsername($platform);
            $followerCount = fake()->numberBetween($minFollowers, $maxFollowers);

            // Add some variance to follower counts across platforms
            $variance = fake()->numberBetween(-20, 20); // ±20% variance
            $followerCount = max(100, $followerCount + ($followerCount * $variance / 100));

            SocialMediaAccount::factory()->create([
                'user_id' => $user->id,
                'platform' => $platform->value,
                'username' => $username,
                'url' => $platform->generateUrl($username),
                'follower_count' => $followerCount,
                'is_primary' => ! $isPrimarySet, // First account is primary
                'is_verified' => $this->shouldBeVerified($followerCount),
            ]);

            $isPrimarySet = true;
        }
    }

    /**
     * Generate a realistic username for a platform
     */
    private function generateUsername(SocialPlatform $platform): string
    {
        $baseUsername = fake()->userName();

        // Add platform-specific variations
        return match ($platform) {
            SocialPlatform::INSTAGRAM => fake()->randomElement([
                $baseUsername,
                $baseUsername.fake()->numberBetween(1, 999),
                fake()->firstName().fake()->lastName(),
            ]),
            SocialPlatform::TIKTOK => fake()->randomElement([
                $baseUsername,
                $baseUsername.fake()->numberBetween(1, 99),
                fake()->firstName().fake()->randomNumber(2),
            ]),
            SocialPlatform::YOUTUBE => fake()->randomElement([
                $baseUsername,
                fake()->firstName().'TV',
                fake()->word().'Channel',
            ]),
            SocialPlatform::X => fake()->randomElement([
                $baseUsername,
                fake()->firstName().fake()->randomNumber(2),
                fake()->word().fake()->randomNumber(3),
            ]),
            default => $baseUsername,
        };
    }

    /**
     * Determine if an account should be verified based on follower count
     */
    private function shouldBeVerified(int $followerCount): bool
    {
        // Higher follower counts have higher chance of verification
        return match (true) {
            $followerCount >= 500000 => fake()->boolean(80), // 80% chance for 500K+
            $followerCount >= 100000 => fake()->boolean(50), // 50% chance for 100K+
            $followerCount >= 50000 => fake()->boolean(20),  // 20% chance for 50K+
            $followerCount >= 10000 => fake()->boolean(5),   // 5% chance for 10K+
            default => fake()->boolean(1),                   // 1% chance for others
        };
    }
}
