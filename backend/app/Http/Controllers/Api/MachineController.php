<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MachineController extends Controller
{
    /**
     * Display a listing of machines.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Machine::with('organization')
                ->where('organization_id', auth()->user()->organization_id);

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $machines = $query->orderBy('name')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $machines,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to fetch machines',
                ],
            ], 500);
        }
    }

    /**
     * Store a newly created machine.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:tractor,harvester,rotavator,planter,sprayer,other',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'registration' => 'nullable|string|max:50',
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
            $machine = Machine::create([
                'organization_id' => auth()->user()->organization_id,
                'name' => $request->name,
                'type' => $request->type,
                'make' => $request->make,
                'model' => $request->model,
                'year' => $request->year,
                'registration' => $request->registration,
                'is_active' => $request->get('is_active', true),
            ]);

            return response()->json([
                'success' => true,
                'data' => $machine->load('organization'),
                'message' => 'Machine created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to create machine',
                ],
            ], 500);
        }
    }

    /**
     * Display the specified machine.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $machine = Machine::with(['organization', 'jobs', 'expenses'])
                ->where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $machine,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Machine not found',
                ],
            ], 404);
        }
    }

    /**
     * Update the specified machine.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:tractor,harvester,rotavator,planter,sprayer,other',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'registration' => 'nullable|string|max:50',
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
            $machine = Machine::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            $machine->update($request->only([
                'name', 'type', 'make', 'model', 'year', 'registration', 'is_active'
            ]));

            return response()->json([
                'success' => true,
                'data' => $machine->load('organization'),
                'message' => 'Machine updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Machine not found',
                ],
            ], 404);
        }
    }

    /**
     * Remove the specified machine (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $machine = Machine::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            $machine->delete();

            return response()->json([
                'success' => true,
                'message' => 'Machine deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Machine not found',
                ],
            ], 404);
        }
    }

    /**
     * Get machine statistics.
     */
    public function statistics(string $id): JsonResponse
    {
        try {
            $machine = Machine::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            $stats = [
                'total_jobs' => $machine->jobs()->count(),
                'completed_jobs' => $machine->jobs()->where('status', 'completed')->count(),
                'in_progress_jobs' => $machine->jobs()->where('status', 'in_progress')->count(),
                'total_expenses' => $machine->expenses()->sum('amount'),
                'fuel_expenses' => $machine->expenses()->where('category', 'fuel')->sum('amount'),
                'parts_expenses' => $machine->expenses()->where('category', 'parts')->sum('amount'),
                'maintenance_expenses' => $machine->expenses()->where('category', 'maintenance')->sum('amount'),
                'last_service_date' => $machine->expenses()
                    ->where('category', 'maintenance')
                    ->latest('date')
                    ->value('date'),
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
                    'message' => 'Machine not found',
                ],
            ], 404);
        }
    }

    /**
     * Toggle machine active status.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $machine = Machine::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            $machine->update(['is_active' => !$machine->is_active]);

            return response()->json([
                'success' => true,
                'data' => $machine->load('organization'),
                'message' => 'Machine status updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Machine not found',
                ],
            ], 404);
        }
    }

    /**
     * Get available machine types.
     */
    public function types(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'tractor',
                'harvester',
                'rotavator',
                'planter',
                'sprayer',
                'other',
            ],
        ]);
    }
}
