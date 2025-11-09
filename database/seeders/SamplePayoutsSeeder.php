<?php

namespace Database\Seeders;

use App\Enums\PayoutStatus;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayout;
use Illuminate\Database\Seeder;

class SamplePayoutsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create some enrolled users
        $enrollments = ReferralEnrollment::query()->get();

        if ($enrollments->count() < 5) {
            // Create some enrollments if we don't have enough
            for ($i = 0; $i < 5 - $enrollments->count(); $i++) {
                $enrollments->push(
                    ReferralEnrollment::factory()->create([
                        'paypal_email' => fake()->email(),
                        'paypal_verified' => true,
                        'paypal_connected_at' => now()->subMonths(3),
                    ])
                );
            }
        }

        // Get the past 3 months
        $currentDate = now();
        $months = [
            ['month' => $currentDate->copy()->subMonths(2)->month, 'year' => $currentDate->copy()->subMonths(2)->year],
            ['month' => $currentDate->copy()->subMonths(1)->month, 'year' => $currentDate->copy()->subMonths(1)->year],
            ['month' => $currentDate->month, 'year' => $currentDate->year],
        ];

        // Status distribution for variety
        $statusDistribution = [
            PayoutStatus::PAID,
            PayoutStatus::PAID,
            PayoutStatus::PAID,
            PayoutStatus::FAILED,
            PayoutStatus::FAILED,
            PayoutStatus::PENDING,
            PayoutStatus::PROCESSING,
            PayoutStatus::CANCELLED,
        ];

        foreach ($months as $period) {
            // Create 8-12 payouts per month
            $payoutsCount = fake()->numberBetween(8, 12);

            for ($i = 0; $i < $payoutsCount; $i++) {
                $enrollment = $enrollments->random();
                $status = fake()->randomElement($statusDistribution);
                $amount = fake()->randomFloat(2, 10, 500);

                $payout = ReferralPayout::factory()->create([
                    'referral_enrollment_id' => $enrollment->id,
                    'amount' => $amount,
                    'currency' => 'USD',
                    'status' => $status,
                    'month' => $period['month'],
                    'year' => $period['year'],
                    'referral_count' => fake()->numberBetween(1, 10),
                ]);

                // Add relevant dates and data based on status
                match ($status) {
                    PayoutStatus::PAID => $payout->update([
                        'paypal_batch_id' => 'BATCH_'.fake()->uuid(),
                        'paypal_payout_item_id' => 'ITEM_'.fake()->uuid(),
                        'paypal_transaction_id' => 'TXN_'.fake()->uuid(),
                        'processed_at' => now()->subDays(fake()->numberBetween(1, 30)),
                        'paid_at' => now()->subDays(fake()->numberBetween(1, 25)),
                    ]),
                    PayoutStatus::FAILED => $payout->update([
                        'paypal_batch_id' => 'BATCH_'.fake()->uuid(),
                        'processed_at' => now()->subDays(fake()->numberBetween(1, 30)),
                        'failed_at' => now()->subDays(fake()->numberBetween(1, 25)),
                        'failure_reason' => fake()->randomElement([
                            'Recipient account is not able to receive this currency.',
                            'Recipient has not yet accepted the payment.',
                            'Payment was refused by recipient.',
                            'Recipient email is not confirmed.',
                            'Recipient account is restricted.',
                        ]),
                    ]),
                    PayoutStatus::PROCESSING => $payout->update([
                        'paypal_batch_id' => 'BATCH_'.fake()->uuid(),
                        'processed_at' => now()->subDays(fake()->numberBetween(1, 5)),
                    ]),
                    PayoutStatus::PENDING => $payout->update([
                        // Pending payouts don't have batch IDs or processing dates
                    ]),
                    PayoutStatus::CANCELLED => $payout->update([
                        'paypal_batch_id' => 'BATCH_'.fake()->uuid(),
                        'processed_at' => now()->subDays(fake()->numberBetween(1, 30)),
                        'failed_at' => now()->subDays(fake()->numberBetween(1, 25)),
                        'failure_reason' => 'Payout was cancelled by administrator.',
                    ]),
                    default => null,
                };
            }
        }

        $this->command->info('Created sample payouts for the past 3 months!');
        $this->command->info('Total payouts created: '.ReferralPayout::count());
        $this->command->info('Paid: '.ReferralPayout::where('status', PayoutStatus::PAID)->count());
        $this->command->info('Failed: '.ReferralPayout::where('status', PayoutStatus::FAILED)->count());
        $this->command->info('Pending: '.ReferralPayout::where('status', PayoutStatus::PENDING)->count());
        $this->command->info('Processing: '.ReferralPayout::where('status', PayoutStatus::PROCESSING)->count());
        $this->command->info('Cancelled: '.ReferralPayout::where('status', PayoutStatus::CANCELLED)->count());
    }
}
