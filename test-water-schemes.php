<?php

/**
 * Test Water Schemes API
 */

echo "🧪 Testing Water Schemes API\n";
echo "============================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

echo "🔄 Testing GET /api/water-schemes...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/water-schemes');
curl_setopt($ch, CURLOPT_HTTPGET, true);
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

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "Response: $response\n\n";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Response is valid JSON\n";
        
        if (isset($data['success']) && $data['success']) {
            echo "✅ Water schemes API working!\n";
            echo "Message: " . ($data['message'] ?? 'N/A') . "\n";
            echo "Data count: " . (isset($data['data']) ? count($data['data']) : 0) . " schemes\n";
        } else {
            echo "❌ API returned error\n";
            echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Response is not valid JSON\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
}

echo "\n🎯 Available Water Scheme Endpoints:\n";
echo "GET    /api/water-schemes          - List all water schemes\n";
echo "POST   /api/water-schemes          - Create new water scheme\n";
echo "GET    /api/water-schemes/{id}     - Get specific water scheme\n";
echo "PUT    /api/water-schemes/{id}     - Update water scheme\n";
echo "DELETE /api/water-schemes/{id}    - Delete water scheme\n\n";

echo "✅ Test completed!\n";

