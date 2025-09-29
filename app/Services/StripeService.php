<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class StripeService
{
    protected $secretKey;
    protected $publishableKey;
    protected $webhookSecret;
    protected $currency;

    public function __construct()
    {
        // Use direct configuration for now
        $this->secretKey = 'sk_test_51SCNpJAIxD4wuxCfuKUxypDhUpg2QLjXZbnVMevF7mzkZylHr8t9DYq6boqxxmsgaBblEianFcUTrOvqXyA7PnOA004BtLS9Zw';
        $this->publishableKey = 'pk_test_51SCNpJAIxD4wuxCfYYHXZFHbCfjzh4B13yCYdjQ4FlbhKnv7QphPIKAQuRqSEUkrZhBSCETSAYSeTt7uEw7qmjaq00OlX59TT2';
        $this->webhookSecret = 'whsec_your_webhook_secret_here';
        $this->currency = 'lkr';

        // Set the API key
        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Create a Stripe checkout session
     */
    public function createCheckoutSession(array $data): array
    {
        try {
            $session = Session::create([
                'payment_method_types' => config('stripe.payment_methods', ['card']),
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $this->currency,
                            'product_data' => [
                                'name' => $data['taxType'] ?? 'Tax Payment',
                                'description' => "Tax payment for {$data['taxpayerName']}",
                            ],
                            'unit_amount' => $this->convertToCents($data['amount']),
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => $data['success_url'] ?? config('stripe.success_url'),
                'cancel_url' => $data['cancel_url'] ?? config('stripe.cancel_url'),
                'metadata' => [
                    'taxpayer_name' => $data['taxpayerName'],
                    'nic' => $data['nic'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? '',
                    'address' => $data['address'] ?? '',
                    'tax_type' => $data['taxType'],
                    'payment_id' => $data['payment_id'] ?? '',
                ],
                'customer_email' => $data['email'],
                'billing_address_collection' => 'required',
                'payment_intent_data' => [
                    'metadata' => [
                        'taxpayer_name' => $data['taxpayerName'],
                        'nic' => $data['nic'],
                        'tax_type' => $data['taxType'],
                        'payment_id' => $data['payment_id'] ?? '',
                    ],
                ],
            ]);

            $this->logStripeOperation('checkout_session_created', [
                'session_id' => $session->id,
                'payment_id' => $data['payment_id'] ?? null,
                'amount' => $data['amount'],
                'taxpayer' => $data['taxpayerName'],
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'session_url' => $session->url,
                'publishable_key' => $this->publishableKey,
            ];

        } catch (ApiErrorException $e) {
            $this->logStripeError('checkout_session_creation_failed', $e, $data);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
        }
    }

    /**
     * Retrieve a checkout session
     */
    public function retrieveCheckoutSession(string $sessionId): array
    {
        try {
            $session = Session::retrieve($sessionId);

            return [
                'success' => true,
                'session' => $session,
            ];

        } catch (ApiErrorException $e) {
            $this->logStripeError('checkout_session_retrieval_failed', $e, ['session_id' => $sessionId]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve a payment intent
     */
    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => true,
                'payment_intent' => $paymentIntent,
            ];

        } catch (ApiErrorException $e) {
            $this->logStripeError('payment_intent_retrieval_failed', $e, ['payment_intent_id' => $paymentIntentId]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $this->webhookSecret
            );

            return true;

        } catch (\Exception $e) {
            $this->logStripeError('webhook_signature_verification_failed', $e, [
                'signature' => $signature,
                'payload_length' => strlen($payload),
            ]);

            return false;
        }
    }

    /**
     * Process webhook event
     */
    public function processWebhookEvent($event): array
    {
        try {
            $eventType = $event->type;
            $eventData = $event->data->object;

            $this->logStripeOperation('webhook_event_received', [
                'event_type' => $eventType,
                'event_id' => $event->id,
            ]);

            switch ($eventType) {
                case 'checkout.session.completed':
                    return $this->handleCheckoutSessionCompleted($eventData);
                
                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($eventData);
                
                case 'payment_intent.payment_failed':
                    return $this->handlePaymentIntentFailed($eventData);
                
                default:
                    return [
                        'success' => true,
                        'message' => 'Event type not handled',
                        'event_type' => $eventType,
                    ];
            }

        } catch (\Exception $e) {
            $this->logStripeError('webhook_event_processing_failed', $e, [
                'event_type' => $event->type ?? 'unknown',
                'event_id' => $event->id ?? 'unknown',
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle checkout session completed event
     */
    protected function handleCheckoutSessionCompleted($session): array
    {
        $this->logStripeOperation('checkout_session_completed', [
            'session_id' => $session->id,
            'payment_intent_id' => $session->payment_intent,
            'amount_total' => $session->amount_total,
            'customer_email' => $session->customer_email,
        ]);

        return [
            'success' => true,
            'message' => 'Checkout session completed',
            'session_id' => $session->id,
            'payment_intent_id' => $session->payment_intent,
            'amount_total' => $session->amount_total,
            'customer_email' => $session->customer_email,
            'metadata' => $session->metadata,
        ];
    }

    /**
     * Handle payment intent succeeded event
     */
    protected function handlePaymentIntentSucceeded($paymentIntent): array
    {
        $this->logStripeOperation('payment_intent_succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'status' => $paymentIntent->status,
        ]);

        return [
            'success' => true,
            'message' => 'Payment intent succeeded',
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'status' => $paymentIntent->status,
            'metadata' => $paymentIntent->metadata,
        ];
    }

    /**
     * Handle payment intent failed event
     */
    protected function handlePaymentIntentFailed($paymentIntent): array
    {
        $this->logStripeOperation('payment_intent_failed', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'status' => $paymentIntent->status,
            'last_payment_error' => $paymentIntent->last_payment_error,
        ]);

        return [
            'success' => true,
            'message' => 'Payment intent failed',
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'status' => $paymentIntent->status,
            'error' => $paymentIntent->last_payment_error,
            'metadata' => $paymentIntent->metadata,
        ];
    }

    /**
     * Convert amount to cents (Stripe requirement)
     */
    protected function convertToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Log Stripe operations
     */
    protected function logStripeOperation(string $operation, array $data): void
    {
        if (config('stripe.logging.enabled', true)) {
            Log::info("Stripe {$operation}", $data);
        }
    }

    /**
     * Log Stripe errors
     */
    protected function logStripeError(string $operation, \Exception $e, array $context = []): void
    {
        Log::error("Stripe {$operation}", array_merge([
            'error' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], $context));
    }

    /**
     * Get publishable key
     */
    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }

    /**
     * Get currency
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}

