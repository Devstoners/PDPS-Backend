<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find user by email with roles
     */
    public function findByEmailWithRoles(string $email): ?User;

    /**
     * Activate user account
     */
    public function activate(string $email, string $password): bool;

    /**
     * Register new user
     */
    public function registerNew(array $data): array;

    /**
     * Login user
     */
    public function login(array $credentials): array;

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection;

    /**
     * Update user status
     */
    public function updateStatus(int $id, int $status): bool;
}