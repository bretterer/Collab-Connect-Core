<?php

namespace Database\Factories;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompensationType;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Influencer>
 */
class InfluencerFactory extends Factory
{
    protected $model = Influencer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => $this->faker->paragraph(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'county' => $this->faker->optional()->word(),
            'postal_code' => $this->faker->postcode(),
            'phone_number' => $this->faker->phoneNumber(),
            'content_types' => $this->faker->randomElements(
                array_column(BusinessIndustry::cases(), 'value'),
                $this->faker->numberBetween(1, 3)
            ),
            'preferred_business_types' => $this->faker->randomElements(
                array_column(BusinessType::cases(), 'value'),
                $this->faker->numberBetween(1, 2)
            ),
            'compensation_types' => $this->faker->randomElements(
                array_column(CompensationType::cases(), 'value'),
                $this->faker->numberBetween(1, 3)
            ),
            'typical_lead_time_days' => $this->faker->numberBetween(1, 30),
        ];
    }

    /**
     * Indicate that the influencer has completed onboarding.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'onboarding_complete' => true,
        ]);
    }

    /**
     * Create an influencer with specific content types.
     */
    public function withContentTypes(array $types): static
    {
        return $this->state(fn (array $attributes) => [
            'content_types' => $types,
        ]);
    }

    /**
     * Create an influencer with specific business type preferences.
     */
    public function withBusinessTypePreferences(array $types): static
    {
        return $this->state(fn (array $attributes) => [
            'preferred_business_types' => $types,
        ]);
    }

    /**
     * Create an influencer with specific compensation preferences.
     */
    public function withCompensationTypes(array $types): static
    {
        return $this->state(fn (array $attributes) => [
            'compensation_types' => $types,
        ]);
    }

    /**
     * Create an influencer with social media accounts.
     */
    public function withSocialAccounts(): static
    {
        return $this->afterCreating(function (Influencer $influencer) {
            $platforms = ['instagram', 'tiktok', 'youtube', 'facebook'];
            $selectedPlatforms = $this->faker->randomElements($platforms, $this->faker->numberBetween(1, 3));

            foreach ($selectedPlatforms as $platform) {
                \App\Models\InfluencerSocial::create([
                    'influencer_id' => $influencer->id,
                    'platform' => $platform,
                    'username' => $this->faker->userName(),
                    'url' => $this->faker->url(),
                    'followers' => $this->faker->numberBetween(100, 100000),
                ]);
            }
        });
    }
}
