<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\StripePayment;
use Carbon\Carbon;

class StripeLogger
{
    const LOG_CHANNEL = 'stripe';
    const LOG_LEVEL_INFO = 'info';
    const LOG_LEVEL_WARNING = 'warning';
    const LOG_LEVEL_ERROR = 'error';

    /**
     * Log Stripe operation with context
     */
    public static function log(string $level, string $operation, array $data = [], array $context = []): void
    {
        $logData = array_merge([
            'operation' => $operation,
            'timestamp' => Carbon::now()->toISOString(),
            'service' => 'stripe',
        ], $data, $context);

        Log::channel(self::LOG_CHANNEL)->$level("Stripe {$operation}", $logData);
    }

    /**
     * Log info level messages
     */
    public static function info(string $operation, array $data = [], array $context = []): void
    {
        self::log(self::LOG_LEVEL_INFO, $operation, $data, $context);
    }

    /**
     * Log successful operations
     */
    public static function success(string $operation, array $data = [], array $context = []): void
    {
        self::log(self::LOG_LEVEL_INFO, $operation, array_merge($data, ['status' => 'success']), $context);
    }

    /**
     * Log warnings
     */
    public static function warning(string $operation, array $data = [], array $context = []): void
    {
        self::log(self::LOG_LEVEL_WARNING, $operation, array_merge($data, ['status' => 'warning']), $context);
    }

    /**
     * Log errors
     */
    public static function error(string $operation, array $data = [], array $context = []): void
    {
        self::log(self::LOG_LEVEL_ERROR, $operation, array_merge($data, ['status' => 'error']), $context);
    }

    /**
     * Log payment creation
     */
    public static function logPaymentCreated(StripePayment $payment): void
    {
        self::success('payment_created', [
            'payment_id' => $payment->payment_id,
            'stripe_session_id' => $payment->stripe_session_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'taxpayer_name' => $payment->taxpayer_name,
            'email' => $payment->email,
        ]);
    }

    /**
     * Log payment status update
     */
    public static function logPaymentStatusUpdate(StripePayment $payment, string $oldStatus, string $newStatus): void
    {
        self::info('payment_status_updated', [
            'payment_id' => $payment->payment_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'amount' => $payment->amount,
            'taxpayer_name' => $payment->taxpayer_name,
        ]);
    }

    /**
     * Log webhook events
     */
    public static function logWebhookEvent(string $eventType, array $eventData, bool $success = true): void
    {
        $level = $success ? self::LOG_LEVEL_INFO : self::LOG_LEVEL_ERROR;
        $status = $success ? 'processed' : 'failed';
        
        self::log($level, 'webhook_event', [
            'event_type' => $eventType,
            'event_id' => $eventData['id'] ?? 'unknown',
            'status' => $status,
            'event_data' => $eventData,
        ]);
    }

    /**
     * Log API errors
     */
    public static function logApiError(string $operation, \Exception $exception, array $context = []): void
    {
        self::error('api_error', [
            'operation' => $operation,
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'context' => $context,
        ]);
    }

    /**
     * Log validation errors
     */
    public static function logValidationError(string $operation, array $errors, array $input = []): void
    {
        self::warning('validation_error', [
            'operation' => $operation,
            'validation_errors' => $errors,
            'input_data' => $input,
        ]);
    }

    /**
     * Log database operations
     */
    public static function logDatabaseOperation(string $operation, string $table, array $data = []): void
    {
        self::info('database_operation', [
            'operation' => $operation,
            'table' => $table,
            'data' => $data,
        ]);
    }

    /**
     * Log email operations
     */
    public static function logEmailOperation(string $operation, string $email, bool $success = true, string $error = null): void
    {
        $level = $success ? self::LOG_LEVEL_INFO : self::LOG_LEVEL_ERROR;
        
        self::log($level, 'email_operation', [
            'operation' => $operation,
            'email' => $email,
            'success' => $success,
            'error' => $error,
        ]);
    }

    /**
     * Log performance metrics
     */
    public static function logPerformance(string $operation, float $duration, array $metrics = []): void
    {
        self::info('performance_metric', [
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'metrics' => $metrics,
        ]);
    }

    /**
     * Log security events
     */
    public static function logSecurityEvent(string $event, array $data = []): void
    {
        self::warning('security_event', [
            'event' => $event,
            'data' => $data,
        ]);
    }

    /**
     * Get payment statistics for logging
     */
    public static function getPaymentStats(): array
    {
        try {
            $stats = [
                'total_payments' => StripePayment::count(),
                'successful_payments' => StripePayment::successful()->count(),
                'pending_payments' => StripePayment::pending()->count(),
                'failed_payments' => StripePayment::failed()->count(),
                'total_amount' => StripePayment::successful()->sum('amount'),
                'average_amount' => StripePayment::successful()->avg('amount'),
            ];

            return $stats;
        } catch (\Exception $e) {
            self::error('stats_calculation_failed', [
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Log daily statistics
     */
    public static function logDailyStats(): void
    {
        $stats = self::getPaymentStats();
        
        self::info('daily_stats', [
            'date' => Carbon::now()->toDateString(),
            'stats' => $stats,
        ]);
    }
}
