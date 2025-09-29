<?php

/**
 * Configure PDPS Email System
 */

echo "📧 Configuring PDPS Email System\n";
echo "===============================\n\n";

echo "✅ App Password received: ihvn odnx pjyy svcp\n\n";

echo "📝 Add these lines to your .env file:\n\n";

echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=pathadumbarapradeshiyasabawa@gmail.com\n";
echo "MAIL_PASSWORD=ihvn odnx pjyy svcp\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=pathadumbarapradeshiyasabawa@gmail.com\n";
echo "MAIL_FROM_NAME=\"Pathadumbara Pradeshiya Sabawa\"\n\n";

echo "🧪 Test the configuration:\n";
echo "php artisan email:test-pdps --email=your-test-email@gmail.com\n\n";

echo "✅ Configuration ready!\n";
