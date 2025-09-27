<?php

namespace App\Console\Commands;

use App\Services\UnifiedPayHereService;
use Illuminate\Console\Command;

class TestPayHereIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payhere:test {--type=all : Payment type to test (water_bill, hall_reservation, tax_payment, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PayHere sandbox integration for all payment types';

    protected $payHereService;

    public function __construct(UnifiedPayHereService $payHereService)
    {
        parent::__construct();
        $this->payHereService = $payHereService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing PayHere Sandbox Integration...');
        $this->newLine();

        // Check configuration
        $this->checkConfiguration();

        $type = $this->option('type');

        if ($type === 'all' || $type === 'water_bill') {
            $this->testWaterBillPayment();
        }

        if ($type === 'all' || $type === 'hall_reservation') {
            $this->testHallReservationPayment();
        }

        if ($type === 'all' || $type === 'tax_payment') {
            $this->testTaxPayment();
        }

        $this->newLine();
        $this->info('✅ PayHere integration testing completed!');
    }

    private function checkConfiguration()
    {
        $this->info('📋 Checking PayHere Configuration...');

        $merchantId = config('payhere.merchant_id');
        $merchantSecret = config('payhere.merchant_secret');
        $checkoutUrl = config('payhere.checkout_url');

        if (empty($merchantId)) {
            $this->error('❌ PAYHERE_MERCHANT_ID is not set');
            return false;
        }

        if (empty($merchantSecret)) {
            $this->error('❌ PAYHERE_MERCHANT_SECRET is not set');
            return false;
        }

        $this->info("✅ Merchant ID: {$merchantId}");
        $this->info("✅ Checkout URL: {$checkoutUrl}");
        $this->info("✅ Sandbox Mode: " . (config('payhere.sandbox.enabled') ? 'Enabled' : 'Disabled'));
        $this->newLine();

        return true;
    }

    private function testWaterBillPayment()
    {
        $this->info('💧 Testing Water Bill Payment...');

        $testData = [
            'payment_id' => 1,
            'amount' => 500.00,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '0771234567',
            'address' => '123 Main Street',
            'city' => 'Colombo',
            'account_no' => 'WB001'
        ];

        try {
            $checkoutData = $this->payHereService->generateCheckoutData('water_bill', $testData);
            
            $this->info("✅ Checkout data generated successfully");
            $this->info("   Order ID: {$checkoutData['order_id']}");
            $this->info("   Amount: LKR {$checkoutData['amount']}");
            $this->info("   Hash: " . substr($checkoutData['hash'], 0, 20) . "...");
            
        } catch (\Exception $e) {
            $this->error("❌ Water bill payment test failed: " . $e->getMessage());
        }

        $this->newLine();
    }

    private function testHallReservationPayment()
    {
        $this->info('🏛️ Testing Hall Reservation Payment...');

        $testData = [
            'payment_id' => 1,
            'amount' => 2000.00,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '0779876543',
            'address' => '456 Oak Avenue',
            'city' => 'Kandy',
            'hall_name' => 'Main Hall'
        ];

        try {
            $checkoutData = $this->payHereService->generateCheckoutData('hall_reservation', $testData);
            
            $this->info("✅ Checkout data generated successfully");
            $this->info("   Order ID: {$checkoutData['order_id']}");
            $this->info("   Amount: LKR {$checkoutData['amount']}");
            $this->info("   Hash: " . substr($checkoutData['hash'], 0, 20) . "...");
            
        } catch (\Exception $e) {
            $this->error("❌ Hall reservation payment test failed: " . $e->getMessage());
        }

        $this->newLine();
    }

    private function testTaxPayment()
    {
        $this->info('💰 Testing Tax Payment...');

        $testData = [
            'payment_id' => 1,
            'amount' => 1500.00,
            'first_name' => 'Robert',
            'last_name' => 'Johnson',
            'email' => 'robert@example.com',
            'phone' => '0775555555',
            'address' => '789 Pine Street',
            'city' => 'Galle',
            'assessment_id' => 1
        ];

        try {
            $checkoutData = $this->payHereService->generateCheckoutData('tax_payment', $testData);
            
            $this->info("✅ Checkout data generated successfully");
            $this->info("   Order ID: {$checkoutData['order_id']}");
            $this->info("   Amount: LKR {$checkoutData['amount']}");
            $this->info("   Hash: " . substr($checkoutData['hash'], 0, 20) . "...");
            
        } catch (\Exception $e) {
            $this->error("❌ Tax payment test failed: " . $e->getMessage());
        }

        $this->newLine();
    }
}
