<?php

namespace App\Services;

use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;

/**
 * Authentication Service
 *
 * Handles user authentication operations including registration, login, logout, and token refresh.
 */
class AuthService
{
    /**
     * Register a new user.
     *
     * @param RegisterDTO $dto
     * @return User
     * @throws \Exception
     */
    public function register(RegisterDTO $dto): User
    {
        $userData = $dto->toArray();
        $userData['password'] = Hash::make($dto->password);

        $user = User::create($userData);

        return $user->fresh();
    }

    /**
     * Authenticate user and generate JWT tokens.
     *
     * @param string $email
     * @param string $password
     * @return array{access_token: string, token_type: string, expires_in: int, user: User}
     * @throws AuthenticationException
     */
    public function login(string $email, string $password): array
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
            'is_active' => true,
        ];

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials or inactive account.');
        }

        $user = Auth::guard('api')->user();
        
        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        return $this->respondWithToken($token, $user);
    }

    /**
     * Logout the authenticated user.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::guard('api')->logout();
    }

    /**
     * Refresh the JWT token.
     *
     * @return array{access_token: string, token_type: string, expires_in: int, user: User}
     * @throws AuthenticationException
     */
    public function refresh(): array
    {
        try {
            $token = Auth::guard('api')->refresh();
            
            // After refresh, we need to get the user from the new token
            Auth::guard('api')->setToken($token);
            $user = Auth::guard('api')->user();

            if (!$user) {
                throw new AuthenticationException('Unable to retrieve user after token refresh.');
            }

            return $this->respondWithToken($token, $user);
        } catch (\Exception $e) {
            throw new AuthenticationException('Unable to refresh token.');
        }
    }

    /**
     * Get the authenticated user.
     *
     * @return User
     * @throws AuthenticationException
     */
    public function me(): User
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            throw new AuthenticationException('User not authenticated.');
        }

        return $user;
    }

    /**
     * Format the token response.
     *
     * @param string $token
     * @param User $user
     * @return array{access_token: string, token_type: string, expires_in: int, user: User}
     */
    protected function respondWithToken(string $token, User $user): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => $user,
        ];
    }
}
