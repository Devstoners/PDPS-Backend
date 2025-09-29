<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "🔍 Checking tax_properties table...\n";

try {
    $exists = Schema::hasTable('tax_properties');
    echo "Table exists: " . ($exists ? 'YES' : 'NO') . "\n";
    
    if (!$exists) {
        echo "❌ tax_properties table does not exist!\n";
        echo "This is why you're getting 500 errors.\n";
        echo "\nLet's create it manually...\n";
        
        // Create the table manually
        Schema::create('tax_properties', function ($table) {
            $table->id();
            $table->timestamps();
            $table->integer('division_id');
            $table->integer('tax_payee_id');
            $table->string('street', 255);
            $table->integer('property_type');
            $table->string('property_name', 255);
            $table->boolean('property_prohibition')->default(0);
        });
        
        echo "✅ tax_properties table created successfully!\n";
    } else {
        echo "✅ tax_properties table exists!\n";
        
        // Check if it has data
        $count = DB::table('tax_properties')->count();
        echo "Records in table: $count\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Check completed!\n";
