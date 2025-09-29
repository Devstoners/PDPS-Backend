<?php

/**
 * Update Stripe Webhook Secret in .env file
 */

$webhookSecret = 'whsec_13d113129abfb8892f60dc361ceb96c15761157ff6aa00ea516941881259cf63';

// Read current .env file
$envContent = file_get_contents('.env');

// Check if STRIPE_WEBHOOK_SECRET already exists
if (strpos($envContent, 'STRIPE_WEBHOOK_SECRET=') !== false) {
    // Update existing line
    $envContent = preg_replace(
        '/STRIPE_WEBHOOK_SECRET=.*/',
        "STRIPE_WEBHOOK_SECRET={$webhookSecret}",
        $envContent
    );
} else {
    // Add new line
    $envContent .= "\nSTRIPE_WEBHOOK_SECRET={$webhookSecret}\n";
}

// Write back to .env file
file_put_contents('.env', $envContent);

echo "✅ Stripe webhook secret updated successfully!\n";
echo "Webhook Secret: {$webhookSecret}\n";
echo "\nNow test your payment again!\n";
