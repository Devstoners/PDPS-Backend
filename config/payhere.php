<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayHere Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for PayHere payment gateway integration
    | Using sandbox environment for testing
    |
    */

    'merchant_id' => env('PAYHERE_MERCHANT_ID'),
    'merchant_secret' => env('PAYHERE_MERCHANT_SECRET'),
    'checkout_url' => env('PAYHERE_CHECKOUT_URL', 'https://sandbox.payhere.lk/pay/checkout'),
    'return_url' => env('PAYHERE_RETURN_URL', env('APP_URL') . '/api/payhere/return'),
    'cancel_url' => env('PAYHERE_CANCEL_URL', env('APP_URL') . '/api/payhere/cancel'),
    'notify_url' => env('PAYHERE_NOTIFY_URL', env('APP_URL') . '/api/payhere/callback'),
    
    'currency' => 'LKR',
    'country' => 'Sri Lanka',
    
    // Sandbox specific settings
    'sandbox' => [
        'enabled' => env('PAYHERE_SANDBOX', true),
        'test_cards' => [
            'visa' => '4111111111111111',
            'mastercard' => '5555555555554444',
            'amex' => '378282246310005',
        ],
        'test_amounts' => [
            'min' => 1.00,
            'max' => 10000.00,
        ]
    ],
];