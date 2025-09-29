<?php

/**
 * Test Frontend Response Format
 */

echo "🧪 Testing Frontend Response Format\n";
echo "==================================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

// Test data
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

echo "📋 Test Data:\n";
foreach ($testData as $key => $value) {
    echo "- $key: $value\n";
}
echo "\n";

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
echo "Response Length: " . strlen($response) . " bytes\n\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "Response Body:\n";
    echo $response . "\n\n";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Response is valid JSON\n";
        
        // Check for frontend-expected fields
        echo "\n🔍 Frontend Field Check:\n";
        echo "- success: " . (isset($data['success']) ? ($data['success'] ? 'true' : 'false') : 'MISSING') . "\n";
        echo "- message: " . (isset($data['message']) ? $data['message'] : 'MISSING') . "\n";
        echo "- sessionId: " . (isset($data['sessionId']) ? $data['sessionId'] : 'MISSING') . "\n";
        echo "- url: " . (isset($data['url']) ? $data['url'] : 'MISSING') . "\n";
        echo "- payment: " . (isset($data['payment']) ? 'EXISTS' : 'MISSING') . "\n";
        
        if (isset($data['sessionId']) && isset($data['url'])) {
            echo "\n✅ Frontend should now work correctly!\n";
            echo "Session ID: " . $data['sessionId'] . "\n";
            echo "Checkout URL: " . $data['url'] . "\n";
        } else {
            echo "\n❌ Frontend will still have issues - missing sessionId or url\n";
        }
    } else {
        echo "❌ Response is not valid JSON\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
}

echo "\n✅ Test completed!\n";
