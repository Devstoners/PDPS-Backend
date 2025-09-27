<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Http\Request;
use App\Repositories\HallRepository;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class HallController extends Controller
{
    private $repository;

    public function __construct(HallRepository $repository)
    {
        $this->repository = $repository;
    }

    // ==================== HALL MANAGEMENT ====================

    /**
     * @OA\Get(
     *     path="/hall",
     *     tags={"Hall Management"},
     *     summary="Get all halls",
     *     description="Retrieve a list of all halls",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Halls retrieved successfully",
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
    public function index()
    {
        return $this->repository->getAllHalls();
    }

    /**
     * @OA\Post(
     *     path="/hall",
     *     tags={"Hall Management"},
     *     summary="Create a new hall",
     *     description="Add a new hall to the system",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "location", "tel", "capacity"},
     *             @OA\Property(property="name", type="string", example="Main Hall"),
     *             @OA\Property(property="location", type="string", example="City Center"),
     *             @OA\Property(property="tel", type="string", example="0112345678"),
     *             @OA\Property(property="capacity", type="integer", example=100),
     *             @OA\Property(property="description", type="string", example="Large hall for events")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Hall created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Hall created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addHall($request);
    }

    public function show($id)
    {
        return $this->repository->getHallById($id);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updateHall($id, $request);
    }

    public function destroy($id)
    {
        $result = $this->repository->deleteHall($id);

        if ($result) {
            return response()->json(['message' => 'Hall deleted successfully.']);
        }
        return response()->json(['message' => 'Hall not found.'], 404);
    }

    // ==================== FACILITY MANAGEMENT ====================

    public function getFacilities()
    {
        return $this->repository->getAllFacilities();
    }

    public function addFacility(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addFacility($request);
    }

    public function updateFacility(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updateFacility($id, $request);
    }

    public function deleteFacility($id)
    {
        $result = $this->repository->deleteFacility($id);

        if ($result) {
            return response()->json(['message' => 'Facility deleted successfully.']);
        }
        return response()->json(['message' => 'Facility not found.'], 404);
    }

    // ==================== HALL-FACILITY MANAGEMENT ====================

    public function addFacilityToHall(Request $request)
    {
        $rules = [
            'hall_id' => 'required|exists:halls,id',
            'facility_id' => 'required|exists:facilities,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addFacilityToHall($request);
    }

    public function removeFacilityFromHall($hallId, $facilityId)
    {
        $result = $this->repository->removeFacilityFromHall($hallId, $facilityId);

        if ($result) {
            return response()->json(['message' => 'Facility removed from hall successfully.']);
        }
        return response()->json(['message' => 'Facility not found in hall.'], 404);
    }

    // ==================== RATE MANAGEMENT ====================

    public function addHallRate(Request $request)
    {
        $rules = [
            'hall_facility_id' => 'required|exists:hall_facilities,id',
            'rate' => 'required|numeric|min:0',
            'rate_type' => 'nullable|string|in:per_hour,per_day,per_event',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addHallRate($request);
    }

    public function updateHallRate(Request $request, $id)
    {
        $rules = [
            'rate' => 'required|numeric|min:0',
            'rate_type' => 'nullable|string|in:per_hour,per_day,per_event',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updateHallRate($id, $request);
    }

    public function deleteHallRate($id)
    {
        $result = $this->repository->deleteHallRate($id);

        if ($result) {
            return response()->json(['message' => 'Rate deleted successfully.']);
        }
        return response()->json(['message' => 'Rate not found.'], 404);
    }

    // ==================== RESERVATION MANAGEMENT ====================

    public function getReservations()
    {
        return $this->repository->getAllReservations();
    }

    public function getReservation($id)
    {
        return $this->repository->getReservationById($id);
    }

    public function updateReservationStatus(Request $request, $id)
    {
        $rules = [
            'status' => 'required|integer|in:1,2,3',
            'notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updateReservationStatus($id, $request);
    }

    public function cancelReservation($id)
    {
        return $this->repository->cancelReservation($id);
    }

    // ==================== PAYMENT MANAGEMENT ====================

    public function addPayment(Request $request)
    {
        $rules = [
            'hall_reservation_id' => 'required|exists:hall_reservations,id',
            'pay_amount' => 'required|numeric|min:0',
            'pay_date' => 'required|date',
            'pay_method' => 'required|integer|in:1,2,3',
            'transaction_id' => 'nullable|string',
            'payment_status' => 'nullable|integer|in:0,1',
            'notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->addPayment($request);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $rules = [
            'payment_status' => 'required|integer|in:0,1',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        return $this->repository->updatePaymentStatus($id, $request);
    }

    public function getReservationPayments($reservationId)
    {
        return $this->repository->getReservationPayments($reservationId);
    }

    // ==================== STATISTICS ====================

    public function getStatistics()
    {
        return $this->repository->getHallStatistics();
    }

    public function getAvailability(Request $request)
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
}
