<?php

namespace App\Services;

use App\Models\LandMeasurement;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LandMeasurementService
{
    /**
     * Set coordinates using raw SQL for spatial data
     */
    private function setCoordinates(int $measurementId, array $coordinates): void
    {
        $coordinatesString = $this->formatCoordinatesForDB($coordinates);
        
        if (config('database.default') === 'mysql') {
            DB::statement(
                "UPDATE land_measurements SET coordinates = ST_GeomFromText(?) WHERE id = ?",
                [$coordinatesString, $measurementId]
            );
        } elseif (config('database.default') === 'pgsql') {
            DB::statement(
                "UPDATE land_measurements SET coordinates = ST_GeomFromText(?, 4326) WHERE id = ?",
                [$coordinatesString, $measurementId]
            );
        }
    }

    /**
     * Create a new land measurement
     */
    public function create(Organization $organization, array $data): LandMeasurement
    {
        DB::beginTransaction();
        try {
            $measurement = LandMeasurement::create([
                'organization_id' => $organization->id,
                'name' => $data['name'],
                'area_acres' => $data['area_acres'],
                'area_hectares' => $data['area_hectares'],
                'measured_by' => $data['measured_by'],
                'measured_at' => $data['measured_at'] ?? now(),
            ]);

            $this->setCoordinates($measurement->id, $data['coordinates']);

            DB::commit();
            return $measurement->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create land measurement', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update land measurement
     */
    public function update(LandMeasurement $measurement, array $data): LandMeasurement
    {
        DB::beginTransaction();
        try {
            $measurement->update([
                'name' => $data['name'] ?? $measurement->name,
                'area_acres' => $data['area_acres'] ?? $measurement->area_acres,
                'area_hectares' => $data['area_hectares'] ?? $measurement->area_hectares,
            ]);

            if (isset($data['coordinates'])) {
                $this->setCoordinates($measurement->id, $data['coordinates']);
            }

            DB::commit();
            return $measurement->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update land measurement', [
                'measurement_id' => $measurement->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Format coordinates for database storage
     */
    private function formatCoordinatesForDB(array $coordinates): string
    {
        $points = [];
        foreach ($coordinates as $coord) {
            $points[] = "{$coord['longitude']} {$coord['latitude']}";
        }
        
        // Close the polygon by adding the first point at the end
        if (count($points) > 0) {
            $points[] = $points[0];
        }
        
        return 'POLYGON((' . implode(',', $points) . '))';
    }

    /**
     * Calculate area from coordinates
     */
    public function calculateArea(array $coordinates): array
    {
        $areaSquareMeters = $this->calculatePolygonArea($coordinates);
        
        return [
            'square_meters' => $areaSquareMeters,
            'acres' => $areaSquareMeters / 4046.86,
            'hectares' => $areaSquareMeters / 10000,
        ];
    }

    /**
     * Calculate polygon area using Shoelace formula
     */
    private function calculatePolygonArea(array $coordinates): float
    {
        // Validate minimum unique points
        if (count($coordinates) < 3) {
            return 0;
        }

        // Check for unique points
        $uniquePoints = array_unique($coordinates, SORT_REGULAR);
        if (count($uniquePoints) < 3) {
            return 0;
        }

        $earthRadius = 6378137; // meters
        $area = 0;

        for ($i = 0; $i < count($coordinates); $i++) {
            $j = ($i + 1) % count($coordinates);
            $xi = $coordinates[$i]['longitude'] * M_PI / 180;
            $yi = $coordinates[$i]['latitude'] * M_PI / 180;
            $xj = $coordinates[$j]['longitude'] * M_PI / 180;
            $yj = $coordinates[$j]['latitude'] * M_PI / 180;

            $area += ($xj - $xi) * (2 + sin($yi) + sin($yj));
        }

        $area = abs($area * $earthRadius * $earthRadius / 2);
        return $area;
    }
}
