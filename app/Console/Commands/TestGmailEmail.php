<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestGmailEmail extends Command
{
    protected $signature = 'email:test-gmail {--email=}';
    protected $description = 'Test Gmail SMTP email sending';

    public function handle()
    {
        $this->info('🧪 Testing Gmail SMTP Email');
        $this->line('==========================');
        $this->newLine();

        $testEmail = $this->option('email') ?? 'lak.bope@gmail.com';
        
        try {
            $this->info("📧 Sending test email to: {$testEmail}");
            
            // Simple test email
            Mail::send([], [], function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email from PDPS System')
                        ->from('your-gmail@gmail.com', 'PDPS System')
                        ->setBody('
                        <html>
                        <body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                            <h1 style="color: #2c3e50;">🎉 Test Email Successful!</h1>
                            <p>This is a test email from your PDPS system.</p>
                            <p>If you received this email, your Gmail SMTP configuration is working correctly.</p>
                            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
                                <h3>Email Configuration Status:</h3>
                                <ul>
                                    <li>✅ Gmail SMTP: Working</li>
                                    <li>✅ Laravel Mail: Working</li>
                                    <li>✅ Email Templates: Ready</li>
                                </ul>
                            </div>
                            <p>Best regards,<br>PDPS System</p>
                        </body>
                        </html>
                        ', 'text/html');
            });

            $this->info('✅ Test email sent successfully!');
            $this->line("📧 Check your email: {$testEmail}");
            $this->newLine();
            
            $this->info('📋 Next Steps:');
            $this->line('1. Configure Gmail SMTP in your .env file');
            $this->line('2. Use GmailEmailService for payment confirmations');
            $this->line('3. Test with real payment data');

        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            $this->newLine();
            $this->info('💡 Gmail SMTP Setup Required:');
            $this->line('1. Add to .env file:');
            $this->line('   MAIL_MAILER=smtp');
            $this->line('   MAIL_HOST=smtp.gmail.com');
            $this->line('   MAIL_PORT=587');
            $this->line('   MAIL_USERNAME=your-gmail@gmail.com');
            $this->line('   MAIL_PASSWORD=your-app-password');
            $this->line('   MAIL_ENCRYPTION=tls');
            $this->line('   MAIL_FROM_ADDRESS=your-gmail@gmail.com');
            $this->line('   MAIL_FROM_NAME="PDPS System"');
            $this->newLine();
            $this->line('2. Enable 2FA on Gmail and create App Password');
            $this->line('3. Run this command again');
        }

        return 0;
    }
}
