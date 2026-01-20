<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory()->driver(),
            'license_number' => fake()->numerify('DL########'),
            'license_expiry' => fake()->dateTimeBetween('now', '+5 years'),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the driver is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the license is expired.
     */
    public function expiredLicense(): static
    {
        return $this->state(fn (array $attributes) => [
            'license_expiry' => fake()->dateTimeBetween('-2 years', '-1 day'),
        ]);
    }
}
