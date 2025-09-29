<?php

/**
 * Test Brevo Email Service
 */

echo "🧪 Testing Brevo Email Service\n";
echo "==============================\n\n";

// Include Laravel autoloader
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\BrevoEmailService;

try {
    $brevoService = new BrevoEmailService();
    
    echo "📧 Sending test email...\n";
    
    $result = $brevoService->sendEmail(
        [
            'email' => 'lak.bope@gmail.com',
            'name' => 'Test User'
        ],
        'Test Email from PDPS System',
        '<h1>Test Email</h1><p>This is a test email from the PDPS system.</p>',
        'Test Email - This is a test email from the PDPS system.'
    );
    
    if ($result) {
        echo "✅ Email sent successfully!\n";
    } else {
        echo "❌ Failed to send email\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Test completed!\n";
