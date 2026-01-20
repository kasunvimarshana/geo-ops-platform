<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrackingLog;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    /**
     * Store tracking logs (batch)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'job_id' => 'nullable|exists:jobs,id',
            'locations' => 'required|array|min:1',
            'locations.*.latitude' => 'required|numeric|between:-90,90',
            'locations.*.longitude' => 'required|numeric|between:-180,180',
            'locations.*.accuracy' => 'nullable|numeric',
            'locations.*.speed' => 'nullable|numeric',
            'locations.*.heading' => 'nullable|numeric',
            'locations.*.recorded_at' => 'required|date',
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
            // Verify driver belongs to user's organization
            $driver = Driver::where('id', $request->driver_id)
                ->where('organization_id', $request->user()->organization_id)
                ->firstOrFail();

            $logs = [];
            foreach ($request->locations as $location) {
                $logs[] = [
                    'driver_id' => $driver->id,
                    'job_id' => $request->job_id,
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                    'accuracy' => $location['accuracy'] ?? null,
                    'speed' => $location['speed'] ?? null,
                    'heading' => $location['heading'] ?? null,
                    'recorded_at' => $location['recorded_at'],
                ];
            }

            TrackingLog::insert($logs);

            return response()->json([
                'success' => true,
                'message' => 'Tracking logs saved successfully',
                'data' => [
                    'count' => count($logs),
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to save tracking logs',
                ]
            ], 500);
        }
    }

    /**
     * Get tracking history for a driver
     */
    public function driverHistory(Request $request, int $driverId): JsonResponse
    {
        try {
            // Verify driver belongs to user's organization
            $driver = Driver::where('id', $driverId)
                ->where('organization_id', $request->user()->organization_id)
                ->firstOrFail();

            $hours = $request->input('hours', 24);
            
            $logs = TrackingLog::where('driver_id', $driver->id)
                ->where('recorded_at', '>=', now()->subHours($hours))
                ->orderBy('recorded_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $logs,
                'meta' => [
                    'driver_id' => $driver->id,
                    'hours' => $hours,
                    'count' => $logs->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Driver not found',
                ]
            ], 404);
        }
    }

    /**
     * Get tracking logs for a specific job
     */
    public function jobHistory(Request $request, int $jobId): JsonResponse
    {
        try {
            $logs = TrackingLog::where('job_id', $jobId)
                ->whereHas('job', function($query) use ($request) {
                    $query->where('organization_id', $request->user()->organization_id);
                })
                ->with(['driver.user'])
                ->orderBy('recorded_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $logs,
                'meta' => [
                    'job_id' => $jobId,
                    'count' => $logs->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to retrieve tracking history',
                ]
            ], 500);
        }
    }

    /**
     * Get current location of active drivers
     */
    public function activeDrivers(Request $request): JsonResponse
    {
        try {
            $organization = $request->user()->organization;
            
            // Get latest location for each active driver in the organization
            $activeDrivers = Driver::where('organization_id', $organization->id)
                ->where('is_active', true)
                ->with(['user', 'jobs' => function($query) {
                    $query->whereIn('status', ['assigned', 'in_progress'])
                        ->latest()
                        ->limit(1);
                }])
                ->get()
                ->map(function($driver) {
                    $latestLog = TrackingLog::where('driver_id', $driver->id)
                        ->where('recorded_at', '>=', now()->subHours(1))
                        ->latest('recorded_at')
                        ->first();

                    return [
                        'driver_id' => $driver->id,
                        'driver_name' => $driver->user->name,
                        'latest_location' => $latestLog ? [
                            'latitude' => $latestLog->latitude,
                            'longitude' => $latestLog->longitude,
                            'recorded_at' => $latestLog->recorded_at,
                            'speed' => $latestLog->speed,
                        ] : null,
                        'active_job' => $driver->jobs->first(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $activeDrivers->filter(fn($d) => $d['latest_location'] !== null)->values(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to retrieve active drivers',
                ]
            ], 500);
        }
    }
}
