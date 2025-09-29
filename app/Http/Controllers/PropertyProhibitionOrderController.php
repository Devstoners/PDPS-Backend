<?php

namespace App\Http\Controllers;

use App\Models\PropertyProhibitionOrder;
use App\Models\TaxProperty;
use App\Services\TaxNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyProhibitionOrderController extends Controller
{
    /**
     * Display a listing of prohibition orders
     */
    public function index(Request $request): JsonResponse
    {
        $query = PropertyProhibitionOrder::with(['taxProperty.taxPayee', 'officer']);

        // Filter by property
        if ($request->has('tax_property_id')) {
            $query->where('tax_property_id', $request->tax_property_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(15);
        
        return response()->json($orders);
    }

    /**
     * Store a newly created prohibition order
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tax_property_id' => 'required|exists:tax_properties,id',
            'officer_id' => 'required|exists:users,id',
            'order_date' => 'required|date',
        ]);

        // Check if property already has an active prohibition order
        $existingOrder = PropertyProhibitionOrder::where('tax_property_id', $request->tax_property_id)
            ->where('status', 'active')
            ->first();

        if ($existingOrder) {
            return response()->json([
                'message' => 'Property already has an active prohibition order'
            ], 422);
        }

        $order = PropertyProhibitionOrder::create($request->all());

        // Update property prohibition status
        $property = TaxProperty::find($request->tax_property_id);
        $property->update(['property_prohibition' => 1]);

        // Send notification
        $notificationService = new TaxNotificationService();
        $notificationService->sendProhibitionOrderNotification($order);

        return response()->json([
            'message' => 'Prohibition order issued successfully',
            'data' => $order->load(['taxProperty.taxPayee', 'officer'])
        ], 201);
    }

    /**
     * Display the specified prohibition order
     */
    public function show(PropertyProhibitionOrder $propertyProhibitionOrder): JsonResponse
    {
        $propertyProhibitionOrder->load(['taxProperty.taxPayee', 'officer']);

        return response()->json($propertyProhibitionOrder);
    }

    /**
     * Update the specified prohibition order
     */
    public function update(Request $request, PropertyProhibitionOrder $propertyProhibitionOrder): JsonResponse
    {
        $request->validate([
            'order_date' => 'required|date',
            'status' => 'sometimes|in:active,revoked',
        ]);

        $propertyProhibitionOrder->update($request->all());

        return response()->json([
            'message' => 'Prohibition order updated successfully',
            'data' => $propertyProhibitionOrder->load(['taxProperty.taxPayee', 'officer'])
        ]);
    }

    /**
     * Remove the specified prohibition order
     */
    public function destroy(PropertyProhibitionOrder $propertyProhibitionOrder): JsonResponse
    {
        $propertyProhibitionOrder->delete();

        return response()->json([
            'message' => 'Prohibition order deleted successfully'
        ]);
    }

    /**
     * Issue prohibition order for a property
     */
    public function issueForProperty(Request $request, $propertyId): JsonResponse
    {
        $property = TaxProperty::findOrFail($propertyId);

        $request->validate([
            'officer_id' => 'required|exists:users,id',
        ]);

        // Check if property already has an active prohibition order
        $existingOrder = PropertyProhibitionOrder::where('tax_property_id', $propertyId)
            ->where('status', 'active')
            ->first();

        if ($existingOrder) {
            return response()->json([
                'message' => 'Property already has an active prohibition order'
            ], 422);
        }

        $order = PropertyProhibitionOrder::create([
            'tax_property_id' => $propertyId,
            'officer_id' => $request->officer_id,
            'order_date' => now()->toDateString(),
            'status' => 'active'
        ]);

        // Update property prohibition status
        $property->update(['property_prohibition' => 1]);

        return response()->json([
            'message' => 'Prohibition order issued successfully',
            'data' => $order->load(['taxProperty.taxPayee', 'officer'])
        ], 201);
    }

    /**
     * Revoke prohibition order
     */
    public function revoke(PropertyProhibitionOrder $propertyProhibitionOrder): JsonResponse
    {
        if ($propertyProhibitionOrder->status === 'revoked') {
            return response()->json([
                'message' => 'Order is already revoked'
            ], 422);
        }

        $propertyProhibitionOrder->update([
            'status' => 'revoked',
            'revoked_date' => now()->toDateString()
        ]);

        // Update property prohibition status
        $property = $propertyProhibitionOrder->taxProperty;
        $property->update(['property_prohibition' => 0]);

        return response()->json([
            'message' => 'Prohibition order revoked successfully',
            'data' => $propertyProhibitionOrder->load(['taxProperty.taxPayee', 'officer'])
        ]);
    }

    /**
     * Get active prohibition orders
     */
    public function getActive(): JsonResponse
    {
        $orders = PropertyProhibitionOrder::where('status', 'active')
            ->with(['taxProperty.taxPayee', 'officer'])
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json($orders);
    }
}
