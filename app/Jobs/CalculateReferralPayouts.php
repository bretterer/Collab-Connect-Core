<?php

namespace App\Jobs;

use App\Enums\PayoutStatus;
use App\Enums\ReferralStatus;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayoutItem;
use App\Models\StripePrice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateReferralPayouts implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all enrollments that have active referrals
        $enrollments = ReferralEnrollment::query()
            ->whereHas('referrals', function ($query) {
                $query->where('status', ReferralStatus::ACTIVE);
            })
            ->get();

        $scheduledPayoutDate = now()->day >= 15
            ? now()->addMonth()->day(15)->toDateString()
            : now()->day(15)->toDateString();

        foreach ($enrollments as $enrollment) {
            // Get all active referrals for this enrollment
            $activeReferrals = $enrollment->referrals()
                ->where('status', ReferralStatus::ACTIVE)
                ->with('referred')
                ->get();

            foreach ($activeReferrals as $referral) {
                // Check if a payout item already exists for this referral and scheduled date
                $existingItem = ReferralPayoutItem::where('referral_id', $referral->id)
                    ->whereDate('scheduled_payout_date', $scheduledPayoutDate)
                    ->first();

                if ($existingItem) {
                    // Skip if payout item already exists
                    continue;
                }

                // Get the referred user
                $referredUser = $referral->referred;

                // Get the subscription amount in cents
                $subscriptionAmountCents = $this->getSubscriptionAmount($referredUser);

                if ($subscriptionAmountCents === null) {
                    // Skip if no active subscription found
                    continue;
                }

                // Convert cents to dollars for storage
                $subscriptionAmount = $subscriptionAmountCents / 100;

                // Get the current percentage from the enrollment
                $percentageHistory = $enrollment->percentageHistory()
                    ->latest()
                    ->first();

                if (! $percentageHistory) {
                    // Skip if no percentage history found
                    continue;
                }

                $referralPercentage = $percentageHistory->new_percentage;

                // Calculate the payout amount (subscription amount is already in dollars)
                $payoutAmount = ($subscriptionAmount * $referralPercentage) / 100;

                // Create the payout item
                ReferralPayoutItem::create([
                    'referral_payout_id' => null, // Will be assigned when batch payout is created
                    'referral_enrollment_id' => $enrollment->id,
                    'referral_id' => $referral->id,
                    'referral_percentage_history_id' => $percentageHistory->id,
                    'subscription_amount' => $subscriptionAmount,
                    'referral_percentage' => $referralPercentage,
                    'amount' => $payoutAmount,
                    'currency' => 'USD',
                    'scheduled_payout_date' => $scheduledPayoutDate,
                    'status' => PayoutStatus::DRAFT,
                    'calculated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Get the subscription amount for a referred user.
     */
    protected function getSubscriptionAmount($user): ?int
    {
        // Check if user is a business account
        if ($user->isBusinessAccount()) {
            $business = $user->currentBusiness;

            if (! $business || ! $business->subscribed()) {
                return null;
            }

            return $this->getSubscriptionAmountFromBillable($business);
        }

        // Check if user is an influencer account
        if ($user->isInfluencerAccount()) {
            $influencer = $user->influencer;

            if (! $influencer || ! $influencer->subscribed()) {
                return null;
            }

            return $this->getSubscriptionAmountFromBillable($influencer);
        }

        return null;
    }

    /**
     * Get subscription amount from a billable model (Business or Influencer).
     */
    protected function getSubscriptionAmountFromBillable($billable): ?int
    {
        $subscription = $billable->subscription();

        if (! $subscription) {
            return null;
        }

        // Get the Stripe price from subscription or subscription items
        $stripePriceId = $subscription->stripe_price;

        // If no direct price, try to get from subscription items
        if (! $stripePriceId) {
            $subscriptionItem = $subscription->items()->first();
            if ($subscriptionItem) {
                $stripePriceId = $subscriptionItem->stripe_price;
            }
        }

        if (! $stripePriceId) {
            return null;
        }

        // Get the Stripe price
        $stripePrice = StripePrice::where('stripe_id', $stripePriceId)->first();

        if (! $stripePrice) {
            return null;
        }

        return $stripePrice->unit_amount;
    }
}
