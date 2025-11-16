<?php

namespace Database\Factories;

use App\Models\ReferralEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReferralPayout>
 */
class ReferralPayoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'referral_enrollment_id' => ReferralEnrollment::factory(),
            'amount' => fake()->randomFloat(2, 10, 500),
            'currency' => 'USD',
            'status' => \App\Enums\PayoutStatus::PENDING,
            'month' => fake()->numberBetween(1, 12),
            'year' => fake()->numberBetween(2024, 2025),
            'referral_count' => fake()->numberBetween(1, 10),
        ];
    }
}
