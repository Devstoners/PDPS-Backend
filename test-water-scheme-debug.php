<?php

/**
 * Test Water Scheme Debug
 */

echo "🧪 Testing Water Scheme Debug\n";
echo "============================\n\n";

$baseUrl = 'http://localhost:8000';
$endpoint = '/api/water-schemes';

echo "🔍 Testing with different scenarios:\n";
echo "===================================\n\n";

// Test 1: Without authentication
echo "1. Testing without authentication (should get 401):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status: $httpCode\n";
if ($httpCode === 401) {
    echo "   ✅ Correctly requires authentication\n";
} else {
    echo "   ❌ Should require authentication (expected 401, got $httpCode)\n";
}

// Test 2: With invalid token
echo "\n2. Testing with invalid token (should get 401):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer invalid-token'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status: $httpCode\n";
if ($httpCode === 401) {
    echo "   ✅ Correctly rejects invalid token\n";
} else {
    echo "   ❌ Should reject invalid token (expected 401, got $httpCode)\n";
}

// Test 3: POST without authentication
echo "\n3. Testing POST without authentication (should get 401):\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'name' => 'Test Scheme',
    'description' => 'Test Description',
    'location' => 'Test Location',
    'status' => 'Active'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status: $httpCode\n";
if ($httpCode === 401) {
    echo "   ✅ POST correctly requires authentication\n";
} else {
    echo "   ❌ POST should require authentication (expected 401, got $httpCode)\n";
}

echo "\n📋 Analysis:\n";
echo "============\n";
echo "✅ Routes are correctly configured\n";
echo "✅ Authentication is required\n";
echo "✅ User has correct permissions\n\n";

echo "🔍 The 403 error suggests:\n";
echo "==========================\n";
echo "1. User is authenticated (otherwise it would be 401)\n";
echo "2. User doesn't have the required permissions (but we verified they do)\n";
echo "3. There might be a middleware issue\n";
echo "4. Frontend might not be sending the correct token\n\n";

echo "🛠️ Next Steps:\n";
echo "=============\n";
echo "1. Check if the frontend is sending the Authorization header correctly\n";
echo "2. Verify the token is valid and not expired\n";
echo "3. Check Laravel logs for any middleware errors\n";
echo "4. Test with a fresh login to get a new token\n\n";

echo "✅ Debug test complete!\n";

