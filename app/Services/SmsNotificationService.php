<?php

namespace App\Services;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SmsNotificationService
{
    protected $client;
    protected $fromNumber;
    protected $rateLimitEnabled;

    public function __construct()
    {
        $this->fromNumber = config('twilio.from_number');
        $this->rateLimitEnabled = config('twilio.rate_limiting.enabled');
        
        if (config('twilio.sms.enabled') && config('twilio.account_sid') && config('twilio.auth_token')) {
            $this->client = new Client(
                config('twilio.account_sid'),
                config('twilio.auth_token')
            );
        }
    }

    /**
     * Send SMS notification
     */
    public function sendSms(string $to, string $message, string $type = 'general'): array
    {
        if (!config('twilio.sms.enabled')) {
            Log::info('SMS disabled, message would be sent to: ' . $to . ' - ' . $message);
            return [
                'success' => true,
                'message' => 'SMS disabled (logged only)',
                'sid' => 'disabled'
            ];
        }

        if (!$this->client) {
            Log::info('SMS client not configured, message would be sent to: ' . $to . ' - ' . $message);
            return [
                'success' => true,
                'message' => 'SMS client not configured (logged only)',
                'sid' => 'not_configured'
            ];
        }

        // Check rate limiting
        if ($this->rateLimitEnabled && !$this->checkRateLimit($to)) {
            return [
                'success' => false,
                'message' => 'Rate limit exceeded for this number',
                'error' => 'rate_limit_exceeded'
            ];
        }

        try {
            // Format phone number
            $formattedNumber = $this->formatPhoneNumber($to);
            
            // Send SMS
            $message = $this->client->messages->create(
                $formattedNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            // Log successful send
            Log::info('SMS sent successfully', [
                'to' => $formattedNumber,
                'sid' => $message->sid,
                'type' => $type
            ]);

            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'sid' => $message->sid,
                'to' => $formattedNumber
            ];

        } catch (TwilioException $e) {
            Log::error('SMS sending failed', [
                'to' => $to,
                'error' => $e->getMessage(),
                'type' => $type
            ]);

            return [
                'success' => false,
                'message' => 'SMS sending failed: ' . $e->getMessage(),
                'error' => $e->getCode()
            ];
        }
    }

    /**
     * Send payment confirmation SMS
     */
    public function sendPaymentConfirmation(string $to, array $data): array
    {
        $message = $this->buildMessage('payment_confirmation', $data);
        return $this->sendSms($to, $message, 'payment_confirmation');
    }

    /**
     * Send payment failed SMS
     */
    public function sendPaymentFailed(string $to, array $data): array
    {
        $message = $this->buildMessage('payment_failed', $data);
        return $this->sendSms($to, $message, 'payment_failed');
    }

    /**
     * Send service reminder SMS
     */
    public function sendServiceReminder(string $to, array $data): array
    {
        $message = $this->buildMessage('service_reminder', $data);
        return $this->sendSms($to, $message, 'service_reminder');
    }

    /**
     * Send overdue notice SMS
     */
    public function sendOverdueNotice(string $to, array $data): array
    {
        $message = $this->buildMessage('overdue_notice', $data);
        return $this->sendSms($to, $message, 'overdue_notice');
    }

    /**
     * Send hall reservation confirmation SMS
     */
    public function sendReservationConfirmation(string $to, array $data): array
    {
        $message = $this->buildMessage('reservation_confirmed', $data);
        return $this->sendSms($to, $message, 'reservation_confirmed');
    }

    /**
     * Send hall reservation cancellation SMS
     */
    public function sendReservationCancellation(string $to, array $data): array
    {
        $message = $this->buildMessage('reservation_cancelled', $data);
        return $this->sendSms($to, $message, 'reservation_cancelled');
    }

    /**
     * Send tax assessment SMS
     */
    public function sendTaxAssessment(string $to, array $data): array
    {
        $message = $this->buildMessage('tax_assessment', $data);
        return $this->sendSms($to, $message, 'tax_assessment');
    }

    /**
     * Send water bill SMS
     */
    public function sendWaterBill(string $to, array $data): array
    {
        $message = $this->buildMessage('water_bill', $data);
        return $this->sendSms($to, $message, 'water_bill');
    }

    /**
     * Build message from template
     */
    private function buildMessage(string $template, array $data): string
    {
        $template = config("twilio.templates.{$template}", 'Default message: {message}');
        
        $message = $template;
        foreach ($data as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }

        // Ensure message doesn't exceed SMS length limit
        $maxLength = config('twilio.sms.max_length', 160);
        if (strlen($message) > $maxLength) {
            $message = substr($message, 0, $maxLength - 3) . '...';
        }

        return $message;
    }

    /**
     * Format phone number for international use
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if not present
        if (!str_starts_with($phone, '94')) {
            $phone = '94' . ltrim($phone, '0');
        }
        
        return '+' . $phone;
    }

    /**
     * Check rate limiting
     */
    private function checkRateLimit(string $phone): bool
    {
        $key = 'sms_rate_limit_' . $phone;
        $hourlyKey = $key . '_hourly';
        $dailyKey = $key . '_daily';
        
        $hourlyCount = Cache::get($hourlyKey, 0);
        $dailyCount = Cache::get($dailyKey, 0);
        
        $maxHourly = config('twilio.rate_limiting.max_per_hour', 10);
        $maxDaily = config('twilio.rate_limiting.max_per_day', 100);
        
        if ($hourlyCount >= $maxHourly || $dailyCount >= $maxDaily) {
            return false;
        }
        
        // Increment counters
        Cache::put($hourlyKey, $hourlyCount + 1, 3600); // 1 hour
        Cache::put($dailyKey, $dailyCount + 1, 86400); // 24 hours
        
        return true;
    }

    /**
     * Get SMS delivery status
     */
    public function getDeliveryStatus(string $messageSid): array
    {
        try {
            $message = $this->client->messages($messageSid)->fetch();
            
            return [
                'success' => true,
                'status' => $message->status,
                'to' => $message->to,
                'date_sent' => $message->dateSent,
                'error_code' => $message->errorCode,
                'error_message' => $message->errorMessage
            ];
        } catch (TwilioException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test SMS functionality
     */
    public function testSms(string $to, string $message = 'Test SMS from PDPS system'): array
    {
        return $this->sendSms($to, $message, 'test');
    }

    /**
     * Get account information
     */
    public function getAccountInfo(): array
    {
        try {
            $account = $this->client->api->accounts(config('twilio.account_sid'))->fetch();
            
            return [
                'success' => true,
                'account_sid' => $account->sid,
                'friendly_name' => $account->friendlyName,
                'status' => $account->status,
                'type' => $account->type
            ];
        } catch (TwilioException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
