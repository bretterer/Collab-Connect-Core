<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Support Email Address
    |--------------------------------------------------------------------------
    |
    | This email address is used for support requests and contact forms.
    | Users will be able to send messages to this address when they need help.
    |
    */

    'support_email' => env('SUPPORT_EMAIL', 'support@collabconnect.com'),

    /*
    |--------------------------------------------------------------------------
    | Support Response Days
    |--------------------------------------------------------------------------
    |
    | This value defines the expected response days for support requests.
    | It is used to inform users about how long they can expect to wait for a reply.
    |
    */

    'support_response_days' => env('SUPPORT_RESPONSE_DAYS', 4), // in business days

    /*
    |--------------------------------------------------------------------------
    | Registration Enabled
    |--------------------------------------------------------------------------
    |
    | This setting determines whether user registration is enabled or not.
    | If set to true, users can register; if false, registration is disabled.
    |
    */

    'registration_enabled' => env('REGISTRATION_ENABLED', false),

    'beta_registration_only' => env('BETA_REGISTRATION_ONLY', false),

    /*
    |--------------------------------------------------------------------------
    | Initial User Email
    |--------------------------------------------------------------------------
    |
    | This value is used for development/testing purposes to pre-populate
    | user email fields or other initialization tasks.
    |
    */

    'init_user_email' => env('INIT_USER_EMAIL'),
    'init_business_email' => env('INIT_BUSINESS_EMAIL'),
    'init_influencer_email' => env('INIT_INFLUENCER_EMAIL'),

    'stripe' => [
        'products' => [
            'influencer' => env('STRIPE_PRODUCT_INFLUENCER'),
            'business' => env('STRIPE_PRODUCT_BUSINESS'),
        ],
        'prices' => [
            'influencer_basic' => env('STRIPE_PRICE_INFLUENCER_BASIC'),
            'business_basic' => env('STRIPE_PRICE_BUSINESS_BASIC'),
            'business_pro' => env('STRIPE_PRICE_BUSINESS_PRO'),
        ],
        'subscriptions' => [
            'start_date' => env('SUBSCRIPTIONS_START_DATE', now()->toDateString()),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Initialization / Seeding Configuration
    |--------------------------------------------------------------------------
    |
    | These values are used for seeding test data with real Stripe resources.
    | Set these in your .env file to use actual Stripe subscriptions/customers
    | instead of fake test data.
    |
    */

    'initialization' => [
        'business' => [
            'customer_id' => env('INIT_BUSINESS_STRIPE_CUSTOMER_ID'),
            'subscription_id' => env('INIT_BUSINESS_STRIPE_SUBSCRIPTION_ID'),
        ],
        'influencer' => [
            'customer_id' => env('INIT_INFLUENCER_STRIPE_CUSTOMER_ID'),
            'subscription_id' => env('INIT_INFLUENCER_STRIPE_SUBSCRIPTION_ID'),
        ],
    ],

    'referrals' => [
        'default_percentage' => env('REFERRAL_DEFAULT_PERCENTAGE', 10),
    ],

    'pennant' => [
        'global_enable' => explode('|', env('PENNANT_FEATURES_GLOBAL_ENABLE', '')),
        'global_disable' => explode('|', env('PENNANT_FEATURES_GLOBAL_DISABLE', '')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Tiers Configuration
    |--------------------------------------------------------------------------
    |
    | Define subscription tier hierarchy and feature limits for each account type.
    | Tiers are ordered from lowest to highest. The 'lookup_key' maps to
    | Stripe price lookup keys. Feature limits can be integers or true/false.
    |
    */

    'subscription_tiers' => [
        'influencer' => [
            // Tier hierarchy from lowest to highest
            'hierarchy' => ['professional', 'elite'],

            // Map Stripe lookup keys to tier names
            'lookup_keys' => [
                'influencer_professional' => 'professional',
                'influencer_elite' => 'elite',
            ],

            // Feature limits per tier
            'features' => [
                'professional' => [
                    'link_in_bio_links' => 3,
                    'link_in_bio_customization' => false,
                    'analytics_basic' => true,
                    'analytics_advanced' => false,
                ],
                'elite' => [
                    'link_in_bio_links' => -1, // -1 means unlimited
                    'link_in_bio_customization' => true,
                    'analytics_basic' => true,
                    'analytics_advanced' => true,
                ],
            ],
        ],

        'business' => [
            // Tier hierarchy from lowest to highest
            'hierarchy' => ['essential', 'professional'],

            // Map Stripe lookup keys to tier names
            'lookup_keys' => [
                'business_essential' => 'essential',
                'business_professional' => 'professional',
            ],

            // Feature limits per tier
            'features' => [
                'essential' => [
                    'campaigns_active' => 3,
                    'team_members' => 1,
                    'analytics_basic' => true,
                    'analytics_advanced' => false,
                ],
                'professional' => [
                    'campaigns_active' => -1, // unlimited
                    'team_members' => 5,
                    'analytics_basic' => true,
                    'analytics_advanced' => true,
                ],
            ],
        ],
    ],
];
