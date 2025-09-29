<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\StripePayment;
use App\Services\GmailEmailService;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    protected $emailService;

    public function __construct()
    {
        $this->emailService = new GmailEmailService();
        
        // Set Stripe API key
        Stripe::setApiKey('sk_test_51SCNpJAIxD4wuxCfuKUxypDhUpg2QLjXZbnVMevF7mzkZylHr8t9DYq6boqxxmsgaBblEianFcUTrOvqXyA7PnOA004BtLS9Zw');
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            $endpointSecret = env('STRIPE_WEBHOOK_SECRET', 'whsec_your_webhook_secret_here');

            // Verify webhook signature (optional for demo)
            // $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            
            // For demo purposes, parse the payload directly
            $event = json_decode($payload, true);

            Log::info('Stripe webhook received', [
                'event_type' => $event['type'] ?? 'unknown',
                'event_id' => $event['id'] ?? 'unknown'
            ]);

            // Handle different event types
            switch ($event['type']) {
                case 'checkout.session.completed':
                    return $this->handleCheckoutSessionCompleted($event);
                
                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($event);
                
                case 'payment_intent.payment_failed':
                    return $this->handlePaymentIntentFailed($event);
                
                default:
                    Log::info('Unhandled Stripe webhook event', [
                        'event_type' => $event['type']
                    ]);
                    return response()->json(['status' => 'ignored'], 200);
            }

        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Handle checkout session completed event
     */
    private function handleCheckoutSessionCompleted($event): JsonResponse
    {
        try {
            $session = $event['data']['object'];
            $sessionId = $session['id'];

            // Find the payment record
            $stripePayment = StripePayment::where('stripe_session_id', $sessionId)->first();

            if (!$stripePayment) {
                Log::warning('Stripe payment not found for session', [
                    'session_id' => $sessionId
                ]);
                return response()->json(['status' => 'payment_not_found'], 404);
            }

            // Update payment status
            $stripePayment->update([
                'status' => 'succeeded',
                'stripe_metadata' => array_merge($stripePayment->stripe_metadata ?? [], [
                    'session_completed' => true,
                    'completion_time' => now()->toISOString()
                ])
            ]);

            // Send confirmation email
            // Get taxpayer info from the payment record
            $taxPayee = new \stdClass();
            $taxPayee->name = $stripePayment->taxpayer_name;
            $taxPayee->nic = $stripePayment->nic;
            $taxPayee->email = $stripePayment->email;
            $taxPayee->tel = $stripePayment->phone;
            $taxPayee->address = $stripePayment->address;
            
            $emailSent = $this->emailService->sendStripePaymentConfirmation($stripePayment, $taxPayee);

            // Create tax payment record
            $this->createTaxPaymentRecord($stripePayment, $taxPayee);

            Log::info('Stripe payment completed', [
                'payment_id' => $stripePayment->payment_id,
                'session_id' => $sessionId,
                'email_sent' => $emailSent
            ]);

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Failed to handle checkout session completed', [
                'error' => $e->getMessage(),
                'event' => $event
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Handle payment intent succeeded event
     */
    private function handlePaymentIntentSucceeded($event): JsonResponse
    {
        try {
            $paymentIntent = $event['data']['object'];
            $sessionId = $paymentIntent['metadata']['session_id'] ?? null;

            if ($sessionId) {
                $stripePayment = StripePayment::where('stripe_session_id', $sessionId)->first();
                
                if ($stripePayment) {
                    $stripePayment->update([
                        'status' => 'succeeded',
                        'stripe_metadata' => array_merge($stripePayment->stripe_metadata ?? [], [
                            'payment_intent_succeeded' => true,
                            'completion_time' => now()->toISOString()
                        ])
                    ]);

                    // Send confirmation email
                    // Get taxpayer info from the payment record
                    $taxPayee = new \stdClass();
                    $taxPayee->name = $stripePayment->taxpayer_name;
                    $taxPayee->nic = $stripePayment->nic;
                    $taxPayee->email = $stripePayment->email;
                    $taxPayee->tel = $stripePayment->phone;
                    $taxPayee->address = $stripePayment->address;
                    
                    $this->emailService->sendStripePaymentConfirmation($stripePayment, $taxPayee);
                    
                    // Create tax payment record
                    $this->createTaxPaymentRecord($stripePayment, $taxPayee);
                }
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Failed to handle payment intent succeeded', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Handle payment intent failed event
     */
    private function handlePaymentIntentFailed($event): JsonResponse
    {
        try {
            $paymentIntent = $event['data']['object'];
            $sessionId = $paymentIntent['metadata']['session_id'] ?? null;

            if ($sessionId) {
                $stripePayment = StripePayment::where('stripe_session_id', $sessionId)->first();
                
                if ($stripePayment) {
                    $stripePayment->update([
                        'status' => 'failed',
                        'stripe_metadata' => array_merge($stripePayment->stripe_metadata ?? [], [
                            'payment_intent_failed' => true,
                            'failure_time' => now()->toISOString(),
                            'failure_reason' => $paymentIntent['last_payment_error']['message'] ?? 'Unknown error'
                        ])
                    ]);
                }
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Failed to handle payment intent failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Create tax payment record after successful Stripe payment
     */
    private function createTaxPaymentRecord($stripePayment, $taxPayee)
    {
        try {
            // Find the tax payee in the database
            $taxPayeeRecord = \App\Models\TaxPayee::where('nic', $taxPayee->nic)->first();
            
            if (!$taxPayeeRecord) {
                Log::warning('Tax payee not found for Stripe payment', [
                    'nic' => $taxPayee->nic,
                    'payment_id' => $stripePayment->payment_id
                ]);
                return;
            }

            // Find the tax property for this payee (use the first one if multiple)
            $taxProperty = \App\Models\TaxProperty::where('tax_payee_id', $taxPayeeRecord->id)->first();
            
            if (!$taxProperty) {
                Log::warning('Tax property not found for Stripe payment', [
                    'tax_payee_id' => $taxPayeeRecord->id,
                    'payment_id' => $stripePayment->payment_id
                ]);
                return;
            }

            // Find the tax assessment for this property (use the latest one if multiple)
            $taxAssessment = \App\Models\TaxAssessment::where('tax_property_id', $taxProperty->id)
                ->where('status', 'unpaid')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$taxAssessment) {
                Log::warning('Tax assessment not found for Stripe payment', [
                    'tax_property_id' => $taxProperty->id,
                    'payment_id' => $stripePayment->payment_id
                ]);
                return;
            }

            // Create the tax payment record
            $taxPayment = \App\Models\TaxPayment::create([
                'tax_property_id' => $taxProperty->id,
                'tax_assessment_id' => $taxAssessment->id,
                'officer_id' => null, // Public payment, no officer involved
                'discount_amount' => 0.00,
                'fine_amount' => 0.00,
                'pay_date' => now()->toDateString(),
                'pay_method' => 'online',
                'payment' => $stripePayment->amount,
                'transaction_id' => $stripePayment->payment_id, // Use Stripe payment ID as transaction ID
                'status' => 'confirmed'
            ]);

            // Update the tax assessment status to paid
            $taxAssessment->update(['status' => 'paid']);

            Log::info('Tax payment record created successfully', [
                'tax_payment_id' => $taxPayment->id,
                'stripe_payment_id' => $stripePayment->payment_id,
                'amount' => $stripePayment->amount,
                'tax_assessment_id' => $taxAssessment->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create tax payment record', [
                'error' => $e->getMessage(),
                'stripe_payment_id' => $stripePayment->payment_id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
