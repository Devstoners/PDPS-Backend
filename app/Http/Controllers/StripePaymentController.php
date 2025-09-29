<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStripePaymentRequest;
use App\Models\StripePayment;
use App\Services\StripeService;
use App\Services\BrevoEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StripePaymentController extends Controller
{
    protected $stripeService;
    protected $brevoService;

    public function __construct(StripeService $stripeService, BrevoEmailService $brevoService)
    {
        $this->stripeService = $stripeService;
        $this->brevoService = $brevoService;
    }

    /**
     * Create a Stripe checkout session
     */
    public function createCheckoutSession(CreateStripePaymentRequest $request): JsonResponse
    {
        try {
            // Generate unique payment ID
            $paymentId = 'PAY_' . Str::upper(Str::random(12));

            // Get tax payee information
            $taxPayee = \App\Models\TaxPayee::find($request->tax_payee_id);
            if (!$taxPayee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tax payee not found'
                ], 404);
            }

            // This is a public payment - no officer involvement needed
            $officer = null;

            // Prepare data for Stripe
            $stripeData = [
                'amount' => $request->amount_paying,
                'currency' => $request->currency,
                'taxType' => 'Tax Payment', // Default tax type
                'taxpayerName' => $taxPayee->name,
                'nic' => $taxPayee->nic,
                'email' => $taxPayee->email,
                'phone' => $taxPayee->tel,
                'address' => $taxPayee->address,
                'payment_id' => $paymentId,
                'success_url' => $request->success_url,
                'cancel_url' => $request->cancel_url,
            ];

            // Create Stripe checkout session
            $result = $this->stripeService->createCheckoutSession($stripeData);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create checkout session',
                    'error' => $result['error'] ?? 'Unknown error',
                ], 500);
            }

            // Create payment record with Stripe session ID
            DB::beginTransaction();
            try {
                $payment = StripePayment::create([
                    'stripe_session_id' => $result['session_id'],
                    'payment_id' => $paymentId,
                    'amount' => $request->amount_paying,
                    'currency' => $request->currency,
                    'status' => 'pending',
                    'tax_type' => 'Tax Payment',
                    'taxpayer_name' => $taxPayee->name,
                    'nic' => $taxPayee->nic,
                    'email' => $taxPayee->email,
                    'phone' => $taxPayee->tel,
                    'address' => $taxPayee->address,
                    'stripe_metadata' => $stripeData,
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save payment record',
                    'error' => $e->getMessage(),
                ], 500);
            }

            Log::info('Stripe checkout session created', [
                'payment_id' => $paymentId,
                'session_id' => $result['session_id'],
                'amount' => $request->amount_paying,
                'taxpayer' => $taxPayee->name,
                'payment_type' => 'public_payment',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout session created successfully',
                'sessionId' => $result['session_id'],
                'url' => $result['session_url'],
                'payment' => [
                    'session_id' => $result['session_id'],
                    'amount' => $request->amount_paying,
                    'currency' => $request->currency,
                    'status' => 'pending'
                ],
                'data' => [
                    'payment_id' => $paymentId,
                    'session_id' => $result['session_id'],
                    'session_url' => $result['session_url'],
                    'publishable_key' => $result['publishable_key'],
                    'amount' => $request->amount_paying,
                    'currency' => $request->currency,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Failed to create Stripe checkout session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong',
            ], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentId): JsonResponse
    {
        try {
            $payment = StripePayment::where('payment_id', $paymentId)->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                ], 404);
            }

            // If payment is still pending, check with Stripe
            if ($payment->isPending() && $payment->stripe_session_id) {
                $sessionResult = $this->stripeService->retrieveCheckoutSession($payment->stripe_session_id);
                
                if ($sessionResult['success']) {
                    $session = $sessionResult['session'];
                    
                    // Update payment status based on Stripe session
                    if ($session->payment_status === 'paid') {
                        $payment->markAsSuccessful();
                        $payment->update([
                            'stripe_payment_intent_id' => $session->payment_intent,
                        ]);
                    } elseif ($session->payment_status === 'unpaid' && $session->status === 'expired') {
                        $payment->update(['status' => 'canceled']);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->payment_id,
                    'status' => $payment->status,
                    'status_text' => $payment->status_text,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'formatted_amount' => $payment->formatted_amount,
                    'taxpayer_name' => $payment->taxpayer_name,
                    'tax_type' => $payment->tax_type,
                    'email' => $payment->email,
                    'created_at' => $payment->created_at,
                    'paid_at' => $payment->paid_at,
                    'failed_at' => $payment->failed_at,
                    'failure_reason' => $payment->failure_reason,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get payment status', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment status',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong',
            ], 500);
        }
    }

    /**
     * Get payment details
     */
    public function getPaymentDetails(string $paymentId): JsonResponse
    {
        try {
            $payment = StripePayment::where('payment_id', $paymentId)->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->payment_id,
                    'stripe_session_id' => $payment->stripe_session_id,
                    'stripe_payment_intent_id' => $payment->stripe_payment_intent_id,
                    'status' => $payment->status,
                    'status_text' => $payment->status_text,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'formatted_amount' => $payment->formatted_amount,
                    'tax_type' => $payment->tax_type,
                    'taxpayer_name' => $payment->taxpayer_name,
                    'nic' => $payment->nic,
                    'email' => $payment->email,
                    'phone' => $payment->phone,
                    'address' => $payment->address,
                    'stripe_metadata' => $payment->stripe_metadata,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                    'paid_at' => $payment->paid_at,
                    'failed_at' => $payment->failed_at,
                    'failure_reason' => $payment->failure_reason,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get payment details', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment details',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong',
            ], 500);
        }
    }

    /**
     * List payments with pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = StripePayment::query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by email
            if ($request->has('email')) {
                $query->where('email', 'like', '%' . $request->email . '%');
            }

            // Filter by date range
            if ($request->has('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->has('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Sort by created_at desc by default
            $query->orderBy('created_at', 'desc');

            // Paginate results
            $perPage = $request->get('per_page', 15);
            $payments = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $payments->items(),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to list payments', [
                'error' => $e->getMessage(),
                'request_params' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payments',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong',
            ], 500);
        }
    }

    /**
     * Cancel a pending payment
     */
    public function cancelPayment(string $paymentId): JsonResponse
    {
        try {
            $payment = StripePayment::where('payment_id', $paymentId)->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                ], 404);
            }

            if (!$payment->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending payments can be canceled',
                ], 422);
            }

            $payment->update([
                'status' => 'canceled',
                'failed_at' => now(),
                'failure_reason' => 'Payment canceled by user',
            ]);

            Log::info('Payment canceled', [
                'payment_id' => $paymentId,
                'amount' => $payment->amount,
                'taxpayer' => $payment->taxpayer_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment canceled successfully',
                'data' => [
                    'payment_id' => $payment->payment_id,
                    'status' => $payment->status,
                    'status_text' => $payment->status_text,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cancel payment', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel payment',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong',
            ], 500);
        }
    }
}