<?php

namespace App\Services;

use App\Enums\SocialPlatform;
use App\Events\BusinessJoined;
use App\Events\InfluencerJoined;
use App\Events\ProfileUpdated;
use App\Models\BusinessProfile;
use App\Models\InfluencerProfile;
use App\Models\SocialMediaAccount;
use App\Models\User;

class ProfileService
{
    /**
     * Create a business profile for a user
     */
    public static function createBusinessProfile(User $user, array $data): BusinessProfile
    {
        $businessProfile = $user->businessProfile()->create([
            'user_id' => $user->id,
            'business_name' => $data['businessName'],
            'industry' => $data['industry'],
            'websites' => array_filter($data['websites'] ?? [], fn ($website) => ! empty($website)),
            'primary_zip_code' => $data['primaryZipCode'],
            'location_count' => $data['locationCount'],
            'is_franchise' => $data['isFranchise'] ?? false,
            'is_national_brand' => $data['isNationalBrand'] ?? false,
            'contact_name' => $data['contactName'],
            'contact_email' => $data['contactEmail'],
            'collaboration_goals' => $data['collaborationGoals'] ?? [],
            'campaign_types' => $data['campaignTypes'] ?? [],
            'team_members' => $data['teamMembers'] ?? [],
            'onboarding_completed' => true,
        ]);

        // Fire the BusinessJoined event
        event(new BusinessJoined($user, $businessProfile));

        return $businessProfile;
    }

    /**
     * Create an influencer profile for a user
     */
    public static function createInfluencerProfile(User $user, array $data): InfluencerProfile
    {
        $influencerProfile = $user->influencerProfile()->create([
            'user_id' => $user->id,
            'creator_name' => $data['creatorName'],
            'primary_niche' => $data['primaryNiche'],
            'primary_zip_code' => $data['primaryZipCode'],
            'onboarding_completed' => true,
        ]);

        // Fire the InfluencerJoined event
        event(new InfluencerJoined($user, $influencerProfile));

        return $influencerProfile;
    }

    /**
     * Create social media accounts for a user
     */
    public static function createSocialMediaAccounts(User $user, array $accounts): array
    {
        $createdAccounts = [];

        foreach ($accounts as $account) {
            if (! empty($account['username'])) {
                $createdAccounts[] = $user->socialMediaAccounts()->create([
                    'user_id' => $user->id,
                    'platform' => $account['platform'],
                    'username' => $account['username'],
                    'url' => self::generateSocialMediaUrl($account['platform'], $account['username']),
                    'follower_count' => $account['follower_count'] ?? 0,
                    'is_primary' => $account['is_primary'] ?? false,
                    'is_verified' => $account['is_verified'] ?? false,
                ]);
            }
        }

        return $createdAccounts;
    }

    /**
     * Generate social media URL from platform and username
     */
    public static function generateSocialMediaUrl(string $platform, string $username): string
    {
        $platformEnum = SocialPlatform::from($platform);

        return $platformEnum->generateUrl($username);
    }

    /**
     * Update business profile
     */
    public static function updateBusinessProfile(BusinessProfile $profile, array $data): BusinessProfile
    {
        // Store original values to track changes
        $originalData = $profile->toArray();

        $profile->update([
            'business_name' => $data['businessName'] ?? $profile->business_name,
            'industry' => $data['industry'] ?? $profile->industry,
            'websites' => isset($data['websites']) ? array_filter($data['websites'], fn ($website) => ! empty($website)) : $profile->websites,
            'primary_zip_code' => $data['primaryZipCode'] ?? $profile->primary_zip_code,
            'location_count' => $data['locationCount'] ?? $profile->location_count,
            'is_franchise' => $data['isFranchise'] ?? $profile->is_franchise,
            'is_national_brand' => $data['isNationalBrand'] ?? $profile->is_national_brand,
            'contact_name' => $data['contactName'] ?? $profile->contact_name,
            'contact_email' => $data['contactEmail'] ?? $profile->contact_email,
            'subscription_plan' => $data['subscriptionPlan'] ?? $profile->subscription_plan,
            'collaboration_goals' => $data['collaborationGoals'] ?? $profile->collaboration_goals,
            'campaign_types' => $data['campaignTypes'] ?? $profile->campaign_types,
            'team_members' => $data['teamMembers'] ?? $profile->team_members,
        ]);

        // Determine what changed
        $changes = array_diff_assoc($profile->fresh()->toArray(), $originalData);

        // Fire the ProfileUpdated event if there were changes
        if (! empty($changes)) {
            event(new ProfileUpdated($profile->user, 'business', $changes));
        }

        return $profile;
    }

