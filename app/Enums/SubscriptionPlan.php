<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum SubscriptionPlan: string
{
    use HasFormOptions;

    // Business Plans
    case BUSINESS_STARTER = 'starter';
    case BUSINESS_PROFESSIONAL = 'professional';
    case BUSINESS_ENTERPRISE = 'enterprise';

    // Influencer Plans (based on follower count)
    case INFLUENCER_UNDER_10K = 'under_10k';
    case INFLUENCER_10K_TO_50K = '10k_to_50k';
    case INFLUENCER_OVER_50K = 'over_50k';
    case INFLUENCER_A_LA_CARTE = 'a_la_carte';

    public function label(): string
    {
        return match ($this) {
            self::BUSINESS_STARTER => 'Starter - $29/month (Perfect for single location businesses)',
            self::BUSINESS_PROFESSIONAL => 'Professional - $79/month (Great for multi-location businesses)',
            self::BUSINESS_ENTERPRISE => 'Enterprise - Custom pricing (For large chains and franchises)',
            self::INFLUENCER_UNDER_10K => 'Under 10,000 followers',
            self::INFLUENCER_10K_TO_50K => '10,000 - 49,999 followers',
            self::INFLUENCER_OVER_50K => '50,000+ followers',
            self::INFLUENCER_A_LA_CARTE => 'A La Carte',
        };
    }

    public function price(): ?float
    {
        return match ($this) {
            self::BUSINESS_STARTER => 29.00,
            self::BUSINESS_PROFESSIONAL => 79.00,
            self::BUSINESS_ENTERPRISE => null, // Custom pricing
            self::INFLUENCER_UNDER_10K => 0.00, // Free tier
            self::INFLUENCER_10K_TO_50K => 19.00,
            self::INFLUENCER_OVER_50K => 39.00,
            self::INFLUENCER_A_LA_CARTE => null, // Variable pricing
        };
    }

    public function isBusinessPlan(): bool
    {
        return in_array($this, [
            self::BUSINESS_STARTER,
            self::BUSINESS_PROFESSIONAL,
            self::BUSINESS_ENTERPRISE,
        ]);
    }

    public function isInfluencerPlan(): bool
    {
        return in_array($this, [
            self::INFLUENCER_UNDER_10K,
            self::INFLUENCER_10K_TO_50K,
            self::INFLUENCER_OVER_50K,
            self::INFLUENCER_A_LA_CARTE,
        ]);
    }

    /**
     * Get subscription plans for businesses
     */
    public static function forBusinesses(): array
    {
        return [
            self::BUSINESS_STARTER,
            self::BUSINESS_PROFESSIONAL,
            self::BUSINESS_ENTERPRISE,
        ];
    }

    /**
     * Get subscription plans for influencers
     */
    public static function forInfluencers(): array
    {
        return [
            self::INFLUENCER_UNDER_10K,
            self::INFLUENCER_10K_TO_50K,
            self::INFLUENCER_OVER_50K,
            self::INFLUENCER_A_LA_CARTE,
        ];
    }


}
