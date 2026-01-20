<?php

namespace App\Services;

use App\Repositories\Contracts\TrackingRepositoryInterface;
use App\DTOs\Tracking\CreateTrackingLogDTO;
use App\DTOs\Tracking\BatchTrackingDTO;
use App\Models\TrackingLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Tracking Service
 * 
 * Handles all business logic related to GPS tracking.
 */
class TrackingService
{
    public function __construct(
        private TrackingRepositoryInterface $trackingRepository
    ) {}

    /**
     * Create a new tracking log
     */
    public function createTrackingLog(CreateTrackingLogDTO $dto): TrackingLog
    {
        $user = Auth::user();
        
        $trackingData = [
            'organization_id' => $user->organization_id,
            'user_id' => $dto->userId,
            'job_id' => $dto->jobId,
            'latitude' => $dto->latitude,
            'longitude' => $dto->longitude,
            'accuracy_meters' => $dto->accuracyMeters,
            'altitude_meters' => $dto->altitudeMeters,
            'speed_mps' => $dto->speedMps,
            'heading_degrees' => $dto->headingDegrees,
            'recorded_at' => $dto->recordedAt ?? Carbon::now(),
            'device_id' => $dto->deviceId,
            'platform' => $dto->platform,
            'metadata' => $dto->metadata,
            'is_synced' => false,
        ];
        
        $trackingLog = $this->trackingRepository->create($trackingData);
        
        return $trackingLog;
    }

    /**
     * Batch create tracking logs (for offline sync)
     */
    public function batchCreateTrackingLogs(BatchTrackingDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            $user = Auth::user();
            
            $logsData = [];
            foreach ($dto->trackingLogs as $logDto) {
                $logsData[] = [
                    'organization_id' => $user->organization_id,
                    'user_id' => $logDto->userId,
                    'job_id' => $logDto->jobId,
                    'latitude' => $logDto->latitude,
                    'longitude' => $logDto->longitude,
                    'accuracy_meters' => $logDto->accuracyMeters,
                    'altitude_meters' => $logDto->altitudeMeters,
                    'speed_mps' => $logDto->speedMps,
                    'heading_degrees' => $logDto->headingDegrees,
                    'recorded_at' => $logDto->recordedAt ?? Carbon::now(),
                    'device_id' => $logDto->deviceId,
                    'platform' => $logDto->platform,
                    'metadata' => $logDto->metadata,
                    'is_synced' => false,
                ];
            }
            
            $count = $this->trackingRepository->batchCreate($logsData);
            
            return [
                'success' => true,
                'count' => $count,
                'message' => "Successfully created {$count} tracking logs",
            ];
        });
    }

    /**
     * Get tracking logs for a user
     */
    public function getUserTrackingLogs(int $userId, array $filters = [])
    {
        $user = Auth::user();
        
        $targetUser = User::findOrFail($userId);
        if ($targetUser->organization_id !== $user->organization_id) {
            throw new \Exception('User does not belong to your organization');
        }
        
        return $this->trackingRepository->findByUser($userId, $filters);
    }

    /**
     * Get paginated tracking logs for a user
     */
    public function getUserTrackingLogsPaginated(int $userId, array $filters = [], int $perPage = 50)
    {
        $user = Auth::user();
        
        $targetUser = User::findOrFail($userId);
        if ($targetUser->organization_id !== $user->organization_id) {
            throw new \Exception('User does not belong to your organization');
        }
        
        return $this->trackingRepository->paginateByUser($userId, $filters, $perPage);
    }

    /**
     * Get tracking logs for a job
     */
    public function getJobTrackingLogs(int $jobId, array $filters = [])
    {
        $user = Auth::user();
        
        // For job tracking, we assume job validation is done at controller level
        return $this->trackingRepository->findByJob($jobId, $filters);
    }

    /**
     * Get paginated tracking logs for a job
     */
    public function getJobTrackingLogsPaginated(int $jobId, array $filters = [], int $perPage = 50)
    {
        $user = Auth::user();
        
        return $this->trackingRepository->paginateByJob($jobId, $filters, $perPage);
    }

    /**
     * Get live locations of all active users
     */
    public function getLiveLocations()
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->trackingRepository->getLiveLocations($organizationId);
    }

    /**
     * Get last known location for a user
     */
    public function getLastLocation(int $userId): ?TrackingLog
    {
        $user = Auth::user();
        
        $targetUser = User::findOrFail($userId);
        if ($targetUser->organization_id !== $user->organization_id) {
            throw new \Exception('User does not belong to your organization');
        }
        
        return $this->trackingRepository->getLastLocation($userId);
    }

    /**
     * Calculate distance traveled
     */
    public function calculateDistanceTraveled(int $userId, ?int $jobId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $user = Auth::user();
        
        $targetUser = User::findOrFail($userId);
        if ($targetUser->organization_id !== $user->organization_id) {
            throw new \Exception('User does not belong to your organization');
        }
        
        $distanceMeters = $this->trackingRepository->calculateDistance($userId, $jobId, $startDate, $endDate);
        $distanceKm = round($distanceMeters / 1000, 2);
        
        return [
            'user_id' => $userId,
            'job_id' => $jobId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'distance_meters' => $distanceMeters,
            'distance_km' => $distanceKm,
        ];
    }

    /**
     * Check if a user is currently active (has location update within 5 minutes)
     */
    public function isUserActive(int $userId): bool
    {
        $lastLocation = $this->trackingRepository->getLastLocation($userId);
        
        if (!$lastLocation) {
            return false;
        }
        
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);
        return $lastLocation->recorded_at->greaterThan($fiveMinutesAgo);
    }

    /**
     * Get tracking statistics for a user
     */
    public function getUserTrackingStats(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $user = Auth::user();
        
        $targetUser = User::findOrFail($userId);
        if ($targetUser->organization_id !== $user->organization_id) {
            throw new \Exception('User does not belong to your organization');
        }
        
        $filters = [];
        if ($startDate && $endDate) {
            $filters['start_date'] = $startDate;
            $filters['end_date'] = $endDate;
        }
        
        $logs = $this->trackingRepository->findByUser($userId, $filters);
        $distance = $this->trackingRepository->calculateDistance($userId, null, $startDate, $endDate);
        
        return [
            'user_id' => $userId,
            'total_logs' => $logs->count(),
            'distance_meters' => $distance,
            'distance_km' => round($distance / 1000, 2),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $this->isUserActive($userId),
        ];
    }
}
