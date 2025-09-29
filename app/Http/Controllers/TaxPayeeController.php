<?php

namespace App\Http\Controllers;

use App\Models\TaxPayee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaxPayeeController extends Controller
{
    /**
     * Display a listing of tax payees
     */
    public function index(Request $request): JsonResponse
    {
        $query = TaxPayee::query();

        // Search by NIC or name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nic', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $payees = $query->paginate(15);
        
        return response()->json($payees);
    }

    /**
     * Store a newly created tax payee
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|integer|in:1,2,3,4',
            'name' => 'required|string|max:255',
            'nic' => 'required|string|max:12|unique:tax_payees,nic',
            'tel' => 'required|string|max:15',
            'address' => 'required|string|max:250',
            'email' => 'required|email|max:255',
        ]);

        $payee = TaxPayee::create($request->all());

        return response()->json([
            'message' => 'Tax payee created successfully',
            'data' => $payee
        ], 201);
    }

    /**
     * Display the specified tax payee
     */
    public function show(TaxPayee $taxPayee): JsonResponse
    {
        return response()->json($taxPayee);
    }

    /**
     * Update the specified tax payee
     */
    public function update(Request $request, TaxPayee $taxPayee): JsonResponse
    {
        $request->validate([
            'title' => 'required|integer|in:1,2,3,4',
            'name' => 'required|string|max:255',
            'nic' => 'required|string|max:12|unique:tax_payees,nic,' . $taxPayee->id,
            'tel' => 'required|string|max:15',
            'address' => 'required|string|max:250',
            'email' => 'required|email|max:255',
        ]);

        $taxPayee->update($request->all());

        return response()->json([
            'message' => 'Tax payee updated successfully',
            'data' => $taxPayee
        ]);
    }

    /**
     * Remove the specified tax payee
     */
    public function destroy(TaxPayee $taxPayee): JsonResponse
    {
        try {
            // Check if payee has any properties (with error handling)
            $propertiesCount = 0;
            try {
                $propertiesCount = $taxPayee->taxProperties()->count();
            } catch (\Exception $e) {
                // If relationship doesn't exist, assume no properties
                $propertiesCount = 0;
            }

            if ($propertiesCount > 0) {
                return response()->json([
                    'message' => 'Cannot delete payee with existing properties'
                ], 422);
            }

            $taxPayee->delete();

            return response()->json([
                'message' => 'Tax payee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete tax payee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search tax payee by NIC
     */
    public function searchByNic(Request $request): JsonResponse
    {
        $request->validate([
            'nic' => 'required|string|max:12'
        ]);

        $payee = TaxPayee::where('nic', $request->nic)->first();

        if (!$payee) {
            return response()->json([
                'message' => 'Tax payee not found'
            ], 404);
        }

        return response()->json($payee);
    }
}
