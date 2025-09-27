<?php

/**
 * PayHere API Integration Test Script
 * 
 * This script tests the PayHere API endpoints to ensure they're working correctly
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

echo "🧪 PayHere API Integration Test\n";
echo "==============================\n\n";

// Test configuration
$baseUrl = 'http://localhost:8000'; // Adjust this to your server URL
$apiToken = 'your_api_token_here'; // You'll need to get this from login

echo "📋 Testing PayHere API Endpoints...\n\n";

// Test 1: Water Bill Payment
echo "💧 Testing Water Bill Payment API...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->post($baseUrl . '/api/water-bills/online-payment', [
        'water_bill_id' => 1,
        'amount_paid' => 500.00,
        'customer_data' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '0771234567',
            'address' => '123 Main Street',
            'city' => 'Colombo'
        ]
    ]);

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Water Bill Payment API working\n";
        echo "   Payment ID: " . ($data['payment_id'] ?? 'N/A') . "\n";
        echo "   Order ID: " . ($data['checkout_data']['order_id'] ?? 'N/A') . "\n";
        echo "   Amount: LKR " . ($data['checkout_data']['amount'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Water Bill Payment API failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Water Bill Payment API error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Hall Reservation Payment
echo "🏛️ Testing Hall Reservation Payment API...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->post($baseUrl . '/api/hall-reservations/1/payments/online', [
        'amount' => 2000.00,
        'customer_data' => [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '0779876543',
            'address' => '456 Oak Avenue',
            'city' => 'Kandy'
        ]
    ]);

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Hall Reservation Payment API working\n";
        echo "   Payment ID: " . ($data['payment_id'] ?? 'N/A') . "\n";
        echo "   Order ID: " . ($data['checkout_data']['order_id'] ?? 'N/A') . "\n";
        echo "   Amount: LKR " . ($data['checkout_data']['amount'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Hall Reservation Payment API failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Hall Reservation Payment API error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Tax Payment
echo "💰 Testing Tax Payment API...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->post($baseUrl . '/api/tax-payments/online/1', [
        'payment' => 1500.00,
        'discount_amount' => 0,
        'fine_amount' => 0,
        'customer_data' => [
            'first_name' => 'Robert',
            'last_name' => 'Johnson',
            'email' => 'robert@example.com',
            'phone' => '0775555555',
            'address' => '789 Pine Street',
            'city' => 'Galle'
        ]
    ]);

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Tax Payment API working\n";
        echo "   Payment ID: " . ($data['payment_id'] ?? 'N/A') . "\n";
        echo "   Order ID: " . ($data['checkout_data']['order_id'] ?? 'N/A') . "\n";
        echo "   Amount: LKR " . ($data['checkout_data']['amount'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Tax Payment API failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Tax Payment API error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Unified Payment API
echo "🔄 Testing Unified Payment API...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->post($baseUrl . '/api/payments/online', [
        'payment_type' => 'water_bill',
        'payment_id' => 1,
        'amount' => 750.00,
        'customer_data' => [
            'first_name' => 'Alice',
            'last_name' => 'Brown',
            'email' => 'alice@example.com',
            'phone' => '0771111111',
            'address' => '321 Elm Street',
            'city' => 'Negombo'
        ]
    ]);

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Unified Payment API working\n";
        echo "   Payment ID: " . ($data['payment_id'] ?? 'N/A') . "\n";
        echo "   Order ID: " . ($data['checkout_data']['order_id'] ?? 'N/A') . "\n";
        echo "   Amount: LKR " . ($data['checkout_data']['amount'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Unified Payment API failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Unified Payment API error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Payment Status Check
echo "📊 Testing Payment Status API...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->get($baseUrl . '/api/payments/status/WB_1');

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Payment Status API working\n";
        echo "   Status: " . ($data['success'] ? 'Success' : 'Failed') . "\n";
        echo "   Message: " . ($data['message'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Payment Status API failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Payment Status API error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Receipt Generation
echo "🧾 Testing Receipt Generation API...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->get($baseUrl . '/api/payments/water_bill/1/receipt');

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Receipt Generation API working\n";
        echo "   Receipt Number: " . ($data['receipt_number'] ?? 'N/A') . "\n";
        echo "   Customer Name: " . ($data['receipt']['customer_name'] ?? 'N/A') . "\n";
        echo "   Amount: LKR " . ($data['receipt']['amount'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Receipt Generation API failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Receipt Generation API error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "🎉 PayHere API Integration Test Completed!\n\n";

echo "📝 Next Steps:\n";
echo "1. Start your Laravel server: php artisan serve\n";
echo "2. Get an API token by logging in\n";
echo "3. Update the API token in this script\n";
echo "4. Run this script again: php test-payhere-api.php\n";
echo "5. Test with real PayHere sandbox payments\n\n";

echo "💡 Test Card Numbers for PayHere Sandbox:\n";
echo "- Visa (Success): 4111111111111111\n";
echo "- Mastercard (Success): 5555555555554444\n";
echo "- Amex (Success): 378282246310005\n\n";

echo "✅ All tests completed!\n";
