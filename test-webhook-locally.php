<?php

/**
 * Test Stripe Webhook Locally
 */

echo "🧪 Testing Stripe Webhook Locally\n";
echo "=================================\n\n";

echo "📋 Steps to Test Webhook:\n\n";

echo "1. Install ngrok (if not installed):\n";
echo "   - Download from: https://ngrok.com/download\n";
echo "   - Or use: winget install ngrok/ngrok\n\n";

echo "2. Start your Laravel server:\n";
echo "   php artisan serve --host=0.0.0.0 --port=8000\n\n";

echo "3. In another terminal, expose your server:\n";
echo "   ngrok http 8000\n\n";

echo "4. Copy the ngrok URL (like: https://abc123.ngrok.io)\n\n";

echo "5. Configure Stripe Webhook:\n";
echo "   - Go to: https://dashboard.stripe.com/test/webhooks\n";
echo "   - Add endpoint: https://abc123.ngrok.io/api/webhooks/stripe\n";
echo "   - Select events: checkout.session.completed\n\n";

echo "6. Test the complete flow:\n";
echo "   - Create payment session\n";
echo "   - Complete payment with test card\n";
echo "   - Check for webhook delivery in Stripe dashboard\n";
echo "   - Check your email for confirmation\n\n";

echo "🎯 Alternative: Manual Webhook Test\n";
echo "==================================\n\n";

echo "You can also manually trigger the webhook for testing:\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

// Simulate a webhook payload
$webhookPayload = [
    'id' => 'evt_test_webhook',
    'object' => 'event',
    'type' => 'checkout.session.completed',
    'data' => [
        'object' => [
            'id' => 'cs_test_manual_webhook',
            'object' => 'checkout.session',
            'payment_status' => 'paid',
            'customer_email' => 'lak.bope@gmail.com',
            'amount_total' => 250000, // 2500.00 LKR in cents
            'currency' => 'lkr'
        ]
    ]
];

echo "📤 Sending manual webhook test...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/webhooks/stripe');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📡 Webhook Response:\n";
echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode === 200) {
    echo "✅ Webhook processed successfully!\n";
    echo "📧 Check your email for payment confirmation\n";
} else {
    echo "❌ Webhook processing failed\n";
}

echo "\n✅ Test completed!\n";
