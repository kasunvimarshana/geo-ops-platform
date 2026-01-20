<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Constants\Role as RoleConstant;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private OrganizationRepositoryInterface $organizationRepository
    ) {}

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        if (!$user->is_active) {
            throw new \Exception('Account is deactivated');
        }

        if ($user->organization->status !== 'active') {
            throw new \Exception('Organization is inactive');
        }

        $token = JWTAuth::fromUser($user);

        $user->update(['last_login_at' => now()]);

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role->name,
                'organization_id' => $user->organization_id,
                'organization_name' => $user->organization->name,
            ],
        ];
    }

    public function register(array $data): array
    {
        if ($this->userRepository->findByEmail($data['email'])) {
            throw new \Exception('Email already exists');
        }

        if (isset($data['phone']) && $this->userRepository->findByPhone($data['phone'])) {
            throw new \Exception('Phone number already exists');
        }

        $organization = $this->organizationRepository->create([
            'name' => $data['organization_name'],
            'slug' => \Illuminate\Support\Str::slug($data['organization_name']),
            'subscription_package' => 'trial',
            'subscription_expires_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        $adminRole = \App\Models\Role::where('name', RoleConstant::ADMIN)->first();

        $user = $this->userRepository->create([
            'organization_id' => $organization->id,
            'role_id' => $adminRole->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'language' => $data['language'] ?? 'en',
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role->name,
                'organization_id' => $user->organization_id,
                'organization_name' => $user->organization->name,
            ],
        ];
    }

    public function refresh(): array
    {
        $token = JWTAuth::refresh();

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ];
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function me(): array
    {
        $user = JWTAuth::user();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role->name,
            'organization_id' => $user->organization_id,
            'organization_name' => $user->organization->name,
            'is_active' => $user->is_active,
            'last_login_at' => $user->last_login_at,
        ];
    }
}
