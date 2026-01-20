<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

class MachineFactory extends Factory
{
    protected $model = Machine::class;

    public function definition(): array
    {
        $types = ['Tractor', 'Harvester', 'Plough', 'Rotavator', 'Sprayer'];
        $type = fake()->randomElement($types);

        return [
            'organization_id' => Organization::factory(),
            'name' => $type . ' ' . fake()->numberBetween(100, 999),
            'machine_type' => $type,
            'registration_number' => fake()->optional()->bothify('??-####'),
            'description' => fake()->optional()->sentence(),
            'rate_per_acre' => fake()->randomFloat(2, 1000, 10000),
            'rate_per_hectare' => fake()->randomFloat(2, 2500, 25000),
            'is_active' => true,
        ];
    }
}
