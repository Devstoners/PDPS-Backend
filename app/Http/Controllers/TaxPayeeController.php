<?php

namespace App\Http\Controllers;

use App\Models\TaxPayee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class TaxPayeeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tax-payees",
     *     tags={"Tax Management"},
     *     summary="Get all tax payees",
     *     description="Retrieve a paginated list of tax payees with optional search",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by NIC or name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tax payees retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
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
     * @OA\Post(
     *     path="/tax-payees",
     *     tags={"Tax Management"},
     *     summary="Create a new tax payee",
     *     description="Add a new tax payee to the system",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "name", "nic", "tel", "address", "email"},
     *             @OA\Property(property="title", type="integer", example=1, description="Title: 1=Mr, 2=Mrs, 3=Miss, 4=Dr"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="nic", type="string", example="123456789V"),
     *             @OA\Property(property="tel", type="string", example="0771234567"),
     *             @OA\Property(property="address", type="string", example="123 Main Street, Colombo"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tax payee created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tax payee created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
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
