<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Register a new user and organization.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'organization_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create organization
            $organization = Organization::create([
                'name' => $request->organization_name,
                'owner_id' => 1, // Temporary, will update
                'subscription_package' => 'free',
                'subscription_expires_at' => now()->addMonths(1),
            ]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'owner',
                'organization_id' => $organization->id,
            ]);

            // Update organization owner
            $organization->update(['owner_id' => $user->id]);

            DB::commit();

            // Generate JWT token
            $token = auth()->login($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                    ],
                    'organization' => [
                        'id' => $organization->id,
                        'name' => $organization->name,
                        'subscription_package' => $organization->subscription_package,
                    ],
                    'token' => $token,
                    'expires_in' => auth()->factory()->getTTL() * 60,
                ],
                'message' => 'Registration successful',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Registration failed',
                ]
            ], 500);
        }
    }

    /**
     * Login user and return JWT token.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Invalid credentials',
                ]
            ], 401);
        }

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'organization_id' => $user->organization_id,
                ],
                'token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60,
            ],
        ]);
    }

    /**
     * Get authenticated user details.
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();
        $organization = $user->organization;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'organization' => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'subscription_package' => $organization->subscription_package,
                    'subscription_expires_at' => $organization->subscription_expires_at,
                ],
            ],
        ]);
    }

    /**
     * Refresh JWT token.
     */
    public function refresh(): JsonResponse
    {
        $token = auth()->refresh();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60,
            ],
        ]);
    }

    /**
     * Logout user (invalidate token).
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }
}
