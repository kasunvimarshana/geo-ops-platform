<?php

namespace App\Utils;

class GeoCalculator
{
    /**
     * Calculate polygon area using Shoelace formula (Gauss's area formula)
     * 
     * @param array $coordinates Array of [lat, lng] pairs
     * @return array Area in square meters, acres, and hectares
     */
    public static function calculatePolygonArea(array $coordinates): array
    {
        if (count($coordinates) < 3) {
            throw new \InvalidArgumentException('At least 3 coordinates are required to calculate area');
        }

        // Earth's radius in meters
        $earthRadius = 6371000;

        // Convert lat/lng to radians and calculate area
        $area = 0;
        $numPoints = count($coordinates);

        for ($i = 0; $i < $numPoints; $i++) {
            $p1 = $coordinates[$i];
            $p2 = $coordinates[($i + 1) % $numPoints];

            $lat1 = deg2rad($p1[0]);
            $lat2 = deg2rad($p2[0]);
            $lng1 = deg2rad($p1[1]);
            $lng2 = deg2rad($p2[1]);

            $area += ($lng2 - $lng1) * (2 + sin($lat1) + sin($lat2));
        }

        $area = abs($area * $earthRadius * $earthRadius / 2);

        return [
            'area_sqm' => round($area, 2),
            'area_acres' => round($area * 0.000247105, 4),
            'area_hectares' => round($area * 0.0001, 4),
        ];
    }

    /**
     * Calculate distance between two points using Haversine formula
     * 
     * @param float $lat1 Latitude of first point
     * @param float $lng1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lng2 Longitude of second point
     * @return float Distance in meters
     */
    public static function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get center point (centroid) of a polygon
     * 
     * @param array $coordinates Array of [lat, lng] pairs
     * @return array Center point [lat, lng]
     */
    public static function getCentroid(array $coordinates): array
    {
        $lat = 0;
        $lng = 0;
        $count = count($coordinates);

        foreach ($coordinates as $coord) {
            $lat += $coord[0];
            $lng += $coord[1];
        }

        return [
            round($lat / $count, 6),
            round($lng / $count, 6),
        ];
    }

    /**
     * Convert coordinates to GeoJSON format
     * 
     * @param array $coordinates Array of [lat, lng] pairs
     * @return string GeoJSON string
     */
    public static function toGeoJSON(array $coordinates): string
    {
        // Close the polygon if not already closed
        // Use epsilon comparison to handle floating point precision
        $firstPoint = $coordinates[0];
        $lastPoint = $coordinates[count($coordinates) - 1];

        $epsilon = 0.0000001; // ~1cm precision for GPS coordinates
        $isClosed = abs($firstPoint[0] - $lastPoint[0]) < $epsilon && 
                    abs($firstPoint[1] - $lastPoint[1]) < $epsilon;

        if (!$isClosed) {
            $coordinates[] = $firstPoint;
        }

        // GeoJSON uses [lng, lat] order
        $swappedCoords = array_map(function ($coord) {
            return [$coord[1], $coord[0]];
        }, $coordinates);

        return json_encode([
            'type' => 'Polygon',
            'coordinates' => [$swappedCoords],
        ]);
    }
}
