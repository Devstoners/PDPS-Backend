<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEmail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user
     * @param array $data
     * @return array
     */
    public function registerUser(array $data): array
    {
        // Determine role based on request type
        $role = $this->determineRole($data['requesttype']);
        
        // Create user with role
        $user = $this->userRepository->createWithRole($data, $role);
        
        // Generate token
        $token = $user->createToken('apptoken')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Authenticate user login
     * @param array $credentials
     * @return array
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     */
    public function authenticateUser(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new NotFoundHttpException('Please check email or Password !');
        }

        if ($user->status === 0) {
            throw new UnauthorizedHttpException(
                "Account Is Disabled! Please Contact Administrator.",
                "Account Is Disabled! Please Contact Administrator."
            );
        }

        $token = $user->createToken('apptoken')->plainTextToken;

        return [
            'user' => $user->load('roles'),
            'token' => $token,
        ];
    }

    /**
     * Activate user account
     * @param array $data
     * @return User
     */
    public function activateUserAccount(array $data): User
    {
        return $this->userRepository->activateUser($data['username'], $data['password']);
    }

    /**
     * Get user dashboard statistics
     * @return array
     */
    public function getUserStatistics(): array
    {
        return [
            'total_users' => $this->userRepository->count(),
            'active_users' => $this->userRepository->count(['status' => 1]),
            'inactive_users' => $this->userRepository->count(['status' => 0]),
            'recent_users' => $this->userRepository->latest(5),
        ];
    }

    /**
     * Update user profile
     * @param int $userId
     * @param array $data
     * @return User
     */
    public function updateUserProfile(int $userId, array $data): User
    {
        // Remove sensitive fields that shouldn't be updated via profile
        unset($data['password'], $data['status'], $data['email_verified_at']);
        
        return $this->userRepository->update($userId, $data);
    }

    /**
     * Change user password
     * @param int $userId
     * @param string $newPassword
     * @return User
     */
    public function changeUserPassword(int $userId, string $newPassword): User
    {
        return $this->userRepository->updatePassword($userId, $newPassword);
    }

    /**
     * Toggle user status (activate/deactivate)
     * @param int $userId
     * @return User
     */
    public function toggleUserStatus(int $userId): User
    {
        return $this->userRepository->toggleStatus($userId);
    }

    /**
     * Get users by role
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole(string $role): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userRepository->getUsersByRole($role);
    }

    /**
     * Send email notification to user
     * @param User $user
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function sendEmailNotification(User $user, string $subject, string $message): bool
    {
        try {
            Mail::to($user->email)->send(new CustomEmail($message));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Determine user role based on request type
     * @param string $requestType
     * @return string
     */
    private function determineRole(string $requestType): string
    {
        return match($requestType) {
            '1' => 'admin',
            '2' => 'gramasewaka',
            '3' => 'customer',
            '4' => 'member',
            '5' => 'officer',
            default => 'customer'
        };
    }
}