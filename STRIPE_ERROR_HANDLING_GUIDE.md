# Stripe Integration Error Handling & Logging Guide

## Overview
This guide covers the comprehensive error handling and logging system implemented for the Stripe payment integration.

## Components

### 1. Custom Exception Classes

#### `StripeException`
- **Location**: `app/Exceptions/StripeException.php`
- **Purpose**: Custom exception for Stripe-specific errors
- **Features**:
  - Stores Stripe error details
  - Includes context information
  - Provides formatted error messages

```php
// Usage example
throw new StripeException(
    'Payment processing failed',
    500,
    $previousException,
    $stripeError,
    ['payment_id' => $paymentId]
);
```

### 2. Logging Service

#### `StripeLogger`
- **Location**: `app/Services/StripeLogger.php`
- **Purpose**: Centralized logging for all Stripe operations
- **Features**:
  - Structured logging with context
  - Multiple log levels (info, warning, error)
  - Performance metrics tracking
  - Security event logging

```php
// Log successful operations
StripeLogger::success('payment_created', [
    'payment_id' => $payment->payment_id,
    'amount' => $payment->amount,
]);

// Log errors
StripeLogger::error('api_error', [
    'operation' => 'create_checkout_session',
    'error' => $exception->getMessage(),
]);
```

### 3. Error Handling Middleware

#### `StripeErrorHandler`
- **Location**: `app/Http/Middleware/StripeErrorHandler.php`
- **Purpose**: Global error handling for Stripe operations
- **Features**:
  - Handles different Stripe exception types
  - Provides appropriate HTTP status codes
  - Logs all errors with context

### 4. Health Check Command

#### `StripeHealthCheck`
- **Location**: `app/Console/Commands/StripeHealthCheck.php`
- **Purpose**: Monitor Stripe integration health
- **Features**:
  - Configuration validation
  - Database connectivity check
  - Stripe API connection test
  - Payment statistics analysis

```bash
# Run health check
php artisan stripe:health-check
```

## Log Channels

### Stripe Channel
- **File**: `storage/logs/stripe.log`
- **Retention**: 30 days
- **Level**: Configurable via `STRIPE_LOG_LEVEL`

### Payments Channel
- **File**: `storage/logs/payments.log`
- **Retention**: 90 days
- **Level**: Configurable via `PAYMENT_LOG_LEVEL`

### Security Channel
- **File**: `storage/logs/security.log`
- **Retention**: 365 days
- **Level**: Warning and above

## Error Types & Handling

### 1. Stripe API Errors

#### Card Errors
- **HTTP Status**: 400
- **Handling**: User-friendly error messages
- **Logging**: Full error context with card details (masked)

#### Rate Limit Errors
- **HTTP Status**: 429
- **Handling**: Retry after delay message
- **Logging**: Rate limit details

#### Authentication Errors
- **HTTP Status**: 401
- **Handling**: Configuration issue notification
- **Logging**: Security event logging

### 2. Validation Errors
- **HTTP Status**: 422
- **Handling**: Detailed field-specific errors
- **Logging**: Input data and validation rules

### 3. Database Errors
- **HTTP Status**: 500
- **Handling**: Generic error message
- **Logging**: Full database error context

## Logging Best Practices

### 1. Structured Logging
```php
StripeLogger::info('payment_created', [
    'payment_id' => $payment->payment_id,
    'amount' => $payment->amount,
    'currency' => $payment->currency,
    'taxpayer' => $payment->taxpayer_name,
    'user_id' => auth()->id(),
]);
```

### 2. Security Events
```php
StripeLogger::logSecurityEvent('invalid_webhook_signature', [
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'signature' => $signature,
]);
```

### 3. Performance Metrics
```php
$startTime = microtime(true);
// ... operation ...
$duration = microtime(true) - $startTime;

StripeLogger::logPerformance('checkout_session_creation', $duration, [
    'payment_id' => $paymentId,
]);
```

## Monitoring & Alerts

### 1. Daily Statistics
```php
// Log daily payment statistics
StripeLogger::logDailyStats();
```

### 2. Health Monitoring
```bash
# Check integration health
php artisan stripe:health-check

# Monitor logs
tail -f storage/logs/stripe.log
```

### 3. Error Rate Monitoring
- Track error rates by operation type
- Monitor failed payment percentages
- Alert on unusual error patterns

## Configuration

### Environment Variables
```env
# Stripe Configuration
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Logging Configuration
STRIPE_LOG_LEVEL=info
PAYMENT_LOG_LEVEL=info
LOG_CHANNEL=stack
```

### Log Rotation
- Daily log files
- Automatic cleanup based on retention policy
- Compressed old logs

## Troubleshooting

### Common Issues

#### 1. Webhook Signature Verification Failed
- **Cause**: Incorrect webhook secret
- **Solution**: Verify `STRIPE_WEBHOOK_SECRET` in environment
- **Logs**: Check `security.log` for signature details

#### 2. API Authentication Failed
- **Cause**: Invalid secret key
- **Solution**: Verify `STRIPE_SECRET_KEY` in environment
- **Logs**: Check `stripe.log` for authentication errors

#### 3. Database Connection Issues
- **Cause**: Database connectivity problems
- **Solution**: Check database configuration
- **Logs**: Check Laravel logs for database errors

### Debug Mode
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Security Considerations

### 1. Sensitive Data
- Never log full card numbers
- Mask sensitive information in logs
- Use secure log storage

### 2. Access Control
- Restrict log file access
- Use secure log transmission
- Monitor log access

### 3. Compliance
- Follow PCI DSS requirements
- Implement audit trails
- Regular security reviews

## Performance Optimization

### 1. Log Level Management
- Use appropriate log levels
- Disable debug logging in production
- Monitor log file sizes

### 2. Asynchronous Logging
- Use queue for heavy logging operations
- Implement log batching
- Monitor logging performance impact

## Testing Error Handling

### 1. Unit Tests
```php
public function test_stripe_exception_handling()
{
    $this->expectException(StripeException::class);
    
    // Test error scenarios
}
```

### 2. Integration Tests
```php
public function test_webhook_error_handling()
{
    // Test webhook error scenarios
}
```

### 3. Load Testing
- Test error handling under load
- Monitor error rates
- Validate recovery mechanisms

## Maintenance

### 1. Regular Tasks
- Monitor log file sizes
- Clean up old logs
- Review error patterns
- Update error handling as needed

### 2. Monitoring
- Set up log monitoring
- Configure alerts for critical errors
- Regular health checks

### 3. Documentation
- Keep error handling documentation updated
- Document new error scenarios
- Maintain troubleshooting guides
