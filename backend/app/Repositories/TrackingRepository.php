<?php

namespace App\Repositories;

use App\Models\TrackingLog;
use App\Repositories\Contracts\TrackingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Tracking Repository
 * 
 * Implements the TrackingRepositoryInterface.
 * Handles all database operations for tracking logs.
 */
class TrackingRepository implements TrackingRepositoryInterface
{
    /**
     * Create a new tracking log record
     */
    public function create(array $data): TrackingLog
    {
        return TrackingLog::create($data);
    }

    /**
     * Batch create tracking log records
     */
    public function batchCreate(array $logsData): int
    {
        $timestamp = Carbon::now();
        
        foreach ($logsData as &$logData) {
            $logData['created_at'] = $timestamp;
            $logData['updated_at'] = $timestamp;
        }

        DB::table('tracking_logs')->insert($logsData);
        
        return count($logsData);
    }

    /**
     * Find a tracking log by ID
     */
    public function findById(int $id): TrackingLog
    {
        return TrackingLog::with(['user', 'job', 'organization'])
            ->findOrFail($id);
    }

    /**
     * Find all tracking logs for a specific user
     */
    public function findByUser(int $userId, array $filters = []): Collection
    {
        $query = TrackingLog::where('user_id', $userId)
            ->with(['job']);

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get paginated tracking logs for a specific user
     */
    public function paginateByUser(int $userId, array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = TrackingLog::where('user_id', $userId)
            ->with(['job']);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Find tracking logs by job
     */
    public function findByJob(int $jobId, array $filters = []): Collection
    {
        $query = TrackingLog::where('job_id', $jobId)
            ->with(['user']);

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get paginated tracking logs for a specific job
     */
    public function paginateByJob(int $jobId, array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = TrackingLog::where('job_id', $jobId)
            ->with(['user']);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Get tracking logs within date range
     */
    public function findByDateRange(int $organizationId, string $startDate, string $endDate): Collection
    {
        return TrackingLog::where('organization_id', $organizationId)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->with(['user', 'job'])
            ->orderBy('recorded_at', 'asc')
            ->get();
    }

    /**
     * Get live locations (last update within 5 minutes)
     */
    public function getLiveLocations(int $organizationId): Collection
    {
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);

        // Use parameter binding to prevent SQL injection
        $latestLogs = TrackingLog::select('tracking_logs.*')
            ->join(DB::raw('(SELECT user_id, MAX(recorded_at) as max_recorded_at 
                            FROM tracking_logs 
                            WHERE organization_id = ? 
                            AND recorded_at >= ? 
                            GROUP BY user_id) as latest'), function ($join) {
                $join->on('tracking_logs.user_id', '=', 'latest.user_id')
                     ->on('tracking_logs.recorded_at', '=', 'latest.max_recorded_at');
            })
            ->addBinding([$organizationId, $fiveMinutesAgo->toDateTimeString()], 'join')
            ->where('tracking_logs.organization_id', $organizationId)
            ->with(['user', 'job'])
            ->get();

        return $latestLogs;
    }

    /**
     * Get last tracking log for a user
     */
    public function getLastLocation(int $userId): ?TrackingLog
    {
        return TrackingLog::where('user_id', $userId)
            ->with(['job'])
            ->orderBy('recorded_at', 'desc')
            ->first();
    }

    /**
     * Calculate distance traveled for a user/job
     */
    public function calculateDistance(int $userId, ?int $jobId = null, ?string $startDate = null, ?string $endDate = null): float
    {
        $query = TrackingLog::where('user_id', $userId)
            ->orderBy('recorded_at', 'asc');

        if ($jobId) {
            $query->where('job_id', $jobId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('recorded_at', [$startDate, $endDate]);
        }

        $logs = $query->get(['latitude', 'longitude']);

        if ($logs->count() < 2) {
            return 0;
        }

        $totalDistance = 0;
        for ($i = 0; $i < $logs->count() - 1; $i++) {
            $totalDistance += $this->haversineDistance(
                $logs[$i]->latitude,
                $logs[$i]->longitude,
                $logs[$i + 1]->latitude,
                $logs[$i + 1]->longitude
            );
        }

        return round($totalDistance, 2);
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['job_id'])) {
            $query->where('job_id', $filters['job_id']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('recorded_at', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }

        $sortBy = $filters['sort_by'] ?? 'recorded_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula
     */
    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
