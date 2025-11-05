<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReferralPercentageHistory>
 */
class ReferralPercentageHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'referral_enrollment_id' => \App\Models\ReferralEnrollment::factory(),
            'old_percentage' => 0,
            'new_percentage' => 10,
            'change_type' => \App\Enums\PercentageChangeType::ENROLLMENT,
            'expires_at' => null,
            'months' => null,
            'reason' => 'Initial enrollment percentage',
            'changed_by_user_id' => null,
        ];
    }
}
