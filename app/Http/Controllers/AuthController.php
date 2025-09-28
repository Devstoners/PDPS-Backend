<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register a new user
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validate request type first
            $request->validate([
                'requesttype' => 'required|in:1,2,3,4,5'
            ]);

            // Define constants for better readability
            $ADMIN = 1;
            $GRAMASEWAKA = 2;
            $CUSTOMER = 3;
            $MEMBER = 4;
            $OFFICER = 5;

            $requestType = $request->input('requesttype');

            // Validate based on request type
            $validationRules = [
                'email' => 'required|string|email|unique:users,email',
                'name' => 'required|string|max:255',
                'nic' => 'required|string|max:20',
                'status' => 'required|integer|in:0,1',
                'type' => 'required|string',
                'requesttype' => 'required|in:1,2,3,4,5'
            ];

            // Add password validation for admin
            if ($requestType == $ADMIN) {
                $validationRules['password'] = 'required|string|min:8|confirmed';
            }

            $fields = $request->validate($validationRules);

            // Register user through service
            $response = $this->userService->registerUser($fields);

            return response()->json($response, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Authenticate user login
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $response = $this->userService->authenticateUser($credentials);

            return response()->json($response, 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Activate user account
     * @param Request $request
     * @return JsonResponse
     */
    public function activate(Request $request): JsonResponse
    {
        try {
            $fields = $request->validate([
                'username' => 'required|string|email',
                'password' => 'required|string|min:8',
            ]);

            $user = $this->userService->activateUserAccount($fields);

            return response()->json([
                'message' => 'Account activated successfully',
                'user' => $user
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Account activation failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Logout user (revoke token)
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('roles')
        ], 200);
    }

    /**
     * Change password
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $fields = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();
            
            if (!\Hash::check($fields['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            $this->userService->changeUserPassword($user->id, $fields['new_password']);

            return response()->json([
                'message' => 'Password changed successfully'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Password change failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}