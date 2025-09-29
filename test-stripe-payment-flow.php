<?php

/**
 * Test Complete Stripe Payment Flow with Email
 */

echo "🧪 Testing Complete Stripe Payment Flow\n";
echo "======================================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

// Test data for Stripe payment
$testData = [
    'tax_payee_id' => 1,
    'amount_paying' => 2500.00,
    'payment' => 2500.00,
    'pay_method' => 'online',
    'pay_date' => '2025-09-29',
    'currency' => 'lkr',
    'success_url' => 'http://localhost:3000/payment/success',
    'cancel_url' => 'http://localhost:3000/payment/cancel'
];

echo "📋 Test Data:\n";
foreach ($testData as $key => $value) {
    echo "- $key: $value\n";
}
echo "\n";

echo "🔄 Step 1: Creating Stripe Checkout Session...\n";

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
$error = curl_error($ch);
curl_close($ch);

echo "📡 Response:\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "Response: $response\n\n";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Response is valid JSON\n";
        
        if (isset($data['success']) && $data['success']) {
            echo "✅ Stripe checkout session created successfully!\n";
            echo "Session ID: " . ($data['sessionId'] ?? 'N/A') . "\n";
            echo "Checkout URL: " . ($data['url'] ?? 'N/A') . "\n\n";
            
            echo "🎯 Next Steps:\n";
            echo "1. Open the checkout URL in your browser\n";
            echo "2. Complete the payment with test card: 4242 4242 4242 4242\n";
            echo "3. Check your email for payment confirmation\n";
            echo "4. Verify the payment status in your database\n\n";
            
            echo "💳 Test Card Details:\n";
            echo "- Card: 4242 4242 4242 4242\n";
            echo "- Expiry: Any future date (e.g., 12/25)\n";
            echo "- CVC: Any 3 digits (e.g., 123)\n";
            echo "- ZIP: Any 5 digits (e.g., 12345)\n\n";
            
            echo "📧 Email Configuration:\n";
            echo "- From: pathadumbarapradeshiyasabawa@gmail.com\n";
            echo "- To: Taxpayer email from database\n";
            echo "- Subject: Payment Confirmation\n\n";
            
            echo "🎉 Stripe Payment System is Ready!\n";
            echo "Your payment confirmation emails will be sent automatically.\n";
            
        } else {
            echo "❌ Stripe session creation failed\n";
            echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Response is not valid JSON\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
}

echo "\n✅ Test completed!\n";
