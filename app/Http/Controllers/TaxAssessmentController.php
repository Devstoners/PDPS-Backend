<?php

namespace App\Http\Controllers;

use App\Models\TaxAssessment;
use App\Models\TaxProperty;
use App\Services\TaxNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaxAssessmentController extends Controller
{
    /**
     * Display a listing of tax assessments
     */
    public function index(Request $request): JsonResponse
    {
        $query = TaxAssessment::with(['taxProperty.taxPayee', 'officer']);

        // Filter by property
        if ($request->has('tax_property_id')) {
            $query->where('tax_property_id', $request->tax_property_id);
        }

        // Filter by year
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $assessments = $query->orderBy('due_date', 'desc')->paginate(15);
        
        return response()->json($assessments);
    }

    /**
     * Store a newly created tax assessment
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tax_property_id' => 'required|exists:tax_properties,id',
            'year' => 'required|integer|min:1990|max:2030',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
            'officer_id' => 'required|exists:users,id',
        ]);

        $assessment = TaxAssessment::create($request->all());

        // Send notification (with error handling)
        try {
            $notificationService = new TaxNotificationService();
            $notificationService->sendAssessmentCreatedNotification($assessment);
        } catch (\Exception $e) {
            \Log::error('Tax notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Tax assessment created successfully',
            'data' => $assessment
        ], 201);
    }

    /**
     * Display the specified tax assessment
     */
    public function show(TaxAssessment $taxAssessment): JsonResponse
    {
        $taxAssessment->load([
            'taxProperty.taxPayee',
            'officer',
            'taxPayments',
            'penaltyNotices'
        ]);

        return response()->json($taxAssessment);
    }

    /**
     * Update the specified tax assessment
     */
    public function update(Request $request, TaxAssessment $taxAssessment): JsonResponse
    {
        $request->validate([
            'year' => 'required|integer|min:1990|max:2030',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'sometimes|in:unpaid,paid,overdue',
        ]);

        $taxAssessment->update($request->all());

        return response()->json([
            'message' => 'Tax assessment updated successfully',
            'data' => $taxAssessment->load(['taxProperty.taxPayee', 'officer'])
        ]);
    }

    /**
     * Remove the specified tax assessment
     */
    public function destroy(TaxAssessment $taxAssessment): JsonResponse
    {
        // Check if assessment has payments
        if ($taxAssessment->taxPayments()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete assessment with existing payments'
            ], 422);
        }

        $taxAssessment->delete();

        return response()->json([
            'message' => 'Tax assessment deleted successfully'
        ]);
    }

    /**
     * Get assessments by payee
     */
    public function getByPayee(Request $request, $payeeId): JsonResponse
    {
        $assessments = TaxAssessment::whereHas('taxProperty', function($query) use ($payeeId) {
            $query->where('tax_payee_id', $payeeId);
        })
        ->with(['taxProperty.taxPayee', 'officer', 'taxPayments'])
        ->orderBy('due_date', 'desc')
        ->get();

        return response()->json($assessments);
    }

    /**
     * Mark assessment as overdue
     */
    public function markOverdue(TaxAssessment $taxAssessment): JsonResponse
    {
        if ($taxAssessment->status === 'paid') {
            return response()->json([
                'message' => 'Cannot mark paid assessment as overdue'
            ], 422);
        }

        $taxAssessment->update(['status' => 'overdue']);

        return response()->json([
            'message' => 'Assessment marked as overdue',
            'data' => $taxAssessment
        ]);
    }
}
