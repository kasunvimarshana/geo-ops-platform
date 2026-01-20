<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Measurement\CreateMeasurementRequest;
use App\Http\Requests\Measurement\UpdateMeasurementRequest;
use App\Services\Measurement\MeasurementService;
use App\Http\Resources\MeasurementResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Measurement Controller
 * 
 * Handles GPS land measurement endpoints.
 */
class MeasurementController extends Controller
{
    public function __construct(
        private MeasurementService $measurementService
    ) {}

    /**
     * Get list of measurements
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $measurements = $this->measurementService->list(
            filters: $request->only([
                'customer_phone',
                'measured_by',
                'status',
                'from_date',
                'to_date'
            ]),
            perPage: $request->input('per_page', 15)
        );

        return $this->paginatedResponse(
            data: MeasurementResource::collection($measurements),
            paginator: $measurements
        );
    }

    /**
     * Create new measurement
     * 
     * @param CreateMeasurementRequest $request
     * @return JsonResponse
     */
    public function store(CreateMeasurementRequest $request): JsonResponse
    {
        $measurement = $this->measurementService->create($request->validated());

        return $this->successResponse(
            data: new MeasurementResource($measurement),
            message: 'Measurement created successfully',
            statusCode: 201
        );
    }

    /**
     * Get measurement details
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $measurement = $this->measurementService->findById($id);

        return $this->successResponse(
            data: new MeasurementResource($measurement)
        );
    }

    /**
     * Update measurement
     * 
     * @param UpdateMeasurementRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateMeasurementRequest $request, int $id): JsonResponse
    {
        $measurement = $this->measurementService->update($id, $request->validated());

        return $this->successResponse(
            data: new MeasurementResource($measurement),
            message: 'Measurement updated successfully'
        );
    }

    /**
     * Delete measurement
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->measurementService->delete($id);

        return $this->successResponse(
            message: 'Measurement deleted successfully'
        );
    }
}
