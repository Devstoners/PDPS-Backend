<?php

/**
 * Test both Stripe endpoints to compare responses
 */

echo "🧪 Testing Both Stripe Endpoints\n";
echo "===============================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';
$testData = [
    'amount' => 1000,
    'currency' => 'lkr',
    'taxType' => 'Property Tax',
    'taxpayerName' => 'John Doe',
    'nic' => '123456789V',
    'email' => 'john@example.com'
];

// Test 1: Simple test endpoint
echo "📋 Test 1: Simple Test Endpoint\n";
echo "URL: $baseUrl/stripe/test\n";
echo "Method: POST\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/stripe/test');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response1 = curl_exec($ch);
$httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType1 = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "Response 1:\n";
echo "HTTP Code: $httpCode1\n";
echo "Content-Type: $contentType1\n";
echo "Response: $response1\n\n";

// Test 2: Full Stripe endpoint
echo "📋 Test 2: Full Stripe Endpoint\n";
echo "URL: $baseUrl/stripe/create-checkout-session\n";
echo "Method: POST\n\n";

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

$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType2 = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "Response 2:\n";
echo "HTTP Code: $httpCode2\n";
echo "Content-Type: $contentType2\n";
echo "Response: $response2\n\n";

// Analysis
echo "📊 Analysis:\n";
echo "Test 1 (Simple): " . ($httpCode1 === 200 ? "✅ Working" : "❌ Failed") . "\n";
echo "Test 2 (Stripe): " . ($httpCode2 === 201 ? "✅ Working" : "❌ Failed") . "\n";

if ($httpCode1 !== 200) {
    echo "❌ Simple test failed - routing issue\n";
} elseif ($httpCode2 !== 201) {
    echo "❌ Stripe endpoint failed - controller issue\n";
} else {
    echo "✅ Both endpoints working - issue is frontend-specific\n";
}

echo "\n✅ Test completed!\n";
