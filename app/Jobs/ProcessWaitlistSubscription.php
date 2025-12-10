<?php

namespace App\Jobs;

use App\Enums\AccountType;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessWaitlistSubscription implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public string $subscriptionStripePriceId,
        public int $intendedTrialDays,
        public ?int $customSignupPageId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            Log::warning('ProcessWaitlistSubscription: User not found', [
                'user_id' => $this->userId,
            ]);

            return;
        }

        $billableModel = $this->getBillableModel($user);

        if (! $billableModel) {
            Log::warning('ProcessWaitlistSubscription: Could not find billable model', [
                'user_id' => $user->id,
                'account_type' => $user->account_type->value,
            ]);

            return;
        }

        // Check if user already has an active subscription
        if ($billableModel->subscribed('default')) {
            Log::info('ProcessWaitlistSubscription: User already has subscription, skipping', [
                'user_id' => $user->id,
            ]);

            return;
        }

        // Check if billable model has a Stripe ID and default payment method
        if (! $billableModel->hasStripeId()) {
            Log::warning('ProcessWaitlistSubscription: No Stripe ID', [
                'user_id' => $user->id,
            ]);

            return;
        }

        if (! $billableModel->hasDefaultPaymentMethod()) {
            Log::warning('ProcessWaitlistSubscription: No default payment method', [
                'user_id' => $user->id,
            ]);

            return;
        }

        // Create the subscription
        $billableModel->newSubscription('default', $this->subscriptionStripePriceId)
            ->trialDays($this->intendedTrialDays)
            ->create($billableModel->defaultPaymentMethod()->id, [
                'email' => $billableModel->email ?? $user->email,
                'name' => $user->name,
                'metadata' => [
                    'custom_signup_page_id' => $this->customSignupPageId,
                    'activated_via' => 'market_approval',
                ],
            ]);

        Log::info('ProcessWaitlistSubscription: Subscription created', [
            'user_id' => $user->id,
            'price_id' => $this->subscriptionStripePriceId,
            'trial_days' => $this->intendedTrialDays,
        ]);
    }

    /**
     * Get the billable model (Business or Influencer) for a user.
     */
    protected function getBillableModel(User $user): mixed
    {
        if ($user->account_type === AccountType::BUSINESS) {
            return $user->currentBusiness;
        }

        if ($user->account_type === AccountType::INFLUENCER) {
            return $user->influencer;
        }

        return null;
    }
}
