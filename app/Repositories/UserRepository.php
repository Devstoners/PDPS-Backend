<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find user by email with roles
     */
    public function findByEmailWithRoles(string $email): ?User
    {
        return $this->model->where('email', $email)->with('roles')->first();
    }

    /**
     * Activate user account
     */
    public function activate(string $email, string $password): bool
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }

        if ($user->status == 0) {
            $user->status = 1;
            $user->password = bcrypt($password);
            return $user->save();
        }

        return false;
    }

    /**
     * Register new user
     */
    public function registerNew(array $data): array
    {
        $user = $this->create([
            'nic' => $data['nic'],
            'email' => $data['email'],
            'name' => $data['name'],
            'status' => $data['status'],
            'type' => $data['type'],
            'requesttype' => $data['requesttype'],
        ]);

        $token = $user->createToken('apptoken')->plainTextToken;
        
        // Assign role based on request type
        $this->assignRoleByRequestType($user, $data['requesttype']);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Login user
     */
    public function login(array $credentials): array
    {
        $user = $this->findByEmailWithRoles($credentials['email']);

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
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection
    {
        return $this->model->role($role)->get();
    }

    /**
     * Update user status
     */
    public function updateStatus(int $id, int $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Assign role based on request type
     */
    private function assignRoleByRequestType(User $user, string $requestType): void
    {
        switch ($requestType) {
            case '1':
                $user->assignRole('admin');
                break;
            case '5':
                $user->assignRole('officer');
                break;
            case '2':
                $user->assignRole('gramasewaka');
                break;
            case '4':
                $user->assignRole('member');
                break;
            case '3':
                $user->assignRole('customer');
                break;
            default:
                // Handle any other cases or throw an exception if needed
                break;
        }
    }
}


