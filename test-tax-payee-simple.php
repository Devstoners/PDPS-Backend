<?php

/**
 * Simple Tax Payee API Test
 * 
 * This script tests the Tax Payee API with a simple payload
 */

echo "🧪 Testing Tax Payee API (Simple Test)\n";
echo "=====================================\n\n";

// Test data with a unique NIC
$testData = [
    'title' => '1',
    'name' => 'Test User ' . time(),
    'nic' => '88332338' . rand(10, 99) . 'V',
    'tel' => '0778590294',
    'address' => '126 Test Address',
    'email' => 'test' . time() . '@gmail.com'
];

echo "📋 Test Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Test the API endpoint
$url = 'http://127.0.0.1:8000/api/tax-payees';

echo "🚀 Testing POST request to: $url\n\n";

// Initialize cURL
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "📊 Response:\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "Response Body:\n";
    $responseData = json_decode($response, true);
    if ($responseData) {
        echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
        
        if ($httpCode === 201) {
            echo "\n✅ SUCCESS: Tax payee created successfully!\n";
            echo "ID: " . ($responseData['data']['id'] ?? 'N/A') . "\n";
            echo "Name: " . ($responseData['data']['name'] ?? 'N/A') . "\n";
            echo "NIC: " . ($responseData['data']['nic'] ?? 'N/A') . "\n";
        } else {
            echo "\n❌ ERROR: Request failed\n";
        }
    } else {
        echo "Raw Response: $response\n";
    }
}

echo "\n🎉 Test completed!\n";
