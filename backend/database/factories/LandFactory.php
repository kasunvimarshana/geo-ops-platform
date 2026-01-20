<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use App\Models\Land;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LandFactory extends Factory
{
    protected $model = Land::class;

    public function definition(): array
    {
        $areaAcres = fake()->randomFloat(4, 0.5, 50);
        $areaHectares = $areaAcres * 0.404686;

        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->streetName() . ' Land',
            'description' => fake()->sentence(),
            'area_acres' => $areaAcres,
            'area_hectares' => round($areaHectares, 4),
            'measurement_type' => fake()->randomElement(['walk-around', 'point-based']),
            'location_name' => fake()->city(),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'measured_by' => User::factory(),
            'measured_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'status' => fake()->randomElement(['draft', 'confirmed', 'archived']),
            'sync_status' => 'synced',
            'offline_id' => null,
        ];
    }
}
