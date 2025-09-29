<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StripePayment;
use App\Models\TaxPayee;
use App\Services\StripePaymentEmailService;

class TestStripePaymentEmail extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'stripe:test-email {--email=}';

    /**
     * The console command description.
     */
    protected $description = 'Test Stripe payment confirmation email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Stripe Payment Confirmation Email');
        $this->line('==========================================');
        $this->newLine();

        try {
            // Get test email from option or use default
            $testEmail = $this->option('email') ?? 'lak.bope@gmail.com';
            
            $this->info("📧 Sending test email to: {$testEmail}");
            $this->newLine();

            // Create a test Stripe payment record
            $taxPayee = TaxPayee::first();
            
            if (!$taxPayee) {
                $this->error('❌ No tax payees found. Please create a tax payee first.');
                return 1;
            }

            // Create a test Stripe payment
            $stripePayment = StripePayment::create([
                'stripe_session_id' => 'cs_test_' . time(),
                'payment_id' => 'PAY_TEST_' . strtoupper(uniqid()),
                'amount' => 1500.00,
                'currency' => 'lkr',
                'status' => 'succeeded',
                'tax_type' => 'Tax Payment',
                'taxpayer_name' => $taxPayee->name,
                'nic' => $taxPayee->nic,
                'email' => $testEmail,
                'phone' => $taxPayee->tel,
                'address' => $taxPayee->address,
                'stripe_metadata' => [
                    'test_payment' => true,
                    'created_at' => now()->toISOString()
                ]
            ]);

            $this->info("✅ Created test Stripe payment: {$stripePayment->payment_id}");

            // Send the email
            $emailService = new StripePaymentEmailService();
            $result = $emailService->sendPaymentConfirmation($stripePayment);

            if ($result) {
                $this->info('✅ Payment confirmation email sent successfully!');
                $this->line("📧 Check your email: {$testEmail}");
                $this->newLine();
                
                $this->info('📋 Email Details:');
                $this->line("- Payment ID: {$stripePayment->payment_id}");
                $this->line("- Amount: LKR " . number_format($stripePayment->amount, 2));
                $this->line("- Taxpayer: {$taxPayee->name}");
                $this->line("- Email: {$testEmail}");
                
            } else {
                $this->error('❌ Failed to send payment confirmation email');
                $this->line('Check the logs for more details.');
                return 1;
            }

            // Clean up test record
            $stripePayment->delete();
            $this->info('🧹 Cleaned up test payment record');

            $this->newLine();
            $this->info('🎉 Test completed successfully!');

        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->line('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
