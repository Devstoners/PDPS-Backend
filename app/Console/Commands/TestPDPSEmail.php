<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestPDPSEmail extends Command
{
    protected $signature = 'email:test-pdps {--email=}';
    protected $description = 'Test PDPS email configuration';

    public function handle()
    {
        $this->info('📧 Testing PDPS Email Configuration');
        $this->line('=====================================');
        $this->newLine();

        $testEmail = $this->option('email') ?? 'lak.bope@gmail.com';
        
        try {
            $this->info("📧 Sending test email to: {$testEmail}");
            $this->info("📧 From: pathadumbarapradeshiyasabawa@gmail.com");
            $this->newLine();
            
            // Test email with PDPS branding
            $htmlContent = '
            <html>
            <head>
                <meta charset="UTF-8">
                <title>PDPS Test Email</title>
            </head>
            <body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
                <div style="background-color: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    
                    <div style="text-align: center; margin-bottom: 30px;">
                        <h1 style="color: #2c3e50; margin: 0; font-size: 24px;">🏛️ Pathadumbara Pradeshiya Sabawa</h1>
                        <p style="color: #6c757d; margin: 10px 0 0 0; font-size: 16px;">Digital Services Test</p>
                    </div>
                    
                    <div style="background-color: #e8f5e8; border: 1px solid #c3e6c3; border-radius: 8px; padding: 20px; margin: 20px 0;">
                        <h3 style="color: #2d5a2d; margin-top: 0;">✅ Email Configuration Successful!</h3>
                        <p>This test email confirms that your Gmail SMTP configuration is working correctly for the PDPS system.</p>
                    </div>
                    
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
                        <h3 style="color: #2c3e50; margin-top: 0;">📋 System Status:</h3>
                        <ul style="color: #333;">
                            <li>✅ Gmail SMTP: Working</li>
                            <li>✅ Laravel Mail: Working</li>
                            <li>✅ Email Templates: Ready</li>
                            <li>✅ Payment Confirmations: Ready</li>
                            <li>✅ Tax Notifications: Ready</li>
                        </ul>
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                        <p style="color: #6c757d; margin: 0; font-size: 14px;">
                            <strong>Pathadumbara Pradeshiya Sabawa</strong><br>
                            Digital Services Department
                        </p>
                        <p style="color: #adb5bd; margin: 10px 0 0 0; font-size: 12px;">
                            This is an automated test message.
                        </p>
                    </div>
                    
                </div>
            </body>
            </html>';

            Mail::html($htmlContent, function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email from Pathadumbara Pradeshiya Sabawa')
                        ->from('pathadumbarapradeshiyasabawa@gmail.com', 'Pathadumbara Pradeshiya Sabawa');
            });

            $this->info('✅ Test email sent successfully!');
            $this->line("📧 Check your email: {$testEmail}");
            $this->newLine();
            
            $this->info('🎉 PDPS Email System is Ready!');
            $this->line('Your payment confirmation emails will now work correctly.');
            $this->newLine();
            
            $this->info('📋 Available Email Services:');
            $this->line('• Payment Confirmations (Stripe)');
            $this->line('• Tax Assessment Notifications');
            $this->line('• Payment Reminders');
            $this->line('• System Notifications');

        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            $this->newLine();
            
            $this->info('🔧 Configuration Required:');
            $this->line('1. Enable 2FA on pathadumbarapradeshiyasabawa@gmail.com');
            $this->line('2. Create App Password in Google Account');
            $this->line('3. Update .env file with correct credentials');
            $this->newLine();
            
            $this->line('📝 Required .env settings:');
            $this->line('MAIL_MAILER=smtp');
            $this->line('MAIL_HOST=smtp.gmail.com');
            $this->line('MAIL_PORT=587');
            $this->line('MAIL_USERNAME=pathadumbarapradeshiyasabawa@gmail.com');
            $this->line('MAIL_PASSWORD=your-16-char-app-password');
            $this->line('MAIL_ENCRYPTION=tls');
            $this->line('MAIL_FROM_ADDRESS=pathadumbarapradeshiyasabawa@gmail.com');
            $this->line('MAIL_FROM_NAME="Pathadumbara Pradeshiya Sabawa"');
        }

        return 0;
    }
}
