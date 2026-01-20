<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function __construct(
        private JobService $jobService
    ) {}

    /**
     * List jobs for authenticated user's organization
     */
    public function index(Request $request): JsonResponse
    {
        $organization = $request->user()->organization;
        
        $query = Job::where('organization_id', $organization->id)
            ->with(['customer', 'landMeasurement', 'driver.user', 'machine', 'creator']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by driver if provided
        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $jobs->items(),
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'total' => $jobs->total(),
                'per_page' => $jobs->perPage(),
            ]
        ]);
    }

    /**
     * Create a new job
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'land_measurement_id' => 'nullable|exists:land_measurements,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'machine_id' => 'nullable|exists:machines,id',
            'scheduled_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
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
            $organization = $request->user()->organization;
            
            $job = $this->jobService->create($organization, [
                'customer_id' => $request->customer_id,
                'land_measurement_id' => $request->land_measurement_id,
                'driver_id' => $request->driver_id,
                'machine_id' => $request->machine_id,
                'scheduled_at' => $request->scheduled_at,
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Job created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to create job',
                ]
            ], 500);
        }
    }

    /**
     * Show a specific job
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $organization = $request->user()->organization;
        
        $job = Job::where('organization_id', $organization->id)
            ->with(['customer', 'landMeasurement', 'driver.user', 'machine', 'creator', 'invoice'])
            ->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Job not found',
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $job,
        ]);
    }

    /**
     * Update job status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $organization = $request->user()->organization;
        
        $job = Job::where('organization_id', $organization->id)->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Job not found',
                ]
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,assigned,in_progress,completed,billed,paid',
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
            $job = $this->jobService->updateStatus($job, $request->status);

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Job status updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to update job status',
                ]
            ], 500);
        }
    }

    /**
     * Assign driver and machine to job
     */
    public function assign(Request $request, int $id): JsonResponse
    {
        $organization = $request->user()->organization;
        
        $job = Job::where('organization_id', $organization->id)->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Job not found',
                ]
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'machine_id' => 'required|exists:machines,id',
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
            $job = $this->jobService->assign($job, $request->driver_id, $request->machine_id);

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Job assigned successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to assign job',
                ]
            ], 500);
        }
    }

    /**
     * Delete a job
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $organization = $request->user()->organization;
        
        $job = Job::where('organization_id', $organization->id)->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Job not found',
                ]
            ], 404);
        }

        try {
            $job->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to delete job',
                ]
            ], 500);
        }
    }
}
