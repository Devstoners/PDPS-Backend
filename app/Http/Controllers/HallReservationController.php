<?php

namespace App\Http\Controllers;

use App\Models\HallReservation;
use App\Services\UnifiedPayHereService;
use Illuminate\Http\Request;
use App\Repositories\HallRepository;
use Illuminate\Support\Facades\Validator;

class HallReservationController extends Controller
{
    private $repository;
    private $payHereService;

    public function __construct(HallRepository $repository, UnifiedPayHereService $payHereService)
    {
        $this->repository = $repository;
        $this->payHereService = $payHereService;
    }

    // ==================== CUSTOMER MANAGEMENT ====================

    /**
     * Register hall customer
     */
    public function registerCustomer(Request $request)
    {
        $rules = [
            'title' => 'required|integer|in:1,2,3,4',
            'name' => 'required|string|max:255',
            'nic' => 'required|string|unique:hall_customers,nic',
            'tel' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'required|email|unique:hall_customers,email',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addHallCustomer($request);
    }

    /**
     * Get customer by ID
     */
    public function getCustomer($id)
    {
        return $this->repository->getHallCustomerById($id);
    }

    /**
     * Update customer
     */
    public function updateCustomer(Request $request, $id)
    {
        $rules = [
            'title' => 'required|integer|in:1,2,3,4',
            'name' => 'required|string|max:255',
            'nic' => 'required|string|unique:hall_customers,nic,' . $id,
            'tel' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'required|email|unique:hall_customers,email,' . $id,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updateHallCustomer($id, $request);
    }

    // ==================== RESERVATION MANAGEMENT ====================

    /**
     * Get available halls
     */
    public function getAvailableHalls(Request $request)
    {
        $rules = [
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->getHallAvailability($request);
    }

    /**
     * Get all halls
     */
    public function getHalls()
    {
        return $this->repository->getAllHalls();
    }

    /**
     * Get hall details
     */
    public function getHall($id)
    {
        return $this->repository->getHallById($id);
    }

    /**
     * Create reservation
     */
    public function createReservation(Request $request)
    {
        $rules = [
            'hall_id' => 'required|exists:halls,id',
            'hall_customer_id' => 'required|exists:hall_customers,id',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->createReservation($request);
    }

    /**
     * Get customer reservations
     */
    public function getCustomerReservations($customerId)
    {
        $reservations = \App\Models\HallReservation::with(['hall', 'payments'])
            ->where('hall_customer_id', $customerId)
            ->orderBy('start_datetime', 'desc')
            ->get();

        return response([
            'reservations' => $reservations
        ], 200);
    }

    /**
     * Get reservation details
     */
    public function getReservation($id)
    {
        return $this->repository->getReservationById($id);
    }

    // ==================== PAYMENT MANAGEMENT ====================

    /**
     * Add payment (Cash/Manual)
     */
    public function addPayment(Request $request)
    {
        $rules = [
            'hall_reservation_id' => 'required|exists:hall_reservations,id',
            'pay_amount' => 'required|numeric|min:0',
            'pay_date' => 'required|date',
            'pay_method' => 'required|integer|in:1,2,3',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addPayment($request);
    }

    /**
     * Process online payment for hall reservation
     */
    public function processOnlinePayment(Request $request)
    {
        $rules = [
            'hall_reservation_id' => 'required|exists:hall_reservations,id',
            'amount' => 'required|numeric|min:0',
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

        // Validate reservation exists and is not paid
        $reservation = HallReservation::with('hall')->find($request->hall_reservation_id);
        if (!$reservation) {
            return response()->json(['message' => 'Hall reservation not found'], 404);
        }
        if ($reservation->status === 'paid') {
            return response()->json(['message' => 'Reservation already paid'], 422);
        }

        // Create pending payment record
        $payment = \App\Models\HallCustomerPayment::create([
            'hall_reservation_id' => $request->hall_reservation_id,
            'amount' => $request->amount,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'online',
            'status' => 'pending',
            'customer_name' => $request->customer_data['first_name'] . ' ' . ($request->customer_data['last_name'] ?? ''),
            'customer_email' => $request->customer_data['email'],
            'customer_phone' => $request->customer_data['phone'],
        ]);

        // Generate PayHere checkout data
        $checkoutData = $this->payHereService->generateCheckoutData('hall_reservation', [
            'payment_id' => $payment->id,
            'amount' => $request->amount,
            'first_name' => $request->customer_data['first_name'],
            'last_name' => $request->customer_data['last_name'] ?? '',
            'email' => $request->customer_data['email'],
            'phone' => $request->customer_data['phone'],
            'address' => $request->customer_data['address'],
            'city' => $request->customer_data['city'] ?? '',
            'hall_name' => $reservation->hall->name ?? null,
        ]);

        return response()->json([
            'message' => 'Payment initiated',
            'payment_id' => $payment->id,
            'checkout_data' => $checkoutData,
            'checkout_url' => config('payhere.checkout_url')
        ]);
    }

    /**
     * Get reservation payments
     */
    public function getReservationPayments($reservationId)
    {
        return $this->repository->getReservationPayments($reservationId);
    }
}
