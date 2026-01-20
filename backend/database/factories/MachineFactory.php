<?php

namespace Database\Factories;

use App\Models\Machine;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Machine>
 */
class MachineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['tractor', 'harvester', 'plough', 'seeder'];
        
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->word() . ' ' . fake()->word(),
            'type' => fake()->randomElement($types),
            'registration_number' => fake()->optional()->regexify('[A-Z]{2}-[0-9]{4}'),
            'model' => fake()->word() . ' ' . fake()->numberBetween(100, 999),
            'year' => fake()->year(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the machine is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the machine is a tractor.
     */
    public function tractor(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'tractor',
        ]);
    }

    /**
     * Indicate that the machine is a harvester.
     */
    public function harvester(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'harvester',
        ]);
    }
}
