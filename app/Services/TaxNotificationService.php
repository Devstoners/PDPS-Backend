<?php

namespace App\Services;

use App\Models\TaxAssessment;
use App\Models\TaxPayment;
use App\Models\PropertyProhibitionOrder;
use App\Notifications\TaxAssessmentCreated;
use App\Notifications\TaxPaymentConfirmed;
use App\Notifications\TaxOverdueNotice;
use App\Notifications\ProhibitionOrderIssued;
use Illuminate\Support\Facades\Notification;

class TaxNotificationService
{
    /**
     * Send notification when tax assessment is created
     */
    public function sendAssessmentCreatedNotification(TaxAssessment $assessment)
    {
        $payee = $assessment->taxProperty->taxPayee;
        
        // Send email notification
        Notification::route('mail', $payee->email)
            ->notify(new TaxAssessmentCreated($assessment));
    }

    /**
     * Send notification when tax payment is confirmed
     */
    public function sendPaymentConfirmedNotification(TaxPayment $payment)
    {
        $payee = $payment->taxProperty->taxPayee;
        
        // Send email notification
        Notification::route('mail', $payee->email)
            ->notify(new TaxPaymentConfirmed($payment));
    }

    /**
     * Send notification for overdue tax assessments
     */
    public function sendOverdueNotifications()
    {
        $overdueAssessments = TaxAssessment::where('status', 'unpaid')
            ->where('due_date', '<', now()->toDateString())
            ->with('taxProperty.taxPayee')
            ->get();

        foreach ($overdueAssessments as $assessment) {
            // Update status to overdue
            $assessment->update(['status' => 'overdue']);
            
            // Send notification
            $this->sendOverdueNotification($assessment);
        }
    }

    /**
     * Send overdue notification for specific assessment
     */
    public function sendOverdueNotification(TaxAssessment $assessment)
    {
        $payee = $assessment->taxProperty->taxPayee;
        
        // Send email notification
        Notification::route('mail', $payee->email)
            ->notify(new TaxOverdueNotice($assessment));
    }

    /**
     * Send notification when prohibition order is issued
     */
    public function sendProhibitionOrderNotification(PropertyProhibitionOrder $order)
    {
        $payee = $order->taxProperty->taxPayee;
        
        // Send email notification
        Notification::route('mail', $payee->email)
            ->notify(new ProhibitionOrderIssued($order));
    }

    /**
     * Send SMS notification (if SMS service is configured)
     */
    public function sendSmsNotification(string $phone, string $message)
    {
        // This would integrate with SMS service like Twilio
        // For now, we'll just log the message
        \Log::info('SMS Notification', [
            'phone' => $phone,
            'message' => $message
        ]);
    }

    /**
     * Send combined email and SMS for critical notifications
     */
    public function sendCriticalNotification(string $email, string $phone, string $subject, string $message)
    {
        // Send email
        \Mail::raw($message, function ($mail) use ($email, $subject) {
            $mail->to($email)->subject($subject);
        });

        // Send SMS
        $this->sendSmsNotification($phone, $message);
    }
}
