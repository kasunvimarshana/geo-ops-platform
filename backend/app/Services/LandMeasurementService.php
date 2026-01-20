<?php

namespace App\Services;

use App\Repositories\Interfaces\LandRepositoryInterface;
use App\DTOs\LandMeasurementDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Land Measurement Service
 * 
 * Business logic for GPS-based land measurement
 * Follows SOLID principles and Clean Architecture
 */
class LandMeasurementService
{
    public function __construct(
        private LandRepositoryInterface $landRepository
    ) {}

    /**
     * Create a new land measurement
     * Calculates area from GPS polygon coordinates
     */
    public function createMeasurement(LandMeasurementDTO $dto, int $userId, int $organizationId): array
    {
        DB::beginTransaction();
        
        try {
            // Calculate area from polygon coordinates
            $areaAcres = $this->calculateAreaInAcres($dto->polygon);
            $areaHectares = $this->acrestoHectares($areaAcres);
            
            // Create polygon geometry for spatial queries
            $polygonWKT = $this->createPolygonWKT($dto->polygon);
            
            $landData = [
                'organization_id' => $organizationId,
                'name' => $dto->name,
                'description' => $dto->description,
                'polygon' => DB::raw("ST_GeomFromText('$polygonWKT', 4326)"),
                'area_acres' => $areaAcres,
                'area_hectares' => $areaHectares,
                'measurement_type' => $dto->measurementType,
                'location_name' => $dto->locationName,
                'customer_name' => $dto->customerName,
                'customer_phone' => $dto->customerPhone,
                'measured_by' => $userId,
                'measured_at' => $dto->measuredAt ?? now(),
                'status' => 'confirmed',
                'sync_status' => $dto->offlineId ? 'synced' : 'synced',
                'offline_id' => $dto->offlineId,
            ];
            
            $land = $this->landRepository->create($landData);
            
            // Save individual measurement points
            foreach ($dto->polygon as $index => $point) {
                $land->measurementPoints()->create([
                    'latitude' => $point['latitude'],
                    'longitude' => $point['longitude'],
                    'altitude' => $point['altitude'] ?? null,
                    'accuracy' => $point['accuracy'],
                    'sequence' => $index,
                    'recorded_at' => $point['recorded_at'] ?? now(),
                ]);
            }
            
            DB::commit();
            
            return [
                'id' => $land->id,
                'name' => $land->name,
                'area_acres' => $land->area_acres,
                'area_hectares' => $land->area_hectares,
                'status' => $land->status,
                'polygon' => $dto->polygon,
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calculate area in acres using Shoelace formula
     * For GPS coordinates (assuming relatively small areas)
     */
    private function calculateAreaInAcres(array $polygon): float
    {
        if (count($polygon) < 3) {
            return 0;
        }
        
        $area = 0;
        $n = count($polygon);
        
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            
            $lat1 = deg2rad($polygon[$i]['latitude']);
            $lon1 = deg2rad($polygon[$i]['longitude']);
            $lat2 = deg2rad($polygon[$j]['latitude']);
            $lon2 = deg2rad($polygon[$j]['longitude']);
            
            // Convert to meters using Haversine
            $x1 = $lon1 * cos($lat1);
            $y1 = $lat1;
            $x2 = $lon2 * cos($lat2);
            $y2 = $lat2;
            
            $area += ($x1 * $y2 - $x2 * $y1);
        }
        
        $area = abs($area / 2);
        
        // Convert from square radians to square meters
        // Earth radius in meters
        $earthRadius = 6371000;
        $areaSquareMeters = $area * $earthRadius * $earthRadius;
        
        // Convert to acres (1 acre = 4046.86 square meters)
        $areaAcres = $areaSquareMeters / 4046.86;
        
        return round($areaAcres, 4);
    }

    /**
     * Convert acres to hectares
     */
    private function acrestoHectares(float $acres): float
    {
        // 1 acre = 0.404686 hectares
        return round($acres * 0.404686, 4);
    }

    /**
     * Create WKT polygon string for database storage
     */
    private function createPolygonWKT(array $polygon): string
    {
        $coordinates = array_map(function($point) {
            return "{$point['longitude']} {$point['latitude']}";
        }, $polygon);
        
        // Close the polygon by adding the first point at the end
        $coordinates[] = "{$polygon[0]['longitude']} {$polygon[0]['latitude']}";
        
        $coordinateString = implode(', ', $coordinates);
        return "POLYGON(($coordinateString))";
    }

    /**
     * Get measurements within radius of a point
     */
    public function getNearbyLands(float $latitude, float $longitude, int $radiusMeters, int $organizationId): array
    {
        return $this->landRepository->findNearby($latitude, $longitude, $radiusMeters, $organizationId);
    }

    /**
     * Update land measurement
     */
    public function updateMeasurement(int $id, LandMeasurementDTO $dto, int $organizationId): array
    {
        $land = $this->landRepository->findByIdAndOrganization($id, $organizationId);
        
        if (!$land) {
            throw new \Exception('Land measurement not found');
        }
        
        DB::beginTransaction();
        
        try {
            // Recalculate area if polygon changed
            if ($dto->polygon) {
                $areaAcres = $this->calculateAreaInAcres($dto->polygon);
                $areaHectares = $this->acrestoHectares($areaAcres);
                $polygonWKT = $this->createPolygonWKT($dto->polygon);
                
                $land->update([
                    'polygon' => DB::raw("ST_GeomFromText('$polygonWKT', 4326)"),
                    'area_acres' => $areaAcres,
                    'area_hectares' => $areaHectares,
                ]);
                
                // Update measurement points
                $land->measurementPoints()->delete();
                foreach ($dto->polygon as $index => $point) {
                    $land->measurementPoints()->create([
                        'latitude' => $point['latitude'],
                        'longitude' => $point['longitude'],
                        'altitude' => $point['altitude'] ?? null,
                        'accuracy' => $point['accuracy'],
                        'sequence' => $index,
                        'recorded_at' => $point['recorded_at'] ?? now(),
                    ]);
                }
            }
            
            // Update other fields
            $land->update([
                'name' => $dto->name ?? $land->name,
                'description' => $dto->description ?? $land->description,
                'location_name' => $dto->locationName ?? $land->location_name,
                'customer_name' => $dto->customerName ?? $land->customer_name,
                'customer_phone' => $dto->customerPhone ?? $land->customer_phone,
            ]);
            
            DB::commit();
            
            return [
                'id' => $land->id,
                'name' => $land->name,
                'area_acres' => $land->area_acres,
                'area_hectares' => $land->area_hectares,
                'status' => $land->status,
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
