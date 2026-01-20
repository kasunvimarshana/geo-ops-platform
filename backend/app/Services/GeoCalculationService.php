<?php

namespace App\Services;

/**
 * Geo Calculation Service
 * 
 * Handles all GPS and geographical calculations.
 * Uses the Haversine formula for accurate area and distance calculations.
 */
class GeoCalculationService
{
    private const EARTH_RADIUS_METERS = 6371000; // Earth's radius in meters
    private const SQUARE_METERS_PER_ACRE = 4046.86;
    private const SQUARE_METERS_PER_HECTARE = 10000;

    /**
     * Calculate area from an array of GPS coordinates
     * 
     * @param array $coordinates Array of ['lat' => float, 'lng' => float]
     * @return array ['acres' => float, 'hectares' => float, 'square_meters' => float]
     */
    public function calculateArea(array $coordinates): array
    {
        if (count($coordinates) < 3) {
            throw new \InvalidArgumentException('At least 3 coordinates are required to calculate area');
        }

        // Calculate area in square meters using the Shoelace formula
        $areaSquareMeters = $this->calculatePolygonAreaInSquareMeters($coordinates);
        
        // Convert to acres and hectares
        $acres = $areaSquareMeters / self::SQUARE_METERS_PER_ACRE;
        $hectares = $areaSquareMeters / self::SQUARE_METERS_PER_HECTARE;

        return [
            'square_meters' => round($areaSquareMeters, 2),
            'acres' => round($acres, 4),
            'hectares' => round($hectares, 4),
        ];
    }

    /**
     * Calculate the area of a polygon in square meters
     * Uses the Shoelace formula (also known as the surveyor's formula)
     * 
     * @param array $coordinates
     * @return float
     */
    private function calculatePolygonAreaInSquareMeters(array $coordinates): float
    {
        $n = count($coordinates);
        $area = 0;

        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            
            $lat1 = $coordinates[$i]['lat'];
            $lng1 = $coordinates[$i]['lng'];
            $lat2 = $coordinates[$j]['lat'];
            $lng2 = $coordinates[$j]['lng'];

            // Convert to radians
            $lat1Rad = deg2rad($lat1);
            $lat2Rad = deg2rad($lat2);
            $lng1Rad = deg2rad($lng1);
            $lng2Rad = deg2rad($lng2);

            $area += ($lng2Rad - $lng1Rad) * (2 + sin($lat1Rad) + sin($lat2Rad));
        }

        $area = abs($area * self::EARTH_RADIUS_METERS * self::EARTH_RADIUS_METERS / 2);

        return $area;
    }

    /**
     * Calculate the center point (centroid) of a polygon
     * 
     * @param array $coordinates
     * @return array ['latitude' => float, 'longitude' => float]
     */
    public function calculateCenterPoint(array $coordinates): array
    {
        if (empty($coordinates)) {
            throw new \InvalidArgumentException('Coordinates array cannot be empty');
        }

        $latSum = 0;
        $lngSum = 0;
        $count = count($coordinates);

        foreach ($coordinates as $coord) {
            $latSum += $coord['lat'];
            $lngSum += $coord['lng'];
        }

        return [
            'latitude' => round($latSum / $count, 7),
            'longitude' => round($lngSum / $count, 7),
        ];
    }

    /**
     * Calculate distance between two GPS points in meters
     * Uses the Haversine formula
     * 
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Distance in meters
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLngRad = deg2rad($lng2 - $lng1);

        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLngRad / 2) * sin($deltaLngRad / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return self::EARTH_RADIUS_METERS * $c;
    }

    /**
     * Calculate the perimeter of a polygon in meters
     * 
     * @param array $coordinates
     * @return float
     */
    public function calculatePerimeter(array $coordinates): float
    {
        if (count($coordinates) < 2) {
            return 0;
        }

        $perimeter = 0;
        $n = count($coordinates);

        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            
            $distance = $this->calculateDistance(
                $coordinates[$i]['lat'],
                $coordinates[$i]['lng'],
                $coordinates[$j]['lat'],
                $coordinates[$j]['lng']
            );
            
            $perimeter += $distance;
        }

        return round($perimeter, 2);
    }

    /**
     * Validate if coordinates form a valid polygon
     * 
     * @param array $coordinates
     * @return bool
     */
    public function isValidPolygon(array $coordinates): bool
    {
        // Must have at least 3 points
        if (count($coordinates) < 3) {
            return false;
        }

        // Check if all coordinates have valid lat/lng
        foreach ($coordinates as $coord) {
            if (!isset($coord['lat']) || !isset($coord['lng'])) {
                return false;
            }

            $lat = $coord['lat'];
            $lng = $coord['lng'];

            // Validate latitude (-90 to 90)
            if ($lat < -90 || $lat > 90) {
                return false;
            }

            // Validate longitude (-180 to 180)
            if ($lng < -180 || $lng > 180) {
                return false;
            }
        }

        // Check if polygon is closed (first and last points are the same or close)
        $first = $coordinates[0];
        $last = $coordinates[count($coordinates) - 1];
        
        $distance = $this->calculateDistance(
            $first['lat'],
            $first['lng'],
            $last['lat'],
            $last['lng']
        );

        // If distance > 10 meters, polygon is not closed
        if ($distance > 10) {
            return false;
        }

        return true;
    }

    /**
     * Convert acres to other units
     * 
     * @param float $acres
     * @return array
     */
    public function convertAcres(float $acres): array
    {
        return [
            'acres' => round($acres, 4),
            'hectares' => round($acres * 0.404686, 4),
            'square_meters' => round($acres * self::SQUARE_METERS_PER_ACRE, 2),
            'square_feet' => round($acres * 43560, 2),
        ];
    }

    /**
     * Convert square meters to other units
     * 
     * @param float $squareMeters
     * @return array
     */
    public function convertSquareMeters(float $squareMeters): array
    {
        return [
            'square_meters' => round($squareMeters, 2),
            'acres' => round($squareMeters / self::SQUARE_METERS_PER_ACRE, 4),
            'hectares' => round($squareMeters / self::SQUARE_METERS_PER_HECTARE, 4),
            'square_feet' => round($squareMeters * 10.7639, 2),
        ];
    }
}
