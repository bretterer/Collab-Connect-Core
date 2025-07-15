<?php

namespace Database\Factories;

use App\Enums\CampaignType;
use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Enums\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusinessProfile>
 */
class BusinessProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_name' => fake()->company(),
            'industry' => fake()->randomElement(Niche::forBusinesses()),
            'websites' => [
                fake()->url(),
                fake()->optional(0.3)->url(),
            ],
            'primary_zip_code' => fake()->postcode(),
            'location_count' => fake()->numberBetween(1, 50),
            'is_franchise' => fake()->boolean(30),
            'is_national_brand' => fake()->boolean(20),
            'contact_name' => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'subscription_plan' => fake()->randomElement(SubscriptionPlan::forBusinesses())->value,
            'collaboration_goals' => fake()->randomElements(
                array_column(CollaborationGoal::forBusinesses(), 'value'),
                fake()->numberBetween(2, 4)
            ),
            'campaign_types' => fake()->randomElements(
                array_column(CampaignType::cases(), 'value'),
                fake()->numberBetween(1, 3)
            ),
            'team_members' => fake()->optional(0.7)->randomElements([
                ['name' => fake()->name(), 'email' => fake()->safeEmail(), 'role' => 'Marketing Manager'],
                ['name' => fake()->name(), 'email' => fake()->safeEmail(), 'role' => 'Social Media Coordinator'],
                ['name' => fake()->name(), 'email' => fake()->safeEmail(), 'role' => 'Brand Manager'],
            ], fake()->numberBetween(1, 3)),
            'onboarding_completed' => true,
        ];
    }

    /**
     * Indicate that the business profile has not completed onboarding.
     */
    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'onboarding_completed' => false,
        ]);
    }

    /**
     * Indicate that the business is a franchise.
     */
    public function franchise(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_franchise' => true,
            'location_count' => fake()->numberBetween(5, 100),
        ]);
    }

    /**
     * Indicate that the business is a national brand.
     */
    public function nationalBrand(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_national_brand' => true,
            'location_count' => fake()->numberBetween(50, 1000),
            'subscription_plan' => SubscriptionPlan::BUSINESS_ENTERPRISE->value,
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
}
