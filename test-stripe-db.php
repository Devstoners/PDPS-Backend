<?php

/**
 * Test Stripe Database Connection
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Stripe Database Connection\n";
echo "====================================\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $connection = \Illuminate\Support\Facades\DB::connection();
    $connection->getPdo();
    echo "✅ Database connection successful\n\n";

    // Test if stripe_payments table exists
    echo "2. Checking stripe_payments table...\n";
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('stripe_payments');
    echo "Table exists: " . ($tableExists ? 'Yes' : 'No') . "\n";
    
    if ($tableExists) {
        echo "✅ stripe_payments table found\n\n";
        
        // Test model
        echo "3. Testing StripePayment model...\n";
        $count = \App\Models\StripePayment::count();
        echo "✅ StripePayment model working - Count: $count\n\n";
        
        // Test creating a record
        echo "4. Testing record creation...\n";
        $payment = new \App\Models\StripePayment();
        $payment->payment_id = 'TEST_' . time();
        $payment->amount = 1000;
        $payment->currency = 'lkr';
        $payment->status = 'pending';
        $payment->tax_type = 'Test';
        $payment->taxpayer_name = 'Test User';
        $payment->nic = '123456789V';
        $payment->email = 'test@example.com';
        
        $saved = $payment->save();
        echo "Record creation: " . ($saved ? 'Success' : 'Failed') . "\n";
        
        if ($saved) {
            echo "✅ Test record created with ID: " . $payment->id . "\n";
            // Clean up test record
            $payment->delete();
            echo "✅ Test record cleaned up\n";
        }
    } else {
        echo "❌ stripe_payments table not found\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n✅ Database test completed!\n";
