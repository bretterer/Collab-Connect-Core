<?php

namespace App\Services;

use App\Enums\CollaborationStatus;
use App\Models\Business;
use App\Models\Collaboration;
use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\SubscriptionCredit;
use App\Subscription\SubscriptionMetadataSchema;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Subscription;

class SubscriptionLimitsService
{
    /**
     * Get the active subscription for a billable model.
     */
    public function getActiveSubscription(Model $billable): ?Subscription
    {
        if (! method_exists($billable, 'subscription')) {
            return null;
        }

        return $billable->subscription('default');
    }

    /**
     * Get the Stripe price metadata for a billable's active subscription.
     *
     * @return array<string, mixed>
     */
    public function getPriceMetadata(Model $billable): array
    {
        $subscription = $this->getActiveSubscription($billable);

        if (! $subscription || ! $subscription->stripe_price) {
            return [];
        }

        $price = StripePrice::where('stripe_id', $subscription->stripe_price)->first();

        return $price?->metadata ?? [];
    }

    /**
     * Get a specific limit value from the subscription's price metadata.
     * Returns 0 if not set, -1 means unlimited.
     */
    public function getLimit(Model $billable, string $key): int
    {
        $metadata = $this->getPriceMetadata($billable);

        return (int) ($metadata[$key] ?? 0);
    }

    /**
     * Check if a limit is unlimited (-1).
     */
    public function isUnlimited(Model $billable, string $key): bool
    {
        return $this->getLimit($billable, $key) === SubscriptionMetadataSchema::UNLIMITED;
    }

    /**
     * Get the credit record for a billable and key.
     */
    public function getCredit(Model $billable, string $key): ?SubscriptionCredit
    {
        $subscription = $this->getActiveSubscription($billable);

        if (! $subscription) {
            return null;
        }

        return SubscriptionCredit::forSubscription($subscription->id)
            ->forKey($key)
            ->first();
    }

    /**
     * Get remaining credits for a specific key.
     * Returns -1 if unlimited, 0 if no credits or no subscription.
     */
    public function getRemainingCredits(Model $billable, string $key): int
    {
        // Check if unlimited first
        if ($this->isUnlimited($billable, $key)) {
            return SubscriptionMetadataSchema::UNLIMITED;
        }

        $credit = $this->getCredit($billable, $key);

        return $credit?->remaining() ?? 0;
    }

    /**
     * Deduct one credit for a specific key.
     *
     * @return bool True if credit was deducted, false if no credits remaining or unlimited
     */
    public function deductCredit(Model $billable, string $key, int $amount = 1): bool
    {
        // Unlimited doesn't need tracking
        if ($this->isUnlimited($billable, $key)) {
            return true;
        }

        $credit = $this->getCredit($billable, $key);

        if (! $credit) {
            return false;
        }

        return $credit->deduct($amount);
    }

    /**
     * Set credits for a specific key (creates record if doesn't exist).
     */
    public function setCredit(Model $billable, string $key, int $value): void
    {
        $subscription = $this->getActiveSubscription($billable);

        if (! $subscription) {
            return;
        }

        SubscriptionCredit::updateOrCreate(
            [
                'subscription_id' => $subscription->id,
                'key' => $key,
            ],
            [
                'value' => $value,
                'reset_at' => now(),
            ]
        );
    }

    /**
     * Reset all credits for a billable to their subscription limits.
     * Preserves excess credits - if user has more than the plan limit, keeps their current value.
     */
    public function resetAllCredits(Model $billable): void
    {
        $subscription = $this->getActiveSubscription($billable);

        if (! $subscription) {
            return;
        }

        $metadata = $this->getPriceMetadata($billable);

        foreach (SubscriptionMetadataSchema::getCreditKeys() as $key) {
            $limit = (int) ($metadata[$key] ?? 0);

            // Skip unlimited and zero limits
            if ($limit <= 0) {
                continue;
            }

            // Get existing credit record to preserve excess
            $existingCredit = SubscriptionCredit::forSubscription($subscription->id)
                ->forKey($key)
                ->first();

            $currentValue = $existingCredit?->value ?? 0;
            $newValue = max($currentValue, $limit);

            SubscriptionCredit::updateOrCreate(
                [
                    'subscription_id' => $subscription->id,
                    'key' => $key,
                ],
                [
                    'value' => $newValue,
                    'reset_at' => now(),
                ]
            );
        }
    }

