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
];
