<?php

/**
 * Test Taxpayer Verification Endpoint
 */

$baseUrl = 'http://127.0.0.1:8000/api';

echo "🧪 Testing Taxpayer Verification Endpoint\n";
echo "=====================================\n\n";

// Test data
$testData = [
    'nic' => '883323386V'  // Use a known NIC from your database
];

echo "📋 Test Data:\n";
echo "NIC: " . $testData['nic'] . "\n\n";

// Make the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/taxpayers/verify');
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
echo "HTTP Code: " . $httpCode . "\n";

if ($error) {
    echo "❌ cURL Error: " . $error . "\n";
} else {
    echo "Response Body: " . $response . "\n";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "\n📊 Parsed Response:\n";
        echo "Success: " . ($data['success'] ?? 'N/A') . "\n";
        echo "Message: " . ($data['message'] ?? 'N/A') . "\n";
        
        if (isset($data['data']) && $data['data']) {
            echo "Taxpayer Data:\n";
            echo "- ID: " . ($data['data']['id'] ?? 'N/A') . "\n";
            echo "- Name: " . ($data['data']['name'] ?? 'N/A') . "\n";
            echo "- NIC: " . ($data['data']['nic'] ?? 'N/A') . "\n";
            echo "- Email: " . ($data['data']['email'] ?? 'N/A') . "\n";
            echo "- Phone: " . ($data['data']['tel'] ?? 'N/A') . "\n";
            echo "- Address: " . ($data['data']['address'] ?? 'N/A') . "\n";
        }
    }
}

echo "\n✅ Test completed!\n";
