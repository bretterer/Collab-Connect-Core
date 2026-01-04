<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Default business feature categories
        $this->migrator->add('pricing_matrix.business_categories', [
            [
                'key' => 'core_features',
                'label' => 'Core Features',
                'features' => [
                    ['key' => 'campaign_discover_matching', 'label' => 'Campaign Discovery & Matching', 'type' => 'boolean', 'description' => 'Find and connect with influencers that match your brand through smart matching algorithms.'],
                    ['key' => 'collaboration_lifecycle_tools', 'label' => 'Campaign Lifecycle Tools', 'type' => 'boolean', 'description' => 'Manage campaigns from creation to completion with built-in workflow tools.'],
                    ['key' => 'in_app_chat', 'label' => 'In App Chat', 'type' => 'boolean', 'description' => 'Communicate directly with influencers through secure in-app messaging.'],
                    ['key' => 'affiliate_program', 'label' => 'Affiliate Program', 'type' => 'text', 'description' => 'Earn commission by referring new businesses and influencers to CollabConnect.'],
                ],
            ],
            [
                'key' => 'campaign_collab_limits',
                'label' => 'Campaign & Collaboration',
                'features' => [
                    ['key' => 'active_applications', 'label' => 'Active Applications', 'type' => 'number', 'description' => 'Maximum number of influencer applications you can review at once.'],
                ],
            ],
            [
                'key' => 'profile_visibility',
                'label' => 'Profile & Visibility',
                'features' => [
                    ['key' => 'introductary_promotion_boosts', 'label' => 'Introductory Profile Promotion Boost Credits', 'type' => 'number', 'description' => 'One-time credits to boost your profile visibility when you sign up.'],
                ],
            ],
        ]);

        // Default influencer feature categories
        $this->migrator->add('pricing_matrix.influencer_categories', [
            [
                'key' => 'core_features',
                'label' => 'Core Features',
                'features' => [
                    ['key' => 'campaign_discover_matching', 'label' => 'Campaign Discovery & Matching', 'type' => 'boolean', 'description' => 'Discover campaigns that match your niche, location, and audience through smart recommendations.'],
                    ['key' => 'collaboration_lifecycle_tools', 'label' => 'Campaign Lifecycle Tools', 'type' => 'boolean', 'description' => 'Track deliverables, deadlines, and communication for all your collaborations.'],
                    ['key' => 'in_app_chat', 'label' => 'In App Chat', 'type' => 'boolean', 'description' => 'Message businesses directly through secure in-app chat.'],
                    ['key' => 'affiliate_program', 'label' => 'Affiliate Program', 'type' => 'text', 'description' => 'Earn commission by referring new influencers and businesses to CollabConnect.'],
                ],
            ],
            [
                'key' => 'campaign_collab_limits',
                'label' => 'Campaign & Collaboration',
                'features' => [
                    ['key' => 'application_credits', 'label' => 'Application Credits per Month', 'type' => 'number', 'description' => 'Number of campaign applications you can submit each month.'],
                    ['key' => 'active_applications', 'label' => 'Active Applications', 'type' => 'number', 'description' => 'Maximum pending applications you can have at any time.'],
                ],
            ],
            [
                'key' => 'profile_visibility',
                'label' => 'Profile & Visibility',
                'features' => [
                    ['key' => 'introductary_promotion_boosts', 'label' => 'Introductory Profile Promotion Boost Credits', 'type' => 'number', 'description' => 'One-time credits to boost your profile visibility to businesses when you sign up.'],
                ],
            ],
        ]);

        // Highlighted price IDs (null by default)
        $this->migrator->add('pricing_matrix.highlighted_business_price_id', null);
        $this->migrator->add('pricing_matrix.highlighted_influencer_price_id', null);
    }
};
