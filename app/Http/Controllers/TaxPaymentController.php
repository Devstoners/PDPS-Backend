<?php

namespace App\Http\Controllers;

use App\Models\TaxPayment;
use App\Models\TaxAssessment;
use App\Services\UnifiedPayHereService;
use App\Services\BrevoEmailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TaxPaymentController extends Controller
{
    protected $payHereService;

    public function __construct(UnifiedPayHereService $payHereService)
    {
        $this->payHereService = $payHereService;
    }
    /**
     * Display a listing of tax payments
     */
    public function index(Request $request): JsonResponse
    {
        $query = TaxPayment::with(['taxProperty.taxPayee', 'taxAssessment', 'officer']);

        // Filter by property
        if ($request->has('tax_property_id')) {
            $query->where('tax_property_id', $request->tax_property_id);
        }

        // Filter by assessment
        if ($request->has('tax_assessment_id')) {
            $query->where('tax_assessment_id', $request->tax_assessment_id);
        }

        // Filter by payment method
        if ($request->has('pay_method')) {
            $query->where('pay_method', $request->pay_method);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('pay_date', 'desc')->paginate(15);
        
        return response()->json($payments);
    }

    /**
     * Store a newly created tax payment (Cash payment)
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tax_property_id' => 'required|exists:tax_properties,id',
            'tax_assessment_id' => 'required|exists:tax_assessments,id',
            'officer_id' => 'required|exists:users,id',
            'discount_amount' => 'required|numeric|min:0',
            'fine_amount' => 'required|numeric|min:0',
            'pay_date' => 'required|date',
            'payment' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $payment = TaxPayment::create([
                ...$request->all(),
                'pay_method' => 'cash',
                'status' => 'confirmed'
            ]);

            // Update assessment status if fully paid
            $assessment = TaxAssessment::find($request->tax_assessment_id);
            $totalPaid = $assessment->taxPayments()->where('status', 'confirmed')->sum('payment');
            
            if ($totalPaid >= $assessment->amount) {
                $assessment->update(['status' => 'paid']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Tax payment recorded successfully',
                'data' => $payment->load(['taxProperty.taxPayee', 'taxAssessment', 'officer'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified tax payment
     */
    public function show(TaxPayment $taxPayment): JsonResponse
    {
        $taxPayment->load([
            'taxProperty.taxPayee',
            'taxAssessment',
            'officer'
        ]);

        return response()->json($taxPayment);
    }

    /**
     * Update the specified tax payment
     */
    public function update(Request $request, TaxPayment $taxPayment): JsonResponse
    {
        $request->validate([
            'discount_amount' => 'required|numeric|min:0',
            'fine_amount' => 'required|numeric|min:0',
            'pay_date' => 'required|date',
            'payment' => 'required|numeric|min:0',
            'status' => 'sometimes|in:pending,confirmed,failed',
        ]);

        $taxPayment->update($request->all());

        return response()->json([
            'message' => 'Tax payment updated successfully',
            'data' => $taxPayment->load(['taxProperty.taxPayee', 'taxAssessment', 'officer'])
        ]);
    }

    /**
     * Remove the specified tax payment
     */
    public function destroy(TaxPayment $taxPayment): JsonResponse
    {
        if ($taxPayment->status === 'confirmed') {
            return response()->json([
                'message' => 'Cannot delete confirmed payment'
            ], 422);
        }

        $taxPayment->delete();

        return response()->json([
            'message' => 'Tax payment deleted successfully'
        ]);
    }

    /**
     * Process online payment (PayHere integration)
     */
    public function processOnlinePayment(Request $request, $assessmentId): JsonResponse
    {
        $assessment = TaxAssessment::findOrFail($assessmentId);

        $request->validate([
            'payment' => 'required|numeric|min:0',
            'discount_amount' => 'sometimes|numeric|min:0',
            'fine_amount' => 'sometimes|numeric|min:0',
            'customer_data' => 'required|array',
            'customer_data.first_name' => 'required|string',
            'customer_data.last_name' => 'nullable|string',
            'customer_data.email' => 'required|email',
            'customer_data.phone' => 'required|string',
            'customer_data.address' => 'required|string',
            'customer_data.city' => 'nullable|string',
        ]);

        // Create pending payment record
        $payment = TaxPayment::create([
            'tax_property_id' => $assessment->tax_property_id,
            'tax_assessment_id' => $assessmentId,
            'discount_amount' => $request->discount_amount ?? 0,
            'fine_amount' => $request->fine_amount ?? 0,
            'pay_date' => now()->toDateString(),
            'pay_method' => 'online',
            'payment' => $request->payment,
            'status' => 'pending'
        ]);

        // Generate PayHere checkout data using unified service
        $checkoutData = $this->payHereService->generateCheckoutData('tax_payment', [
            'payment_id' => $payment->id,
            'amount' => $request->payment,
            'first_name' => $request->customer_data['first_name'],
            'last_name' => $request->customer_data['last_name'] ?? '',
            'email' => $request->customer_data['email'],
            'phone' => $request->customer_data['phone'],
            'address' => $request->customer_data['address'],
            'city' => $request->customer_data['city'] ?? '',
            'assessment_id' => $assessmentId,
        ]);

        return response()->json([
            'message' => 'Payment initiated',
            'payment_id' => $payment->id,
            'checkout_data' => $checkoutData,
            'checkout_url' => config('payhere.checkout_url')
        ]);
    }

    /**
     * Handle PayHere callback
     */
    public function payhereCallback(Request $request): JsonResponse
    {
        // Validate PayHere signature
        $merchantSecret = config('payhere.merchant_secret');
        $receivedSignature = $request->input('signature');
        
        // Generate expected signature
        $expectedSignature = strtoupper(hash('sha256', 
            $request->input('merchant_id') . 
            $request->input('order_id') . 
            $request->input('payhere_amount') . 
            $request->input('payhere_currency') . 
            $request->input('status_code') . 
            strtoupper(hash('sha256', $merchantSecret))
        ));

        if ($receivedSignature !== $expectedSignature) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $orderId = $request->input('order_id');
        $status = $request->input('status_code');
        $amount = $request->input('payhere_amount');
        $transactionId = $request->input('payhere_payment_id');

        // Extract payment ID from order ID
        $paymentId = str_replace('TAX_', '', $orderId);
        $payment = TaxPayment::find($paymentId);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        DB::beginTransaction();
        try {
            if ($status === '2') { // Success
                $payment->update([
                    'status' => 'confirmed',
                    'transaction_id' => $transactionId
                ]);

                // Update assessment status if fully paid
                $assessment = $payment->taxAssessment;
                $totalPaid = $assessment->taxPayments()->where('status', 'confirmed')->sum('payment');
                
                if ($totalPaid >= $assessment->amount) {
                    $assessment->update(['status' => 'paid']);
                }

                // Send email confirmation for online payments
                try {
                    $brevoService = new BrevoEmailService();
                    $brevoService->sendTaxPaymentConfirmation($payment);
                } catch (\Exception $e) {
                    \Log::error('Brevo email notification failed: ' . $e->getMessage());
                }
            } else {
                $payment->update(['status' => 'failed']);
            }

            DB::commit();

            return response()->json(['message' => 'Payment status updated']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to update payment'], 500);
        }
    }
}
