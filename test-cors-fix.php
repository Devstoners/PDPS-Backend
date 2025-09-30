<?php

/**
 * Test CORS Fix for taxpayers/verify endpoint
 */

echo "🧪 Testing CORS Fix\n";
echo "==================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

// Test data for taxpayer verification
$testData = [
    'nic' => '883323386V'
];

echo "📋 Test Data:\n";
foreach ($testData as $key => $value) {
    echo "- $key: $value\n";
}
echo "\n";

echo "🔄 Testing taxpayers/verify endpoint...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/taxpayers/verify');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Origin: http://localhost:3000',
    'Access-Control-Request-Method: POST',
    'Access-Control-Request-Headers: Content-Type'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
curl_close($ch);

echo "📡 Response:\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "Response: $response\n\n";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Response is valid JSON\n";
        
        if (isset($data['success']) && $data['success']) {
            echo "✅ Taxpayer verification successful!\n";
            echo "Taxpayer: " . ($data['data']['name'] ?? 'N/A') . "\n";
            echo "NIC: " . ($data['data']['nic'] ?? 'N/A') . "\n";
        } else {
            echo "❌ Taxpayer verification failed\n";
            echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Response is not valid JSON\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
}

echo "\n🔧 CORS Configuration Check:\n";
echo "1. Check config/cors.php\n";
echo "2. Verify HandleCors middleware is enabled\n";
echo "3. Check allowed origins include your frontend URL\n";
echo "4. Ensure proper headers are set\n\n";

echo "🎯 Frontend Fix:\n";
echo "Make sure your frontend includes proper headers:\n";
echo "- Content-Type: application/json\n";
echo "- Accept: application/json\n";
echo "- Origin: http://localhost:3000\n\n";

echo "✅ Test completed!\n";

