<?php

// Read the clean .env file
$envContent = file_get_contents('.env');

// Add Stripe configuration at the end
$stripeConfig = "\n# Stripe Configuration\nSTRIPE_SECRET_KEY=sk_test_51SCNpJAIxD4wuxCfuKUxypDhUpg2QLjXZbnVMevF7mzkZylHr8t9DYq6boqxxmsgaBblEianFcUTrOvqXyA7PnOA004BtLS9Zw\nSTRIPE_PUBLISHABLE_KEY=pk_test_51SCNpJAIxD4wuxCfYYHXZFHbCfjzh4B13yCYdjQ4FlbhKnv7QphPIKAQuRqSEUkrZhBSCETSAYSeTt7uEw7qmjaq00OlX59TT2\nSTRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here\nSTRIPE_CURRENCY=lkr\nSTRIPE_LOG_LEVEL=info";

// Write the updated content
file_put_contents('.env', $envContent . $stripeConfig);

echo "✅ Environment file fixed successfully!\n";
echo "📋 Stripe configuration added:\n";
echo "   - STRIPE_SECRET_KEY\n";
echo "   - STRIPE_PUBLISHABLE_KEY\n";
echo "   - STRIPE_WEBHOOK_SECRET (placeholder)\n";
echo "   - STRIPE_CURRENCY=lkr\n";
echo "   - STRIPE_LOG_LEVEL=info\n";
echo "\n";
echo "🚀 You can now run: php artisan serve\n";
