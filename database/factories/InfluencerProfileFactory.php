<?php

namespace Database\Factories;

use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Enums\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InfluencerProfile>
 */
class InfluencerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasMediaKit = fake()->boolean(60);

        return [
            'creator_name' => fake()->userName(),
            'primary_niche' => fake()->randomElement(Niche::forInfluencers())->value,
            'primary_zip_code' => fake()->postcode(),
            'media_kit_url' => $hasMediaKit ? fake()->url() : null,
            'has_media_kit' => $hasMediaKit,
            'collaboration_preferences' => fake()->randomElements(
                array_column(CollaborationGoal::forInfluencers(), 'value'),
                fake()->numberBetween(2, 4)
            ),
            'preferred_brands' => fake()->randomElements([
                'Nike', 'Adidas', 'Starbucks', 'Apple', 'Samsung', 'Coca-Cola',
                'Target', 'Walmart', 'Amazon', 'Netflix', 'Spotify', 'Uber',
                'Local Restaurants', 'Local Boutiques', 'Local Gyms', 'Local Cafes',
            ], fake()->numberBetween(3, 6)),
            'subscription_plan' => fake()->randomElement(SubscriptionPlan::forInfluencers())->value,
            'onboarding_completed' => true,
        ];
    }

    /**
     * Indicate that the influencer profile has not completed onboarding.
     */
    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'onboarding_completed' => false,
        ]);
    }

    /**
     * Indicate that the influencer has a media kit.
     */
    public function withMediaKit(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_media_kit' => true,
            'media_kit_url' => fake()->url(),
        ]);
    }

    /**
     * Indicate that the influencer does not have a media kit.
     */
    public function withoutMediaKit(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_media_kit' => false,
            'media_kit_url' => null,
        ]);
    }

    /**
     * Set a specific niche.
     */
    public function withNiche(Niche $niche): static
    {
        return $this->state(fn (array $attributes) => [
            'primary_niche' => $niche->value,
        ]);
    }

    /**
     * Set a specific subscription plan.
     */
    public function withPlan(SubscriptionPlan $plan): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => $plan->value,
        ]);
    }

    /**
     * Create an influencer with high follower count.
     */
    public function highFollowerCount(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => SubscriptionPlan::INFLUENCER_OVER_50K->value,
        ]);
    }

    /**
     * Create an influencer with medium follower count.
     */
    public function mediumFollowerCount(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => SubscriptionPlan::INFLUENCER_10K_TO_50K->value,
        ]);
    }

    /**
     * Create an influencer with low follower count.
     */
    public function lowFollowerCount(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => SubscriptionPlan::INFLUENCER_UNDER_10K->value,
        ]);
    }

    /**
     * Create a micro-influencer (1K-10K followers).
     */
    public function microInfluencer(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => SubscriptionPlan::INFLUENCER_UNDER_10K->value,
        ]);
    }

    /**
     * Create a mid-tier influencer (10K-50K followers).
     */
    public function midTierInfluencer(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => SubscriptionPlan::INFLUENCER_10K_TO_50K->value,
        ]);
    }

    /**
     * Create a macro-influencer (50K+ followers).
     */
    public function macroInfluencer(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_plan' => SubscriptionPlan::INFLUENCER_OVER_50K->value,
        ]);
    }
}
