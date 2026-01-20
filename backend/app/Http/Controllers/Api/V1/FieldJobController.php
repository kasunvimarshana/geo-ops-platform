<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\FieldJob\CreateJobDTO;
use App\DTOs\FieldJob\UpdateJobDTO;
use App\DTOs\FieldJob\AssignJobDTO;
use App\DTOs\FieldJob\CompleteJobDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\FieldJob\StoreJobRequest;
use App\Http\Requests\FieldJob\UpdateJobRequest;
use App\Http\Requests\FieldJob\AssignJobRequest;
use App\Http\Requests\FieldJob\CompleteJobRequest;
use App\Http\Resources\JobResource;
use App\Services\FieldJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * FieldJob Controller
 *
 * Handles field job management endpoints.
 */
class FieldJobController extends Controller
{
    public function __construct(
        protected FieldJobService $jobService
    ) {}

    /**
     * List jobs with filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->input('status'),
                'driver_id' => $request->input('driver_id'),
                'customer_id' => $request->input('customer_id'),
                'service_type' => $request->input('service_type'),
                'land_id' => $request->input('land_id'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'search' => $request->input('search'),
                'sort_by' => $request->input('sort_by', 'created_at'),
                'sort_direction' => $request->input('sort_direction', 'desc'),
            ];

            $perPage = $request->input('per_page', 15);
            $jobs = $this->jobService->getJobsPaginated($filters, $perPage);

            return $this->successResponse(
                JobResource::collection($jobs)->response()->getData(true),
                'Jobs retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve jobs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve jobs.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create new job
     */
    public function store(StoreJobRequest $request): JsonResponse
    {
        try {
            $dto = CreateJobDTO::fromArray($request->validated());
            $job = $this->jobService->createJob($dto);

            return $this->successResponse(
                new JobResource($job),
                'Job created successfully.',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            \Log::error('Failed to create job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to create job: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get single job with details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $job = $this->jobService->getJob($id);
            $job->load(['land', 'customer', 'driver']);

            return $this->successResponse(
                new JobResource($job),
                'Job retrieved successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Job not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve job', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve job: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update job
     */
    public function update(UpdateJobRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateJobDTO::fromArray($request->validated());
            $job = $this->jobService->updateJob($id, $dto);

            return $this->successResponse(
                new JobResource($job),
                'Job updated successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Job not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to update job', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to update job: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Soft delete job
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->jobService->deleteJob($id);

            return $this->successResponse(
                null,
                'Job deleted successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Job not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to delete job', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to delete job: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Assign job to driver
     */
    public function assign(AssignJobRequest $request, int $id): JsonResponse
    {
        try {
            $dto = AssignJobDTO::fromArray($request->validated());
            $job = $this->jobService->assignJob($id, $dto);

            return $this->successResponse(
                new JobResource($job),
                'Job assigned successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Job not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to assign job', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to assign job: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Start job (change to in_progress)
     */
    public function start(int $id): JsonResponse
    {
        try {
            $job = $this->jobService->startJob($id);

            return $this->successResponse(
                new JobResource($job),
                'Job started successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Job not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to start job', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to start job: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Complete job (with completion data)
     */
    public function complete(CompleteJobRequest $request, int $id): JsonResponse
    {
        try {
            $dto = CompleteJobDTO::fromArray($request->validated());
            $job = $this->jobService->completeJob($id, $dto);

            return $this->successResponse(
                new JobResource($job),
                'Job completed successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Job not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to complete job', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to complete job: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    protected function successResponse(mixed $data, string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(string $message, int $status = Response::HTTP_BAD_REQUEST, ?array $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
