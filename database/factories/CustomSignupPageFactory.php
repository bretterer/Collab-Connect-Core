<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\CustomSignupPage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomSignupPage>
 */
class CustomSignupPageFactory extends Factory
{
    protected $model = CustomSignupPage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'account_type' => fake()->randomElement([AccountType::INFLUENCER, AccountType::BUSINESS]),
            'is_active' => true,
            'settings' => [
                'package' => [
                    'name' => 'Elite Package',
                    'benefits' => [
                        'Access to premium features',
                        'Priority support',
                        'Extended trial period',
                    ],
                ],
                'one_time_payment' => [
                    'amount' => 29700,
                    'stripe_price_id' => null,
                    'description' => 'One-time setup fee',
                ],
                'subscription' => [
                    'stripe_price_id' => null,
                    'trial_days' => 90,
                ],
                'webhook' => [
                    'url' => null,
                    'headers' => [],
                ],
            ],
            'published_at' => now(),
        ];
    }

    /**
     * Indicate the page is for influencers.
     */
    public function influencer(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => AccountType::INFLUENCER,
        ]);
    }

    /**
     * Indicate the page is for businesses.
     */
    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => AccountType::BUSINESS,
        ]);
    }

    /**
     * Indicate the page is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'published_at' => null,
        ]);
    }

    /**
     * Add a creator to the page.
     */
    public function withCreator(?User $user = null): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user?->id ?? User::factory(),
        ]);
    }
}
