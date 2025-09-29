<?php

/**
 * Test Payment Completion with Email
 */

echo "🧪 Testing Payment Completion with Email\n";
echo "========================================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

echo "🔄 Step 1: Creating a test payment record...\n";

// First, create a Stripe payment record
$testData = [
    'tax_payee_id' => 1,
    'amount_paying' => 1500.00,
    'payment' => 1500.00,
    'pay_method' => 'online',
    'pay_date' => '2025-09-29',
    'currency' => 'lkr',
    'success_url' => 'http://localhost:3000/payment/success',
    'cancel_url' => 'http://localhost:3000/payment/cancel'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/stripe/create-checkout-session');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    $data = json_decode($response, true);
    $sessionId = $data['sessionId'] ?? null;
    
    if ($sessionId) {
        echo "✅ Payment session created: $sessionId\n\n";
        
        echo "🔄 Step 2: Simulating payment completion...\n";
        
        // Simulate webhook payload for completed payment
        $webhookPayload = [
            'id' => 'evt_test_webhook_' . time(),
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => $sessionId,
                    'object' => 'checkout.session',
                    'payment_status' => 'paid',
                    'customer_email' => 'lak.bope@gmail.com',
                    'amount_total' => 150000, // 1500.00 LKR in cents
                    'currency' => 'lkr',
                    'payment_intent' => 'pi_test_' . time()
                ]
            ]
        ];
        
        echo "📤 Sending webhook to simulate payment completion...\n";
        
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
        
        $webhookResponse = curl_exec($ch);
        $webhookHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "📡 Webhook Response:\n";
        echo "HTTP Code: $webhookHttpCode\n";
        echo "Response: $webhookResponse\n\n";
        
        if ($webhookHttpCode === 200) {
            echo "✅ Payment completion processed successfully!\n";
            echo "📧 Check your email (lak.bope@gmail.com) for payment confirmation\n";
            echo "📧 Email should be from: pathadumbarapradeshiyasabawa@gmail.com\n";
            echo "📧 Subject: Payment Confirmation - Stripe Payment #PAY_XXXXX\n\n";
            
            echo "🎯 What to expect in the email:\n";
            echo "- Professional HTML design\n";
            echo "- Payment details (amount, date, etc.)\n";
            echo "- PDPS branding\n";
            echo "- Payment confirmation message\n";
            
        } else {
            echo "❌ Webhook processing failed\n";
            echo "This means the payment record wasn't found or there's an issue with the webhook handler\n";
        }
        
    } else {
        echo "❌ Failed to get session ID from response\n";
    }
} else {
    echo "❌ Failed to create payment session\n";
    echo "Response: $response\n";
}

echo "\n✅ Test completed!\n";
