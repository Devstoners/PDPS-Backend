<?php

/**
 * Simple Delete Test (without authentication)
 */

echo "🧪 Testing Tax Payee Delete (Simple Test)\n";
echo "=========================================\n\n";

// Test delete with a non-existent ID (should return 404 or 401)
$testId = 999;
$deleteUrl = "http://127.0.0.1:8000/api/tax-payees/$testId";

echo "🚀 Testing DELETE request to: $deleteUrl\n\n";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $deleteUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
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
        
        if ($httpCode === 401) {
            echo "\n✅ EXPECTED: Unauthenticated (this is normal without auth token)\n";
        } elseif ($httpCode === 404) {
            echo "\n✅ EXPECTED: Not found (tax payee doesn't exist)\n";
        } elseif ($httpCode === 500) {
            echo "\n❌ ERROR: Server error (this is the problem we're fixing)\n";
        } else {
            echo "\n📋 Response received (HTTP $httpCode)\n";
        }
    } else {
        echo "Raw Response: $response\n";
    }
}

echo "\n🎉 Simple delete test completed!\n";
