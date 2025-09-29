<?php

namespace App\Console\Commands;

use App\Services\SmsNotificationService;
use Illuminate\Console\Command;

class TestSmsIntegration extends Command
{
    protected $signature = 'sms:test 
                            {--phone= : Phone number to test (e.g., +94771234567)}
                            {--type=all : Test type (all, payment, reminder, overdue, reservation, tax, water)}
                            {--message= : Custom message for testing}';

    protected $description = 'Test SMS integration with Twilio';

    protected $smsService;

    public function __construct(SmsNotificationService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $this->info('🧪 Testing SMS Integration with Twilio');
        $this->info('=====================================');

        // Check configuration
        $this->checkConfiguration();

        $phone = $this->option('phone');
        $type = $this->option('type');
        $message = $this->option('message');

        if (!$phone) {
            $phone = $this->ask('Enter phone number to test (e.g., +94771234567)');
        }

        if (!$phone) {
            $this->error('Phone number is required for testing');
            return 1;
        }

        $this->info("📱 Testing SMS to: {$phone}");
        $this->newLine();

        switch ($type) {
            case 'all':
                $this->testAllSmsTypes($phone);
                break;
            case 'payment':
                $this->testPaymentConfirmation($phone);
                break;
            case 'reminder':
                $this->testServiceReminder($phone);
                break;
            case 'overdue':
                $this->testOverdueNotice($phone);
                break;
            case 'reservation':
                $this->testReservationConfirmation($phone);
                break;
            case 'tax':
                $this->testTaxAssessment($phone);
                break;
            case 'water':
                $this->testWaterBill($phone);
                break;
            case 'custom':
                $this->testCustomSms($phone, $message);
                break;
            default:
                $this->error("Unknown test type: {$type}");
                return 1;
        }

        $this->newLine();
        $this->info('✅ SMS testing completed!');
        return 0;
    }

    private function checkConfiguration()
    {
        $this->info('📋 Checking SMS Configuration...');

        $accountSid = config('twilio.account_sid');
        $authToken = config('twilio.auth_token');
        $fromNumber = config('twilio.from_number');
        $enabled = config('twilio.sms.enabled');

        if ($accountSid) {
            $this->info("✅ Account SID: {$accountSid}");
        } else {
            $this->error('❌ Account SID not configured');
        }

        if ($authToken) {
            $this->info("✅ Auth Token: " . str_repeat('*', strlen($authToken) - 4) . substr($authToken, -4));
        } else {
            $this->error('❌ Auth Token not configured');
        }

        if ($fromNumber) {
            $this->info("✅ From Number: {$fromNumber}");
        } else {
            $this->error('❌ From Number not configured');
        }

        if ($enabled) {
            $this->info('✅ SMS Enabled: Yes');
        } else {
            $this->warn('⚠️ SMS Disabled: Messages will be logged only');
        }

        $this->newLine();
    }

    private function testAllSmsTypes($phone)
    {
        $this->info('🔄 Testing all SMS types...');
        $this->newLine();

        $this->testPaymentConfirmation($phone);
        $this->testServiceReminder($phone);
        $this->testOverdueNotice($phone);
        $this->testReservationConfirmation($phone);
        $this->testTaxAssessment($phone);
        $this->testWaterBill($phone);
    }

    private function testPaymentConfirmation($phone)
    {
        $this->info('💳 Testing Payment Confirmation SMS...');
        
        $result = $this->smsService->sendPaymentConfirmation($phone, [
            'amount' => '1,500.00',
            'receipt_no' => 'TEST001',
            'service' => 'Water Bill'
        ]);

        $this->displayResult($result);
    }

    private function testServiceReminder($phone)
    {
        $this->info('⏰ Testing Service Reminder SMS...');
        
        $result = $this->smsService->sendServiceReminder($phone, [
            'service' => 'Water Bill',
            'due_date' => '2024-02-15',
            'amount' => '750.00'
        ]);

        $this->displayResult($result);
    }

    private function testOverdueNotice($phone)
    {
        $this->info('🚨 Testing Overdue Notice SMS...');
        
        $result = $this->smsService->sendOverdueNotice($phone, [
            'service' => 'Tax Payment',
            'amount' => '2,500.00'
        ]);

        $this->displayResult($result);
    }

    private function testReservationConfirmation($phone)
    {
        $this->info('🏛️ Testing Hall Reservation Confirmation SMS...');
        
        $result = $this->smsService->sendReservationConfirmation($phone, [
            'date' => '2024-02-20',
            'time' => '18:00 - 22:00',
            'hall_name' => 'Main Hall'
        ]);

        $this->displayResult($result);
    }

    private function testTaxAssessment($phone)
    {
        $this->info('💰 Testing Tax Assessment SMS...');
        
        $result = $this->smsService->sendTaxAssessment($phone, [
            'amount' => '3,000.00',
            'due_date' => '2024-03-01',
            'property_name' => '123 Main Street'
        ]);

        $this->displayResult($result);
    }

    private function testWaterBill($phone)
    {
        $this->info('💧 Testing Water Bill SMS...');
        
        $result = $this->smsService->sendWaterBill($phone, [
            'amount' => '850.00',
            'due_date' => '2024-02-28',
            'account_no' => 'WB001234'
        ]);

        $this->displayResult($result);
    }

    private function testCustomSms($phone, $message = null)
    {
        $this->info('📝 Testing Custom SMS...');
        
        $message = $message ?? 'Custom test message from PDPS system';
        $result = $this->smsService->testSms($phone, $message);

        $this->displayResult($result);
    }

    private function displayResult($result)
    {
        if ($result['success']) {
            $this->info("✅ Success: {$result['message']}");
            if (isset($result['sid'])) {
                $this->info("📱 Message SID: {$result['sid']}");
            }
        } else {
            $this->error("❌ Failed: {$result['message']}");
            if (isset($result['error'])) {
                $this->error("🔍 Error Code: {$result['error']}");
            }
        }
        $this->newLine();
    }
}
