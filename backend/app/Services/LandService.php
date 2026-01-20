<?php

namespace App\Services;

use App\Repositories\Contracts\LandRepositoryInterface;
use App\DTOs\Land\CreateLandDTO;
use App\DTOs\Land\UpdateLandDTO;
use App\Models\Land;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Land Service
 * 
 * Handles all business logic related to land management.
 * Follows Clean Architecture principles with clear separation of concerns.
 */
class LandService
{
    public function __construct(
        private LandRepositoryInterface $landRepository,
        private GeoCalculationService $geoService
    ) {}

    /**
     * Create a new land record
     * 
     * @param CreateLandDTO $dto
     * @return Land
     * @throws \Exception
     */
    public function createLand(CreateLandDTO $dto): Land
    {
        return DB::transaction(function () use ($dto) {
            $user = Auth::user();
            
            // Calculate area from coordinates
            $areaData = $this->geoService->calculateArea($dto->coordinates);
            
            // Calculate center point
            $centerPoint = $this->geoService->calculateCenterPoint($dto->coordinates);
            
            // Prepare land data
            $landData = [
                'organization_id' => $user->organization_id,
                'owner_user_id' => $dto->ownerUserId ?? $user->id,
                'name' => $dto->name,
                'description' => $dto->description,
                'coordinates' => $dto->coordinates,
                'area_acres' => $areaData['acres'],
                'area_hectares' => $areaData['hectares'],
                'area_square_meters' => $areaData['square_meters'],
                'center_latitude' => $centerPoint['latitude'],
                'center_longitude' => $centerPoint['longitude'],
                'location_address' => $dto->locationAddress,
                'location_district' => $dto->locationDistrict,
                'location_province' => $dto->locationProvince,
                'status' => 'active',
                'created_by' => $user->id,
            ];
            
            $land = $this->landRepository->create($landData);
            
            return $land;
        });
    }

    /**
     * Update an existing land record
     * 
     * @param int $landId
     * @param UpdateLandDTO $dto
     * @return Land
     * @throws \Exception
     */
    public function updateLand(int $landId, UpdateLandDTO $dto): Land
    {
        return DB::transaction(function () use ($landId, $dto) {
            $user = Auth::user();
            
            $land = $this->landRepository->findById($landId);
            
            // Ensure land belongs to user's organization
            if ($land->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to land');
            }
            
            $updateData = [
                'updated_by' => $user->id,
            ];
            
            // Update coordinates and recalculate if provided
            if ($dto->coordinates) {
                $areaData = $this->geoService->calculateArea($dto->coordinates);
                $centerPoint = $this->geoService->calculateCenterPoint($dto->coordinates);
                
                $updateData['coordinates'] = $dto->coordinates;
                $updateData['area_acres'] = $areaData['acres'];
                $updateData['area_hectares'] = $areaData['hectares'];
                $updateData['area_square_meters'] = $areaData['square_meters'];
                $updateData['center_latitude'] = $centerPoint['latitude'];
                $updateData['center_longitude'] = $centerPoint['longitude'];
            }
            
            // Update other fields if provided
            if ($dto->name) {
                $updateData['name'] = $dto->name;
            }
            
            if ($dto->description !== null) {
                $updateData['description'] = $dto->description;
            }
            
            if ($dto->status) {
                $updateData['status'] = $dto->status;
            }
            
            $land = $this->landRepository->update($land->id, $updateData);
            
            return $land;
        });
    }

    /**
     * Get all lands for the current organization
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getLands(array $filters = [])
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->landRepository->findByOrganization($organizationId, $filters);
    }

    /**
     * Get a specific land by ID
     * 
     * @param int $landId
     * @return Land
     * @throws \Exception
     */
    public function getLand(int $landId): Land
    {
        $user = Auth::user();
        $land = $this->landRepository->findById($landId);
        
        // Ensure land belongs to user's organization
        if ($land->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to land');
        }
        
        return $land;
    }

    /**
     * Delete a land (soft delete)
     * 
     * @param int $landId
     * @return bool
     * @throws \Exception
     */
    public function deleteLand(int $landId): bool
    {
        $user = Auth::user();
        $land = $this->landRepository->findById($landId);
        
        // Ensure land belongs to user's organization
        if ($land->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to land');
        }
        
        // Check if land has active field jobs
        if ($land->fieldJobs()->where('status', '!=', 'completed')->exists()) {
            throw new \Exception('Cannot delete land with active jobs');
        }
        
        $result = $this->landRepository->delete($land->id);
        
        return $result;
    }

    /**
     * Get lands with pagination
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getLandsPaginated(array $filters = [], int $perPage = 15)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->landRepository->paginateByOrganization($organizationId, $filters, $perPage);
    }
}
