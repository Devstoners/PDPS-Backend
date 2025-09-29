<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StripeService;
use App\Services\StripeLogger;
use App\Models\StripePayment;
use Illuminate\Support\Facades\DB;

class StripeHealthCheck extends Command
{
    protected $signature = 'stripe:health-check';
    protected $description = 'Perform Stripe integration health check';

    public function handle()
    {
        $this->info('🔍 Starting Stripe Health Check...');
        $this->newLine();

        $checks = [
            'Configuration' => $this->checkConfiguration(),
            'Database Connection' => $this->checkDatabase(),
            'Stripe API Connection' => $this->checkStripeApi(),
            'Webhook Configuration' => $this->checkWebhookConfig(),
            'Payment Statistics' => $this->checkPaymentStats(),
        ];

        $allPassed = true;

        foreach ($checks as $checkName => $result) {
            if ($result['status'] === 'pass') {
                $this->info("✅ {$checkName}: {$result['message']}");
            } elseif ($result['status'] === 'warning') {
                $this->warn("⚠️  {$checkName}: {$result['message']}");
            } else {
                $this->error("❌ {$checkName}: {$result['message']}");
                $allPassed = false;
            }
        }

        $this->newLine();

        if ($allPassed) {
            $this->info('🎉 All health checks passed! Stripe integration is healthy.');
        } else {
            $this->error('🚨 Some health checks failed. Please review the issues above.');
        }

        // Log health check results
        StripeLogger::info('health_check_completed', [
            'all_checks_passed' => $allPassed,
            'checks' => $checks,
        ]);

        return $allPassed ? 0 : 1;
    }

    protected function checkConfiguration(): array
    {
        $requiredConfigs = [
            'STRIPE_SECRET_KEY' => config('stripe.secret_key'),
            'STRIPE_PUBLISHABLE_KEY' => config('stripe.publishable_key'),
            'STRIPE_WEBHOOK_SECRET' => config('stripe.webhook_secret'),
        ];

        $missing = [];
        foreach ($requiredConfigs as $key => $value) {
            if (empty($value)) {
                $missing[] = $key;
            }
        }

        if (empty($missing)) {
            return [
                'status' => 'pass',
                'message' => 'All required configuration keys are set',
            ];
        }

        return [
            'status' => 'fail',
            'message' => 'Missing configuration: ' . implode(', ', $missing),
        ];
    }

    protected function checkDatabase(): array
    {
        try {
            // Check if table exists
            if (!DB::getSchemaBuilder()->hasTable('stripe_payments')) {
                return [
                    'status' => 'fail',
                    'message' => 'stripe_payments table does not exist',
                ];
            }

            // Check if we can query the table
            $count = StripePayment::count();
            
            return [
                'status' => 'pass',
                'message' => "Database connection successful. {$count} payments found.",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'fail',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    protected function checkStripeApi(): array
    {
        try {
            $stripeService = new StripeService();
            
            // Try to retrieve account information (this is a lightweight API call)
            $stripe = new \Stripe\StripeClient(config('stripe.secret_key'));
            $account = $stripe->accounts->retrieve();
            
            return [
                'status' => 'pass',
                'message' => "API connection successful. Account: {$account->id}",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'fail',
                'message' => 'Stripe API connection failed: ' . $e->getMessage(),
            ];
        }
    }

    protected function checkWebhookConfig(): array
    {
        $webhookSecret = config('stripe.webhook_secret');
        
        if (empty($webhookSecret)) {
            return [
                'status' => 'warning',
                'message' => 'Webhook secret not configured. Webhooks will not be verified.',
            ];
        }

        return [
            'status' => 'pass',
            'message' => 'Webhook secret is configured',
        ];
    }

    protected function checkPaymentStats(): array
    {
        try {
            $stats = StripeLogger::getPaymentStats();
            
            if (empty($stats)) {
                return [
                    'status' => 'warning',
                    'message' => 'No payment statistics available',
                ];
            }

            $message = sprintf(
                'Total: %d, Successful: %d, Pending: %d, Failed: %d',
                $stats['total_payments'],
                $stats['successful_payments'],
                $stats['pending_payments'],
                $stats['failed_payments']
            );

            return [
                'status' => 'pass',
                'message' => $message,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'message' => 'Could not retrieve payment statistics: ' . $e->getMessage(),
            ];
        }
    }
}
