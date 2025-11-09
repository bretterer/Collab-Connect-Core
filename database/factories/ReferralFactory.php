<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referral>
 */
class ReferralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $referred = \App\Models\User::factory()->influencer()->withProfile()->subscribed()->create();
        $referrer = \App\Models\User::factory()->influencer()->withProfile()->create();
        $enrollment = \App\Models\ReferralEnrollment::factory()->create([
            'user_id' => $referrer->id,
        ]);

        return [
            'referrer_user_id' => $referrer->id,
            'referred_user_id' => $referred->id,
            'referral_code_used' => $enrollment->code,
            'status' => \App\Enums\ReferralStatus::PENDING,
            'base_referral_percentage' => config('collabconnect.referrals.default_percentage', 10),
        ];
    }
}
