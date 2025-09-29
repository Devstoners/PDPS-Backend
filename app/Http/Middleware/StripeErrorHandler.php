<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\StripeException;
use App\Services\StripeLogger;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;

class StripeErrorHandler
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        try {
            return $next($request);
        } catch (StripeException $e) {
            return $this->handleStripeException($e);
        } catch (ApiErrorException $e) {
            return $this->handleStripeApiError($e);
        } catch (\Exception $e) {
            return $this->handleGenericException($e);
        }
    }

    /**
     * Handle custom Stripe exceptions
     */
    protected function handleStripeException(StripeException $e): JsonResponse
    {
        StripeLogger::error('stripe_exception', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'stripe_error' => $e->getStripeError(),
            'context' => $e->getContext(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Payment processing failed',
            'error' => $e->getMessage(),
            'error_code' => $e->getCode(),
        ], 500);
    }

    /**
     * Handle Stripe API errors
     */
    protected function handleStripeApiError(ApiErrorException $e): JsonResponse
    {
        $statusCode = 500;
        $errorMessage = 'Payment processing failed';
        $errorCode = $e->getCode();

        // Handle specific Stripe error types
        if ($e instanceof CardException) {
            $statusCode = 400;
            $errorMessage = 'Card payment failed: ' . $e->getMessage();
            $errorCode = 'card_error';
        } elseif ($e instanceof RateLimitException) {
            $statusCode = 429;
            $errorMessage = 'Too many requests. Please try again later.';
            $errorCode = 'rate_limit_error';
        } elseif ($e instanceof InvalidRequestException) {
            $statusCode = 400;
            $errorMessage = 'Invalid payment request: ' . $e->getMessage();
            $errorCode = 'invalid_request_error';
        } elseif ($e instanceof AuthenticationException) {
            $statusCode = 401;
            $errorMessage = 'Payment authentication failed';
            $errorCode = 'authentication_error';
        } elseif ($e instanceof ApiConnectionException) {
            $statusCode = 503;
            $errorMessage = 'Payment service temporarily unavailable';
            $errorCode = 'api_connection_error';
        }

        StripeLogger::error('stripe_api_error', [
            'error_type' => get_class($e),
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'stripe_error' => $e->getJsonBody(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $errorMessage,
            'error' => $e->getMessage(),
            'error_code' => $errorCode,
        ], $statusCode);
    }

    /**
     * Handle generic exceptions
     */
    protected function handleGenericException(\Exception $e): JsonResponse
    {
        StripeLogger::error('generic_exception', [
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
        ], 500);
    }
}
