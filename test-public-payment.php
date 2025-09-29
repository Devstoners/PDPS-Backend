<?php

/**
 * Test Public Payment (No Officer Involvement)
 */

echo "🧪 Testing Public Payment System\n";
echo "=================================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

// Test data for public payment (no officer)
$testData = [
    'tax_payee_id' => 1,
    'tax_property_id' => 1,
    'tax_assessment_id' => 1,
    'amount_paying' => 1000.00,
    'payment' => 1000.00,
    'pay_method' => 'online',
    'pay_date' => '2025-09-29',
    'currency' => 'lkr',
    // No officer_id - this is a public payment
    'success_url' => 'http://localhost:3000/payment/success',
    'cancel_url' => 'http://localhost:3000/payment/cancel'
];

echo "📋 Test Data (Public Payment):\n";
foreach ($testData as $key => $value) {
    echo "- $key: " . ($value === null ? 'null' : $value) . "\n";
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
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Response is valid JSON\n";
        if (isset($data['success']) && $data['success']) {
            echo "✅ Public payment session created successfully\n";
            echo "Session URL: " . ($data['data']['session_url'] ?? 'N/A') . "\n";
            echo "Payment Type: Public Payment (No Officer)\n";
        } else {
            echo "❌ Payment session creation failed\n";
            echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
            if (isset($data['errors'])) {
                echo "Validation Errors:\n";
                foreach ($data['errors'] as $field => $errors) {
                    echo "  - $field: " . implode(', ', $errors) . "\n";
                }
            }
        }
    } else {
        echo "❌ Response is not valid JSON\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
}

echo "\n✅ Test completed!\n";
