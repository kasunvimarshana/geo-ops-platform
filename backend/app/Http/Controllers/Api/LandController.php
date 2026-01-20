<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Land\StoreLandRequest;
use App\Http\Requests\Land\UpdateLandRequest;
use App\Services\LandMeasurementService;
use App\Repositories\Interfaces\LandRepositoryInterface;
use App\DTOs\LandMeasurementDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Land Measurement Controller
 * 
 * Thin controller following Clean Architecture principles
 * Handles HTTP requests and delegates to service layer
 */
class LandController extends Controller
{
    public function __construct(
        private LandMeasurementService $landService,
        private LandRepositoryInterface $landRepository
    ) {}

    /**
     * Create new land measurement
     */
    public function store(StoreLandRequest $request): JsonResponse
    {
        try {
            $dto = LandMeasurementDTO::fromArray($request->validated());
            
            $land = $this->landService->createMeasurement(
                $dto,
                $request->user()->id,
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $land,
                'message' => 'Land measurement created successfully',
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create land measurement',
            ], 500);
        }
    }

    /**
     * List land measurements with pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $lands = $this->landRepository->findByOrganization(
                $request->user()->organization_id,
                [
                    'status' => $request->status,
                    'search' => $request->search,
                    'measured_by' => $request->measured_by,
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'per_page' => $request->per_page ?? 15,
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $lands->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $lands->total(),
                        'per_page' => $lands->perPage(),
                        'current_page' => $lands->currentPage(),
                        'last_page' => $lands->lastPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch land measurements',
            ], 500);
        }
    }

    /**
     * Get land measurement details
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $land = $this->landRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$land) {
                return response()->json([
                    'success' => false,
                    'message' => 'Land measurement not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $land,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch land measurement',
            ], 500);
        }
    }

    /**
     * Update land measurement
     */
    public function update(int $id, UpdateLandRequest $request): JsonResponse
    {
        try {
            $dto = LandMeasurementDTO::fromArray($request->validated());
            
            $land = $this->landService->updateMeasurement(
                $id,
                $dto,
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $land,
                'message' => 'Land measurement updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update land measurement',
            ], 500);
        }
    }

    /**
     * Delete land measurement
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $land = $this->landRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$land) {
                return response()->json([
                    'success' => false,
                    'message' => 'Land measurement not found',
                ], 404);
            }
            
            $this->landRepository->delete($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Land measurement deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete land measurement',
            ], 500);
        }
    }

    /**
     * Get nearby lands
     */
    public function nearby(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'required|integer|min:100|max:50000',
            ]);
            
            $lands = $this->landService->getNearbyLands(
                $validated['latitude'],
                $validated['longitude'],
                $validated['radius'],
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $lands,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch nearby lands',
            ], 500);
        }
    }
}
