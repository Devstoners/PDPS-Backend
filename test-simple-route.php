<?php

/**
 * Simple Route Test
 */

echo "🧪 Testing Simple Route\n";
echo "======================\n\n";

// Test the property types endpoint
$url = 'http://127.0.0.1:8000/api/tax-properties/types';

echo "🚀 Testing: $url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

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
    echo $response . "\n";
    
    if ($httpCode === 200) {
        echo "\n✅ SUCCESS: Route is working!\n";
    } else {
        echo "\n❌ ERROR: HTTP $httpCode\n";
    }
}

echo "\n🎉 Test completed!\n";
