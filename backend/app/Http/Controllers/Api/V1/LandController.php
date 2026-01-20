<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Land\CreateLandDTO;
use App\DTOs\Land\UpdateLandDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Land\StoreLandRequest;
use App\Http\Requests\Land\UpdateLandRequest;
use App\Http\Resources\LandResource;
use App\Http\Resources\MeasurementResource;
use App\Services\LandService;
use App\Services\MeasurementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Land Controller
 *
 * Handles land management endpoints.
 */
class LandController extends Controller
{
    public function __construct(
        protected LandService $landService,
        protected MeasurementService $measurementService
    ) {}

    /**
     * List lands with filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->input('status'),
                'owner_user_id' => $request->input('owner_user_id'),
                'location_district' => $request->input('location_district'),
                'search' => $request->input('search'),
                'sort_by' => $request->input('sort_by', 'created_at'),
                'sort_direction' => $request->input('sort_direction', 'desc'),
            ];

            $perPage = $request->input('per_page', 15);
            $lands = $this->landService->getLandsPaginated($filters, $perPage);

            return $this->successResponse(
                LandResource::collection($lands)->response()->getData(true),
                'Lands retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve lands', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve lands.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create new land
     */
    public function store(StoreLandRequest $request): JsonResponse
    {
        try {
            $dto = CreateLandDTO::fromArray($request->validated());
            $land = $this->landService->createLand($dto);

            return $this->successResponse(
                new LandResource($land),
                'Land created successfully.',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            \Log::error('Failed to create land', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to create land: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get single land with measurements
     */
    public function show(int $id): JsonResponse
    {
        try {
            $land = $this->landService->getLand($id);
            $land->load('measurements');

            return $this->successResponse(
                new LandResource($land),
                'Land retrieved successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Land not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve land', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve land: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update land
     */
    public function update(UpdateLandRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateLandDTO::fromArray($request->validated());
            $land = $this->landService->updateLand($id, $dto);

            return $this->successResponse(
                new LandResource($land),
                'Land updated successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Land not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to update land', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to update land: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Soft delete land
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->landService->deleteLand($id);

            return $this->successResponse(
                null,
                'Land deleted successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Land not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to delete land', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to delete land: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get land measurements
     */
    public function measurements(int $id): JsonResponse
    {
        try {
            $measurements = $this->measurementService->getMeasurementsByLand($id);

            return $this->successResponse(
                MeasurementResource::collection($measurements),
                'Measurements retrieved successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Land not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve land measurements', [
                'land_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve measurements: ' . $e->getMessage(),
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
