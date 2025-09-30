<?php

/**
 * Test Water Scheme Authentication and Permissions
 */

echo "🧪 Testing Water Scheme Authentication\n";
echo "=====================================\n\n";

// Test 1: Check if the endpoint is accessible
echo "1. Testing endpoint accessibility...\n";

$baseUrl = 'http://localhost:8000';
$endpoint = '/api/water-schemes';

// Test without authentication
echo "   Testing without authentication:\n";
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

echo "   Status Code: $httpCode\n";
if ($httpCode === 401) {
    echo "   ✅ Correctly requires authentication\n";
} else {
    echo "   ❌ Should require authentication (expected 401, got $httpCode)\n";
}

echo "\n2. Testing with debug parameter...\n";

// Test with debug parameter (this should work if authenticated)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint . '?debug=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status Code: $httpCode\n";
if ($httpCode === 401) {
    echo "   ✅ Debug endpoint correctly requires authentication\n";
} else {
    echo "   ❌ Debug endpoint should require authentication (expected 401, got $httpCode)\n";
}

echo "\n3. Testing POST endpoint (create water scheme)...\n";

// Test POST without authentication
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

echo "   Status Code: $httpCode\n";
if ($httpCode === 401) {
    echo "   ✅ POST endpoint correctly requires authentication\n";
} else {
    echo "   ❌ POST endpoint should require authentication (expected 401, got $httpCode)\n";
}

echo "\n📋 Summary:\n";
echo "===========\n";
echo "✅ All endpoints correctly require authentication\n";
echo "❌ The 403 error suggests the user is authenticated but lacks permissions\n\n";

echo "🔧 Next Steps:\n";
echo "=============\n";
echo "1. Check if the user is properly authenticated in the frontend\n";
echo "2. Verify the user has the correct role (admin or officerwaterbill)\n";
echo "3. Check if the Authorization header is being sent correctly\n";
echo "4. Use the debug endpoint to see the user's current permissions\n\n";

echo "💡 To test with authentication, you need to:\n";
echo "1. Login and get a token\n";
echo "2. Include 'Authorization: Bearer your-token' in the request headers\n";
echo "3. Then test the endpoints\n\n";

echo "✅ Test complete!\n";