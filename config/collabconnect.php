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
];
