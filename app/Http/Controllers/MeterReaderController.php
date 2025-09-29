<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\WaterBillRepository;
use Illuminate\Support\Facades\Validator;

class MeterReaderController extends Controller
{
    private $repository;

    public function __construct(WaterBillRepository $repository)
    {
        $this->repository = $repository;
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

    /**
     * Get all meter readings
     */
    public function getAllMeterReadings()
    {
        $readings = \App\Models\WaterMeterReading::with(['waterCustomer.waterScheme'])
            ->orderBy('reading_month', 'desc')
            ->get();

        return response([
            'readings' => $readings
        ], 200);
    }

    /**
     * Get meter readings by date range
     */
    public function getMeterReadingsByDateRange(Request $request)
    {
        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $readings = \App\Models\WaterMeterReading::with(['waterCustomer.waterScheme'])
            ->whereBetween('reading_month', [$request->start_date, $request->end_date])
            ->orderBy('reading_month', 'desc')
            ->get();

        return response([
            'readings' => $readings
        ], 200);
    }

    /**
     * Get meter reading statistics
     */
    public function getMeterReadingStatistics()
    {
        $stats = [
            'total_readings' => \App\Models\WaterMeterReading::count(),
            'this_month_readings' => \App\Models\WaterMeterReading::whereMonth('reading_month', now()->month)
                ->whereYear('reading_month', now()->year)
                ->count(),
            'total_units_consumed' => \App\Models\WaterMeterReading::sum('units_consumed'),
            'average_units_per_reading' => \App\Models\WaterMeterReading::avg('units_consumed'),
        ];

        return response(['statistics' => $stats], 200);
    }
}
