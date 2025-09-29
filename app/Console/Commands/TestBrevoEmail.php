<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BrevoEmailService;
use Illuminate\Support\Facades\Mail;

class TestBrevoEmail extends Command
{
    protected $signature = 'brevo:test-email {email}';
    protected $description = 'Test Brevo email configuration';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Testing Brevo email configuration...');
        
        // Test 1: Using BrevoEmailService
        $this->info('1. Testing BrevoEmailService...');
        $brevoService = new BrevoEmailService();
        
        $result = $brevoService->sendEmail(
            ['email' => $email, 'name' => 'Test User'],
            'Test Email from Laravel',
            '<h1>Test Email</h1><p>This is a test email from Laravel using Brevo.</p>',
            'Test Email - This is a test email from Laravel using Brevo.'
        );
        
        if ($result) {
            $this->info('✅ BrevoEmailService: Email sent successfully');
        } else {
            $this->error('❌ BrevoEmailService: Failed to send email');
        }
        
        // Test 2: Using Laravel Mail with Brevo SMTP
        $this->info('2. Testing Laravel Mail with Brevo SMTP...');
        
        try {
            Mail::raw('This is a test email from Laravel using Brevo SMTP.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from Laravel (SMTP)');
            });
            
            $this->info('✅ Laravel Mail: Email sent successfully');
        } catch (\Exception $e) {
            $this->error('❌ Laravel Mail: ' . $e->getMessage());
        }
        
        $this->info('Email testing completed!');
    }
}


