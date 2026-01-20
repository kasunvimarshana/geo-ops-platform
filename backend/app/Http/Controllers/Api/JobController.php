<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Requests\Job\UpdateJobStatusRequest;
use App\Services\JobService;
use App\Repositories\Interfaces\JobRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobController extends Controller
{
    public function __construct(
        private JobService $jobService,
        private JobRepositoryInterface $jobRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $organizationId = $request->user()->organization_id;
            
            $jobs = $this->jobRepository->findByOrganization($organizationId, [
                'status' => $request->status,
                'driver_id' => $request->driver_id,
                'machine_id' => $request->machine_id,
                'search' => $request->search,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'per_page' => $request->per_page ?? 15,
            ]);

            return response()->json([
                'success' => true,
                'data' => $jobs->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $jobs->total(),
                        'per_page' => $jobs->perPage(),
                        'current_page' => $jobs->currentPage(),
                        'last_page' => $jobs->lastPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch jobs',
            ], 500);
        }
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        try {
            $job = $this->jobService->createJob(
                $request->validated(),
                $request->user()->organization_id,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Job created successfully',
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
            $job = $this->jobRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $job,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch job',
            ], 500);
        }
    }

    public function update(int $id, UpdateJobRequest $request): JsonResponse
    {
        try {
            $job = $this->jobService->updateJob(
                $id,
                $request->validated(),
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Job updated successfully',
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
            $this->jobService->deleteJob($id, $request->user()->organization_id);

            return response()->json([
                'success' => true,
                'message' => 'Job deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(int $id, UpdateJobStatusRequest $request): JsonResponse
    {
        try {
            $job = $this->jobService->updateStatus(
                $id,
                $request->status,
                $request->user()->organization_id,
                $request->location
            );

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Job status updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function trackLocation(int $id, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'accuracy' => 'nullable|numeric',
                'notes' => 'nullable|string',
                'recorded_at' => 'nullable|date',
            ]);

            $this->jobService->trackLocation(
                $id,
                $validated,
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Location tracked successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function active(Request $request): JsonResponse
    {
        try {
            $jobs = $this->jobService->getActiveJobs($request->user()->organization_id);

            return response()->json([
                'success' => true,
                'data' => $jobs,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active jobs',
            ], 500);
        }
    }
}
