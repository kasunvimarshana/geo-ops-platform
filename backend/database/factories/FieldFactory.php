<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FieldFactory extends Factory
{
    protected $model = Field::class;

    public function definition(): array
    {
        // Create a simple GeoJSON polygon (square shape)
        $centerLat = $this->faker->latitude();
        $centerLng = $this->faker->longitude();
        $offset = 0.001; // Small offset for a small field

        $coordinates = [
            [$centerLng - $offset, $centerLat - $offset],
            [$centerLng + $offset, $centerLat - $offset],
            [$centerLng + $offset, $centerLat + $offset],
            [$centerLng - $offset, $centerLat + $offset],
            [$centerLng - $offset, $centerLat - $offset], // Close the polygon
        ];

        return [
            'name' => $this->faker->words(3, true),
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'boundary' => [
                'type' => 'Polygon',
                'coordinates' => [$coordinates]
            ],
            'area' => $this->faker->randomFloat(2, 0.5, 100),
            'perimeter' => $this->faker->randomFloat(2, 5, 200),
            'crop_type' => $this->faker->optional()->randomElement(['wheat', 'corn', 'rice', 'barley', 'vegetables']),
            'notes' => $this->faker->optional()->sentence(),
            'measurement_type' => $this->faker->randomElement(['walk_around', 'polygon', 'manual']),
            'measured_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
