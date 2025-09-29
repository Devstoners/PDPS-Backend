<?php

/**
 * Tax Payee API Test Script
 * 
 * This script tests the Tax Payee API endpoints to ensure they work correctly
 * with the frontend payload structure
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

echo "🧪 Testing Tax Payee API Integration\n";
echo "==================================\n\n";

// Test configuration
$baseUrl = 'http://localhost:8000'; // Adjust this to your server URL
$apiToken = 'your_api_token_here'; // You'll need to get this from login

echo "📋 Testing Tax Payee API Endpoints...\n\n";

// Test 1: Create Tax Payee
echo "👤 Testing Tax Payee Creation...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->post($baseUrl . '/api/tax-payees', [
        'title' => '1',
        'name' => 'Asanka Bopegedara',
        'nic' => '883323386V',
        'tel' => '0778590294',
        'address' => '126',
        'email' => 'bz@gmail.com'
    ]);

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Tax Payee created successfully\n";
        echo "   ID: " . ($data['data']['id'] ?? 'N/A') . "\n";
        echo "   Name: " . ($data['data']['name'] ?? 'N/A') . "\n";
        echo "   NIC: " . ($data['data']['nic'] ?? 'N/A') . "\n";
        echo "   Phone: " . ($data['data']['tel'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Tax Payee creation failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Tax Payee creation error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Get Tax Payees List
echo "📋 Testing Tax Payees List...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->get($baseUrl . '/api/tax-payees');

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Tax Payees list retrieved successfully\n";
        echo "   Total: " . ($data['total'] ?? 'N/A') . "\n";
        echo "   Per Page: " . ($data['per_page'] ?? 'N/A') . "\n";
        echo "   Current Page: " . ($data['current_page'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Tax Payees list failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Tax Payees list error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Search Tax Payee by NIC
echo "🔍 Testing Tax Payee Search by NIC...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->get($baseUrl . '/api/tax-payees/search/nic?nic=883323386V');

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Tax Payee search successful\n";
        echo "   Name: " . ($data['name'] ?? 'N/A') . "\n";
        echo "   NIC: " . ($data['nic'] ?? 'N/A') . "\n";
        echo "   Phone: " . ($data['tel'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Tax Payee search failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Tax Payee search error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Update Tax Payee
echo "✏️ Testing Tax Payee Update...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->put($baseUrl . '/api/tax-payees/1', [
        'title' => '1',
        'name' => 'Asanka Bopegedara Updated',
        'nic' => '883323386V',
        'tel' => '0778590294',
        'address' => '126 Updated Address',
        'email' => 'bz.updated@gmail.com'
    ]);

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Tax Payee updated successfully\n";
        echo "   Name: " . ($data['data']['name'] ?? 'N/A') . "\n";
        echo "   Address: " . ($data['data']['address'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Tax Payee update failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Tax Payee update error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Get Single Tax Payee
echo "👤 Testing Get Single Tax Payee...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->get($baseUrl . '/api/tax-payees/1');

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Tax Payee retrieved successfully\n";
        echo "   Name: " . ($data['name'] ?? 'N/A') . "\n";
        echo "   NIC: " . ($data['nic'] ?? 'N/A') . "\n";
        echo "   Email: " . ($data['email'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Tax Payee retrieval failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Tax Payee retrieval error: " . $e->getMessage() . "\n";
}

echo "\n";

echo "🎉 Tax Payee API Integration Test Completed!\n\n";

echo "📝 Next Steps:\n";
echo "1. Start your Laravel server: php artisan serve\n";
echo "2. Get an API token by logging in\n";
echo "3. Update the API token in this script\n";
echo "4. Run this script again: php test-tax-payee-api.php\n";
echo "5. Test with your frontend application\n\n";

echo "📋 Available Endpoints:\n";
echo "- GET    /api/tax-payees              (List all tax payees)\n";
echo "- POST   /api/tax-payees              (Create tax payee)\n";
echo "- GET    /api/tax-payees/{id}         (Get single tax payee)\n";
echo "- PUT    /api/tax-payees/{id}         (Update tax payee)\n";
echo "- DELETE /api/tax-payees/{id}         (Delete tax payee)\n";
echo "- GET    /api/tax-payees/search/nic   (Search by NIC)\n\n";

echo "📱 Frontend Payload Structure:\n";
echo "{\n";
echo "  \"title\": \"1\",\n";
echo "  \"name\": \"Asanka Bopegedara\",\n";
echo "  \"nic\": \"883323386V\",\n";
echo "  \"tel\": \"0778590294\",\n";
echo "  \"address\": \"126\",\n";
echo "  \"email\": \"bz@gmail.com\"\n";
echo "}\n\n";

echo "✅ All tests completed!\n";
