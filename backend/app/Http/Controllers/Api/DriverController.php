<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    /**
     * Display a listing of drivers.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Driver::with(['user', 'organization'])
                ->where('organization_id', auth()->user()->organization_id);

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $drivers = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $drivers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to fetch drivers',
                ],
            ], 500);
        }
    }

    /**
     * Store a newly created driver.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|max:50',
            'license_expiry' => 'required|date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors(),
                ],
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create user account for driver
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'driver',
                'organization_id' => auth()->user()->organization_id,
            ]);

            // Create driver profile
            $driver = Driver::create([
                'organization_id' => auth()->user()->organization_id,
                'user_id' => $user->id,
                'license_number' => $request->license_number,
                'license_expiry' => $request->license_expiry,
                'is_active' => $request->get('is_active', true),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $driver->load(['user', 'organization']),
                'message' => 'Driver created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to create driver',
                ],
            ], 500);
        }
    }

    /**
     * Display the specified driver.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $driver = Driver::with(['user', 'organization', 'jobs', 'trackingLogs'])
                ->where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $driver,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Driver not found',
                ],
            ], 404);
        }
    }

    /**
     * Update the specified driver.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'license_number' => 'sometimes|required|string|max:50',
            'license_expiry' => 'sometimes|required|date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors(),
                ],
            ], 422);
        }

        try {
            DB::beginTransaction();

            $driver = Driver::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            // Update driver profile
            $driver->update($request->only(['license_number', 'license_expiry', 'is_active']));

            // Update user if name or phone provided
            if ($request->has('name') || $request->has('phone')) {
                $driver->user->update($request->only(['name', 'phone']));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $driver->load(['user', 'organization']),
                'message' => 'Driver updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Driver not found',
                ],
            ], 404);
        }
    }

    /**
     * Remove the specified driver (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $driver = Driver::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            // Soft delete both driver and user
            $driver->delete();
            $driver->user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Driver deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Driver not found',
                ],
            ], 404);
        }
    }

    /**
     * Get driver performance statistics.
     */
    public function statistics(string $id): JsonResponse
    {
        try {
            $driver = Driver::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            $stats = [
                'total_jobs' => $driver->jobs()->count(),
                'completed_jobs' => $driver->jobs()->where('status', 'completed')->count(),
                'in_progress_jobs' => $driver->jobs()->where('status', 'in_progress')->count(),
                'total_tracking_points' => $driver->trackingLogs()->count(),
                'total_expenses' => $driver->expenses()->sum('amount'),
                'approved_expenses' => $driver->expenses()->where('status', 'approved')->sum('amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Driver not found',
                ],
            ], 404);
        }
    }

    /**
     * Toggle driver active status.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $driver = Driver::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            $driver->update(['is_active' => !$driver->is_active]);

            return response()->json([
                'success' => true,
                'data' => $driver->load(['user', 'organization']),
                'message' => 'Driver status updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Driver not found',
                ],
            ], 404);
        }
    }
}
