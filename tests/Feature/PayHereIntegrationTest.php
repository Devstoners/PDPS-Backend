<?php

namespace Tests\Feature;

use App\Services\UnifiedPayHereService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayHereIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $payHereService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->payHereService = new UnifiedPayHereService();
    }

    /** @test */
    public function it_can_generate_water_bill_checkout_data()
    {
        $paymentData = [
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

        $checkoutData = $this->payHereService->generateCheckoutData('water_bill', $paymentData);

        $this->assertArrayHasKey('merchant_id', $checkoutData);
        $this->assertArrayHasKey('order_id', $checkoutData);
        $this->assertArrayHasKey('amount', $checkoutData);
        $this->assertArrayHasKey('hash', $checkoutData);
        $this->assertEquals('WB_1', $checkoutData['order_id']);
        $this->assertEquals(500.00, $checkoutData['amount']);
    }

    /** @test */
    public function it_can_generate_hall_reservation_checkout_data()
    {
        $paymentData = [
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

        $checkoutData = $this->payHereService->generateCheckoutData('hall_reservation', $paymentData);

        $this->assertArrayHasKey('merchant_id', $checkoutData);
        $this->assertArrayHasKey('order_id', $checkoutData);
        $this->assertArrayHasKey('amount', $checkoutData);
        $this->assertArrayHasKey('hash', $checkoutData);
        $this->assertEquals('HR_1', $checkoutData['order_id']);
        $this->assertEquals(2000.00, $checkoutData['amount']);
    }

    /** @test */
    public function it_can_generate_tax_payment_checkout_data()
    {
        $paymentData = [
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

        $checkoutData = $this->payHereService->generateCheckoutData('tax_payment', $paymentData);

        $this->assertArrayHasKey('merchant_id', $checkoutData);
        $this->assertArrayHasKey('order_id', $checkoutData);
        $this->assertArrayHasKey('amount', $checkoutData);
        $this->assertArrayHasKey('hash', $checkoutData);
        $this->assertEquals('TX_1', $checkoutData['order_id']);
        $this->assertEquals(1500.00, $checkoutData['amount']);
    }

    /** @test */
    public function it_can_verify_payhere_callback_signature()
    {
        $callbackData = [
            'merchant_id' => config('payhere.merchant_id'),
            'order_id' => 'WB_1',
            'payhere_amount' => '500.00',
            'payhere_currency' => 'LKR',
            'status_code' => '2',
            'signature' => 'test_signature'
        ];

        // This will fail in test environment without proper signature
        $isValid = $this->payHereService->verifyCallback($callbackData);
        
        // In test environment, we expect this to be false due to test signature
        $this->assertFalse($isValid);
    }

    /** @test */
    public function it_can_parse_order_id_correctly()
    {
        $reflection = new \ReflectionClass($this->payHereService);
        $method = $reflection->getMethod('parseOrderId');
        $method->setAccessible(true);

        // Test water bill order ID
        $result = $method->invoke($this->payHereService, 'WB_123');
        $this->assertEquals('water_bill', $result['type']);
        $this->assertEquals(123, $result['id']);

        // Test hall reservation order ID
        $result = $method->invoke($this->payHereService, 'HR_456');
        $this->assertEquals('hall_reservation', $result['type']);
        $this->assertEquals(456, $result['id']);

        // Test tax payment order ID
        $result = $method->invoke($this->payHereService, 'TX_789');
        $this->assertEquals('tax_payment', $result['type']);
        $this->assertEquals(789, $result['id']);
    }
}
