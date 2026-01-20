<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Services\Auth\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

/**
 * Authentication Controller
 * 
 * Thin controller following Clean Architecture principles.
 * All business logic delegated to AuthService.
 */
class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Register new user and organization
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->successResponse(
            data: [
                'user' => new UserResource($result['user']),
                'organization' => $result['organization'],
                'tokens' => $result['tokens'],
            ],
            message: 'Registration successful',
            statusCode: 201
        );
    }

    /**
     * Login user
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->successResponse(
            data: [
                'user' => new UserResource($result['user']),
                'tokens' => $result['tokens'],
            ],
            message: 'Login successful'
        );
    }

    /**
     * Refresh access token
     * 
     * @param RefreshRequest $request
     * @return JsonResponse
     */
    public function refresh(RefreshRequest $request): JsonResponse
    {
        $tokens = $this->authService->refresh($request->validated('refresh_token'));

        return $this->successResponse(
            data: ['tokens' => $tokens],
            message: 'Token refreshed successfully'
        );
    }

    /**
     * Logout user
     * 
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->successResponse(
            message: 'Logged out successfully'
        );
    }

    /**
     * Get authenticated user
     * 
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();

        return $this->successResponse(
            data: new UserResource($user)
        );
    }
}