    /**
     * Update influencer profile
     */
    public static function updateInfluencerProfile(InfluencerProfile $profile, array $data): InfluencerProfile
    {
        // Store original values to track changes
        $originalData = $profile->toArray();

        $profile->update([
            'creator_name' => $data['creatorName'] ?? $profile->creator_name,
            'primary_niche' => $data['primaryNiche'] ?? $profile->primary_niche,
            'primary_zip_code' => $data['primaryZipCode'] ?? $profile->primary_zip_code,
            'media_kit_url' => $data['mediaKitUrl'] ?? $profile->media_kit_url,
            'has_media_kit' => $data['hasMediaKit'] ?? $profile->has_media_kit,
            'collaboration_preferences' => $data['collaborationPreferences'] ?? $profile->collaboration_preferences,
            'preferred_brands' => $data['preferredBrands'] ?? $profile->preferred_brands,
            'subscription_plan' => $data['subscriptionPlan'] ?? $profile->subscription_plan,
        ]);

        // Determine what changed
        $changes = array_diff_assoc($profile->fresh()->toArray(), $originalData);

        // Fire the ProfileUpdated event if there were changes
        if (! empty($changes)) {
            event(new ProfileUpdated($profile->user, 'influencer', $changes));
        }

        return $profile;
    }

    /**
     * Check if user has completed onboarding
     */
    public static function hasCompletedOnboarding(User $user): bool
    {
        return match ($user->account_type) {
            \App\Enums\AccountType::BUSINESS => $user->currentBusiness->onboarding_complete ?? false,
            \App\Enums\AccountType::INFLUENCER => $user->influencerProfile?->onboarding_completed ?? false,
            default => false,
        };
    }

    /**
     * Get user's profile based on account type
     */
    public static function getUserProfile(User $user): BusinessProfile|InfluencerProfile|null
    {
        return match ($user->account_type) {
            \App\Enums\AccountType::BUSINESS => $user->businessProfile,
            \App\Enums\AccountType::INFLUENCER => $user->influencerProfile,
            default => null,
        };
    }

    /**
     * Get user's primary social media account
     */
    public static function getPrimarySocialMediaAccount(User $user): ?SocialMediaAccount
    {
        return $user->socialMediaAccounts()->where('is_primary', true)->first();
    }

    /**
     * Set primary social media account
     */
    public static function setPrimarySocialMediaAccount(User $user, int $accountId): void
    {
        // Remove primary flag from all accounts
        $user->socialMediaAccounts()->update(['is_primary' => false]);

        // Set the specified account as primary
        $user->socialMediaAccounts()->where('id', $accountId)->update(['is_primary' => true]);
    }

    /**
     * Get user's total follower count across all platforms
     */
    public static function getTotalFollowerCount(User $user): int
    {
        return $user->socialMediaAccounts()->sum('follower_count');
    }

    /**
     * Get user's postal code info
     */
    public static function getUserPostalCodeInfo(User $user): ?string
    {
        $profile = self::getUserProfile($user);

        return match ($user->account_type) {
            \App\Enums\AccountType::BUSINESS => $profile?->primary_zip_code,
            \App\Enums\AccountType::INFLUENCER => $profile?->primary_zip_code,
            default => null,
        };
    }
}
