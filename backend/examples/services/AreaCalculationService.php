<?php

namespace App\Services\Measurement;

/**
 * Area Calculation Service
 * 
 * Calculates area, perimeter, and center point from GPS polygon coordinates.
 * Uses Haversine formula for accurate geographic calculations.
 */
class AreaCalculationService
{
    private const EARTH_RADIUS_METERS = 6371000;
    private const SQUARE_METERS_PER_ACRE = 4046.86;
    private const SQUARE_METERS_PER_HECTARE = 10000;

    /**
     * Calculate area, perimeter, and center from polygon points
     * 
     * @param array $points Array of ['latitude' => float, 'longitude' => float]
     * @return array
     */
    public function calculate(array $points): array
    {
        if (count($points) < 3) {
            throw new \InvalidArgumentException('At least 3 points required to form a polygon');
        }

        $areaSquareMeters = $this->calculateAreaInSquareMeters($points);
        $perimeterMeters = $this->calculatePerimeter($points);
        $center = $this->calculateCenter($points);

        return [
            'area_square_meters' => $areaSquareMeters,
            'area_acres' => $areaSquareMeters / self::SQUARE_METERS_PER_ACRE,
            'area_hectares' => $areaSquareMeters / self::SQUARE_METERS_PER_HECTARE,
            'perimeter_meters' => $perimeterMeters,
            'center_latitude' => $center['latitude'],
            'center_longitude' => $center['longitude'],
        ];
    }

    /**
     * Calculate area using Shoelace formula with Haversine correction
     * 
     * @param array $points
     * @return float Area in square meters
     */
    private function calculateAreaInSquareMeters(array $points): float
    {
        $n = count($points);
        $area = 0;

        // Convert to radians and calculate using spherical excess
        for ($i = 0; $i < $n; $i++) {
            $p1 = $points[$i];
            $p2 = $points[($i + 1) % $n];

            $lat1 = deg2rad($p1['latitude']);
            $lon1 = deg2rad($p1['longitude']);
            $lat2 = deg2rad($p2['latitude']);
            $lon2 = deg2rad($p2['longitude']);

            $area += ($lon2 - $lon1) * (2 + sin($lat1) + sin($lat2));
        }

        $area = abs($area * self::EARTH_RADIUS_METERS * self::EARTH_RADIUS_METERS / 2.0);

        return $area;
    }

    /**
     * Calculate perimeter using Haversine distance formula
     * 
     * @param array $points
     * @return float Perimeter in meters
     */
    private function calculatePerimeter(array $points): float
    {
        $n = count($points);
        $perimeter = 0;

        for ($i = 0; $i < $n; $i++) {
            $p1 = $points[$i];
            $p2 = $points[($i + 1) % $n];

            $perimeter += $this->haversineDistance(
                $p1['latitude'],
                $p1['longitude'],
                $p2['latitude'],
                $p2['longitude']
            );
        }

        return $perimeter;
    }

    /**
     * Calculate geographic center (centroid) of polygon
     * 
     * @param array $points
     * @return array ['latitude' => float, 'longitude' => float]
     */
    private function calculateCenter(array $points): array
    {
        $n = count($points);
        $x = $y = $z = 0;

        foreach ($points as $point) {
            $lat = deg2rad($point['latitude']);
            $lon = deg2rad($point['longitude']);

            $x += cos($lat) * cos($lon);
            $y += cos($lat) * sin($lon);
            $z += sin($lat);
        }

        $x /= $n;
        $y /= $n;
        $z /= $n;

        $centerLon = atan2($y, $x);
        $centerHyp = sqrt($x * $x + $y * $y);
        $centerLat = atan2($z, $centerHyp);

        return [
            'latitude' => rad2deg($centerLat),
            'longitude' => rad2deg($centerLon),
        ];
    }

    /**
     * Calculate distance between two GPS points using Haversine formula
     * 
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in meters
     */
    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }
}
