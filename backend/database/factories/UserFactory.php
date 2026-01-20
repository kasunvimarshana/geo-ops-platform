<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'role' => 'owner',
            'organization_id' => null, // Will be set after creation if needed
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is an owner.
     */
    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'owner',
        ]);
    }

    /**
     * Indicate that the user is a driver.
     */
    public function driver(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'driver',
        ]);
    }

    /**
     * Indicate that the user is a broker.
     */
    public function broker(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'broker',
        ]);
    }

    /**
     * Indicate that the user is an accountant.
     */
    public function accountant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'accountant',
        ]);
    }
}
