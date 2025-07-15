<?php

namespace App\Services;

use App\Enums\CampaignType;
use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Enums\SubscriptionPlan;

class ValidationService
{
    /**
     * Get validation rules for user fields
     */
    public static function userRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get validation rules for profile fields
     */
    public static function profileRules(): array
    {
        return [
            'primary_zip_code' => 'required|string|max:10',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
        ];
    }

    /**
     * Get validation rules for business profile step 1
     */
    public static function businessStep1Rules(): array
    {
        return [
            'businessName' => 'required|string|max:255',
            'industry' => 'required|' . Niche::validationRule(),
            'primaryZipCode' => 'required|string|max:10',
            'locationCount' => 'required|integer|min:1',
            'websites.*' => 'nullable|url',
        ];
    }

    /**
     * Get validation rules for business profile step 2
     */
    public static function businessStep2Rules(): array
    {
        return [
            'contactName' => 'required|string|max:255',
            'contactEmail' => 'required|email|max:255',
            'subscriptionPlan' => 'required|' . SubscriptionPlan::validationRule(),
        ];
    }

    /**
     * Get validation rules for business profile step 3
     */
    public static function businessStep3Rules(): array
    {
        return [
            'collaborationGoals' => 'required|array|min:1',
            'collaborationGoals.*' => CollaborationGoal::validationRule(),
            'campaignTypes' => 'required|array|min:1',
            'campaignTypes.*' => CampaignType::validationRule(),
        ];
    }

    /**
     * Get validation rules for business profile step 4
     */
    public static function businessStep4Rules(): array
    {
        return [
            'teamMembers.*.name' => 'required|string|max:255',
            'teamMembers.*.email' => 'required|email|max:255',
        ];
    }

    /**
     * Get validation rules for influencer profile step 1
     */
    public static function influencerStep1Rules(): array
    {
        return [
            'creatorName' => 'required|string|max:255',
            'primaryNiche' => 'required|' . Niche::validationRule(),
            'primaryZipCode' => 'required|string|max:10',
        ];
    }

    /**
     * Get validation rules for influencer profile step 2
     */
    public static function influencerStep2Rules(): array
    {
        return [
            'socialMediaAccounts.*.platform' => 'required|' . SocialPlatform::validationRule(),
            'socialMediaAccounts.*.username' => 'required|string|max:255',
            'socialMediaAccounts.*.follower_count' => 'required|integer|min:0',
        ];
    }

    /**
     * Get validation rules for influencer profile step 3
     */
    public static function influencerStep3Rules(): array
    {
        return [
            'mediaKitUrl' => 'nullable|url',
        ];
    }

    /**
     * Get validation rules for influencer profile step 4
     */
    public static function influencerStep4Rules(): array
    {
        return [
            'collaborationPreferences' => 'required|array|min:1',
            'collaborationPreferences.*' => CollaborationGoal::validationRule(),
            'subscriptionPlan' => 'required|' . SubscriptionPlan::validationRule(),
        ];
    }

    /**
     * Get validation rules for social media account
     */
    public static function socialMediaAccountRules(): array
    {
        return [
            'platform' => 'required|' . SocialPlatform::validationRule(),
            'username' => 'required|string|max:255',
            'follower_count' => 'required|integer|min:0',
            'url' => 'nullable|url',
        ];
    }

    /**
     * Get validation rules for search filters
     */
    public static function searchRules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'selectedNiches' => 'nullable|array',
            'selectedNiches.*' => Niche::validationRule(),
            'selectedPlatforms' => 'nullable|array',
            'selectedPlatforms.*' => SocialPlatform::validationRule(),
            'minFollowers' => 'nullable|integer|min:0',
            'maxFollowers' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'sortBy' => 'nullable|in:name,newest,oldest,followers,distance',
            'searchRadius' => 'nullable|integer|min:1|max:500',
        ];
    }

    /**
     * Get validation rules for authentication
     */
    public static function authRules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'remember' => 'boolean',
        ];
    }

    /**
     * Get validation rules for registration
     */
    public static function registrationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get validation rules for password reset
     */
    public static function passwordResetRules(): array
    {
        return [
            'token' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get validation rules for account type selection
     */
    public static function accountTypeRules(): array
    {
        return [
            'selectedAccountType' => 'required|in:business,influencer',
        ];
    }

    /**
     * Get validation rules based on step and type
     */
    public static function getStepRules(string $type, int $step): array
    {
        return match ($type) {
            'business' => match ($step) {
                1 => self::businessStep1Rules(),
                2 => self::businessStep2Rules(),
                3 => self::businessStep3Rules(),
                4 => self::businessStep4Rules(),
                default => [],
            },
            'influencer' => match ($step) {
                1 => self::influencerStep1Rules(),
                2 => self::influencerStep2Rules(),
                3 => self::influencerStep3Rules(),
                4 => self::influencerStep4Rules(),
                default => [],
            },
            default => [],
        };
    }
}