<?php

/**
 * Simple Stripe API Test
 */

echo "🧪 Simple Stripe API Test\n";
echo "========================\n\n";

$url = 'http://127.0.0.1:8000/api/stripe/create-checkout-session';
$data = [
    'amount' => 1000,
    'currency' => 'lkr',
    'taxType' => 'Property Tax',
    'taxpayerName' => 'John Doe',
    'nic' => '123456789V',
    'email' => 'john@example.com',
    'phone' => '+94771234567',
    'address' => '123 Main Street',
    'success_url' => 'http://localhost:3000/payment/success',
    'cancel_url' => 'http://localhost:3000/payment/cancel'
];

echo "📋 Testing URL: $url\n";
echo "📋 Method: POST\n";
echo "📋 Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$error = curl_error($ch);
curl_close($ch);

echo "📡 Response:\n";
echo "HTTP Code: $httpCode\n";
echo "Content-Type: $contentType\n";
echo "Response Length: " . strlen($response) . " bytes\n\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "Response Body:\n";
    echo $response . "\n\n";
    
    // Check if it's JSON
    $jsonData = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Response is valid JSON\n";
    } else {
        echo "❌ Response is NOT JSON\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
        echo "This means the server is returning HTML (error page)\n";
    }
}

echo "\n✅ Test completed!\n";