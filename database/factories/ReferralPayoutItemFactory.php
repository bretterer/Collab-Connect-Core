<?php

namespace Database\Factories;

use App\Models\Referral;
use App\Models\ReferralPercentageHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReferralPayoutItem>
 */
class ReferralPayoutItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subscriptionAmount = fake()->randomFloat(2, 10, 100);
        $defaultPercentage = 10;

        return [
            'referral_payout_id' => null, // Nullable until rolled up
            'referral_id' => Referral::factory(),
            'referral_percentage_history_id' => null, // Will be set in configure()
            'subscription_amount' => $subscriptionAmount,
            'referral_percentage' => $defaultPercentage,
            'amount' => round($subscriptionAmount * ($defaultPercentage / 100), 2),
            'currency' => 'USD',
            'scheduled_payout_date' => now()->addDays(15),
            'status' => \App\Enums\PayoutStatus::DRAFT,
            'calculated_at' => now(),
        ];
    }

    /**
     * Configure the factory to automatically handle referral_percentage_history_id.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($payoutItem) {
            // If no percentage history was provided, create one for the referral's enrollment
            if (! $payoutItem->referral_percentage_history_id) {
                $enrollment = $payoutItem->referral->referrer->referralEnrollment;

                // Try to find existing percentage history or create new one
                $percentageHistory = $enrollment->percentageHistory()->latest()->first()
                    ?? ReferralPercentageHistory::factory()->create([
                        'referral_enrollment_id' => $enrollment->id,
                    ]);

                $payoutItem->update([
                    'referral_percentage_history_id' => $percentageHistory->id,
                    'referral_percentage' => $percentageHistory->new_percentage,
                    'amount' => round($payoutItem->subscription_amount * ($percentageHistory->new_percentage / 100), 2),
                ]);
            } else {
                // If percentage history was provided, sync the percentage
                $percentageHistory = $payoutItem->referralPercentageHistory;
                $payoutItem->update([
                    'referral_percentage' => $percentageHistory->new_percentage,
                    'amount' => round($payoutItem->subscription_amount * ($percentageHistory->new_percentage / 100), 2),
                ]);
            }
        });
    }
}
