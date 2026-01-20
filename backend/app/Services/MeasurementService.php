<?php

namespace App\Services;

use App\Repositories\Contracts\MeasurementRepositoryInterface;
use App\Repositories\Contracts\LandRepositoryInterface;
use App\DTOs\Measurement\CreateMeasurementDTO;
use App\DTOs\Measurement\UpdateMeasurementDTO;
use App\Models\Measurement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Measurement Service
 * 
 * Handles all business logic related to land measurements.
 */
class MeasurementService
{
    public function __construct(
        private MeasurementRepositoryInterface $measurementRepository,
        private LandRepositoryInterface $landRepository,
        private GeoCalculationService $geoService
    ) {}

    public function createMeasurement(CreateMeasurementDTO $dto): Measurement
    {
        return DB::transaction(function () use ($dto) {
            $user = Auth::user();
            
            $land = $this->landRepository->findById($dto->landId);
            
            if ($land->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to land');
            }
            
            $areaData = $this->geoService->calculateArea($dto->coordinates);
            $centerPoint = $this->geoService->calculateCenterPoint($dto->coordinates);
            $perimeter = $this->geoService->calculatePerimeter($dto->coordinates);
            
            $startTime = Carbon::parse($dto->measurementStartedAt);
            $endTime = Carbon::parse($dto->measurementCompletedAt);
            $durationSeconds = $endTime->diffInSeconds($startTime);
            
            $measurementData = [
                'land_id' => $dto->landId,
                'user_id' => $user->id,
                'organization_id' => $user->organization_id,
                'type' => $dto->type,
                'coordinates' => $dto->coordinates,
                'area_square_meters' => $areaData['square_meters'],
                'area_acres' => $areaData['acres'],
                'area_hectares' => $areaData['hectares'],
                'perimeter_meters' => $perimeter,
                'center_point' => $centerPoint,
                'point_count' => count($dto->coordinates),
                'accuracy_meters' => $dto->accuracyMeters,
                'measurement_started_at' => $startTime,
                'measurement_completed_at' => $endTime,
                'duration_seconds' => $durationSeconds,
                'notes' => $dto->notes,
                'is_synced' => $dto->isSynced,
                'device_id' => $dto->deviceId,
                'created_by' => $user->id,
            ];
            
            $measurement = $this->measurementRepository->create($measurementData);
            
            return $measurement;
        });
    }

    public function updateMeasurement(int $measurementId, UpdateMeasurementDTO $dto): Measurement
    {
        return DB::transaction(function () use ($measurementId, $dto) {
            $user = Auth::user();
            
            $measurement = $this->measurementRepository->findById($measurementId);
            
            if ($measurement->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to measurement');
            }
            
            $updateData = [
                'updated_by' => $user->id,
            ];
            
            if ($dto->coordinates) {
                $areaData = $this->geoService->calculateArea($dto->coordinates);
                $centerPoint = $this->geoService->calculateCenterPoint($dto->coordinates);
                $perimeter = $this->geoService->calculatePerimeter($dto->coordinates);
                
                $updateData['coordinates'] = $dto->coordinates;
                $updateData['area_square_meters'] = $areaData['square_meters'];
                $updateData['area_acres'] = $areaData['acres'];
                $updateData['area_hectares'] = $areaData['hectares'];
                $updateData['perimeter_meters'] = $perimeter;
                $updateData['center_point'] = $centerPoint;
                $updateData['point_count'] = count($dto->coordinates);
            }
            
            if ($dto->type) {
                $updateData['type'] = $dto->type;
            }
            
            if ($dto->notes !== null) {
                $updateData['notes'] = $dto->notes;
            }
            
            if ($dto->isSynced !== null) {
                $updateData['is_synced'] = $dto->isSynced;
            }
            
            $measurement = $this->measurementRepository->update($measurement->id, $updateData);
            
            return $measurement;
        });
    }

    public function getMeasurements(array $filters = [])
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->measurementRepository->findByOrganization($organizationId, $filters);
    }

    public function getMeasurement(int $measurementId): Measurement
    {
        $user = Auth::user();
        $measurement = $this->measurementRepository->findById($measurementId);
        
        if ($measurement->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to measurement');
        }
        
        return $measurement;
    }

    public function getMeasurementsPaginated(array $filters = [], int $perPage = 15)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->measurementRepository->paginateByOrganization($organizationId, $filters, $perPage);
    }

    public function getMeasurementsByLand(int $landId)
    {
        $user = Auth::user();
        $land = $this->landRepository->findById($landId);
        
        if ($land->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to land');
        }
        
        return $this->measurementRepository->findByLand($landId);
    }

    public function createBatchMeasurements(array $measurementsData): array
    {
        $user = Auth::user();
        $created = [];
        $errors = [];
        
        foreach ($measurementsData as $index => $data) {
            try {
                $dto = CreateMeasurementDTO::fromArray($data);
                
                $land = $this->landRepository->findById($dto->landId);
                
                if ($land->organization_id !== $user->organization_id) {
                    throw new \Exception('Unauthorized access to land');
                }
                
                $areaData = $this->geoService->calculateArea($dto->coordinates);
                $centerPoint = $this->geoService->calculateCenterPoint($dto->coordinates);
                $perimeter = $this->geoService->calculatePerimeter($dto->coordinates);
                
                $startTime = Carbon::parse($dto->measurementStartedAt);
                $endTime = Carbon::parse($dto->measurementCompletedAt);
                $durationSeconds = $endTime->diffInSeconds($startTime);
                
                $measurementData = [
                    'land_id' => $dto->landId,
                    'user_id' => $user->id,
                    'organization_id' => $user->organization_id,
                    'type' => $dto->type,
                    'coordinates' => $dto->coordinates,
                    'area_square_meters' => $areaData['square_meters'],
                    'area_acres' => $areaData['acres'],
                    'area_hectares' => $areaData['hectares'],
                    'perimeter_meters' => $perimeter,
                    'center_point' => $centerPoint,
                    'point_count' => count($dto->coordinates),
                    'accuracy_meters' => $dto->accuracyMeters,
                    'measurement_started_at' => $startTime,
                    'measurement_completed_at' => $endTime,
                    'duration_seconds' => $durationSeconds,
                    'notes' => $dto->notes,
                    'is_synced' => $dto->isSynced,
                    'device_id' => $dto->deviceId,
                    'created_by' => $user->id,
                ];
                
                $measurement = $this->measurementRepository->create($measurementData);
                $created[] = $measurement;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'data' => $data,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return [
            'created' => $created,
            'errors' => $errors,
        ];
    }
}
