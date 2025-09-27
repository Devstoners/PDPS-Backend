<?php

namespace App\Http\Controllers;

use App\Models\TaxProperty;
use App\Models\TaxPayee;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaxPropertyController extends Controller
{
    /**
     * Display a listing of tax properties
     */
    public function index(Request $request): JsonResponse
    {
        $query = TaxProperty::query();

        // Search by property name or street
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('property_name', 'like', "%{$search}%")
                  ->orWhere('street', 'like', "%{$search}%");
            });
        }

        // Filter by tax payee
        if ($request->has('tax_payee_id')) {
            $query->where('tax_payee_id', $request->get('tax_payee_id'));
        }

        // Filter by division
        if ($request->has('division_id')) {
            $query->where('division_id', $request->get('division_id'));
        }

        $properties = $query->paginate(15);
        
        return response()->json($properties);
    }

    /**
     * Store a newly created tax property
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'division_id' => 'required|integer|exists:divisions,id',
            'tax_payee_id' => 'required|integer|exists:tax_payees,id',
            'street' => 'required|string|max:255',
            'property_type' => 'required|integer|in:1,2,3,4,5', // 1=House, 2=Land, 3=Building, 4=Commercial, 5=Other
            'property_name' => 'required|string|max:255',
            'property_prohibition' => 'boolean'
        ]);

        $property = TaxProperty::create($request->all());

        return response()->json([
            'message' => 'Tax property created successfully',
            'data' => $property
        ], 201);
    }

    /**
     * Display the specified tax property
     */
    public function show(TaxProperty $taxProperty): JsonResponse
    {
        return response()->json($taxProperty);
    }

    /**
     * Update the specified tax property
     */
    public function update(Request $request, TaxProperty $taxProperty): JsonResponse
    {
        $request->validate([
            'division_id' => 'required|integer|exists:divisions,id',
            'tax_payee_id' => 'required|integer|exists:tax_payees,id',
            'street' => 'required|string|max:255',
            'property_type' => 'required|integer|in:1,2,3,4,5',
            'property_name' => 'required|string|max:255',
            'property_prohibition' => 'boolean'
        ]);

        $taxProperty->update($request->all());

        return response()->json([
            'message' => 'Tax property updated successfully',
            'data' => $taxProperty
        ]);
    }

    /**
     * Remove the specified tax property
     */
    public function destroy(TaxProperty $taxProperty): JsonResponse
    {
        try {
            // Check if property has any assessments
            $assessmentsCount = $taxProperty->taxAssessments()->count();
            
            if ($assessmentsCount > 0) {
                return response()->json([
                    'message' => 'Cannot delete property with existing tax assessments'
                ], 422);
            }

            $taxProperty->delete();

            return response()->json([
                'message' => 'Tax property deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete tax property: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get properties by tax payee
     */
    public function getByPayee(Request $request, $payeeId): JsonResponse
    {
        $properties = TaxProperty::where('tax_payee_id', $payeeId)
            ->with(['division'])
            ->get();

        return response()->json($properties);
    }

    /**
     * Get property types
     */
    public function getPropertyTypes(): JsonResponse
    {
        $types = [
            ['id' => 1, 'name' => 'House'],
            ['id' => 2, 'name' => 'Land'],
            ['id' => 3, 'name' => 'Building'],
            ['id' => 4, 'name' => 'Commercial'],
            ['id' => 5, 'name' => 'Other']
        ];

        return response()->json($types);
    }
}
