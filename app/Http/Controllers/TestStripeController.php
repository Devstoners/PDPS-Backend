<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestStripeController extends Controller
{
    /**
     * Simple test endpoint to verify routing works
     */
    public function test(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Test Stripe controller is working!',
            'data' => [
                'request_data' => $request->all(),
                'headers' => $request->headers->all(),
                'method' => $request->method(),
                'url' => $request->url(),
                'timestamp' => now()->toISOString()
            ]
        ]);
    }
}
