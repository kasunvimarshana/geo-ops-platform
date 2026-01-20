<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    /**
     * Login a user and return a JWT token.
     *
     * @param array $credentials
     * @return string
     * @throws ValidationException
     */
    public function login(array $credentials): string
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return Auth::user()->createToken('authToken')->plainTextToken;
    }

    /**
     * Get the authenticated user.
     *
     * @return User|null
     */
    public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Logout the authenticated user.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
    }
}