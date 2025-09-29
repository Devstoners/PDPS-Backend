<?php

namespace App\Services;

use App\Models\StripePayment;
use App\Models\TaxPayee;
use Illuminate\Support\Facades\Log;

class StripePaymentEmailService
{
    protected $brevoService;

    public function __construct()
    {
        $this->brevoService = new BrevoEmailService();
    }

    /**
     * Send payment confirmation email for successful Stripe payment
     */
    public function sendPaymentConfirmation($stripePayment)
    {
        try {
            // Get tax payee information
            $taxPayee = TaxPayee::find($stripePayment->tax_payee_id ?? 1);
            
            if (!$taxPayee) {
                Log::error('Tax payee not found for Stripe payment', [
                    'stripe_payment_id' => $stripePayment->id,
                    'tax_payee_id' => $stripePayment->tax_payee_id
                ]);
                return false;
            }

            $subject = "Payment Confirmation - Stripe Payment #{$stripePayment->payment_id}";
            
            $htmlContent = $this->generatePaymentConfirmationHtml($stripePayment, $taxPayee);
            $textContent = $this->generatePaymentConfirmationText($stripePayment, $taxPayee);

            $result = $this->brevoService->sendEmail(
                [
                    'email' => $taxPayee->email,
                    'name' => $taxPayee->name
                ],
                $subject,
                $htmlContent,
                $textContent
            );

            if ($result) {
                Log::info('Stripe payment confirmation email sent successfully', [
                    'stripe_payment_id' => $stripePayment->id,
                    'payment_id' => $stripePayment->payment_id,
                    'email' => $taxPayee->email
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to send Stripe payment confirmation email', [
                'error' => $e->getMessage(),
                'stripe_payment_id' => $stripePayment->id ?? 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Generate HTML content for Stripe payment confirmation
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
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Payment Confirmation</title>
        </head>
        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;'>
            <div style='background-color: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                
                <!-- Header -->
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h1 style='color: #28a745; margin: 0; font-size: 28px;'>✅ Payment Successful</h1>
                    <p style='color: #6c757d; margin: 10px 0 0 0; font-size: 16px;'>Your tax payment has been processed successfully</p>
                </div>
                
                <!-- Greeting -->
                <p style='font-size: 16px; color: #333; margin-bottom: 20px;'>Dear {$taxPayee->name},</p>
                
                <p style='font-size: 16px; color: #333; margin-bottom: 25px;'>
                    Thank you for your payment! Your tax payment has been successfully processed through Stripe. 
                    Here are the details of your transaction:
                </p>
                
                <!-- Payment Details Card -->
                <div style='background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 25px; margin: 25px 0;'>
                    <h3 style='color: #2c3e50; margin-top: 0; margin-bottom: 20px; font-size: 18px;'>📋 Payment Details</h3>
                    
                    <div style='display: grid; gap: 12px;'>
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e9ecef;'>
                            <span style='font-weight: 600; color: #495057;'>Payment ID:</span>
                            <span style='color: #6c757d; font-family: monospace;'>{$stripePayment->payment_id}</span>
                        </div>
                        
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e9ecef;'>
                            <span style='font-weight: 600; color: #495057;'>Stripe Session:</span>
                            <span style='color: #6c757d; font-family: monospace; font-size: 12px;'>{$stripePayment->stripe_session_id}</span>
                        </div>
                        
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e9ecef;'>
                            <span style='font-weight: 600; color: #495057;'>Amount Paid:</span>
                            <span style='color: #28a745; font-weight: 700; font-size: 18px;'>{$currency} {$amount}</span>
                        </div>
                        
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e9ecef;'>
                            <span style='font-weight: 600; color: #495057;'>Payment Method:</span>
                            <span style='color: #6c757d;'>💳 Stripe (Online)</span>
                        </div>
                        
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e9ecef;'>
                            <span style='font-weight: 600; color: #495057;'>Payment Date:</span>
                            <span style='color: #6c757d;'>{$paymentDate}</span>
                        </div>
                        
                        <div style='display: flex; justify-content: space-between; align-items: center; padding: 8px 0;'>
                            <span style='font-weight: 600; color: #495057;'>Status:</span>
                            <span style='color: #28a745; font-weight: 600;'>✅ Confirmed</span>
                        </div>
                    </div>
                </div>
                
                <!-- Taxpayer Information -->
                <div style='background-color: #e3f2fd; border: 1px solid #bbdefb; border-radius: 8px; padding: 20px; margin: 25px 0;'>
                    <h3 style='color: #1976d2; margin-top: 0; margin-bottom: 15px; font-size: 16px;'>👤 Taxpayer Information</h3>
                    <p style='margin: 5px 0; color: #333;'><strong>Name:</strong> {$taxPayee->name}</p>
                    <p style='margin: 5px 0; color: #333;'><strong>NIC:</strong> {$taxPayee->nic}</p>
                    <p style='margin: 5px 0; color: #333;'><strong>Email:</strong> {$taxPayee->email}</p>
                </div>
                
                <!-- Next Steps -->
                <div style='background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 25px 0;'>
                    <h3 style='color: #856404; margin-top: 0; margin-bottom: 15px; font-size: 16px;'>📝 What's Next?</h3>
                    <ul style='margin: 0; padding-left: 20px; color: #856404;'>
                        <li>Keep this email as your payment receipt</li>
                        <li>Your payment will be reflected in your tax account within 1-2 business days</li>
                        <li>If you have any questions, contact our support team</li>
                    </ul>
                </div>
                
                <!-- Footer -->
                <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;'>
                    <p style='color: #6c757d; margin: 0; font-size: 14px;'>
                        Thank you for using our online payment system!<br>
                        <strong>Tax Department</strong>
                    </p>
                    <p style='color: #adb5bd; margin: 10px 0 0 0; font-size: 12px;'>
                        This is an automated message. Please do not reply to this email.
                    </p>
                </div>
                
            </div>
        </body>
        </html>";
    }

    /**
     * Generate text content for Stripe payment confirmation
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
Stripe Session: {$stripePayment->stripe_session_id}
Amount Paid: {$currency} {$amount}
Payment Method: Stripe (Online)
Payment Date: {$paymentDate}
Status: Confirmed

TAXPAYER INFORMATION:
====================
Name: {$taxPayee->name}
NIC: {$taxPayee->nic}
Email: {$taxPayee->email}

WHAT'S NEXT:
============
- Keep this email as your payment receipt
- Your payment will be reflected in your tax account within 1-2 business days
- If you have any questions, contact our support team

Thank you for using our online payment system!

Tax Department

---
This is an automated message. Please do not reply to this email.";
    }
}
