<?php

/**
 * Test Tax Assessment Creation
 */

echo "🧪 Testing Tax Assessment Creation\n";
echo "=================================\n\n";

// Test data
$testData = [
    'tax_payee_id' => 2,
    'tax_property_id' => '2',
    'year' => '2025',
    'amount' => '100000.00',
    'due_date' => '2026-03-26',
    'officer_id' => '35',
    'status' => 'unpaid'
];

echo "📋 Test Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Test the API endpoint
$url = 'http://127.0.0.1:8000/api/tax-assessments';

echo "🚀 Testing POST request to: $url\n\n";

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
            echo "\n✅ SUCCESS: Tax assessment created successfully!\n";
            echo "ID: " . ($responseData['data']['id'] ?? 'N/A') . "\n";
            echo "Amount: " . ($responseData['data']['amount'] ?? 'N/A') . "\n";
            echo "Due Date: " . ($responseData['data']['due_date'] ?? 'N/A') . "\n";
        } else {
            echo "\n❌ ERROR: Request failed\n";
        }
    } else {
        echo "Raw Response: $response\n";
    }
}

echo "\n🎉 Test completed!\n";
