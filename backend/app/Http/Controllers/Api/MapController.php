<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\LandRepositoryInterface;
use App\Repositories\Interfaces\JobRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    public function __construct(
        private LandRepositoryInterface $landRepository,
        private JobRepositoryInterface $jobRepository
    ) {}

    public function nearbyLands(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'required|integer|min:100|max:50000',
            ]);

            $lands = $this->landRepository->findNearby(
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

    public function activeDrivers(Request $request): JsonResponse
    {
        try {
            $jobs = $this->jobRepository->findActiveJobs($request->user()->organization_id);

            $drivers = collect($jobs)->map(function ($job) {
                $latestTracking = collect($job['tracking'] ?? [])->last();
                
                return [
                    'job_id' => $job['id'],
                    'driver' => $job['driver'],
                    'machine' => $job['machine'],
                    'status' => $job['status'],
                    'location' => $latestTracking ? [
                        'latitude' => $latestTracking['latitude'],
                        'longitude' => $latestTracking['longitude'],
                        'recorded_at' => $latestTracking['recorded_at'],
                    ] : null,
                ];
            })->filter(fn($driver) => $driver['location'] !== null)->values();

            return response()->json([
                'success' => true,
                'data' => $drivers,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active drivers',
            ], 500);
        }
    }
}
