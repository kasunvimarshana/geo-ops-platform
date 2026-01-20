<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
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
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'address' => fake()->address(),
            'nic' => fake()->numerify('##########V'),
            'balance' => fake()->randomFloat(2, -10000, 10000),
        ];
    }

    /**
     * Indicate that the customer has a positive balance.
     */
    public function withBalance(): static
    {
        return $this->state(fn (array $attributes) => [
            'balance' => fake()->randomFloat(2, 0, 50000),
        ]);
    }

    /**
     * Indicate that the customer owes money.
     */
    public function withDebt(): static
    {
        return $this->state(fn (array $attributes) => [
            'balance' => fake()->randomFloat(2, -50000, -100),
        ]);
    }
}