    /**
     * Initialize credits for a new subscription.
     */
    public function initializeCredits(Model $billable): void
    {
        $this->resetAllCredits($billable);
    }

    // =========================================================================
    // Usage Check Methods
    // =========================================================================

    /**
     * Check if an influencer can submit a campaign application.
     */
    public function canSubmitApplication(Influencer $influencer): bool
    {
        $limit = $this->getLimit($influencer, SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT);

        // Not subscribed or no limit set
        if ($limit === 0) {
            return false;
        }

        // Unlimited
        if ($limit === SubscriptionMetadataSchema::UNLIMITED) {
            return true;
        }

        // Check credits
        return $this->getRemainingCredits($influencer, SubscriptionMetadataSchema::ACTIVE_APPLICATIONS_LIMIT) > 0;
    }

    /**
     * Check if a billable can start a new collaboration.
     * This is slot-based - completing a collaboration frees the slot.
     */
    public function canStartCollaboration(Model $billable): bool
    {
        $limit = $this->getLimit($billable, SubscriptionMetadataSchema::COLLABORATION_LIMIT);

        // Not subscribed or no limit set
        if ($limit === 0) {
            return false;
        }

        // Unlimited
        if ($limit === SubscriptionMetadataSchema::UNLIMITED) {
            return true;
        }

        // Count current active collaborations
        $activeCount = $this->getActiveCollaborationCount($billable);

        return $activeCount < $limit;
    }

    /**
     * Get the count of active collaborations for a billable.
     */
    public function getActiveCollaborationCount(Model $billable): int
    {
        if ($billable instanceof Influencer) {
            return Collaboration::where('influencer_id', $billable->user_id)
                ->where('status', CollaborationStatus::ACTIVE)
                ->count();
        }

        if ($billable instanceof Business) {
            return Collaboration::where('business_id', $billable->id)
                ->where('status', CollaborationStatus::ACTIVE)
                ->count();
        }

        return 0;
    }

    /**
     * Check if a business can publish a campaign.
     */
    public function canPublishCampaign(Business $business): bool
    {
        logger()->info('canPublishCampaign() checking', ['business_id' => $business->id]);

        $limit = $this->getLimit($business, SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT);
        logger()->info('canPublishCampaign() limit retrieved', [
            'limit' => $limit,
            'UNLIMITED_CONST' => SubscriptionMetadataSchema::UNLIMITED,
        ]);

        // Not subscribed or no limit set
        if ($limit === 0) {
            logger()->info('canPublishCampaign() returning false - limit is 0');

            return false;
        }

        // Unlimited
        if ($limit === SubscriptionMetadataSchema::UNLIMITED) {
            logger()->info('canPublishCampaign() returning true - unlimited');

            return true;
        }

        // Check credits
        $remainingCredits = $this->getRemainingCredits($business, SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT);
        logger()->info('canPublishCampaign() checking credits', [
            'remainingCredits' => $remainingCredits,
            'result' => $remainingCredits > 0,
        ]);

        return $remainingCredits > 0;
    }

    /**
     * Check if a business can boost a campaign.
     */
    public function canBoostCampaign(Business $business): bool
    {
        $limit = $this->getLimit($business, SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS);

        // Not subscribed or no limit set
        if ($limit === 0) {
            return false;
        }

        // Unlimited
        if ($limit === SubscriptionMetadataSchema::UNLIMITED) {
            return true;
        }

        // Check credits
        return $this->getRemainingCredits($business, SubscriptionMetadataSchema::CAMPAIGN_BOOST_CREDITS) > 0;
    }

    /**
     * Check if a billable can promote their profile.
     */
    public function canPromoteProfile(Model $billable): bool
    {
        $limit = $this->getLimit($billable, SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS);

        // Not subscribed or no limit set
        if ($limit === 0) {
            return false;
        }

        // Unlimited
        if ($limit === SubscriptionMetadataSchema::UNLIMITED) {
            return true;
        }

        // Check credits
        return $this->getRemainingCredits($billable, SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS) > 0;
    }

