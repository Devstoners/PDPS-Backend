<?php

namespace App\Repositories;

use App\Models\WaterScheme;
use App\Models\WaterCustomer;
use App\Models\WaterMeterReader;
use App\Models\WaterMeterReading;
use App\Models\WaterBill;
use App\Models\WaterPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WaterBillRepository
{
    // ==================== WATER SCHEME MANAGEMENT ====================
    
    /**
     * Add a new water scheme
     */
    public function addWaterScheme(Request $request)
    {
        $scheme = WaterScheme::create([
            'division_id' => $request->division_id,
            'name' => $request->name,
            'start_date' => $request->start_date,
        ]);

        return response([
            'scheme' => $scheme,
            'message' => 'Water scheme created successfully'
        ], 201);
    }

    /**
     * Get all water schemes
     */
    public function getAllWaterSchemes()
    {
        $schemes = WaterScheme::with(['division', 'waterCustomers', 'waterMeterReaders'])
            ->get();

        return response([
            'schemes' => $schemes
        ], 200);
    }

    /**
     * Get water scheme by ID
     */
    public function getWaterSchemeById($id)
    {
        $scheme = WaterScheme::with(['division', 'waterCustomers', 'waterMeterReaders'])
            ->find($id);

        if (!$scheme) {
            return response()->json(['error' => 'Water scheme not found'], 404);
        }

        return response()->json(['scheme' => $scheme], 200);
    }

    /**
     * Update water scheme
     */
    public function updateWaterScheme($id, Request $request)
    {
        $scheme = WaterScheme::findOrFail($id);

        $scheme->update([
            'division_id' => $request->division_id,
            'name' => $request->name,
            'start_date' => $request->start_date,
        ]);

        return response(['message' => 'Water scheme updated successfully.'], 200);
    }

    /**
     * Delete water scheme
     */
    public function deleteWaterScheme($id)
    {
        $scheme = WaterScheme::find($id);

        if (!$scheme) {
            return false;
        }

        try {
            DB::beginTransaction();

            // Delete related data
            WaterCustomer::where('water_schemes_id', $id)->delete();
            WaterMeterReader::where('water_schemes_id', $id)->delete();

            $scheme->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    // ==================== WATER CUSTOMER MANAGEMENT ====================

    /**
     * Add a new water customer
     */
    public function addWaterCustomer(Request $request)
    {
        $customer = WaterCustomer::create([
            'account_no' => $request->account_no,
            'title' => $request->title,
            'name' => $request->name,
            'nic' => $request->nic,
            'tel' => $request->tel,
            'address' => $request->address,
            'email' => $request->email,
            'con_date' => $request->dateJoin, // Map dateJoin to con_date
            'water_schemes_id' => $request->water_schemes_id,
        ]);

        return response([
            'customer' => $customer,
            'message' => 'Water customer created successfully'
        ], 201);
    }

    /**
     * Get all water customers
     */
    public function getAllWaterCustomers()
    {
        $customers = WaterCustomer::with(['waterScheme', 'waterBills', 'meterReadings'])
            ->get();

        return response([
            'customers' => $customers
        ], 200);
    }

    /**
     * Get water customer by ID
     */
    public function getWaterCustomerById($id)
    {
        $customer = WaterCustomer::with(['waterScheme', 'waterBills.payments', 'meterReadings'])
            ->find($id);

        if (!$customer) {
            return response()->json(['error' => 'Water customer not found'], 404);
        }

        return response()->json(['customer' => $customer], 200);
    }

    /**
     * Get water customer by account number
     */
    public function getWaterCustomerByAccount($accountNo)
    {
        $customer = WaterCustomer::with(['waterScheme', 'waterBills.payments', 'meterReadings'])
            ->where('account_no', $accountNo)
            ->first();

        if (!$customer) {
            return response()->json(['error' => 'Water customer not found'], 404);
        }

        return response()->json(['customer' => $customer], 200);
    }

    /**
     * Generate next account number for a water scheme
     */
    public function generateAccountNumber($waterSchemeId)
    {
        // Check if water scheme exists
        $waterScheme = \App\Models\WaterScheme::find($waterSchemeId);
        if (!$waterScheme) {
            return response()->json(['error' => 'Water scheme not found'], 404);
        }

        // Use raw SQL to find the highest account number for this water scheme
        $lastNumber = \DB::selectOne("
            SELECT MAX(CAST(SUBSTRING(account_no, -5) AS UNSIGNED)) as last_number
            FROM water_customers 
            WHERE water_schemes_id = ? 
            AND account_no LIKE ?
        ", [$waterSchemeId, 'PS/PATHA/WATER/' . $waterSchemeId . '/%']);

        $nextNumber = 1;
        if ($lastNumber && $lastNumber->last_number !== null) {
            $nextNumber = $lastNumber->last_number + 1;
        }

        // Format: PS/PATHA/WATER/{water_scheme_id}/{00001}
        $accountNumber = 'PS/PATHA/WATER/' . $waterSchemeId . '/' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return response()->json([
            'account_number' => $accountNumber,
            'water_scheme_id' => $waterSchemeId,
            'water_scheme_name' => $waterScheme->name,
            'next_number' => $nextNumber,
            'last_number' => $lastNumber ? $lastNumber->last_number : 0
        ], 200);
    }

    /**
     * Update water customer
     */
    public function updateWaterCustomer($id, Request $request)
    {
        $customer = WaterCustomer::findOrFail($id);

        $customer->update([
            'account_no' => $request->account_no,
            'title' => $request->title,
            'name' => $request->name,
            'nic' => $request->nic,
            'tel' => $request->tel,
            'address' => $request->address,
            'email' => $request->email,
            'con_date' => $request->dateJoin, // Map dateJoin to con_date
            'water_schemes_id' => $request->water_schemes_id,
        ]);

        return response(['message' => 'Water customer updated successfully.'], 200);
    }

    /**
     * Delete water customer
     */
    public function deleteWaterCustomer($id)
    {
        $customer = WaterCustomer::find($id);

        if (!$customer) {
            return false;
        }

        try {
            DB::beginTransaction();

            // Delete related data
            WaterBill::where('water_customer_id', $id)->delete();
            WaterMeterReading::where('water_customer_id', $id)->delete();

            $customer->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    // ==================== METER READER MANAGEMENT ====================

    /**
     * Add meter reader to water scheme
     */
    public function addMeterReader(Request $request)
    {
        $meterReader = WaterMeterReader::create([
            'user_id' => $request->officer_id, // Map officer_id to user_id
            'water_schemes_id' => $request->water_schemes_id,
        ]);

        return response([
            'meter_reader' => $meterReader,
            'message' => 'Meter reader assigned successfully'
        ], 201);
    }

    /**
     * Get all meter readers
     */
    public function getMeterReaders()
    {
        $meterReaders = WaterMeterReader::with(['officer', 'waterScheme'])->get();

        return response([
            'meter_readers' => $meterReaders
        ], 200);
    }

    /**
     * Update meter reader
     */
    public function updateMeterReader($id, Request $request)
    {
        $meterReader = WaterMeterReader::find($id);

        if (!$meterReader) {
            return response(['message' => 'Meter reader not found'], 404);
        }

        $meterReader->update([
            'user_id' => $request->officer_id, // Map officer_id to user_id
            'water_schemes_id' => $request->water_schemes_id,
        ]);

        return response([
            'meter_reader' => $meterReader->fresh(['officer', 'waterScheme']),
            'message' => 'Meter reader updated successfully'
        ], 200);
    }

    /**
     * Delete meter reader
     */
    public function deleteMeterReader($id)
    {
        $meterReader = WaterMeterReader::find($id);

        if (!$meterReader) {
            return response(['message' => 'Meter reader not found'], 404);
        }

        $meterReader->delete();

        return response([
            'message' => 'Meter reader deleted successfully'
        ], 200);
    }

    /**
     * Get meter readers for a water scheme
     */
    public function getMeterReadersByScheme($schemeId)
    {
        $meterReaders = WaterMeterReader::with(['officer', 'waterScheme'])
            ->where('water_schemes_id', $schemeId)
            ->get();

        return response([
            'meter_readers' => $meterReaders
        ], 200);
    }

    // ==================== METER READING MANAGEMENT ====================

    /**
     * Add meter reading (draft mode - no bill creation)
     */
    public function addMeterReading(Request $request)
    {
        // Calculate units consumed
        $previousReading = WaterMeterReading::where('water_customer_id', $request->water_customer_id)
            ->orderBy('reading_month', 'desc')
            ->first();

        $previousValue = $previousReading ? $previousReading->current_reading : 0;
        $unitsConsumed = $request->current_reading - $previousValue;

        // Create meter reading as draft (submitted = 0)
        $reading = WaterMeterReading::create([
            'water_customer_id' => $request->water_customer_id,
            'reading_month' => $request->reading_month,
            'current_reading' => $request->current_reading,
            'previous_reading' => $previousValue,
            'units_consumed' => $unitsConsumed,
            'submitted' => 0, // Draft mode
        ]);

        return response([
            'reading' => $reading,
            'units_consumed' => $unitsConsumed,
            'message' => 'Meter reading saved as draft'
        ], 201);
    }

    /**
     * Update meter reading
     */
    public function updateMeterReading($id, Request $request)
    {
        $reading = WaterMeterReading::findOrFail($id);

        // Recalculate units consumed
        $previousReading = WaterMeterReading::where('water_customer_id', $reading->water_customer_id)
            ->where('id', '!=', $id)
            ->orderBy('reading_month', 'desc')
            ->first();

        $previousValue = $previousReading ? $previousReading->current_reading : 0;
        $unitsConsumed = $request->current_reading - $previousValue;

        $reading->update([
            'reading_month' => $request->reading_month,
            'current_reading' => $request->current_reading,
            'previous_reading' => $previousValue,
            'units_consumed' => $unitsConsumed,
        ]);

        return response(['message' => 'Meter reading updated successfully.'], 200);
    }

    /**
     * Get all meter readings
     */
    public function getAllMeterReadings()
    {
        $readings = WaterMeterReading::with('waterCustomer')
            ->orderBy('reading_month', 'desc')
            ->get();

        return response([
            'readings' => $readings
        ], 200);
    }

    /**
     * Get unsubmitted meter readings (drafts)
     */
    public function getUnsubmittedMeterReadings()
    {
        $readings = WaterMeterReading::with('waterCustomer')
            ->where('submitted', 0)
            ->orderBy('reading_month', 'desc')
            ->get();

        return response([
            'readings' => $readings
        ], 200);
    }

    /**
     * Submit meter reading (mark as submitted)
     */
    public function submitMeterReading($id)
    {
        $reading = WaterMeterReading::find($id);

        if (!$reading) {
            return response(['message' => 'Meter reading not found'], 404);
        }

        $reading->update(['submitted' => 1]);

        return response([
            'reading' => $reading->fresh(['waterCustomer']),
            'message' => 'Meter reading submitted successfully'
        ], 200);
    }

    /**
     * Submit all unsubmitted meter readings and create water bills
     */
    public function submitAllMeterReadings()
    {
        $unsubmittedReadings = WaterMeterReading::with('waterCustomer')
            ->where('submitted', false)
            ->get();

        if ($unsubmittedReadings->isEmpty()) {
            return response([
                'message' => 'No unsubmitted meter readings found'
            ], 200);
        }

        $createdBills = [];
        $errors = [];

        foreach ($unsubmittedReadings as $reading) {
            try {
                // Mark as submitted
                $reading->update(['submitted' => true]);

                // Get water customer to find water scheme
                $waterCustomer = $reading->waterCustomer;
                if (!$waterCustomer) {
                    $errors[] = "Water customer not found for reading ID: {$reading->id}";
                    continue;
                }

                // Get water bill rates for the customer's water scheme
                $billRate = \App\Models\WaterBillRate::where('water_schemes_id', $waterCustomer->water_schemes_id)->first();
                if (!$billRate) {
                    $errors[] = "No bill rates found for water scheme ID: {$waterCustomer->water_schemes_id}";
                    continue;
                }

                // Calculate bill amount using tiered pricing
                $billAmount = $this->calculateBillAmount($reading->units_consumed, $billRate);

                // Use logged-in user's ID as meter reader ID
                $meterReaderId = auth()->id();

                // Create water bill
                $waterBill = \App\Models\WaterBill::create([
                    'water_customer_id' => $reading->water_customer_id,
                    'meter_reader_id' => $meterReaderId,
                    'billing_month' => $reading->reading_month,
                    'due_date' => date('Y-m-d', strtotime($reading->reading_month . ' +30 days')),
                    'amount_due' => $billAmount,
                    'status' => 1, // 1 = unpaid
                ]);

                $createdBills[] = [
                    'reading_id' => $reading->id,
                    'bill_id' => $waterBill->id,
                    'amount_due' => $billAmount,
                    'units_consumed' => $reading->units_consumed
                ];

            } catch (\Exception $e) {
                $errors[] = "Error processing reading ID {$reading->id}: " . $e->getMessage();
            }
        }

        return response([
            'message' => 'Meter readings submitted and water bills created',
            'submitted_count' => $unsubmittedReadings->count(),
            'created_bills' => $createdBills,
            'errors' => $errors
        ], 200);
    }

    /**
     * Get meter readings for customer
     */
    public function getCustomerMeterReadings($customerId)
    {
        $readings = WaterMeterReading::where('water_customer_id', $customerId)
            ->orderBy('reading_month', 'desc')
            ->get();

        return response([
            'readings' => $readings
        ], 200);
    }

    /**
     * Get previous reading for customer
     */
    public function getPreviousReading($customerId)
    {
        $previousReading = WaterMeterReading::where('water_customer_id', $customerId)
            ->orderBy('reading_month', 'desc')
            ->first();

        return response([
            'previous_reading' => $previousReading ? $previousReading->current_reading : 0,
            'reading' => $previousReading
        ], 200);
    }

    /**
     * Delete meter reading
     */
    public function deleteMeterReading($id)
    {
        $reading = WaterMeterReading::find($id);

        if (!$reading) {
            return response(['message' => 'Meter reading not found'], 404);
        }

        $reading->delete();

        return response([
            'message' => 'Meter reading deleted successfully'
        ], 200);
    }

    /**
     * Calculate bill amount using tiered pricing
     */
    private function calculateBillAmount($unitsConsumed, $billRate)
    {
        $totalAmount = 0;
        
        // Tier 1: First 5 units (0-5)
        if ($unitsConsumed > 0) {
            $tier1Units = min($unitsConsumed, 5);
            $totalAmount += $tier1Units * $billRate->units_0_1;
        }
        
        // Tier 2: Next 5 units (6-10)
        if ($unitsConsumed > 5) {
            $tier2Units = min($unitsConsumed - 5, 5);
            $totalAmount += $tier2Units * $billRate->units_1_5;
        }
        
        // Tier 3: Above 10 units (11+)
        if ($unitsConsumed > 10) {
            $tier3Units = $unitsConsumed - 10;
            $totalAmount += $tier3Units * $billRate->units_above_5;
        }
        
        // Add service charge (fixed amount)
        $totalAmount += $billRate->service;
        
        return $totalAmount;
    }

    // ==================== WATER BILL MANAGEMENT ====================

    /**
     * Create water bill
     */
    public function createWaterBill(Request $request)
    {
        // Set default values for missing fields
        $meterReaderId = $request->meter_reader_id ?? $request->user()->id; // Use logged-in user's ID
        $dueDate = $request->due_date ?? date('Y-m-d', strtotime($request->billing_month . ' +30 days')); // 30 days from billing month

        $bill = WaterBill::create([
            'water_customer_id' => $request->water_customer_id,
            'meter_reader_id' => $meterReaderId,
            'billing_month' => $request->billing_month,
            'due_date' => $dueDate,
            'amount_due' => $request->amount_due,
            'status' => $request->status ?? 1, // 1=unpaid, 2=paid, 3=overdue
        ]);

        return response([
            'bill' => $bill,
            'message' => 'Water bill created successfully'
        ], 201);
    }

    /**
     * Get all water bills
     */
    public function getAllWaterBills()
    {
        $bills = WaterBill::with(['waterCustomer', 'meterReader', 'payments'])
            ->orderBy('billing_month', 'desc')
            ->get();

        return response([
            'bills' => $bills
        ], 200);
    }

    /**
     * Get water bill by ID
     */
    public function getWaterBillById($id)
    {
        $bill = WaterBill::with(['waterCustomer', 'meterReader', 'payments'])
            ->find($id);

        if (!$bill) {
            return response()->json(['error' => 'Water bill not found'], 404);
        }

        return response()->json(['bill' => $bill], 200);
    }

    /**
     * Get bills by customer
     */
    public function getCustomerBills($customerId)
    {
        $bills = WaterBill::with(['meterReader', 'payments'])
            ->where('water_customer_id', $customerId)
            ->orderBy('billing_month', 'desc')
            ->get();

        return response([
            'bills' => $bills
        ], 200);
    }

    /**
     * Get bills by account number
     */
    public function getBillsByAccount($accountNo)
    {
        $customer = WaterCustomer::where('account_no', $accountNo)->first();
        
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $bills = WaterBill::with(['meterReader', 'payments'])
            ->where('water_customer_id', $customer->id)
            ->orderBy('billing_month', 'desc')
            ->get();

        return response([
            'customer' => $customer,
            'bills' => $bills
        ], 200);
    }

    /**
     * Update bill status
     */
    public function updateBillStatus($id, Request $request)
    {
        $bill = WaterBill::findOrFail($id);

        $bill->update([
            'status' => $request->status,
        ]);

        return response(['message' => 'Bill status updated successfully.'], 200);
    }

    // ==================== PAYMENT MANAGEMENT ====================

    /**
     * Add water payment
     */
    public function addWaterPayment(Request $request)
    {
        $payment = WaterPayment::create([
            'water_bill_id' => $request->water_bill_id,
            'amount_paid' => $request->amount_paid,
            'pay_date' => $request->pay_date,
            'pay_method' => $request->pay_method,
            'transaction_id' => $request->transaction_id,
            'receipt_no' => $request->receipt_no ?? $this->generateReceiptNumber(),
            'officer_id' => $request->officer_id,
        ]);

        // Update bill status if fully paid
        $bill = WaterBill::find($request->water_bill_id);
        $totalPaid = WaterPayment::where('water_bill_id', $request->water_bill_id)->sum('amount_paid');
        
        if ($totalPaid >= $bill->amount_due) {
            $bill->update(['status' => 2]); // 2=paid
        }

        return response([
            'payment' => $payment,
            'message' => 'Payment recorded successfully'
        ], 201);
    }

    /**
     * Get payments for bill
     */
    public function getBillPayments($billId)
    {
        $payments = WaterPayment::with(['officer'])
            ->where('water_bill_id', $billId)
            ->orderBy('pay_date', 'desc')
            ->get();

        return response([
            'payments' => $payments
        ], 200);
    }

    /**
     * Generate receipt number
     */
    private function generateReceiptNumber()
    {
        return 'WR' . date('Ymd') . Str::random(6);
    }

    // ==================== STATISTICS ====================

    /**
     * Get water bill statistics
     */
    public function getWaterBillStatistics()
    {
        $stats = [
            'total_schemes' => WaterScheme::count(),
            'total_customers' => WaterCustomer::count(),
            'total_bills' => WaterBill::count(),
            'unpaid_bills' => WaterBill::where('status', 1)->count(),
            'paid_bills' => WaterBill::where('status', 2)->count(),
            'overdue_bills' => WaterBill::where('status', 3)->count(),
            'total_revenue' => WaterPayment::sum('amount_paid'),
            'pending_revenue' => WaterBill::where('status', 1)->sum('amount_due'),
        ];

        return response(['statistics' => $stats], 200);
    }

    /**
     * Get payment summary
     */
    public function getPaymentSummary(Request $request)
    {
        $query = WaterPayment::with(['waterBill.waterCustomer', 'officer']);

        if ($request->has('start_date')) {
            $query->where('pay_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('pay_date', '<=', $request->end_date);
        }

        if ($request->has('pay_method')) {
            $query->where('pay_method', $request->pay_method);
        }

        $payments = $query->orderBy('pay_date', 'desc')->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount_paid'),
            'cash_payments' => $payments->where('pay_method', 'cash')->sum('amount_paid'),
            'online_payments' => $payments->where('pay_method', 'online')->sum('amount_paid'),
            'bank_transfer_payments' => $payments->where('pay_method', 'bank_transfer')->sum('amount_paid'),
            'payments' => $payments,
        ];

        return response(['summary' => $summary], 200);
    }
}

