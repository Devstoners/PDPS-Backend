<?php

namespace App\Repositories;

use App\Models\Hall;
use App\Models\Facility;
use App\Models\HallFacility;
use App\Models\HallRate;
use App\Models\HallCustomer;
use App\Models\HallReservation;
use App\Models\HallCustomerPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HallRepository
{
    // ==================== HALL MANAGEMENT ====================
    
    /**
     * Add a new hall
     */
    public function addHall(Request $request)
    {
        $hall = Hall::create([
            'name' => $request->name,
            'location' => $request->location,
            'tel' => $request->tel,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return response([
            'hall' => $hall,
            'message' => 'Hall created successfully'
        ], 201);
    }

    /**
     * Get all halls with facilities
     */
    public function getAllHalls()
    {
        $halls = Hall::with(['facilities', 'hallFacilities.rates'])
            ->where('is_active', true)
            ->get();

        return response([
            'halls' => $halls
        ], 200);
    }

    /**
     * Get hall by ID
     */
    public function getHallById($id)
    {
        $hall = Hall::with(['facilities', 'hallFacilities.rates', 'reservations.customer'])
            ->find($id);

        if (!$hall) {
            return response()->json(['error' => 'Hall not found'], 404);
        }

        return response()->json(['hall' => $hall], 200);
    }

    /**
     * Update hall
     */
    public function updateHall($id, Request $request)
    {
        $hall = Hall::findOrFail($id);

        $hall->update([
            'name' => $request->name,
            'location' => $request->location,
            'tel' => $request->tel,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'is_active' => $request->is_active ?? $hall->is_active,
        ]);

        return response(['message' => 'Hall updated successfully.'], 200);
    }

    /**
     * Delete hall
     */
    public function deleteHall($id)
    {
        $hall = Hall::find($id);

        if (!$hall) {
            return false;
        }

        try {
            DB::beginTransaction();

            // Delete related data
            HallFacility::where('hall_id', $id)->delete();
            HallReservation::where('hall_id', $id)->delete();

            $hall->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    // ==================== FACILITY MANAGEMENT ====================

    /**
     * Add a new facility
     */
    public function addFacility(Request $request)
    {
        $facility = Facility::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return response([
            'facility' => $facility,
            'message' => 'Facility created successfully'
        ], 201);
    }

    /**
     * Get all facilities
     */
    public function getAllFacilities()
    {
        $facilities = Facility::where('is_active', true)->get();

        return response([
            'facilities' => $facilities
        ], 200);
    }

    /**
     * Update facility
     */
    public function updateFacility($id, Request $request)
    {
        $facility = Facility::findOrFail($id);

        $facility->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? $facility->is_active,
        ]);

        return response(['message' => 'Facility updated successfully.'], 200);
    }

    /**
     * Delete facility
     */
    public function deleteFacility($id)
    {
        $facility = Facility::find($id);

        if (!$facility) {
            return false;
        }

        try {
            DB::beginTransaction();

            // Delete related data
            HallFacility::where('facility_id', $id)->delete();

            $facility->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    // ==================== HALL-FACILITY MANAGEMENT ====================

    /**
     * Add facility to hall
     */
    public function addFacilityToHall(Request $request)
    {
        $hallFacility = HallFacility::create([
            'hall_id' => $request->hall_id,
            'facility_id' => $request->facility_id,
        ]);

        return response([
            'hall_facility' => $hallFacility,
            'message' => 'Facility added to hall successfully'
        ], 201);
    }

    /**
     * Remove facility from hall
     */
    public function removeFacilityFromHall($hallId, $facilityId)
    {
        $hallFacility = HallFacility::where('hall_id', $hallId)
            ->where('facility_id', $facilityId)
            ->first();

        if (!$hallFacility) {
            return false;
        }

        try {
            DB::beginTransaction();

            // Delete related rates
            HallRate::where('hall_facility_id', $hallFacility->id)->delete();

            $hallFacility->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    // ==================== RATE MANAGEMENT ====================

    /**
     * Add rate for hall facility
     */
    public function addHallRate(Request $request)
    {
        $rate = HallRate::create([
            'hall_facility_id' => $request->hall_facility_id,
            'rate' => $request->rate,
            'rate_type' => $request->rate_type ?? 'per_hour',
        ]);

        return response([
            'rate' => $rate,
            'message' => 'Rate added successfully'
        ], 201);
    }

    /**
     * Update hall rate
     */
    public function updateHallRate($id, Request $request)
    {
        $rate = HallRate::findOrFail($id);

        $rate->update([
            'rate' => $request->rate,
            'rate_type' => $request->rate_type ?? $rate->rate_type,
        ]);

        return response(['message' => 'Rate updated successfully.'], 200);
    }

    /**
     * Delete hall rate
     */
    public function deleteHallRate($id)
    {
        $rate = HallRate::find($id);

        if (!$rate) {
            return false;
        }

        $rate->delete();
        return true;
    }

    // ==================== CUSTOMER MANAGEMENT ====================

    /**
     * Add hall customer
     */
    public function addHallCustomer(Request $request)
    {
        $customer = HallCustomer::create([
            'title' => $request->title,
            'name' => $request->name,
            'nic' => $request->nic,
            'tel' => $request->tel,
            'address' => $request->address,
            'email' => $request->email,
        ]);

        return response([
            'customer' => $customer,
            'message' => 'Customer registered successfully'
        ], 201);
    }

    /**
     * Get all hall customers
     */
    public function getAllHallCustomers()
    {
        $customers = HallCustomer::with(['reservations.hall'])->get();

        return response([
            'customers' => $customers
        ], 200);
    }

    /**
     * Get hall customer by ID
     */
    public function getHallCustomerById($id)
    {
        $customer = HallCustomer::with(['reservations.hall', 'reservations.payments'])
            ->find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json(['customer' => $customer], 200);
    }

    /**
     * Update hall customer
     */
    public function updateHallCustomer($id, Request $request)
    {
        $customer = HallCustomer::findOrFail($id);

        $customer->update([
            'title' => $request->title,
            'name' => $request->name,
            'nic' => $request->nic,
            'tel' => $request->tel,
            'address' => $request->address,
            'email' => $request->email,
        ]);

        return response(['message' => 'Customer updated successfully.'], 200);
    }

    // ==================== RESERVATION MANAGEMENT ====================

    /**
     * Create hall reservation
     */
    public function createReservation(Request $request)
    {
        // Check for time conflicts
        $conflict = HallReservation::where('hall_id', $request->hall_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_datetime', [$request->start_datetime, $request->end_datetime])
                    ->orWhereBetween('end_datetime', [$request->start_datetime, $request->end_datetime])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_datetime', '<=', $request->start_datetime)
                          ->where('end_datetime', '>=', $request->end_datetime);
                    });
            })
            ->where('status', '!=', 0) // Exclude cancelled reservations
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Hall is already booked for this time period'], 422);
        }

        $reservation = HallReservation::create([
            'hall_id' => $request->hall_id,
            'hall_customer_id' => $request->hall_customer_id,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'status' => $request->status ?? 1, // 1=reserve, 2=pending, 3=booked
            'total_amount' => $request->total_amount,
            'notes' => $request->notes,
        ]);

        return response([
            'reservation' => $reservation,
            'message' => 'Reservation created successfully'
        ], 201);
    }

    /**
     * Get all reservations
     */
    public function getAllReservations()
    {
        $reservations = HallReservation::with(['hall', 'customer', 'payments'])
            ->orderBy('start_datetime', 'desc')
            ->get();

        return response([
            'reservations' => $reservations
        ], 200);
    }

    /**
     * Get reservation by ID
     */
    public function getReservationById($id)
    {
        $reservation = HallReservation::with(['hall', 'customer', 'payments'])
            ->find($id);

        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }

        return response()->json(['reservation' => $reservation], 200);
    }

    /**
     * Update reservation status
     */
    public function updateReservationStatus($id, Request $request)
    {
        $reservation = HallReservation::findOrFail($id);

        $reservation->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $reservation->notes,
        ]);

        return response(['message' => 'Reservation status updated successfully.'], 200);
    }

    /**
     * Cancel reservation
     */
    public function cancelReservation($id)
    {
        $reservation = HallReservation::findOrFail($id);

        $reservation->update(['status' => 0]); // 0 = cancelled

        return response(['message' => 'Reservation cancelled successfully.'], 200);
    }

    // ==================== PAYMENT MANAGEMENT ====================

    /**
     * Add payment for reservation
     */
    public function addPayment(Request $request)
    {
        $payment = HallCustomerPayment::create([
            'hall_reservation_id' => $request->hall_reservation_id,
            'pay_amount' => $request->pay_amount,
            'pay_date' => $request->pay_date,
            'pay_method' => $request->pay_method,
            'transaction_id' => $request->transaction_id,
            'payment_status' => $request->payment_status ?? 0, // 0=pending, 1=completed
            'notes' => $request->notes,
        ]);

        return response([
            'payment' => $payment,
            'message' => 'Payment recorded successfully'
        ], 201);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, Request $request)
    {
        $payment = HallCustomerPayment::findOrFail($id);

        $payment->update([
            'payment_status' => $request->payment_status,
            'transaction_id' => $request->transaction_id ?? $payment->transaction_id,
            'notes' => $request->notes ?? $payment->notes,
        ]);

        return response(['message' => 'Payment status updated successfully.'], 200);
    }

    /**
     * Get payments for reservation
     */
    public function getReservationPayments($reservationId)
    {
        $payments = HallCustomerPayment::where('hall_reservation_id', $reservationId)
            ->orderBy('pay_date', 'desc')
            ->get();

        return response([
            'payments' => $payments
        ], 200);
    }

    // ==================== STATISTICS ====================

    /**
     * Get hall statistics
     */
    public function getHallStatistics()
    {
        $stats = [
            'total_halls' => Hall::count(),
            'active_halls' => Hall::where('is_active', true)->count(),
            'total_facilities' => Facility::count(),
            'total_customers' => HallCustomer::count(),
            'total_reservations' => HallReservation::count(),
            'pending_reservations' => HallReservation::where('status', 2)->count(),
            'confirmed_reservations' => HallReservation::where('status', 3)->count(),
            'total_revenue' => HallReservation::where('status', 3)->sum('total_amount'),
        ];

        return response(['statistics' => $stats], 200);
    }

    /**
     * Get hall availability
     */
    public function getHallAvailability(Request $request)
    {
        $startDate = $request->start_datetime;
        $endDate = $request->end_datetime;

        $availableHalls = Hall::where('is_active', true)
            ->whereDoesntHave('reservations', function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_datetime', [$startDate, $endDate])
                        ->orWhereBetween('end_datetime', [$startDate, $endDate])
                        ->orWhere(function ($subQ) use ($startDate, $endDate) {
                            $subQ->where('start_datetime', '<=', $startDate)
                                ->where('end_datetime', '>=', $endDate);
                        });
                })
                ->where('status', '!=', 0); // Exclude cancelled
            })
            ->with(['facilities'])
            ->get();

        return response([
            'available_halls' => $availableHalls
        ], 200);
    }
}
