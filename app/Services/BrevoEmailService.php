<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BrevoEmailService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.brevo.com/v3/sendEmail';

    public function __construct()
    {
        $this->apiKey = config('mail.brevo.api_key', env('BREVO_API_KEY'));
    }

    /**
     * Send email using Brevo API
     */
    public function sendEmail($to, $subject, $htmlContent, $textContent = null, $from = null)
    {
        try {
            $fromEmail = $from['email'] ?? config('mail.from.address', 'lak.bope@gmail.com');
            $fromName = $from['name'] ?? config('mail.from.name', 'Asanka Lakshitha');

            $data = [
                'sender' => [
                    'name' => $fromName,
                    'email' => $fromEmail
                ],
                'to' => [
                    [
                        'email' => $to['email'],
                        'name' => $to['name'] ?? $to['email']
                    ]
                ],
                'subject' => $subject,
                'htmlContent' => $htmlContent,
            ];

            if ($textContent) {
                $data['textContent'] = $textContent;
            }

            $response = $this->makeRequest($data);

            if ($response && isset($response['messageId'])) {
                Log::info('Brevo email sent successfully', [
                    'messageId' => $response['messageId'],
                    'to' => $to['email'],
                    'subject' => $subject
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Brevo email sending failed', [
                'error' => $e->getMessage(),
                'to' => $to['email'] ?? 'unknown',
                'subject' => $subject
            ]);
            return false;
        }
    }

    /**
     * Send tax payment confirmation email
     */
    public function sendTaxPaymentConfirmation($payment)
    {
        $payee = $payment->taxProperty->taxPayee;
        $assessment = $payment->taxAssessment;

        $subject = "Tax Payment Confirmation - Payment #{$payment->id}";
        
        $htmlContent = $this->generatePaymentConfirmationHtml($payment, $payee, $assessment);
        $textContent = $this->generatePaymentConfirmationText($payment, $payee, $assessment);

        return $this->sendEmail(
            [
                'email' => $payee->email,
                'name' => $payee->name
            ],
            $subject,
            $htmlContent,
            $textContent
        );
    }

    /**
     * Send tax assessment notification email
     */
    public function sendTaxAssessmentNotification($assessment)
    {
        $payee = $assessment->taxProperty->taxPayee;

        $subject = "New Tax Assessment - Year {$assessment->year}";
        
        $htmlContent = $this->generateAssessmentNotificationHtml($assessment, $payee);
        $textContent = $this->generateAssessmentNotificationText($assessment, $payee);

        return $this->sendEmail(
            [
                'email' => $payee->email,
                'name' => $payee->name
            ],
            $subject,
            $htmlContent,
            $textContent
        );
    }

    /**
     * Make HTTP request to Brevo API
     */
    private function makeRequest($data)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'api-key: ' . $this->apiKey,
                'Accept: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 201) {
            return json_decode($response, true);
        }

        Log::error('Brevo API request failed', [
            'http_code' => $httpCode,
            'response' => $response
        ]);

        return false;
    }

    /**
     * Generate HTML content for payment confirmation
     */
    private function generatePaymentConfirmationHtml($payment, $payee, $assessment)
    {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #2c3e50;'>Tax Payment Confirmation</h2>
            
            <p>Dear {$payee->name},</p>
            
            <p>Your tax payment has been successfully processed. Here are the details:</p>
            
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                <h3 style='color: #2c3e50; margin-top: 0;'>Payment Details</h3>
                <p><strong>Payment ID:</strong> #{$payment->id}</p>
                <p><strong>Property:</strong> {$payment->taxProperty->property_name}</p>
                <p><strong>Assessment Year:</strong> {$assessment->year}</p>
                <p><strong>Amount Paid:</strong> LKR " . number_format($payment->payment, 2) . "</p>
                <p><strong>Payment Method:</strong> " . ucfirst($payment->pay_method) . "</p>
                <p><strong>Payment Date:</strong> " . $payment->pay_date->format('d M Y') . "</p>
                <p><strong>Status:</strong> " . ucfirst($payment->status) . "</p>
            </div>
            
            <p>Thank you for your payment. If you have any questions, please contact our office.</p>
            
            <p>Best regards,<br>Tax Department</p>
        </body>
        </html>";
    }

    /**
     * Generate text content for payment confirmation
     */
    private function generatePaymentConfirmationText($payment, $payee, $assessment)
    {
        return "
Tax Payment Confirmation

Dear {$payee->name},

Your tax payment has been successfully processed. Here are the details:

Payment Details:
- Payment ID: #{$payment->id}
- Property: {$payment->taxProperty->property_name}
- Assessment Year: {$assessment->year}
- Amount Paid: LKR " . number_format($payment->payment, 2) . "
- Payment Method: " . ucfirst($payment->pay_method) . "
- Payment Date: " . $payment->pay_date->format('d M Y') . "
- Status: " . ucfirst($payment->status) . "

Thank you for your payment. If you have any questions, please contact our office.

Best regards,
Tax Department";
    }

    /**
     * Generate HTML content for assessment notification
     */
    private function generateAssessmentNotificationHtml($assessment, $payee)
    {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #2c3e50;'>New Tax Assessment</h2>
            
            <p>Dear {$payee->name},</p>
            
            <p>A new tax assessment has been created for your property. Here are the details:</p>
            
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                <h3 style='color: #2c3e50; margin-top: 0;'>Assessment Details</h3>
                <p><strong>Assessment ID:</strong> #{$assessment->id}</p>
                <p><strong>Property:</strong> {$assessment->taxProperty->property_name}</p>
                <p><strong>Year:</strong> {$assessment->year}</p>
                <p><strong>Amount:</strong> LKR " . number_format($assessment->amount, 2) . "</p>
                <p><strong>Due Date:</strong> " . $assessment->due_date->format('d M Y') . "</p>
                <p><strong>Status:</strong> " . ucfirst($assessment->status) . "</p>
            </div>
            
            <p>Please ensure payment is made by the due date to avoid any penalties.</p>
            
            <p>Best regards,<br>Tax Department</p>
        </body>
        </html>";
    }

    /**
     * Generate text content for assessment notification
     */
    private function generateAssessmentNotificationText($assessment, $payee)
    {
        return "
New Tax Assessment

Dear {$payee->name},

A new tax assessment has been created for your property. Here are the details:

Assessment Details:
- Assessment ID: #{$assessment->id}
- Property: {$assessment->taxProperty->property_name}
- Year: {$assessment->year}
- Amount: LKR " . number_format($assessment->amount, 2) . "
- Due Date: " . $assessment->due_date->format('d M Y') . "
- Status: " . ucfirst($assessment->status) . "

Please ensure payment is made by the due date to avoid any penalties.

Best regards,
Tax Department";
    }
}


