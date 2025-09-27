<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Twilio SMS service integration
    | Using free plan for SMS notifications
    |
    */

    'account_sid' => env('TWILIO_ACCOUNT_SID'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),
    'from_number' => env('TWILIO_FROM_NUMBER'),
    
    // SMS Settings
    'sms' => [
        'enabled' => env('TWILIO_SMS_ENABLED', true),
        'max_length' => 160, // Standard SMS length
        'unicode_support' => true,
    ],

    // Notification Settings
    'notifications' => [
        'payment_confirmation' => env('TWILIO_PAYMENT_SMS', true),
        'service_reminders' => env('TWILIO_REMINDER_SMS', true),
        'overdue_notices' => env('TWILIO_OVERDUE_SMS', true),
        'reservation_updates' => env('TWILIO_RESERVATION_SMS', true),
    ],

    // Rate Limiting (Free plan limits)
    'rate_limiting' => [
        'enabled' => true,
        'max_per_hour' => 10, // Free plan limit
        'max_per_day' => 100, // Free plan limit
    ],

    // Message Templates
    'templates' => [
        'payment_confirmation' => 'Payment confirmed! Amount: LKR {amount}. Receipt: {receipt_no}. Thank you!',
        'payment_failed' => 'Payment failed for {service}. Please try again or contact support.',
        'service_reminder' => 'Reminder: Your {service} is due on {due_date}. Amount: LKR {amount}.',
        'overdue_notice' => 'URGENT: Your {service} is overdue. Amount: LKR {amount}. Please pay immediately.',
        'reservation_confirmed' => 'Hall reservation confirmed! Date: {date}, Time: {time}, Hall: {hall_name}.',
        'reservation_cancelled' => 'Hall reservation cancelled for {date}. Refund will be processed.',
        'tax_assessment' => 'New tax assessment: LKR {amount} due on {due_date}. Property: {property_name}.',
        'water_bill' => 'Water bill generated: LKR {amount} due on {due_date}. Account: {account_no}.',
    ],

    // Country Code Settings
    'country_code' => '+94', // Sri Lanka
    'default_country' => 'LK',
];
