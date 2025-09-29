<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WaterPayment;
use App\Models\HallCustomerPayment;
use App\Models\TaxPayment;

class UnifiedPayHereService
{
    protected $merchantId;
    protected $merchantSecret;
    protected $checkoutUrl;
    protected $returnUrl;
    protected $cancelUrl;
    protected $notifyUrl;

    public function __construct()
    {
        $this->merchantId = config('payhere.merchant_id');
        $this->merchantSecret = config('payhere.merchant_secret');
        $this->checkoutUrl = config('payhere.checkout_url');
        $this->returnUrl = config('payhere.return_url');
        $this->cancelUrl = config('payhere.cancel_url');
        $this->notifyUrl = config('payhere.notify_url');
    }

    /**
     * Generate PayHere checkout data for any payment type
     */
    public function generateCheckoutData(string $paymentType, array $paymentData): array
    {
        $orderId = $this->generateOrderId($paymentType, $paymentData['payment_id']);
        $amount = $paymentData['amount'];
        $currency = $paymentData['currency'] ?? config('payhere.currency');
        
        $data = [
            'merchant_id' => $this->merchantId,
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'notify_url' => $this->notifyUrl,
            'first_name' => $paymentData['first_name'],
            'last_name' => $paymentData['last_name'] ?? '',
            'email' => $paymentData['email'],
            'phone' => $paymentData['phone'],
            'address' => $paymentData['address'],
            'city' => $paymentData['city'] ?? '',
            'country' => $paymentData['country'] ?? config('payhere.country'),
            'order_id' => $orderId,
            'items' => $this->getPaymentDescription($paymentType, $paymentData),
            'currency' => $currency,
            'amount' => $amount,
        ];

        // Generate hash for security
        $hash = $this->generateHash($data);
        $data['hash'] = $hash;

        return $data;
    }

    /**
     * Generate order ID based on payment type
     */
    private function generateOrderId(string $paymentType, int $paymentId): string
    {
        $prefix = match($paymentType) {
            'water_bill' => 'WB',
            'hall_reservation' => 'HR',
            'tax_payment' => 'TX',
            default => 'PAY'
        };

        return $prefix . '_' . $paymentId;
    }

    /**
     * Get payment description based on type
     */
    private function getPaymentDescription(string $paymentType, array $paymentData): string
    {
        return match($paymentType) {
            'water_bill' => 'Water Bill Payment - Account #' . ($paymentData['account_no'] ?? ''),
            'hall_reservation' => 'Hall Reservation Payment - ' . ($paymentData['hall_name'] ?? ''),
            'tax_payment' => 'Tax Payment - Assessment #' . ($paymentData['assessment_id'] ?? ''),
            default => 'Payment'
        };
    }

    /**
     * Generate PayHere hash for security
     */
    public function generateHash(array $data): string
    {
        $hashString = $this->merchantId . 
                     $data['order_id'] . 
                     $data['amount'] . 
                     $data['currency'] . 
                     strtoupper(hash('sha256', $this->merchantSecret));

        return strtoupper(hash('sha256', $hashString));
    }

    /**
     * Verify PayHere callback signature
     */
    public function verifyCallback(array $callbackData): bool
    {
        $receivedSignature = $callbackData['signature'] ?? '';
        
        $expectedSignature = strtoupper(hash('sha256', 
            $callbackData['merchant_id'] . 
            $callbackData['order_id'] . 
            $callbackData['payhere_amount'] . 
            $callbackData['payhere_currency'] . 
            $callbackData['status_code'] . 
            strtoupper(hash('sha256', $this->merchantSecret))
        ));

        return $receivedSignature === $expectedSignature;
    }

