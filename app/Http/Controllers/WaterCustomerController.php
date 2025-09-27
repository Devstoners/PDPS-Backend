<?php

namespace App\Http\Controllers;

use App\Models\WaterCustomer;
use App\Services\UnifiedPayHereService;
use App\Services\SmsNotificationService;
use App\Notifications\SmsPaymentConfirmation;
use Illuminate\Http\Request;
use App\Repositories\WaterBillRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class WaterCustomerController extends Controller
{
    private $repository;
    private $payHereService;
    private $smsService;

    public function __construct(WaterBillRepository $repository, UnifiedPayHereService $payHereService, SmsNotificationService $smsService)
    {
        $this->repository = $repository;
        $this->payHereService = $payHereService;
        $this->smsService = $smsService;
    }

    // ==================== CUSTOMER BILL INQUIRY ====================

    /**
     * Check bill details by account number
     */
    public function checkBillDetails(Request $request)
    {
        $rules = [
            'account_no' => 'required|string|exists:water_customers,account_no',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->getBillsByAccount($request->account_no);
    }

    /**
     * Get customer bills by account number
     */
    public function getCustomerBills($accountNo)
    {
        return $this->repository->getBillsByAccount($accountNo);
    }

    /**
     * Get bill details by bill ID
     */
    public function getBillDetails($billId)
    {
        return $this->repository->getWaterBillById($billId);
    }

    // ==================== ONLINE PAYMENT ====================

    /**
     * Make online payment using PayHere
     */
    public function makeOnlinePayment(Request $request)
    {
        $rules = [
            'water_bill_id' => 'required|exists:water_bills,id',
            'amount_paid' => 'required|numeric|min:0',
            'customer_data' => 'required|array',
            'customer_data.first_name' => 'required|string',
            'customer_data.last_name' => 'nullable|string',
            'customer_data.email' => 'required|email',
            'customer_data.phone' => 'required|string',
            'customer_data.address' => 'required|string',
            'customer_data.city' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        // Validate bill exists and is not paid
        $bill = \App\Models\WaterBill::find($request->water_bill_id);
        if (!$bill) {
            return response()->json(['message' => 'Water bill not found'], 404);
        }
        if ($bill->status === 'paid') {
            return response()->json(['message' => 'Bill already paid'], 422);
        }

        // Create pending payment record
        $payment = \App\Models\WaterPayment::create([
            'water_bill_id' => $request->water_bill_id,
            'amount' => $request->amount_paid,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'online',
            'status' => 'pending',
            'customer_name' => $request->customer_data['first_name'] . ' ' . ($request->customer_data['last_name'] ?? ''),
            'customer_email' => $request->customer_data['email'],
            'customer_phone' => $request->customer_data['phone'],
        ]);

        // Generate PayHere checkout data
        $checkoutData = $this->payHereService->generateCheckoutData('water_bill', [
            'payment_id' => $payment->id,
            'amount' => $request->amount_paid,
            'first_name' => $request->customer_data['first_name'],
            'last_name' => $request->customer_data['last_name'] ?? '',
            'email' => $request->customer_data['email'],
            'phone' => $request->customer_data['phone'],
            'address' => $request->customer_data['address'],
            'city' => $request->customer_data['city'] ?? '',
            'account_no' => $bill->waterCustomer->account_no ?? null,
        ]);

        // Send SMS notification for payment initiation
        if ($bill->waterCustomer && $bill->waterCustomer->tel) {
            $this->smsService->sendSms(
                $bill->waterCustomer->tel,
                "Water bill payment initiated for LKR {$request->amount_paid}. Please complete payment at PayHere checkout.",
                'payment_initiated'
            );
        }

        return response()->json([
            'message' => 'Payment initiated',
            'payment_id' => $payment->id,
            'checkout_data' => $checkoutData,
            'checkout_url' => config('payhere.checkout_url')
        ]);
    }

    /**
     * Get payment history for customer
     */
    public function getPaymentHistory(Request $request)
    {
        $rules = [
            'account_no' => 'required|string|exists:water_customers,account_no',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $customer = \App\Models\WaterCustomer::where('account_no', $request->account_no)->first();
        
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $bills = \App\Models\WaterBill::with(['payments'])
            ->where('water_customer_id', $customer->id)
            ->orderBy('billing_month', 'desc')
            ->get();

        return response([
            'customer' => $customer,
            'bills' => $bills
        ], 200);
    }

    /**
     * Get payment receipt
     */
    public function getPaymentReceipt($paymentId)
    {
        $payment = \App\Models\WaterPayment::with(['waterBill.waterCustomer', 'officer'])
            ->find($paymentId);

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        return response()->json(['payment' => $payment], 200);
    }
}