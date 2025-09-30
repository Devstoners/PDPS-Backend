<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\WaterBillRate;
use App\Models\WaterScheme;
use Illuminate\Support\Facades\Validator;

class WaterBillRateController extends Controller
{
    /**
     * Display a listing of water bill rates
     */
    public function index(): JsonResponse
    {
        try {
            $billRates = WaterBillRate::with(['waterScheme.division'])->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Water bill rates retrieved successfully',
                'data' => $billRates
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve water bill rates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created water bill rate
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $rules = [
                'water_schemes_id' => 'required|integer|exists:water_schemes,id',
                'units_0_1' => 'required|numeric|min:0',
                'units_1_5' => 'required|numeric|min:0',
                'units_above_5' => 'required|numeric|min:0',
                'service' => 'required|numeric|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $billRate = WaterBillRate::create($request->all());
            $billRate->load('waterScheme');

            return response()->json([
                'success' => true,
                'message' => 'Water bill rate created successfully',
                'data' => $billRate
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create water bill rate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified water bill rate
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $billRate = WaterBillRate::find($id);

            if (!$billRate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Water bill rate not found'
                ], 404);
            }

            $rules = [
                'water_schemes_id' => 'required|integer|exists:water_schemes,id',
                'units_0_1' => 'required|numeric|min:0',
                'units_1_5' => 'required|numeric|min:0',
                'units_above_5' => 'required|numeric|min:0',
                'service' => 'required|numeric|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $billRate->update($request->all());
            $billRate->load('waterScheme');

            return response()->json([
                'success' => true,
                'message' => 'Water bill rate updated successfully',
                'data' => $billRate
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update water bill rate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified water bill rate
     */
    public function destroy($id): JsonResponse
    {
        try {
            $billRate = WaterBillRate::find($id);

            if (!$billRate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Water bill rate not found'
                ], 404);
            }

            $billRate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Water bill rate deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete water bill rate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get water schemes for dropdown
     */
    public function getWaterSchemes(): JsonResponse
    {
        try {
            $waterSchemes = WaterScheme::select('id', 'name')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Water schemes retrieved successfully',
                'data' => $waterSchemes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve water schemes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get division details by ID
     */
    public function getDivision($divisionId): JsonResponse
    {
        try {
            $division = \App\Models\Division::find($divisionId);
            
            if (!$division) {
                return response()->json([
                    'success' => false,
                    'message' => 'Division not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Division details retrieved successfully',
                'data' => $division
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve division details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}