<?php

/**
 * Test Stripe endpoint from frontend perspective
 */

echo "🌐 Testing Stripe Endpoint from Frontend Perspective\n";
echo "==================================================\n\n";

// Simulate frontend request
$url = 'http://127.0.0.1:8000/api/stripe/create-checkout-session';
$data = [
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

echo "📋 Request Details:\n";
echo "URL: $url\n";
echo "Method: POST\n";
echo "Content-Type: application/json\n";
echo "Accept: application/json\n";
echo "Origin: http://localhost:3000\n\n";

// Make the request with frontend-like headers
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Origin: http://localhost:3000',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$error = curl_error($ch);
curl_close($ch);

echo "📡 Response Details:\n";
echo "HTTP Code: $httpCode\n";
echo "Content-Type: $contentType\n";
echo "Response Length: " . strlen($response) . " bytes\n\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "Response Body:\n";
    echo $response . "\n\n";
    
    // Check if response is JSON
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Response is valid JSON\n";
        if (isset($data['success']) && $data['success']) {
            echo "✅ Stripe session created successfully\n";
            echo "Session URL: " . ($data['data']['session_url'] ?? 'N/A') . "\n";
        } else {
            echo "❌ Stripe session creation failed\n";
            echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Response is not valid JSON\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
        echo "This suggests the server is returning HTML (error page) instead of JSON\n";
    }
}

echo "\n✅ Test completed!\n";
