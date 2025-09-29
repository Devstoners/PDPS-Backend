<?php

/**
 * Update Mail Configuration
 */

echo "📧 Gmail SMTP Configuration for PDPS System\n";
echo "==========================================\n\n";

echo "Add these lines to your .env file:\n\n";

echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=pathadumbarapradeshiyasabawa@gmail.com\n";
echo "MAIL_PASSWORD=YOUR_16_CHAR_APP_PASSWORD_HERE\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=pathadumbarapradeshiyasabawa@gmail.com\n";
echo "MAIL_FROM_NAME=\"Pathadumbara Pradeshiya Sabawa\"\n\n";

echo "⚠️  IMPORTANT STEPS:\n";
echo "1. Enable 2FA on your Gmail account\n";
echo "2. Create App Password (16 characters)\n";
echo "3. Replace YOUR_16_CHAR_APP_PASSWORD_HERE with the actual password\n";
echo "4. Test with: php artisan email:test-gmail --email=your-test-email@gmail.com\n\n";

echo "✅ Configuration complete!\n";
