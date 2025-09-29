<?php

/**
 * Test Stripe CLI Webhook Setup
 */

echo "🧪 Testing Stripe CLI Webhook Setup\n";
echo "===================================\n\n";

echo "📋 Setup Steps:\n\n";

echo "1. Install Stripe CLI:\n";
echo "   winget install stripe.stripe-cli\n\n";

echo "2. Login to Stripe:\n";
echo "   stripe login\n\n";

echo "3. Start webhook forwarding:\n";
echo "   stripe listen --forward-to localhost:8000/api/webhooks/stripe\n\n";

echo "4. Copy the webhook secret from the output\n";
echo "   (starts with 'whsec_')\n\n";

echo "5. Add to your .env file:\n";
echo "   STRIPE_WEBHOOK_SECRET=whsec_your_secret_here\n\n";

echo "6. Test the webhook:\n";
echo "   stripe trigger checkout.session.completed\n\n";

echo "🎯 Benefits of Stripe CLI:\n";
echo "- ✅ No need to configure webhook endpoints manually\n";
echo "- ✅ Automatically forwards events to your local server\n";
echo "- ✅ No need to expose your server to the internet\n";
echo "- ✅ Perfect for development and testing\n";
echo "- ✅ Works with any local port\n\n";

echo "🚀 Ready to test!\n";
echo "Run: stripe listen --forward-to localhost:8000/api/webhooks/stripe\n";
echo "Then test with: stripe trigger checkout.session.completed\n\n";

echo "✅ Test completed!\n";
