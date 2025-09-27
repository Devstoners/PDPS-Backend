<?php

namespace App\Http\Controllers;

use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class SmsNotificationController extends Controller
{
    protected $smsService;

    public function __construct(SmsNotificationService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * @OA\Post(
     *     path="/sms/test",
     *     tags={"SMS Notifications"},
     *     summary="Send test SMS",
     *     description="Send a test SMS message",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="+94771234567"),
     *             @OA\Property(property="message", type="string", example="Test SMS from PDPS system", maxLength=160)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SMS sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="SMS sent successfully"),
     *             @OA\Property(property="message_sid", type="string", example="SM1234567890abcdef")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function sendTestSms(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'nullable|string|max:160'
        ]);

        $phone = $request->phone;
        $message = $request->message ?? 'Test SMS from PDPS system';

        $result = $this->smsService->testSms($phone, $message);

        return response()->json($result);
    }

    /**
     * @OA\Post(
     *     path="/sms/payment-confirmation",
     *     tags={"SMS Notifications"},
     *     summary="Send payment confirmation SMS",
     *     description="Send SMS confirmation for payment",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "amount", "receipt_no", "service"},
     *             @OA\Property(property="phone", type="string", example="+94771234567"),
     *             @OA\Property(property="amount", type="number", format="float", example=1500.00),
     *             @OA\Property(property="receipt_no", type="string", example="RCP123456"),
     *             @OA\Property(property="service", type="string", example="Water Bill Payment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment confirmation SMS sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment confirmation SMS sent successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function sendPaymentConfirmation(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric',
            'receipt_no' => 'required|string',
            'service' => 'required|string'
        ]);

        $result = $this->smsService->sendPaymentConfirmation($request->phone, [
            'amount' => number_format($request->amount, 2),
            'receipt_no' => $request->receipt_no,
            'service' => $request->service
        ]);

        return response()->json($result);
    }

    /**
     * Send service reminder SMS
     */
    public function sendServiceReminder(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'service' => 'required|string',
            'due_date' => 'required|date',
            'amount' => 'required|numeric'
        ]);

        $result = $this->smsService->sendServiceReminder($request->phone, [
            'service' => $request->service,
            'due_date' => $request->due_date,
            'amount' => number_format($request->amount, 2)
        ]);

        return response()->json($result);
    }

    /**
     * Send overdue notice SMS
     */
    public function sendOverdueNotice(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'service' => 'required|string',
            'amount' => 'required|numeric'
        ]);

        $result = $this->smsService->sendOverdueNotice($request->phone, [
            'service' => $request->service,
            'amount' => number_format($request->amount, 2)
        ]);

        return response()->json($result);
    }

    /**
     * Send hall reservation confirmation SMS
     */
    public function sendReservationConfirmation(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|string',
            'hall_name' => 'required|string'
        ]);

        $result = $this->smsService->sendReservationConfirmation($request->phone, [
            'date' => $request->date,
            'time' => $request->time,
            'hall_name' => $request->hall_name
        ]);

        return response()->json($result);
    }

    /**
     * Send tax assessment SMS
     */
    public function sendTaxAssessment(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'property_name' => 'required|string'
        ]);

        $result = $this->smsService->sendTaxAssessment($request->phone, [
            'amount' => number_format($request->amount, 2),
            'due_date' => $request->due_date,
            'property_name' => $request->property_name
        ]);

        return response()->json($result);
    }

    /**
     * Send water bill SMS
     */
    public function sendWaterBill(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'account_no' => 'required|string'
        ]);

        $result = $this->smsService->sendWaterBill($request->phone, [
            'amount' => number_format($request->amount, 2),
            'due_date' => $request->due_date,
            'account_no' => $request->account_no
        ]);

        return response()->json($result);
    }

    /**
     * Get SMS delivery status
     */
    public function getDeliveryStatus(Request $request, string $messageSid): JsonResponse
    {
        $result = $this->smsService->getDeliveryStatus($messageSid);
        return response()->json($result);
    }

    /**
     * Get Twilio account information
     */
    public function getAccountInfo(): JsonResponse
    {
        $result = $this->smsService->getAccountInfo();
        return response()->json($result);
    }

    /**
     * Send custom SMS
     */
    public function sendCustomSms(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:160'
        ]);

        $result = $this->smsService->sendSms(
            $request->phone,
            $request->message,
            'custom'
        );

        return response()->json($result);
    }
}
