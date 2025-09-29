<?php

/**
 * Tax Properties API Test Script
 * 
 * This script tests the Tax Properties API endpoints
 */

echo "🧪 Testing Tax Properties API Integration\n";
echo "=======================================\n\n";

// Test configuration
$baseUrl = 'http://127.0.0.1:8000'; // Adjust this to your server URL
$apiToken = 'your_api_token_here'; // You'll need to get this from login

echo "📋 Testing Tax Properties API Endpoints...\n\n";

// Test 1: Get Property Types
echo "🏠 Testing Property Types API...\n";
try {
    $response = file_get_contents($baseUrl . '/api/tax-properties/types');
    
    if ($response !== false) {
        $data = json_decode($response, true);
        echo "✅ Property Types API working\n";
        echo "   Available types: " . count($data) . "\n";
        foreach ($data as $type) {
            echo "   - " . $type['name'] . " (ID: " . $type['id'] . ")\n";
        }
    } else {
        echo "❌ Property Types API failed\n";
    }
} catch (Exception $e) {
    echo "❌ Property Types API error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Create Tax Property (will fail without auth, but we can test the endpoint)
echo "🏗️ Testing Tax Property Creation API...\n";
try {
    $testData = [
        'division_id' => 1,
        'tax_payee_id' => 1,
        'street' => '123 Test Street',
        'property_type' => 1,
        'property_name' => 'Test Property',
        'property_prohibition' => false
    ];

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($testData)
        ]
    ]);

    $response = file_get_contents($baseUrl . '/api/tax-properties', false, $context);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['message']) && $data['message'] === 'Unauthenticated.') {
            echo "✅ Tax Property Creation API working (requires authentication)\n";
            echo "   Response: " . $data['message'] . "\n";
        } else {
            echo "✅ Tax Property Creation API working\n";
            echo "   Response: " . json_encode($data) . "\n";
        }
    } else {
        echo "❌ Tax Property Creation API failed\n";
    }
} catch (Exception $e) {
    echo "❌ Tax Property Creation API error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Get Tax Properties List
echo "📋 Testing Tax Properties List API...\n";
try {
    $response = file_get_contents($baseUrl . '/api/tax-properties');
    
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['message']) && $data['message'] === 'Unauthenticated.') {
            echo "✅ Tax Properties List API working (requires authentication)\n";
            echo "   Response: " . $data['message'] . "\n";
        } else {
            echo "✅ Tax Properties List API working\n";
            echo "   Total properties: " . (isset($data['total']) ? $data['total'] : 'N/A') . "\n";
        }
    } else {
        echo "❌ Tax Properties List API failed\n";
    }
} catch (Exception $e) {
    echo "❌ Tax Properties List API error: " . $e->getMessage() . "\n";
}

echo "\n";

echo "🎉 Tax Properties API Integration Test Completed!\n\n";

echo "📝 Next Steps:\n";
echo "1. Start your Laravel server: php artisan serve\n";
echo "2. Get an API token by logging in\n";
echo "3. Test with proper authentication\n";
echo "4. Use the frontend to create tax properties\n\n";

echo "📋 Available Endpoints:\n";
echo "- GET    /api/tax-properties              (List all tax properties)\n";
echo "- POST   /api/tax-properties              (Create tax property)\n";
echo "- GET    /api/tax-properties/{id}         (Get single tax property)\n";
echo "- PUT    /api/tax-properties/{id}         (Update tax property)\n";
echo "- DELETE /api/tax-properties/{id}         (Delete tax property)\n";
echo "- GET    /api/tax-properties/payee/{id}  (Get properties by payee)\n";
echo "- GET    /api/tax-properties/types        (Get property types)\n\n";

echo "📱 Frontend Payload Structure for Tax Property:\n";
echo "{\n";
echo "  \"division_id\": 1,\n";
echo "  \"tax_payee_id\": 1,\n";
echo "  \"street\": \"123 Main Street\",\n";
echo "  \"property_type\": 1,\n";
echo "  \"property_name\": \"My House\",\n";
echo "  \"property_prohibition\": false\n";
echo "}\n\n";

echo "🏠 Property Types:\n";
echo "- 1: House\n";
echo "- 2: Land\n";
echo "- 3: Building\n";
echo "- 4: Commercial\n";
echo "- 5: Other\n\n";

echo "✅ All tests completed!\n";
