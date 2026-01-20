<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $organizationId = $request->user()->organization_id;
            
            $users = $this->userRepository->findByOrganization($organizationId, [
                'role_id' => $request->role_id,
                'is_active' => $request->is_active,
                'search' => $request->search,
                'per_page' => $request->per_page ?? 15,
            ]);

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $users->total(),
                        'per_page' => $users->perPage(),
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
            ], 500);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $data['organization_id'] = $request->user()->organization_id;
            $data['is_active'] = true;

            $user = $this->userRepository->create($data);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User created successfully',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user',
            ], 500);
        }
    }

    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $data = $request->validated();
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $this->userRepository->update($id, $data);

            return response()->json([
                'success' => true,
                'data' => $this->userRepository->findById($id),
                'message' => 'User updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $this->userRepository->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function activate(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $this->userRepository->update($id, ['is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'User activated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deactivate(int $id, Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $this->userRepository->update($id, ['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'User deactivated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
