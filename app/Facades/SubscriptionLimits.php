<?php

namespace App\Facades;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\SubscriptionCredit;
use App\Services\SubscriptionLimitsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Laravel\Cashier\Subscription;

/**
 * @method static Subscription|null getActiveSubscription(Model $billable)
 * @method static array getPriceMetadata(Model $billable)
 * @method static int getLimit(Model $billable, string $key)
 * @method static bool isUnlimited(Model $billable, string $key)
 * @method static SubscriptionCredit|null getCredit(Model $billable, string $key)
 * @method static int getRemainingCredits(Model $billable, string $key)
 * @method static bool deductCredit(Model $billable, string $key, int $amount = 1)
 * @method static void setCredit(Model $billable, string $key, int $value)
 * @method static void resetAllCredits(Model $billable)
 * @method static void initializeCredits(Model $billable)
 * @method static bool canSubmitApplication(Influencer $influencer)
 * @method static bool canStartCollaboration(Model $billable)
 * @method static int getActiveCollaborationCount(Model $billable)
 * @method static bool canPublishCampaign(Business $business)
 * @method static bool canBoostCampaign(Business $business)
 * @method static bool canPromoteProfile(Model $billable)
 * @method static bool canInviteTeamMember(Business $business)
 * @method static array getLimitsSummary(Model $billable)
 *
 * @see \App\Services\SubscriptionLimitsService
 */
class SubscriptionLimits extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SubscriptionLimitsService::class;
    }
}
