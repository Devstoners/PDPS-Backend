<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayHereService
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
     * Generate PayHere checkout form data
     */
    public function generateCheckoutData(array $paymentData): array
    {
        $orderId = $paymentData['order_id'];
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
            'items' => $paymentData['items'],
            'currency' => $currency,
            'amount' => $amount,
        ];

        // Generate hash for security
        $hash = $this->generateHash($data);
        $data['hash'] = $hash;

        return $data;
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
     * Process PayHere callback
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

            $result = [
                'success' => $status === '2', // PayHere success status code
                'order_id' => $orderId,
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'status' => $status,
                'message' => $status === '2' ? 'Payment successful' : 'Payment failed'
            ];

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
}
