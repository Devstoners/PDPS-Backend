<?php

// Read the current .env file
$envContent = file_get_contents('.env');

// Remove any existing STRIPE_ entries
$lines = explode("\n", $envContent);
$cleanLines = [];

foreach ($lines as $line) {
    if (!str_starts_with(trim($line), 'STRIPE_')) {
        $cleanLines[] = $line;
    }
}

// Add Stripe configuration
$stripeConfig = [
    '',
    '# Stripe Configuration',
    'STRIPE_SECRET_KEY=sk_test_51SCNpJAIxD4wuxCfuKUxypDhUpg2QLjXZbnVMevF7mzkZylHr8t9DYq6boqxxmsgaBblEianFcUTrOvqXyA7PnOA004BtLS9Zw',
    'STRIPE_PUBLISHABLE_KEY=pk_test_51SCNpJAIxD4wuxCfYYHXZFHbCfjzh4B13yCYdjQ4FlbhKnv7QphPIKAQuRqSEUkrZhBSCETSAYSeTt7uEw7qmjaq00OlX59TT2',
    'STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here',
    'STRIPE_CURRENCY=lkr',
    'STRIPE_LOG_LEVEL=info'
];

// Combine and write back
$newContent = implode("\n", $cleanLines) . "\n" . implode("\n", $stripeConfig);

file_put_contents('.env', $newContent);

echo "✅ Stripe configuration added successfully!\n";
echo "📋 Added configuration:\n";
echo "   - STRIPE_SECRET_KEY\n";
echo "   - STRIPE_PUBLISHABLE_KEY\n";
echo "   - STRIPE_WEBHOOK_SECRET (placeholder)\n";
echo "   - STRIPE_CURRENCY=lkr\n";
echo "   - STRIPE_LOG_LEVEL=info\n";
echo "\n";
echo "🔧 Next steps:\n";
echo "   1. Get your webhook secret from Stripe Dashboard\n";
echo "   2. Update STRIPE_WEBHOOK_SECRET in .env file\n";
echo "   3. Test with: php artisan stripe:health-check\n";
