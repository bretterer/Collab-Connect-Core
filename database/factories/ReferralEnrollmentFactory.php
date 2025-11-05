<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Symfony\Component\Uid\Ulid;

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
            'code' => Ulid::generate(),
            'user_id' => \App\Models\User::factory(),
            'enrolled_at' => now(),
            'disabled_at' => null,
            'paypal_email' => null,
            'paypal_verified' => false,
            'paypal_connected_at' => null,
        ];
    }
}
