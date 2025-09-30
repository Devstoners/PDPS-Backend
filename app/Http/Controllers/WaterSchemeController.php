<?php

namespace App\Http\Controllers;

use App\Models\WaterScheme;
use App\Http\Requests\StoreWaterSchemeRequest;
use App\Http\Requests\UpdateWaterSchemeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WaterSchemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $waterSchemes = WaterScheme::all();
            
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'Create water scheme form data',
            'data' => [
                'scheme_types' => ['Gravity', 'Pump', 'Borehole', 'Well'],
                'status_options' => ['Active', 'Inactive', 'Under Maintenance']
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterSchemeRequest $request)
    {
        try {
            // Check if user has permission to create water schemes
            if (!$request->user()->can('water-schemes.create')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to create water schemes'
                ], 403);
            }

            // Get validated data
            $data = $request->validated();
            
            $waterScheme = WaterScheme::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Water scheme created successfully',
                'data' => $waterScheme
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create water scheme',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WaterScheme $waterScheme)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Water scheme retrieved successfully',
                'data' => $waterScheme
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve water scheme',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WaterScheme $waterScheme)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Water scheme edit form data',
                'data' => $waterScheme
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve water scheme for editing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWaterSchemeRequest $request, WaterScheme $waterScheme)
    {
        try {
            // Check if user has permission to update water schemes
            if (!$request->user()->can('water-schemes.update')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update water schemes'
                ], 403);
            }

            $waterScheme->update($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Water scheme updated successfully',
                'data' => $waterScheme
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update water scheme',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, WaterScheme $waterScheme)
    {
        try {
            // Check if user has permission to delete water schemes
            if (!$request->user()->can('water-schemes.delete')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete water schemes'
                ], 403);
            }

            $waterScheme->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Water scheme deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete water scheme',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
