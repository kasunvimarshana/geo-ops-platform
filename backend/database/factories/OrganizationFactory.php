<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'address' => $this->faker->optional()->address(),
            'type' => $this->faker->randomElement(['farm', 'service_provider', 'cooperative']),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}
