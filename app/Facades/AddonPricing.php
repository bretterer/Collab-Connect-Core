<?php

namespace App\Facades;

use App\Enums\AccountType;
use App\Models\AddonPriceMapping;
use App\Models\StripePrice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection getAddonPricesForCreditKey(string $creditKey, AccountType $accountType)
 * @method static AddonPriceMapping|null getPrimaryAddonPrice(string $creditKey, AccountType $accountType)
 * @method static Collection getAllPurchasableAddons(AccountType $accountType)
 * @method static Collection getAvailableOneTimePrices()
 * @method static AddonPriceMapping createMapping(StripePrice $price, string $creditKey, int $creditsGranted, string $accountType, array $options = [])
 *
 * @see \App\Services\AddonPricingService
 */
class AddonPricing extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\AddonPricingService::class;
    }
}
