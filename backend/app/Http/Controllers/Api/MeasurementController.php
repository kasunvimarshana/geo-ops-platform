<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandMeasurement;
use App\Services\LandMeasurementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MeasurementController extends Controller
{
    public function __construct(
        private LandMeasurementService $measurementService
    ) {}

    /**
     * List measurements for authenticated user's organization
     */
    public function index(Request $request): JsonResponse
    {
        $organization = $request->user()->organization;
        
        $measurements = LandMeasurement::where('organization_id', $organization->id)
            ->with(['measuredBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $measurements->items(),
            'meta' => [
                'current_page' => $measurements->currentPage(),
                'total' => $measurements->total(),
                'per_page' => $measurements->perPage(),
            ]
        ]);
    }

    /**
     * Create a new measurement
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'coordinates' => 'required|array|min:3',
            'coordinates.*.latitude' => 'required|numeric|between:-90,90',
            'coordinates.*.longitude' => 'required|numeric|between:-180,180',
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
            
            // Calculate area from coordinates
            $areaData = $this->measurementService->calculateArea($request->coordinates);
            
            $measurement = $this->measurementService->create($organization, [
                'name' => $request->name,
                'coordinates' => $request->coordinates,
                'area_acres' => $areaData['acres'],
                'area_hectares' => $areaData['hectares'],
                'measured_by' => $request->user()->id,
                'measured_at' => $request->measured_at ?? now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $measurement,
                'message' => 'Measurement created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to create measurement',
                ]
            ], 500);
        }
    }

    /**
     * Show a specific measurement
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $organization = $request->user()->organization;
        
        $measurement = LandMeasurement::where('organization_id', $organization->id)
            ->with(['measuredBy'])
            ->find($id);

        if (!$measurement) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Measurement not found',
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $measurement,
        ]);
    }

    /**
     * Update a measurement
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $organization = $request->user()->organization;
        
        $measurement = LandMeasurement::where('organization_id', $organization->id)->find($id);

        if (!$measurement) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Measurement not found',
                ]
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'coordinates' => 'sometimes|array|min:3',
            'coordinates.*.latitude' => 'required_with:coordinates|numeric|between:-90,90',
            'coordinates.*.longitude' => 'required_with:coordinates|numeric|between:-180,180',
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
            $data = ['name' => $request->name];
            
            if ($request->has('coordinates')) {
                $areaData = $this->measurementService->calculateArea($request->coordinates);
                $data['coordinates'] = $request->coordinates;
                $data['area_acres'] = $areaData['acres'];
                $data['area_hectares'] = $areaData['hectares'];
            }

            $measurement = $this->measurementService->update($measurement, $data);

            return response()->json([
                'success' => true,
                'data' => $measurement,
                'message' => 'Measurement updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to update measurement',
                ]
            ], 500);
        }
    }

    /**
     * Delete a measurement
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $organization = $request->user()->organization;
        
        $measurement = LandMeasurement::where('organization_id', $organization->id)->find($id);

        if (!$measurement) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Measurement not found',
                ]
            ], 404);
        }

        try {
            $measurement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Measurement deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to delete measurement',
                ]
            ], 500);
        }
    }
}
