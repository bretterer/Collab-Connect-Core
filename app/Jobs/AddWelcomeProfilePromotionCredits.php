<?php

namespace App\Jobs;

use App\Models\StripePrice;
use App\Services\CashierService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Laravel\Cashier\Subscription;

class AddWelcomeProfilePromotionCredits implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $subscriptionData)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscriptionId = $this->subscriptionData['id'];

        $subscription = Subscription::where('stripe_id', $subscriptionId)
            ->first();

        if (! $subscription) {
            return;
        }

        if ($subscription->stripe_status != 'active') {
            return;
        }

        // get the billable model based on influencer_id or business_id column
        $billable = CashierService::findBillable($subscription->stripe_id);

        if (! $billable) {
            return;
        }

        if ($billable->promotion_credits !== null) {
            return;
        }

        $stripePrice = StripePrice::where('stripe_id', $subscription->stripe_price)->first();

        if (! $stripePrice) {
            return;
        }

        $welcomeCredits = $stripePrice->metadata['profile_promotion_credits'] ?? 0;

        if ($welcomeCredits > 0) {
            $billable->promotion_credits = $welcomeCredits;
            $billable->save();
        }

    }
}
