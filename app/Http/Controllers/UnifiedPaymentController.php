<?php

namespace App\Http\Controllers;

use App\Services\UnifiedPayHereService;
use App\Services\SmsNotificationService;
use App\Models\WaterPayment;
use App\Models\HallCustomerPayment;
use App\Models\TaxPayment;
use App\Models\WaterBill;
use App\Models\HallReservation;
use App\Models\TaxAssessment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UnifiedPaymentController extends Controller
{
    protected $payHereService;
    protected $smsService;

    public function __construct(UnifiedPayHereService $payHereService, SmsNotificationService $smsService)
    {
        $this->payHereService = $payHereService;
        $this->smsService = $smsService;
    }

    /**
     * Process online payment for any payment type
     */
    public function processOnlinePayment(Request $request): JsonResponse
    {
        $request->validate([
            'payment_type' => 'required|in:water_bill,hall_reservation,tax_payment',
            'payment_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'customer_data' => 'required|array',
            'customer_data.first_name' => 'required|string',
            'customer_data.last_name' => 'nullable|string',
            'customer_data.email' => 'required|email',
            'customer_data.phone' => 'required|string',
            'customer_data.address' => 'required|string',
            'customer_data.city' => 'nullable|string',
        ]);

        $paymentType = $request->payment_type;
        $paymentId = $request->payment_id;
        $amount = $request->amount;
        $customerData = $request->customer_data;

        // Validate payment exists and get additional data
        $paymentData = $this->validateAndGetPaymentData($paymentType, $paymentId, $amount);
        if (!$paymentData['valid']) {
            return response()->json([
                'message' => $paymentData['message']
            ], 422);
        }

        // Create pending payment record
        $payment = $this->createPendingPayment($paymentType, $paymentId, $amount, $customerData);

        // Generate PayHere checkout data
        $checkoutData = $this->payHereService->generateCheckoutData($paymentType, [
            'payment_id' => $payment->id,
            'amount' => $amount,
            'first_name' => $customerData['first_name'],
            'last_name' => $customerData['last_name'] ?? '',
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address'],
            'city' => $customerData['city'] ?? '',
            'account_no' => $paymentData['account_no'] ?? null,
            'hall_name' => $paymentData['hall_name'] ?? null,
            'assessment_id' => $paymentData['assessment_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'Payment initiated',
            'payment_id' => $payment->id,
            'checkout_data' => $checkoutData,
            'checkout_url' => config('payhere.checkout_url')
        ]);
    }

    /**
     * Validate payment and get additional data
     */
    private function validateAndGetPaymentData(string $paymentType, int $paymentId, float $amount): array
    {
        switch ($paymentType) {
            case 'water_bill':
                $bill = WaterBill::with('waterCustomer')->find($paymentId);
                if (!$bill) {
                    return ['valid' => false, 'message' => 'Water bill not found'];
                }
                if ($bill->status === 'paid') {
                    return ['valid' => false, 'message' => 'Bill already paid'];
                }
                return [
                    'valid' => true,
                    'account_no' => $bill->waterCustomer->account_no ?? null
                ];

            case 'hall_reservation':
                $reservation = HallReservation::with('hall')->find($paymentId);
                if (!$reservation) {
                    return ['valid' => false, 'message' => 'Hall reservation not found'];
                }
                if ($reservation->status === 'paid') {
                    return ['valid' => false, 'message' => 'Reservation already paid'];
                }
                return [
                    'valid' => true,
                    'hall_name' => $reservation->hall->name ?? null
                ];

            case 'tax_payment':
                $assessment = TaxAssessment::find($paymentId);
                if (!$assessment) {
                    return ['valid' => false, 'message' => 'Tax assessment not found'];
                }
                if ($assessment->status === 'paid') {
                    return ['valid' => false, 'message' => 'Assessment already paid'];
                }
                return [
                    'valid' => true,
                    'assessment_id' => $assessment->id
                ];

            default:
                return ['valid' => false, 'message' => 'Invalid payment type'];
        }
    }

    /**
     * Create pending payment record
     */
    private function createPendingPayment(string $paymentType, int $originalId, float $amount, array $customerData)
    {
        switch ($paymentType) {
            case 'water_bill':
                return WaterPayment::create([
                    'water_bill_id' => $originalId,
                    'amount' => $amount,
                    'payment_date' => now()->toDateString(),
                    'payment_method' => 'online',
                    'status' => 'pending',
                    'customer_name' => $customerData['first_name'] . ' ' . ($customerData['last_name'] ?? ''),
                    'customer_email' => $customerData['email'],
                    'customer_phone' => $customerData['phone'],
                ]);

            case 'hall_reservation':
                return HallCustomerPayment::create([
                    'hall_reservation_id' => $originalId,
                    'amount' => $amount,
                    'payment_date' => now()->toDateString(),
                    'payment_method' => 'online',
                    'status' => 'pending',
                    'customer_name' => $customerData['first_name'] . ' ' . ($customerData['last_name'] ?? ''),
                    'customer_email' => $customerData['email'],
                    'customer_phone' => $customerData['phone'],
                ]);

            case 'tax_payment':
                $assessment = TaxAssessment::find($originalId);
                return TaxPayment::create([
                    'tax_property_id' => $assessment->tax_property_id,
                    'tax_assessment_id' => $originalId,
                    'payment' => $amount,
                    'pay_date' => now()->toDateString(),
                    'pay_method' => 'online',
                    'status' => 'pending',
                ]);

            default:
                throw new \InvalidArgumentException('Invalid payment type');
        }
    }

    /**
     * Handle PayHere callback
     */
    public function handleCallback(Request $request): JsonResponse
    {
        $result = $this->payHereService->processCallback($request->all());

        if ($result['success']) {
            // Update related records based on payment type
            $this->updateRelatedRecords($result['payment_type'], $result['payment_id']);
            
            // Send SMS notification for successful payment
            $this->sendPaymentConfirmationSms($result['payment_type'], $result['payment_id']);
        }

        return response()->json($result);
    }

    /**
     * Update related records after successful payment
     */
    private function updateRelatedRecords(string $paymentType, int $paymentId): void
    {
        switch ($paymentType) {
            case 'water_bill':
                $payment = WaterPayment::with('waterBill')->find($paymentId);
                if ($payment) {
                    $payment->waterBill->update(['status' => 'paid']);
                }
                break;

            case 'hall_reservation':
                $payment = HallCustomerPayment::with('hallReservation')->find($paymentId);
                if ($payment) {
                    $payment->hallReservation->update(['status' => 'paid']);
                }
                break;

            case 'tax_payment':
                $payment = TaxPayment::with('taxAssessment')->find($paymentId);
                if ($payment) {
                    $assessment = $payment->taxAssessment;
                    $totalPaid = $assessment->taxPayments()->where('status', 'confirmed')->sum('payment');
                    
                    if ($totalPaid >= $assessment->amount) {
                        $assessment->update(['status' => 'paid']);
                    }
                }
                break;
        }
    }

    /**
     * Send payment confirmation SMS
     */
    private function sendPaymentConfirmationSms(string $paymentType, int $paymentId): void
    {
        try {
            switch ($paymentType) {
                case 'water_bill':
                    $payment = WaterPayment::with('waterBill.waterCustomer')->find($paymentId);
                    if ($payment && $payment->waterBill->waterCustomer && $payment->waterBill->waterCustomer->tel) {
                        $this->smsService->sendPaymentConfirmation(
                            $payment->waterBill->waterCustomer->tel,
                            [
                                'amount' => number_format($payment->amount, 2),
                                'receipt_no' => $payment->id,
                                'service' => 'Water Bill'
                            ]
                        );
                    }
                    break;

                case 'hall_reservation':
                    $payment = HallCustomerPayment::with('hallReservation.hallCustomer')->find($paymentId);
                    if ($payment && $payment->hallReservation->hallCustomer && $payment->hallReservation->hallCustomer->tel) {
                        $this->smsService->sendPaymentConfirmation(
                            $payment->hallReservation->hallCustomer->tel,
                            [
                                'amount' => number_format($payment->amount, 2),
                                'receipt_no' => $payment->id,
                                'service' => 'Hall Reservation'
                            ]
                        );
                    }
                    break;

                case 'tax_payment':
                    $payment = TaxPayment::with('taxProperty.taxPayee')->find($paymentId);
                    if ($payment && $payment->taxProperty->taxPayee && $payment->taxProperty->taxPayee->tel) {
                        $this->smsService->sendPaymentConfirmation(
                            $payment->taxProperty->taxPayee->tel,
                            [
                                'amount' => number_format($payment->payment, 2),
                                'receipt_no' => $payment->id,
                                'service' => 'Tax Payment'
                            ]
                        );
                    }
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('SMS notification failed', [
                'payment_type' => $paymentType,
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get payment receipt
     */
    public function getReceipt(Request $request, string $paymentType, int $paymentId): JsonResponse
    {
        $receiptData = $this->payHereService->generateReceiptData($paymentType, $paymentId);

        if (empty($receiptData)) {
            return response()->json([
                'message' => 'Payment not found'
            ], 404);
        }

        return response()->json([
            'receipt' => $receiptData,
            'receipt_number' => $receiptData['payment_id'],
            'download_url' => url("/api/payments/{$paymentType}/{$paymentId}/receipt/download")
        ]);
    }

    /**
     * Download payment receipt
     */
    public function downloadReceipt(string $paymentType, int $paymentId)
    {
        $receiptData = $this->payHereService->generateReceiptData($paymentType, $paymentId);

        if (empty($receiptData)) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Generate PDF receipt (you can use a PDF library like dompdf or tcpdf)
        $html = $this->generateReceiptHTML($receiptData);
        
        // For now, return the receipt data as JSON
        // In production, you would generate and return a PDF file
        return response()->json([
            'receipt' => $receiptData,
            'html' => $html
        ]);
    }

    /**
     * Generate receipt HTML
     */
    private function generateReceiptHTML(array $receiptData): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='text-align: center; color: #333;'>Payment Receipt</h2>
            <hr>
            <div style='margin: 20px 0;'>
                <p><strong>Receipt Number:</strong> {$receiptData['payment_id']}</p>
                <p><strong>Payment Type:</strong> {$receiptData['type']}</p>
                <p><strong>Customer Name:</strong> {$receiptData['customer_name']}</p>
                <p><strong>Amount:</strong> LKR " . number_format($receiptData['amount'], 2) . "</p>
                <p><strong>Payment Date:</strong> {$receiptData['payment_date']}</p>
                <p><strong>Transaction ID:</strong> {$receiptData['transaction_id']}</p>
            </div>
            <hr>
            <p style='text-align: center; color: #666; font-size: 12px;'>
                Thank you for your payment!
            </p>
        </div>
        ";
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Request $request, string $orderId): JsonResponse
    {
        $status = $this->payHereService->getPaymentStatus($orderId);
        return response()->json($status);
    }
}
