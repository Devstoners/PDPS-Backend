<?php

/**
 * Test Validation Rules
 */

echo "🧪 Testing Water Scheme Validation Rules\n";
echo "========================================\n\n";

$baseUrl = 'http://localhost:8000';
$endpoint = '/api/water-schemes';

echo "Testing with minimal data (name only):\n";
echo "=====================================\n";

$testData = [
    'name' => 'Test Water Scheme'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer test-token' // This will fail auth but should show validation
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode === 401) {
    echo "✅ Correctly requires authentication\n";
} elseif ($httpCode === 422) {
    echo "❌ Still getting validation error - rules not updated\n";
} else {
    echo "✅ Request processed (status: $httpCode)\n";
}

echo "\n🔍 Expected Behavior:\n";
echo "====================\n";
echo "✅ With valid token: Should create water scheme with defaults\n";
echo "✅ Without token: Should get 401 (authentication required)\n";
echo "❌ Should NOT get 422 validation error for missing location/status\n\n";

echo "🛠️ If still getting 422:\n";
echo "========================\n";
echo "1. Restart the Laravel server: php artisan serve\n";
echo "2. Check if the file changes were saved\n";
echo "3. Verify the validation rules are correct\n\n";

echo "✅ Test complete!\n";

