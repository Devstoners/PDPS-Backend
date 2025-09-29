<?php

namespace App\Http\Controllers;

use App\Models\TaxPenaltyNotice;
use App\Models\TaxAssessment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaxPenaltyNoticeController extends Controller
{
    /**
     * Display a listing of penalty notices
     */
    public function index(Request $request): JsonResponse
    {
        $query = TaxPenaltyNotice::with(['assessment.taxProperty.taxPayee']);

        // Filter by assessment
        if ($request->has('assessment_id')) {
            $query->where('assessment_id', $request->assessment_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $notices = $query->orderBy('issue_date', 'desc')->paginate(15);
        
        return response()->json($notices);
    }

    /**
     * Store a newly created penalty notice
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'assessment_id' => 'required|exists:tax_assessments,id',
            'penalty_amount' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
        ]);

        $notice = TaxPenaltyNotice::create($request->all());

        return response()->json([
            'message' => 'Penalty notice issued successfully',
            'data' => $notice->load(['assessment.taxProperty.taxPayee'])
        ], 201);
    }

    /**
     * Display the specified penalty notice
     */
    public function show(TaxPenaltyNotice $taxPenaltyNotice): JsonResponse
    {
        $taxPenaltyNotice->load(['assessment.taxProperty.taxPayee']);

        return response()->json($taxPenaltyNotice);
    }

    /**
     * Update the specified penalty notice
     */
    public function update(Request $request, TaxPenaltyNotice $taxPenaltyNotice): JsonResponse
    {
        $request->validate([
            'penalty_amount' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'status' => 'sometimes|in:issued,resolved',
        ]);

        $taxPenaltyNotice->update($request->all());

        return response()->json([
            'message' => 'Penalty notice updated successfully',
            'data' => $taxPenaltyNotice->load(['assessment.taxProperty.taxPayee'])
        ]);
    }

    /**
     * Remove the specified penalty notice
     */
    public function destroy(TaxPenaltyNotice $taxPenaltyNotice): JsonResponse
    {
        $taxPenaltyNotice->delete();

        return response()->json([
            'message' => 'Penalty notice deleted successfully'
        ]);
    }

    /**
     * Issue penalty notice for an assessment
     */
    public function issueForAssessment(Request $request, $assessmentId): JsonResponse
    {
        $assessment = TaxAssessment::findOrFail($assessmentId);

        $request->validate([
            'penalty_amount' => 'required|numeric|min:0',
        ]);

        $notice = TaxPenaltyNotice::create([
            'assessment_id' => $assessmentId,
            'penalty_amount' => $request->penalty_amount,
            'issue_date' => now()->toDateString(),
            'status' => 'issued'
        ]);

        return response()->json([
            'message' => 'Penalty notice issued successfully',
            'data' => $notice->load(['assessment.taxProperty.taxPayee'])
        ], 201);
    }

    /**
     * Resolve penalty notice
     */
    public function resolve(TaxPenaltyNotice $taxPenaltyNotice): JsonResponse
    {
        $taxPenaltyNotice->update(['status' => 'resolved']);

        return response()->json([
            'message' => 'Penalty notice resolved',
            'data' => $taxPenaltyNotice->load(['assessment.taxProperty.taxPayee'])
        ]);
    }
}
