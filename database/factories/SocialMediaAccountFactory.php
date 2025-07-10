<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialMediaAccount>
 */
class SocialMediaAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platforms = ['instagram', 'tiktok', 'youtube', 'facebook', 'x'];
        $platform = fake()->randomElement($platforms);
        $username = fake()->userName();

        return [
            'platform' => $platform,
            'username' => $username,
            'url' => $this->generateUrl($platform, $username),
            'follower_count' => fake()->numberBetween(100, 100000),
            'is_primary' => false, // Will be set explicitly when needed
            'is_verified' => fake()->boolean(10), // 10% chance of being verified
        ];
    }

    /**
     * Generate the appropriate URL for the platform and username.
     */
    private function generateUrl(string $platform, string $username): string
    {
        $baseUrls = [
            'instagram' => 'https://instagram.com/',
            'tiktok' => 'https://tiktok.com/@',
            'youtube' => 'https://youtube.com/@',
            'facebook' => 'https://facebook.com/',
            'x' => 'https://x.com/',
            'linkedin' => 'https://linkedin.com/in/',
            'pinterest' => 'https://pinterest.com/',
            'snapchat' => 'https://snapchat.com/add/',
        ];

        return ($baseUrls[$platform] ?? 'https://example.com/').$username;
    }

    /**
     * Indicate that this is the primary social media account.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that this account is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }

    /**
     * Create an Instagram account.
     */
    public function instagram(): static
    {
        $username = fake()->userName();

        return $this->state(fn (array $attributes) => [
            'platform' => 'instagram',
            'username' => $username,
            'url' => 'https://instagram.com/'.$username,
            'follower_count' => fake()->numberBetween(500, 50000),
        ]);
    }

    /**
     * Create a TikTok account.
     */
    public function tiktok(): static
    {
        $username = fake()->userName();

        return $this->state(fn (array $attributes) => [
            'platform' => 'tiktok',
            'username' => $username,
            'url' => 'https://tiktok.com/@'.$username,
            'follower_count' => fake()->numberBetween(1000, 100000),
        ]);
    }

    /**
     * Create a YouTube account.
     */
    public function youtube(): static
    {
        $username = fake()->userName();

        return $this->state(fn (array $attributes) => [
            'platform' => 'youtube',
            'username' => $username,
            'url' => 'https://youtube.com/@'.$username,
            'follower_count' => fake()->numberBetween(100, 25000),
        ]);
    }

    /**
     * Create an account with high follower count.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'follower_count' => fake()->numberBetween(50000, 500000),
            'is_verified' => fake()->boolean(60), // Popular accounts more likely to be verified
        ]);
    }

    /**
     * Create an account with low follower count (micro-influencer).
     */
    public function micro(): static
    {
        return $this->state(fn (array $attributes) => [
            'follower_count' => fake()->numberBetween(1000, 10000),
        ]);
    }
}
