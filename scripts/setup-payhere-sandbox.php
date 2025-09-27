<?php

/**
 * PayHere Sandbox Setup Script
 * 
 * This script helps you set up PayHere sandbox integration
 * Run this script after configuring your .env file
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Config;

echo "🚀 PayHere Sandbox Setup Script\n";
echo "================================\n\n";

// Check if .env file exists
if (!file_exists(__DIR__ . '/../.env')) {
    echo "❌ .env file not found. Please create one from .env.example\n";
    exit(1);
}

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

echo "📋 Checking PayHere Configuration...\n";

$requiredVars = [
    'PAYHERE_MERCHANT_ID',
    'PAYHERE_MERCHANT_SECRET',
    'PAYHERE_CHECKOUT_URL',
    'PAYHERE_RETURN_URL',
    'PAYHERE_CANCEL_URL',
    'PAYHERE_NOTIFY_URL'
];

$missingVars = [];

foreach ($requiredVars as $var) {
    if (empty($_ENV[$var])) {
        $missingVars[] = $var;
    }
}

if (!empty($missingVars)) {
    echo "❌ Missing required environment variables:\n";
    foreach ($missingVars as $var) {
        echo "   - $var\n";
    }
    echo "\nPlease add these to your .env file:\n\n";
    echo "# PayHere Sandbox Configuration\n";
    echo "PAYHERE_MERCHANT_ID=your_sandbox_merchant_id\n";
    echo "PAYHERE_MERCHANT_SECRET=your_sandbox_merchant_secret\n";
    echo "PAYHERE_CHECKOUT_URL=https://sandbox.payhere.lk/pay/checkout\n";
    echo "PAYHERE_RETURN_URL=https://yourdomain.com/api/payhere/return\n";
    echo "PAYHERE_CANCEL_URL=https://yourdomain.com/api/payhere/cancel\n";
    echo "PAYHERE_NOTIFY_URL=https://yourdomain.com/api/payhere/callback\n";
    echo "PAYHERE_SANDBOX=true\n";
    exit(1);
}

echo "✅ All required environment variables are set\n\n";

// Test configuration
echo "🧪 Testing PayHere configuration...\n";

try {
    // Test merchant ID format
    $merchantId = $_ENV['PAYHERE_MERCHANT_ID'];
    if (empty($merchantId) || strlen($merchantId) < 3) {
        echo "❌ Invalid merchant ID. Should be at least 3 characters.\n";
        exit(1);
    }
    echo "✅ Merchant ID format is valid\n";

    // Test URLs
    $checkoutUrl = $_ENV['PAYHERE_CHECKOUT_URL'];
    if (!filter_var($checkoutUrl, FILTER_VALIDATE_URL)) {
        echo "❌ Invalid checkout URL format\n";
        exit(1);
    }
    echo "✅ Checkout URL format is valid\n";

    // Check if using sandbox
    if (strpos($checkoutUrl, 'sandbox') !== false) {
        echo "✅ Using PayHere sandbox environment\n";
    } else {
        echo "⚠️  Warning: Not using sandbox environment. Make sure this is intended for production.\n";
    }

} catch (Exception $e) {
    echo "❌ Configuration test failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 PayHere sandbox configuration is ready!\n\n";

echo "📝 Next Steps:\n";
echo "1. Test the integration: php artisan payhere:test\n";
echo "2. Run the test suite: php artisan test --filter=PayHereIntegrationTest\n";
echo "3. Test with real API calls using the provided examples\n\n";

echo "🔗 Useful Links:\n";
echo "- PayHere Sandbox: https://sandbox.payhere.lk/\n";
echo "- PayHere Documentation: https://www.payhere.lk/developers\n";
echo "- Test Cards: https://www.payhere.lk/developers/sandbox\n\n";

echo "💡 Test Card Numbers:\n";
echo "- Visa (Success): 4111111111111111\n";
echo "- Mastercard (Success): 5555555555554444\n";
echo "- Amex (Success): 378282246310005\n\n";

echo "✅ Setup completed successfully!\n";
