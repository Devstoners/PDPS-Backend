<?php

namespace App\Http\Controllers;

use App\Models\WaterBill;
use Illuminate\Http\Request;
use App\Repositories\WaterBillRepository;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class WaterBillController extends Controller
{
    private $repository;

    public function __construct(WaterBillRepository $repository)
    {
        $this->repository = $repository;
    }

    // ==================== WATER SCHEME MANAGEMENT ====================

    /**
     * @OA\Get(
     *     path="/water-schemes",
     *     tags={"Water Bill Management"},
     *     summary="Get all water schemes",
     *     description="Retrieve a list of all water schemes",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Water schemes retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getWaterSchemes()
    {
        return $this->repository->getAllWaterSchemes();
    }

    /**
     * @OA\Post(
     *     path="/water-schemes",
     *     tags={"Water Bill Management"},
     *     summary="Create a new water scheme",
     *     description="Add a new water scheme to the system",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"division_id", "name", "start_date"},
     *             @OA\Property(property="division_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Main Water Scheme"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Water scheme created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Water scheme created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function addWaterScheme(Request $request)
    {
        $rules = [
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addWaterScheme($request);
    }

    /**
     * Display the specified water scheme
     */
    public function getWaterScheme($id)
    {
        return $this->repository->getWaterSchemeById($id);
    }

    /**
     * Update the specified water scheme
     */
    public function updateWaterScheme(Request $request, $id)
    {
        $rules = [
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updateWaterScheme($id, $request);
    }

    /**
     * Remove the specified water scheme
     */
    public function deleteWaterScheme($id)
    {
        $result = $this->repository->deleteWaterScheme($id);

        if ($result) {
            return response()->json(['message' => 'Water scheme deleted successfully.']);
        }
        return response()->json(['message' => 'Water scheme not found.'], 404);
    }

    // ==================== WATER CUSTOMER MANAGEMENT ====================

    /**
     * Display a listing of water customers
     */
    public function getWaterCustomers()
    {
        return $this->repository->getAllWaterCustomers();
    }

    /**
     * Store a newly created water customer
     */
    public function addWaterCustomer(Request $request)
    {
        $rules = [
            'account_no' => 'required|string|unique:water_customers,account_no',
            'title' => 'required|integer|in:1,2,3,4',
            'name' => 'required|string|max:255',
            'nic' => 'required|string|unique:water_customers,nic',
            'tel' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'required|email|unique:water_customers,email',
            'dateJoin' => 'required|date',
            'water_schemes_id' => 'required|exists:water_schemes,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addWaterCustomer($request);
    }

    /**
     * Display the specified water customer
     */
    public function getWaterCustomer($id)
    {
        return $this->repository->getWaterCustomerById($id);
    }

    /**
     * Get water customer by account number
     */
    public function getWaterCustomerByAccount($accountNo)
    {
        return $this->repository->getWaterCustomerByAccount($accountNo);
    }

    /**
     * Update the specified water customer
     */
    public function updateWaterCustomer(Request $request, $id)
    {
        $rules = [
            'account_no' => 'required|string|unique:water_customers,account_no,' . $id,
            'title' => 'required|integer|in:1,2,3,4',
            'name' => 'required|string|max:255',
            'nic' => 'required|string|unique:water_customers,nic,' . $id,
            'tel' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'required|email|unique:water_customers,email,' . $id,
            'dateJoin' => 'required|date',
            'water_schemes_id' => 'required|exists:water_schemes,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updateWaterCustomer($id, $request);
    }

    /**
     * Remove the specified water customer
     */
    public function deleteWaterCustomer($id)
    {
        $result = $this->repository->deleteWaterCustomer($id);

        if ($result) {
            return response()->json(['message' => 'Water customer deleted successfully.']);
        }
        return response()->json(['message' => 'Water customer not found.'], 404);
    }

    // ==================== METER READER MANAGEMENT ====================

    /**
     * Assign meter reader to water scheme
     */
    public function addMeterReader(Request $request)
    {
        $rules = [
            'officer_id' => 'required|exists:officers,id',
            'water_schemes_id' => 'required|exists:water_schemes,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addMeterReader($request);
    }

    /**
     * Get meter readers for water scheme
     */
    public function getMeterReadersByScheme($schemeId)
    {
        return $this->repository->getMeterReadersByScheme($schemeId);
    }

    // ==================== METER READING MANAGEMENT ====================

    /**
     * Add meter reading
     */
    public function addMeterReading(Request $request)
    {
        $rules = [
            'water_customer_id' => 'required|exists:water_customers,id',
            'reading_month' => 'required|date',
            'current_reading' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addMeterReading($request);
    }

    /**
     * Update meter reading
     */
    public function updateMeterReading(Request $request, $id)
    {
        $rules = [
            'reading_month' => 'required|date',
            'current_reading' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updateMeterReading($id, $request);
    }

    /**
     * Get meter readings for customer
     */
    public function getCustomerMeterReadings($customerId)
    {
        return $this->repository->getCustomerMeterReadings($customerId);
    }

    // ==================== WATER BILL MANAGEMENT ====================

    /**
     * Display a listing of water bills
     */
    public function index()
    {
        return $this->repository->getAllWaterBills();
    }

    /**
     * Store a newly created water bill
     */
    public function store(Request $request)
    {
        $rules = [
            'water_customer_id' => 'required|exists:water_customers,id',
            'meter_reader_id' => 'required|exists:water_meter_readers,id',
            'billing_month' => 'required|date',
            'due_date' => 'required|date|after:billing_month',
            'amount_due' => 'required|numeric|min:0',
            'status' => 'nullable|integer|in:1,2,3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->createWaterBill($request);
    }

    /**
     * Display the specified water bill
     */
    public function show($id)
    {
        return $this->repository->getWaterBillById($id);
    }

    /**
     * Get bills by customer
     */
    public function getCustomerBills($customerId)
    {
        return $this->repository->getCustomerBills($customerId);
    }

    /**
     * Get bills by account number
     */
    public function getBillsByAccount($accountNo)
    {
        return $this->repository->getBillsByAccount($accountNo);
    }

    /**
     * Update bill status
     */
    public function updateBillStatus(Request $request, $id)
    {
        $rules = [
            'status' => 'required|integer|in:1,2,3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updateBillStatus($id, $request);
    }

    // ==================== PAYMENT MANAGEMENT ====================

    /**
     * Add water payment
     */
    public function addWaterPayment(Request $request)
    {
        $rules = [
            'water_bill_id' => 'required|exists:water_bills,id',
            'amount_paid' => 'required|numeric|min:0',
            'pay_date' => 'required|date',
            'pay_method' => 'required|in:cash,online,bank_transfer',
            'transaction_id' => 'nullable|string',
            'receipt_no' => 'nullable|string',
            'officer_id' => 'nullable|exists:officers,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addWaterPayment($request);
    }

    /**
     * Get payments for bill
     */
    public function getBillPayments($billId)
    {
        return $this->repository->getBillPayments($billId);
    }

    // ==================== STATISTICS ====================

    /**
     * Get water bill statistics
     */
    public function getStatistics()
    {
        return $this->repository->getWaterBillStatistics();
    }

    /**
     * Get payment summary
     */
    public function getPaymentSummary(Request $request)
    {
        return $this->repository->getPaymentSummary($request);
    }
}