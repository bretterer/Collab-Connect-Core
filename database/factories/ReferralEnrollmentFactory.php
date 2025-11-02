<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReferralEnrollment>
 */
class ReferralEnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('[A-Z0-9]{8}'),
            'user_id' => \App\Models\User::factory(),
            'current_percentage' => fake()->numberBetween(0, 100),
            'paypal_email' => null,
            'paypal_payer_id' => null,
            'paypal_verified' => false,
            'paypal_connected_at' => null,
            'paypal_metadata' => null,
        ];
    }

    /**
     * Indicate that the enrollment has a connected PayPal account.
     */
    public function withPayPal(): static
    {
        return $this->state(fn (array $attributes) => [
            'paypal_email' => fake()->unique()->safeEmail(),
            'paypal_verified' => true,
            'paypal_connected_at' => now(),
            'paypal_metadata' => [
                'verification_method' => 'email',
                'verified_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Indicate that the enrollment has an unverified PayPal account.
     */
    public function withUnverifiedPayPal(): static
    {
        return $this->state(fn (array $attributes) => [
            'paypal_email' => fake()->unique()->safeEmail(),
            'paypal_verified' => false,
            'paypal_connected_at' => null,
            'paypal_metadata' => null,
        ]);
    }
}
