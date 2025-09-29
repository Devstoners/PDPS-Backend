<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GmailEmailService
{
    /**
     * Send email using Gmail SMTP
     */
    public function sendEmail($to, $subject, $htmlContent, $textContent = null)
    {
        try {
            // Use Mail::html instead of Mail::send for better control
            Mail::html($htmlContent, function ($message) use ($to, $subject) {
                $message->to($to['email'], $to['name'] ?? $to['email'])
                        ->subject($subject)
                        ->from('pathadumbarapradeshiyasabawa@gmail.com', 'Pathadumbara Pradeshiya Sabawa');
            });

            Log::info('Gmail email sent successfully', [
                'to' => $to['email'],
                'subject' => $subject
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Gmail email sending failed', [
                'error' => $e->getMessage(),
                'to' => $to['email'] ?? 'unknown',
                'subject' => $subject
            ]);
            return false;
        }
    }

    /**
     * Send Stripe payment confirmation email
     */
    public function sendStripePaymentConfirmation($stripePayment, $taxPayee)
    {
        $subject = "Payment Confirmation - Stripe Payment #{$stripePayment->payment_id}";
        
        $htmlContent = $this->generatePaymentConfirmationHtml($stripePayment, $taxPayee);
        $textContent = $this->generatePaymentConfirmationText($stripePayment, $taxPayee);

        return $this->sendEmail(
            [
                'email' => $taxPayee->email,
                'name' => $taxPayee->name
            ],
            $subject,
            $htmlContent,
            $textContent
        );
    }

    /**
     * Generate HTML content for payment confirmation
     */
    private function generatePaymentConfirmationHtml($stripePayment, $taxPayee)
    {
        $amount = number_format($stripePayment->amount, 2);
        $currency = strtoupper($stripePayment->currency);
        $paymentDate = $stripePayment->created_at->format('d M Y, h:i A');
        
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Payment Confirmation</title>
        </head>
        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;'>
            <div style='background-color: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h1 style='color: #28a745; margin: 0; font-size: 28px;'>✅ Payment Successful</h1>
                    <p style='color: #6c757d; margin: 10px 0 0 0; font-size: 16px;'>Your tax payment has been processed successfully</p>
                </div>
                
                <p style='font-size: 16px; color: #333; margin-bottom: 20px;'>Dear {$taxPayee->name},</p>
                
                <p style='font-size: 16px; color: #333; margin-bottom: 25px;'>
                    Thank you for your payment! Your tax payment has been successfully processed through Stripe.
                </p>
                
                <div style='background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 25px; margin: 25px 0;'>
                    <h3 style='color: #2c3e50; margin-top: 0; margin-bottom: 20px; font-size: 18px;'>📋 Payment Details</h3>
                    
                    <div style='display: grid; gap: 12px;'>
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e9ecef;'>
                            <span style='font-weight: 600; color: #495057;'>Payment ID:</span>
                            <span style='color: #6c757d; font-family: monospace;'>{$stripePayment->payment_id}</span>
                        </div>
                        
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e9ecef;'>
                            <span style='font-weight: 600; color: #495057;'>Amount Paid:</span>
                            <span style='color: #28a745; font-weight: 700; font-size: 18px;'>{$currency} {$amount}</span>
                        </div>
                        
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e9ecef;'>
                            <span style='font-weight: 600; color: #495057;'>Payment Method:</span>
                            <span style='color: #6c757d;'>💳 Stripe (Online)</span>
                        </div>
                        
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0;'>
                            <span style='font-weight: 600; color: #495057;'>Payment Date:</span>
                            <span style='color: #6c757d;'>{$paymentDate}</span>
                        </div>
                    </div>
                </div>
                
                <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;'>
                    <p style='color: #6c757d; margin: 0; font-size: 14px;'>
                        Thank you for using our online payment system!<br>
                        <strong>Tax Department</strong>
                    </p>
                </div>
                
            </div>
        </body>
        </html>";
    }

    /**
     * Generate text content for payment confirmation
     */
    private function generatePaymentConfirmationText($stripePayment, $taxPayee)
    {
        $amount = number_format($stripePayment->amount, 2);
        $currency = strtoupper($stripePayment->currency);
        $paymentDate = $stripePayment->created_at->format('d M Y, h:i A');
        
        return "
PAYMENT CONFIRMATION - TAX PAYMENT SUCCESSFUL
=============================================

Dear {$taxPayee->name},

Thank you for your payment! Your tax payment has been successfully processed through Stripe.

PAYMENT DETAILS:
================
Payment ID: {$stripePayment->payment_id}
Amount Paid: {$currency} {$amount}
Payment Method: Stripe (Online)
Payment Date: {$paymentDate}
Status: Confirmed

Thank you for using our online payment system!

Tax Department";
    }
}
