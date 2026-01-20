<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Measurement\CreateMeasurementDTO;
use App\DTOs\Measurement\UpdateMeasurementDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Measurement\StoreMeasurementRequest;
use App\Http\Requests\Measurement\BatchStoreMeasurementRequest;
use App\Http\Resources\MeasurementResource;
use App\Services\MeasurementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Measurement Controller
 *
 * Handles measurement management endpoints.
 */
class MeasurementController extends Controller
{
    public function __construct(
        protected MeasurementService $measurementService
    ) {}

    /**
     * List measurements with filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'land_id' => $request->input('land_id'),
                'user_id' => $request->input('user_id'),
                'type' => $request->input('type'),
                'is_synced' => $request->input('is_synced'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'sort_by' => $request->input('sort_by', 'measurement_started_at'),
                'sort_direction' => $request->input('sort_direction', 'desc'),
            ];

            $perPage = $request->input('per_page', 15);
            $measurements = $this->measurementService->getMeasurementsPaginated($filters, $perPage);

            return $this->successResponse(
                MeasurementResource::collection($measurements)->response()->getData(true),
                'Measurements retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve measurements', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve measurements.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create new measurement
     */
    public function store(StoreMeasurementRequest $request): JsonResponse
    {
        try {
            $dto = CreateMeasurementDTO::fromArray($request->validated());
            $measurement = $this->measurementService->createMeasurement($dto);

            return $this->successResponse(
                new MeasurementResource($measurement),
                'Measurement created successfully.',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            \Log::error('Failed to create measurement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to create measurement: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get single measurement
     */
    public function show(int $id): JsonResponse
    {
        try {
            $measurement = $this->measurementService->getMeasurement($id);

            return $this->successResponse(
                new MeasurementResource($measurement),
                'Measurement retrieved successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Measurement not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve measurement', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve measurement: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create multiple measurements (batch operation for offline sync)
     */
    public function batchStore(BatchStoreMeasurementRequest $request): JsonResponse
    {
        try {
            $measurements = $request->validated()['measurements'];
            $result = $this->measurementService->createBatchMeasurements($measurements);

            $response = [
                'created' => MeasurementResource::collection($result['created']),
                'created_count' => count($result['created']),
                'failed_count' => count($result['errors']),
            ];

            if (!empty($result['errors'])) {
                $response['errors'] = $result['errors'];
            }

            $status = empty($result['errors']) ? Response::HTTP_CREATED : Response::HTTP_MULTI_STATUS;

            return $this->successResponse(
                $response,
                sprintf(
                    'Batch operation completed. %d created, %d failed.',
                    count($result['created']),
                    count($result['errors'])
                ),
                $status
            );
        } catch (\Exception $e) {
            \Log::error('Failed to create batch measurements', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to create batch measurements: ' . $e->getMessage(),
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
