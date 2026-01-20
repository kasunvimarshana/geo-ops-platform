<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function login(array $credentials): ?string
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }

        $user = auth()->user();
        $user->update(['last_login_at' => now()]);

        return $token;
    }

    public function refresh(): string
    {
        return JWTAuth::refresh();
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function me(): ?User
    {
        return auth()->user();
    }
}
