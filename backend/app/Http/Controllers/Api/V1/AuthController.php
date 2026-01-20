<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'organization_id' => $request->organization_id,
            'role' => $request->role ?? 'field_worker',
        ]);

        $token = auth()->login($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl', 60) * 60
        ], 201);
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl', 60) * 60
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Logout user
     */
    public function logout()
    {
        try {
            auth()->logout();
        } catch (\Exception $e) {
            // If logout fails (e.g., no token), still return success
            // as the client-side token will be discarded anyway
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl', 60) * 60
        ]);
    }
}
