<?php

namespace Database\Seeders;

use App\Enums\PercentageChangeType;
use App\Enums\ReferralStatus;
use App\Models\Referral;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPercentageHistory;
use App\Models\StripePrice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Usage examples:
     *
     * // Seed with default options (finds first user, creates 10 active referrals)
     * php artisan db:seed --class=ReferralSeeder
     *
     * // Seed for a specific user by ID
     * $this->call(ReferralSeeder::class, false, ['userId' => 1, 'count' => 5, 'status' => 'active']);
     *
     * // Seed for a specific user by email
     * $this->call(ReferralSeeder::class, false, ['userEmail' => 'user@example.com', 'count' => 20, 'status' => 'pending']);
     */
    public function run(
        ?int $userId = null,
        ?string $userEmail = null,
        int $count = 10,
        string $status = 'active',
        int $subscriptionAmount = 1000
    ): void {
        // Find the referrer user
        $referrer = $this->findReferrer($userId, $userEmail);

        if (! $referrer) {
            $this->command->error('No referrer user found. Please specify a valid userId or userEmail.');

            return;
        }

        $this->command->info("Creating {$count} {$status} referrals for {$referrer->name} ({$referrer->email})");

        // Ensure the referrer is enrolled in the referral program
        $enrollment = $this->ensureEnrolled($referrer);

        // Create a StripePrice for the subscriptions
        $stripePrice = $this->createStripePrice($subscriptionAmount);

        // Parse the status
        $referralStatus = $this->parseStatus($status);

        // Create the referrals
        for ($i = 0; $i < $count; $i++) {
            $this->createReferral($referrer, $enrollment, $stripePrice, $referralStatus);
        }

        $this->command->info("✓ Created {$count} {$status} referrals for {$referrer->name}");
        $this->command->info("  Referral Code: {$enrollment->code}");
        $this->command->info("  Total Referrals: {$enrollment->referrals()->count()}");
    }

    /**
     * Find the referrer user by ID or email.
     */
    protected function findReferrer(?int $userId, ?string $userEmail): ?User
    {
        if ($userId) {
            return User::find($userId);
        }

        if ($userEmail) {
            return User::where('email', $userEmail)->first();
        }

        // Default: find the first user with a referral enrollment, or just the first user
        return User::whereHas('referralEnrollment')->first()
            ?? User::first();
    }

    /**
     * Ensure the user is enrolled in the referral program.
     */
    protected function ensureEnrolled(User $user): ReferralEnrollment
    {
        $enrollment = $user->referralEnrollment;

        if (! $enrollment) {
            $enrollment = ReferralEnrollment::create([
                'user_id' => $user->id,
                'code' => strtoupper(Str::ulid()),
            ]);

            ReferralPercentageHistory::create([
                'referral_enrollment_id' => $enrollment->id,
                'old_percentage' => 0,
                'new_percentage' => config('collabconnect.referrals.default_percentage', 10),
                'change_type' => PercentageChangeType::ENROLLMENT,
                'reason' => 'Initial enrollment percentage',
                'changed_by_user_id' => null,
            ]);

            $this->command->info('  ✓ Enrolled user in referral program');
        }

        return $enrollment;
    }

    /**
     * Create or find a StripePrice for testing.
     */
    protected function createStripePrice(int $amount): StripePrice
    {
        // Try to find an existing price with this amount
        $price = StripePrice::where('unit_amount', $amount)->first();

        if (! $price) {
            $price = StripePrice::factory()->create([
                'stripe_id' => 'price_seeder_'.Str::random(14),
                'unit_amount' => $amount,
            ]);
        }

        return $price;
    }

    /**
     * Parse the status string to ReferralStatus enum.
     */
    protected function parseStatus(string $status): ReferralStatus
    {
        return match (strtolower($status)) {
            'pending' => ReferralStatus::PENDING,
            'active' => ReferralStatus::ACTIVE,
            'churned' => ReferralStatus::CHURNED,
            'cancelled' => ReferralStatus::CANCELLED,
            default => ReferralStatus::ACTIVE,
        };
    }

    /**
     * Create a single referral with a referred user.
     */
    protected function createReferral(
        User $referrer,
        ReferralEnrollment $enrollment,
        StripePrice $stripePrice,
        ReferralStatus $status
    ): void {
        // Create a referred user (alternating between business and influencer)
        $accountType = $this->getNextAccountType();

        $referredUser = User::factory()
            ->{$accountType}()
            ->withProfile()
            ->create();

        // Create subscription for the referred user
        $this->createSubscription($referredUser, $stripePrice);

        // Create the referral record
        Referral::create([
            'referrer_user_id' => $referrer->id,
            'referred_user_id' => $referredUser->id,
            'referral_code_used' => $enrollment->code,
            'status' => $status,
            'converted_at' => $status === ReferralStatus::ACTIVE ? now()->subDays(rand(1, 30)) : null,
            'churned_at' => $status === ReferralStatus::CHURNED ? now()->subDays(rand(1, 10)) : null,
        ]);
    }

    /**
     * Create a subscription for the referred user.
     */
    protected function createSubscription(User $user, StripePrice $stripePrice): void
    {
        $billable = $user->isBusinessAccount() ? $user->currentBusiness : $user->influencer;

        if ($billable) {
            $billable->subscriptions()->create([
                'type' => 'default',
                'stripe_id' => 'sub_seeder_'.Str::random(14),
                'stripe_status' => 'active',
                'stripe_price' => $stripePrice->stripe_id,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ]);
        }
    }

    /**
     * Alternate between business and influencer account types.
     */
    protected function getNextAccountType(): string
    {
        static $counter = 0;
        $counter++;

        return $counter % 2 === 0 ? 'business' : 'influencer';
    }
}
