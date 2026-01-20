<?php

namespace App\Services\Auth;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Authentication Service
 * 
 * Handles all authentication-related business logic.
 * Clean Architecture: Service layer manages workflows.
 */
class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private OrganizationRepositoryInterface $organizationRepository,
        private JWTService $jwtService
    ) {}

    /**
     * Register new user and organization
     * 
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Create organization
            $organization = $this->organizationRepository->create([
                'name' => $data['organization_name'],
                'slug' => \Illuminate\Support\Str::slug($data['organization_name']),
                'owner_id' => 0, // Temporary, will update after user creation
            ]);

            // Create user as owner
            $user = $this->userRepository->create([
                'organization_id' => $organization->id,
                'role_id' => $this->getRoleId('owner'),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'language' => $data['language'] ?? 'en',
                'status' => 'active',
            ]);

            // Update organization owner
            $this->organizationRepository->update($organization->id, [
                'owner_id' => $user->id,
            ]);

            // Create default subscription (Free tier)
            $this->createDefaultSubscription($organization->id);

            // Generate tokens
            $tokens = $this->jwtService->generateTokens($user);

            return [
                'user' => $user->fresh(['organization', 'role']),
                'organization' => $organization,
                'tokens' => $tokens,
            ];
        });
    }

    /**
     * Login user
     * 
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Your account is not active.'],
            ]);
        }

        // Update last login
        $this->userRepository->update($user->id, [
            'last_login_at' => now(),
        ]);

        // Generate tokens
        $tokens = $this->jwtService->generateTokens($user);

        return [
            'user' => $user->fresh(['organization', 'role']),
            'tokens' => $tokens,
        ];
    }

    /**
     * Refresh access token
     * 
     * @param string $refreshToken
     * @return array
     * @throws ValidationException
     */
    public function refresh(string $refreshToken): array
    {
        return $this->jwtService->refreshToken($refreshToken);
    }

    /**
     * Logout user
     * 
     * @return void
     */
    public function logout(): void
    {
        $user = auth()->user();
        
        if ($user) {
            $this->jwtService->invalidateToken();
        }
    }

    /**
     * Get role ID by name
     * 
     * @param string $roleName
     * @return int
     */
    private function getRoleId(string $roleName): int
    {
        return \App\Models\Role::where('name', $roleName)->value('id');
    }

    /**
     * Create default subscription for new organization
     * 
     * @param int $organizationId
     * @return void
     */
    private function createDefaultSubscription(int $organizationId): void
    {
        \App\Models\Subscription::create([
            'organization_id' => $organizationId,
            'package_type' => 'free',
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => null, // Free tier never expires
            'features' => [
                'max_measurements' => config('subscription.free_tier_measurements', 10),
                'max_drivers' => config('subscription.free_tier_drivers', 1),
                'max_exports' => config('subscription.free_tier_exports', 5),
                'has_gps_tracking' => true,
                'has_offline_mode' => true,
            ],
        ]);
    }
}