    /**
     * Check if a business can invite a team member.
     */
    public function canInviteTeamMember(Business $business): bool
    {
        $limit = $this->getLimit($business, SubscriptionMetadataSchema::TEAM_MEMBER_LIMIT);

        // Not subscribed or no limit set (Essential tier)
        if ($limit === 0) {
            return false;
        }

        // Unlimited
        if ($limit === SubscriptionMetadataSchema::UNLIMITED) {
            return true;
        }

        // Count current members (excluding owner) + pending invites
        $currentMemberCount = $business->members()
            ->wherePivot('role', '!=', 'owner')
            ->count();

        $pendingInviteCount = $business->pendingInvites()->count();

        $totalCount = $currentMemberCount + $pendingInviteCount;

        return $totalCount < $limit;
    }

    // =========================================================================
    // Billing Cycle Methods
    // =========================================================================

    /**
     * Get the billing cycle reset date for a billable.
     * Returns the date when credits will reset (current_period_end from Stripe).
     */
    public function getBillingCycleResetDate(Model $billable): ?\Carbon\Carbon
    {
        $subscription = $this->getActiveSubscription($billable);

        if (! $subscription) {
            return null;
        }

        // In test environments or when Stripe is not configured, skip API call
        if (app()->environment('testing') || ! config('services.stripe.secret')) {
            return null;
        }

        try {
            $stripeSubscription = $subscription->asStripeSubscription();

            if ($stripeSubscription && $stripeSubscription->current_period_end) {
                return \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end);
            }
        } catch (\Exception $e) {
            // If we can't get the Stripe subscription, fall back to null
            return null;
        }

        return null;
    }

    /**
     * Get comprehensive limit info for a specific credit type.
     * Returns all information needed to display to the user.
     *
     * @return array{remaining: int, limit: int, is_unlimited: bool, reset_date: ?\Carbon\Carbon, formatted_reset_date: ?string, is_slot_based: bool}
     */
    public function getLimitInfo(Model $billable, string $key): array
    {
        $limit = $this->getLimit($billable, $key);
        $isUnlimited = $limit === SubscriptionMetadataSchema::UNLIMITED;
        $resetDate = $this->getBillingCycleResetDate($billable);

        // Slot-based limits (team members, collaborations) calculate remaining differently
        $isSlotBased = in_array($key, [
            SubscriptionMetadataSchema::TEAM_MEMBER_LIMIT,
            SubscriptionMetadataSchema::COLLABORATION_LIMIT,
        ]);

        if ($isSlotBased) {
            $usage = $this->getUsageForKey($billable, $key) ?? 0;
            $remaining = $isUnlimited ? SubscriptionMetadataSchema::UNLIMITED : max(0, $limit - $usage);
        } else {
            $remaining = $this->getRemainingCredits($billable, $key);
        }

        return [
            'remaining' => $remaining,
            'limit' => $limit,
            'is_unlimited' => $isUnlimited,
            'reset_date' => $resetDate,
            'formatted_reset_date' => $resetDate?->format('M j, Y'),
            'is_slot_based' => $isSlotBased,
        ];
    }

    // =========================================================================
    // Utility Methods
    // =========================================================================

    /**
     * Get a summary of all limits and current usage for a billable.
     *
     * @return array<string, array{limit: int, remaining: int|null, usage: int|null}>
     */
    public function getLimitsSummary(Model $billable): array
    {
        $accountType = $billable instanceof Influencer ? 'influencer' : 'business';
        $keys = $accountType === 'influencer'
            ? SubscriptionMetadataSchema::getInfluencerKeys()
            : SubscriptionMetadataSchema::getBusinessKeys();

        $summary = [];

        foreach ($keys as $key) {
            $limit = $this->getLimit($billable, $key);
            $isCredit = SubscriptionMetadataSchema::isCredit($key);

            $summary[$key] = [
                'limit' => $limit,
                'remaining' => $isCredit ? $this->getRemainingCredits($billable, $key) : null,
                'usage' => $this->getUsageForKey($billable, $key),
                'is_unlimited' => $limit === SubscriptionMetadataSchema::UNLIMITED,
            ];
        }

        return $summary;
    }

    /**
     * Get current usage for a specific key.
     */
    private function getUsageForKey(Model $billable, string $key): ?int
    {
        return match ($key) {
            SubscriptionMetadataSchema::COLLABORATION_LIMIT => $this->getActiveCollaborationCount($billable),
            SubscriptionMetadataSchema::TEAM_MEMBER_LIMIT => $billable instanceof Business
                ? $billable->members()->wherePivot('role', '!=', 'owner')->count() + $billable->pendingInvites()->count()
                : null,
            default => null,
        };
    }
}
