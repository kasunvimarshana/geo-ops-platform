<?php

namespace Database\Factories;

use App\Models\LandMeasurement;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LandMeasurement>
 */
class LandMeasurementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a simple rectangular area coordinates
        $baseLat = fake()->latitude(6.0, 10.0); // Sri Lanka latitude range
        $baseLng = fake()->longitude(79.5, 82.0); // Sri Lanka longitude range
        
        $deltaLat = fake()->randomFloat(4, 0.001, 0.01);
        $deltaLng = fake()->randomFloat(4, 0.001, 0.01);
        
        // Create a rectangular polygon
        $coordinates = [
            ['latitude' => $baseLat, 'longitude' => $baseLng],
            ['latitude' => $baseLat + $deltaLat, 'longitude' => $baseLng],
            ['latitude' => $baseLat + $deltaLat, 'longitude' => $baseLng + $deltaLng],
            ['latitude' => $baseLat, 'longitude' => $baseLng + $deltaLng],
        ];
        
        // Calculate area in square meters using simplified formula
        $areaInSqMeters = fake()->randomFloat(2, 4046.86, 40468.6); // 1 to 10 acres
        $areaInAcres = $areaInSqMeters / 4046.86;
        $areaInHectares = $areaInSqMeters / 10000;
        
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->streetName() . ' Field',
            'coordinates' => json_encode($coordinates),
            'area_acres' => round($areaInAcres, 4),
            'area_hectares' => round($areaInHectares, 4),
            'measured_by' => User::factory(),
            'measured_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the measurement is for a small plot.
     */
    public function small(): static
    {
        return $this->state(function (array $attributes) {
            $areaInSqMeters = fake()->randomFloat(2, 1000, 4046.86); // Less than 1 acre
            return [
                'area_acres' => round($areaInSqMeters / 4046.86, 4),
                'area_hectares' => round($areaInSqMeters / 10000, 4),
            ];
        });
    }

    /**
     * Indicate that the measurement is for a large plot.
     */
    public function large(): static
    {
        return $this->state(function (array $attributes) {
            $areaInSqMeters = fake()->randomFloat(2, 40468.6, 404686); // 10 to 100 acres
            return [
                'area_acres' => round($areaInSqMeters / 4046.86, 4),
                'area_hectares' => round($areaInSqMeters / 10000, 4),
            ];
        });
    }
}
