<?php

/**
 * Debug Stripe Checkout Session Creation
 */

$baseUrl = 'http://127.0.0.1:8000/api';

echo "🔍 Debugging Stripe Checkout Session Creation\n";
echo "============================================\n\n";

// Test data that matches the frontend request
$testData = [
    'amount' => 1000.00,
    'currency' => 'lkr',
    'taxType' => 'Property Tax',
    'taxpayerName' => 'John Doe',
    'nic' => '123456789V',
    'email' => 'john@example.com',
    'phone' => '+94771234567',
    'address' => '123 Main Street, Colombo',
    'success_url' => 'http://localhost:3000/payment/success',
    'cancel_url' => 'http://localhost:3000/payment/cancel'
];

echo "📋 Test Data:\n";
foreach ($testData as $key => $value) {
    echo "- $key: $value\n";
}
echo "\n";

// Make the request
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
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "📡 Response Details:\n";
echo "HTTP Code: " . $httpCode . "\n";
echo "Content Type: " . ($info['content_type'] ?? 'N/A') . "\n";
echo "Response Size: " . strlen($response) . " bytes\n";

if ($error) {
    echo "❌ cURL Error: " . $error . "\n";
} else {
    echo "Response Body:\n";
    echo $response . "\n";
    
    // Try to parse as JSON
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "\n📊 Parsed JSON Response:\n";
        print_r($data);
    } else {
        echo "\n❌ Response is not valid JSON\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
}

echo "\n✅ Debug completed!\n";
