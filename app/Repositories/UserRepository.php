<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserRepository extends BaseRepository
{
    /**
     * Specify Model class name
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * Find user by email
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findFirstByCriteria(['email' => $email]);
    }

    /**
     * Find user by NIC
     * @param string $nic
     * @return User|null
     */
    public function findByNic(string $nic): ?User
    {
        return $this->findFirstByCriteria(['nic' => $nic]);
    }

    /**
     * Create user with role
     * @param array $data
     * @param string $role
     * @return User
     */
    public function createWithRole(array $data, string $role): User
    {
        $user = $this->create($data);
        $user->assignRole($role);
        return $user;
    }

    /**
     * Activate user account
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function activateUser(string $email, string $password): ?User
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            throw new NotFoundHttpException('Your email address is not available. Please contact the administrator');
        }

        if ($user->status == 1) {
            throw new UnauthorizedHttpException('', 'Your account is already activated.');
        }

        $user->update([
            'status' => 1,
            'password' => Hash::make($password)
        ]);

        return $user;
    }

    /**
     * Get active users
     * @param array $columns
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveUsers(array $columns = ['*'], array $relations = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->findByCriteria(['status' => 1], $columns, $relations);
    }

    /**
     * Get inactive users
     * @param array $columns
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInactiveUsers(array $columns = ['*'], array $relations = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->findByCriteria(['status' => 0], $columns, $relations);
    }

    /**
     * Get users by role
     * @param string $role
     * @param array $columns
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole(string $role, array $columns = ['*'], array $relations = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->where_callback(function($query) use ($role) {
            return $query->role($role);
        })->getQuery()->with($relations)->get($columns);
    }

    /**
     * Update user password
     * @param int $userId
     * @param string $password
     * @return User
     */
    public function updatePassword(int $userId, string $password): User
    {
        return $this->update($userId, ['password' => Hash::make($password)]);
    }

    /**
     * Toggle user status
     * @param int $userId
     * @return User
     */
    public function toggleStatus(int $userId): User
    {
        $user = $this->findOrFail($userId);
        $newStatus = $user->status == 1 ? 0 : 1;
        return $this->update($userId, ['status' => $newStatus]);
    }
}


