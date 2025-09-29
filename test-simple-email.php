<?php

/**
 * Simple Email Test
 */

echo "🧪 Simple Gmail SMTP Test\n";
echo "========================\n\n";

// Test basic SMTP connection
$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$username = 'pathadumbarapradeshiyasabawa@gmail.com';
$password = 'ihvnodnxpjyyvcp'; // Remove spaces

echo "📧 Testing SMTP Connection:\n";
echo "Host: $smtp_host\n";
echo "Port: $smtp_port\n";
echo "Username: $username\n";
echo "Password: " . str_repeat('*', strlen($password)) . "\n\n";

// Test connection
$connection = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 30);

if (!$connection) {
    echo "❌ Connection failed: $errstr ($errno)\n";
} else {
    echo "✅ SMTP connection successful!\n";
    fclose($connection);
}

echo "\n✅ Test completed!\n";
