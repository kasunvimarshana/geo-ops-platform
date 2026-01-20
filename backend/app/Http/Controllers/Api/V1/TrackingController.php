<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Tracking\CreateTrackingLogDTO;
use App\DTOs\Tracking\BatchTrackingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tracking\StoreTrackingRequest;
use App\Http\Requests\Tracking\BatchStoreTrackingRequest;
use App\Http\Resources\TrackingLogResource;
use App\Http\Resources\LiveLocationResource;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Tracking Controller
 *
 * Handles GPS tracking endpoints.
 */
class TrackingController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    /**
     * Submit GPS location (single or batch for offline sync)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Check if it's a batch submission
            if ($request->has('tracking_logs')) {
                $batchRequest = new BatchStoreTrackingRequest();
                $validator = \Validator::make($request->all(), $batchRequest->rules(), $batchRequest->messages());
                
                if ($validator->fails()) {
                    return $this->errorResponse(
                        'Validation failed.',
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        $validator->errors()->toArray()
                    );
                }

                $dto = BatchTrackingDTO::fromRequest($request);
                $result = $this->trackingService->batchCreateTrackingLogs($dto);

                return $this->successResponse(
                    $result,
                    'Tracking logs created successfully.',
                    Response::HTTP_CREATED
                );
            } else {
                // Single submission
                $singleRequest = new StoreTrackingRequest();
                $validator = \Validator::make($request->all(), $singleRequest->rules(), $singleRequest->messages());
                
                if ($validator->fails()) {
                    return $this->errorResponse(
                        'Validation failed.',
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        $validator->errors()->toArray()
                    );
                }

                $dto = CreateTrackingLogDTO::fromRequest($request);
                $trackingLog = $this->trackingService->createTrackingLog($dto);

                return $this->successResponse(
                    new TrackingLogResource($trackingLog),
                    'Tracking log created successfully.',
                    Response::HTTP_CREATED
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create tracking log', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to create tracking log: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get user's tracking history
     */
    public function getUserTracking(Request $request, int $userId): JsonResponse
    {
        try {
            $filters = [
                'job_id' => $request->input('job_id'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'platform' => $request->input('platform'),
                'sort_by' => $request->input('sort_by', 'recorded_at'),
                'sort_direction' => $request->input('sort_direction', 'desc'),
            ];

            $perPage = $request->input('per_page', 50);
            $trackingLogs = $this->trackingService->getUserTrackingLogsPaginated($userId, $filters, $perPage);

            return $this->successResponse(
                TrackingLogResource::collection($trackingLogs)->response()->getData(true),
                'Tracking logs retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve user tracking logs', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve tracking logs: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get tracking logs for a specific job
     */
    public function getJobTracking(Request $request, int $jobId): JsonResponse
    {
        try {
            $filters = [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'platform' => $request->input('platform'),
                'sort_by' => $request->input('sort_by', 'recorded_at'),
                'sort_direction' => $request->input('sort_direction', 'asc'),
            ];

            $perPage = $request->input('per_page', 50);
            $trackingLogs = $this->trackingService->getJobTrackingLogsPaginated($jobId, $filters, $perPage);

            return $this->successResponse(
                TrackingLogResource::collection($trackingLogs)->response()->getData(true),
                'Job tracking logs retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve job tracking logs', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve tracking logs: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get current live locations of all active users
     */
    public function getLiveLocations(): JsonResponse
    {
        try {
            $liveLocations = $this->trackingService->getLiveLocations();

            return $this->successResponse(
                LiveLocationResource::collection($liveLocations),
                'Live locations retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve live locations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve live locations.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get tracking statistics for a user
     */
    public function getUserStats(Request $request, int $userId): JsonResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $stats = $this->trackingService->getUserTrackingStats($userId, $startDate, $endDate);

            return $this->successResponse(
                $stats,
                'User tracking statistics retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve user tracking stats', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve tracking statistics: ' . $e->getMessage(),
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
