<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SubscriptionSeeder extends Seeder
{
    /**
     * Seed subscriptions for the initial business and influencer users.
     *
     * This seeder will use real Stripe customer/subscription IDs if provided
     * in the config (via .env variables), otherwise it will skip subscription
     * creation entirely to avoid fake data in the database.
     *
     * Required .env variables for real Stripe data:
     * - INIT_BUSINESS_STRIPE_CUSTOMER_ID (e.g., cus_xxx)
     * - INIT_BUSINESS_STRIPE_SUBSCRIPTION_ID (e.g., sub_xxx)
     * - INIT_INFLUENCER_STRIPE_CUSTOMER_ID (e.g., cus_xxx)
     * - INIT_INFLUENCER_STRIPE_SUBSCRIPTION_ID (e.g., sub_xxx)
     */
    public function run(): void
    {
        $this->seedBusinessSubscription();
        $this->seedInfluencerSubscription();
    }

    /**
     * Seed the business user's subscription.
     */
    protected function seedBusinessSubscription(): void
    {
        $customerId = config('collabconnect.initialization.business.customer_id');
        $subscriptionId = config('collabconnect.initialization.business.subscription_id');

        if (! $customerId || ! $subscriptionId) {
            $this->command->warn('Skipping business subscription - INIT_BUSINESS_STRIPE_CUSTOMER_ID and/or INIT_BUSINESS_STRIPE_SUBSCRIPTION_ID not set');

            return;
        }

        $businessEmail = config('collabconnect.init_business_email', 'business@example.com');
        $user = User::where('email', $businessEmail)->first();

        if (! $user || ! $user->currentBusiness) {
            $this->command->error("Business user not found with email: {$businessEmail}");

            return;
        }

        $business = $user->currentBusiness;

        // Fetch the subscription from Stripe to get accurate data
        try {
            $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);

            // Update business with Stripe customer ID
            $business->update([
                'stripe_id' => $customerId,
            ]);

            // Fetch payment method details if available
            $this->updatePaymentMethodDetails($business, $customerId);

            // Create subscription record
            $business->subscriptions()->updateOrCreate(
                ['type' => 'default'],
                [
                    'stripe_id' => $subscriptionId,
                    'stripe_status' => $stripeSubscription->status,
                    'stripe_price' => $stripeSubscription->items->data[0]->price->id ?? null,
                    'quantity' => $stripeSubscription->items->data[0]->quantity ?? 1,
                    'trial_ends_at' => $stripeSubscription->trial_end
                        ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                        : null,
                    'ends_at' => $stripeSubscription->cancel_at
                        ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at)
                        : null,
                ]
            );

            $this->command->info("✓ Business subscription created for {$user->name} ({$user->email})");
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $this->command->error("Failed to fetch Stripe subscription: {$e->getMessage()}");
            Log::error('SubscriptionSeeder: Failed to fetch business subscription', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Seed the influencer user's subscription.
     */
    protected function seedInfluencerSubscription(): void
    {
        $customerId = config('collabconnect.initialization.influencer.customer_id');
        $subscriptionId = config('collabconnect.initialization.influencer.subscription_id');

        if (! $customerId || ! $subscriptionId) {
            $this->command->warn('Skipping influencer subscription - INIT_INFLUENCER_STRIPE_CUSTOMER_ID and/or INIT_INFLUENCER_STRIPE_SUBSCRIPTION_ID not set');

            return;
        }

        $influencerEmail = config('collabconnect.init_influencer_email', 'influencer@example.com');
        $user = User::where('email', $influencerEmail)->first();

        if (! $user || ! $user->influencer) {
            $this->command->error("Influencer user not found with email: {$influencerEmail}");

            return;
        }

        $influencer = $user->influencer;

        // Fetch the subscription from Stripe to get accurate data
        try {
            $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);

            // Update influencer with Stripe customer ID
            $influencer->update([
                'stripe_id' => $customerId,
            ]);

            // Fetch payment method details if available
            $this->updatePaymentMethodDetails($influencer, $customerId);

            // Create subscription record
            $influencer->subscriptions()->updateOrCreate(
                ['type' => 'default'],
                [
                    'stripe_id' => $subscriptionId,
                    'stripe_status' => $stripeSubscription->status,
                    'stripe_price' => $stripeSubscription->items->data[0]->price->id ?? null,
                    'quantity' => $stripeSubscription->items->data[0]->quantity ?? 1,
                    'trial_ends_at' => $stripeSubscription->trial_end
                        ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                        : null,
                    'ends_at' => $stripeSubscription->cancel_at
                        ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at)
                        : null,
                ]
            );

            $this->command->info("✓ Influencer subscription created for {$user->name} ({$user->email})");
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $this->command->error("Failed to fetch Stripe subscription: {$e->getMessage()}");
            Log::error('SubscriptionSeeder: Failed to fetch influencer subscription', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update payment method details from Stripe customer.
     */
    protected function updatePaymentMethodDetails(Business|Influencer $billable, string $customerId): void
    {
        try {
            $customer = \Stripe\Customer::retrieve($customerId, [
                'expand' => ['invoice_settings.default_payment_method'],
            ]);

            $paymentMethod = $customer->invoice_settings->default_payment_method ?? null;

            if ($paymentMethod && isset($paymentMethod->card)) {
                $billable->update([
                    'pm_type' => $paymentMethod->card->brand ?? 'card',
                    'pm_last_four' => $paymentMethod->card->last4 ?? null,
                ]);
            }
        } catch (\Exception $e) {
            // Non-critical - just log and continue
            Log::warning('SubscriptionSeeder: Could not fetch payment method details', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
