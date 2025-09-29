<?php

/**
 * Test Stripe Route - Check if it's working correctly
 */

echo "🧪 Testing Stripe Route\n";
echo "=====================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

// Test with GET request (should return 405)
echo "1. Testing GET request (should return 405):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/stripe/create-checkout-session');
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Test with POST request (should work)
echo "2. Testing POST request (should work):\n";
$testData = [
    'tax_payee_id' => 1,
    'amount_paying' => 1000.00,
    'payment' => 1000.00,
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

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

echo "✅ Test completed!\n";
