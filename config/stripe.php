<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Stripe payment processing.
    | You can set your API keys, webhook secrets, and other settings here.
    |
    */

    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    |
    | Default currency for payments. Note that Stripe supports many currencies,
    | but some features may be limited for certain currencies.
    |
    */
    'currency' => env('STRIPE_CURRENCY', 'lkr'),

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for payment processing behavior.
    |
    */
    'payment_methods' => [
        'card',
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for handling Stripe webhooks.
    |
    */
    'webhook_tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300), // 5 minutes

    /*
    |--------------------------------------------------------------------------
    | Tax Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for tax calculations and display.
    |
    */
    'tax_behavior' => 'inclusive', // 'inclusive' or 'exclusive'
    'automatic_tax' => [
        'enabled' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Success/Cancel URLs
    |--------------------------------------------------------------------------
    |
    | Default URLs for payment success and cancellation.
    |
    */
    'success_url' => env('STRIPE_SUCCESS_URL', '/payment/success'),
    'cancel_url' => env('STRIPE_CANCEL_URL', '/payment/cancel'),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging for Stripe operations.
    |
    */
    'logging' => [
        'enabled' => env('STRIPE_LOGGING_ENABLED', true),
        'level' => env('STRIPE_LOG_LEVEL', 'info'),
    ],
];