    /**
     * Process PayHere callback for any payment type
     */
    public function processCallback(array $callbackData): array
    {
        try {
            if (!$this->verifyCallback($callbackData)) {
                Log::error('PayHere callback verification failed', $callbackData);
                return [
                    'success' => false,
                    'message' => 'Invalid signature'
                ];
            }

            $status = $callbackData['status_code'];
            $orderId = $callbackData['order_id'];
            $amount = $callbackData['payhere_amount'];
            $transactionId = $callbackData['payhere_payment_id'] ?? null;

            // Extract payment type and ID from order ID
            $paymentInfo = $this->parseOrderId($orderId);
            if (!$paymentInfo) {
                return [
                    'success' => false,
                    'message' => 'Invalid order ID format'
                ];
            }

            $result = [
                'success' => $status === '2', // PayHere success status code
                'order_id' => $orderId,
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'status' => $status,
                'payment_type' => $paymentInfo['type'],
                'payment_id' => $paymentInfo['id'],
                'message' => $status === '2' ? 'Payment successful' : 'Payment failed'
            ];

            // Update the specific payment record
            if ($status === '2') {
                $this->updatePaymentRecord($paymentInfo['type'], $paymentInfo['id'], $transactionId);
            }

            Log::info('PayHere callback processed', $result);
            return $result;

        } catch (\Exception $e) {
            Log::error('PayHere callback processing error', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData
            ]);

            return [
                'success' => false,
                'message' => 'Callback processing failed'
            ];
        }
    }

    /**
     * Parse order ID to extract payment type and ID
     */
    private function parseOrderId(string $orderId): ?array
    {
        $parts = explode('_', $orderId);
        if (count($parts) !== 2) {
            return null;
        }

        $typeMap = [
            'WB' => 'water_bill',
            'HR' => 'hall_reservation',
            'TX' => 'tax_payment'
        ];

        $type = $typeMap[$parts[0]] ?? null;
        $id = (int) $parts[1];

        return $type ? ['type' => $type, 'id' => $id] : null;
    }

    /**
     * Update payment record based on type
     */
    private function updatePaymentRecord(string $paymentType, int $paymentId, string $transactionId): void
    {
        switch ($paymentType) {
            case 'water_bill':
                WaterPayment::where('id', $paymentId)->update([
                    'status' => 'confirmed',
                    'transaction_id' => $transactionId
                ]);
                break;

            case 'hall_reservation':
                HallCustomerPayment::where('id', $paymentId)->update([
                    'status' => 'confirmed',
                    'transaction_id' => $transactionId
                ]);
                break;

            case 'tax_payment':
                TaxPayment::where('id', $paymentId)->update([
                    'status' => 'confirmed',
                    'transaction_id' => $transactionId
                ]);
                break;
        }
    }

    /**
     * Get payment status from PayHere
     */
    public function getPaymentStatus(string $orderId): array
    {
        try {
            $response = Http::post('https://sandbox.payhere.lk/merchant/v1/payment/search', [
                'merchant_id' => $this->merchantId,
                'order_id' => $orderId,
                'hash' => $this->generateHash(['order_id' => $orderId])
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Failed to get payment status'
            ];

        } catch (\Exception $e) {
            Log::error('PayHere status check error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Status check failed'
            ];
        }
    }

    /**
     * Generate payment receipt data
     */
    public function generateReceiptData(string $paymentType, int $paymentId): array
    {
        switch ($paymentType) {
            case 'water_bill':
                $payment = WaterPayment::with(['waterBill.waterCustomer'])->find($paymentId);
                return [
                    'payment_id' => $payment->id,
                    'customer_name' => $payment->waterBill->waterCustomer->name ?? 'N/A',
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                    'transaction_id' => $payment->transaction_id,
                    'type' => 'Water Bill Payment'
                ];

            case 'hall_reservation':
                $payment = HallCustomerPayment::with(['hallCustomer', 'hallReservation.hall'])->find($paymentId);
                return [
                    'payment_id' => $payment->id,
                    'customer_name' => $payment->hallCustomer->name ?? 'N/A',
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                    'transaction_id' => $payment->transaction_id,
                    'type' => 'Hall Reservation Payment'
                ];

            case 'tax_payment':
                $payment = TaxPayment::with(['taxProperty.taxPayee'])->find($paymentId);
                return [
                    'payment_id' => $payment->id,
                    'customer_name' => $payment->taxProperty->taxPayee->name ?? 'N/A',
                    'amount' => $payment->payment,
                    'payment_date' => $payment->pay_date,
                    'transaction_id' => $payment->transaction_id,
                    'type' => 'Tax Payment'
                ];

            default:
                return [];
        }
    }
}
