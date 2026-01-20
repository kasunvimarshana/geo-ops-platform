<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\Organization;
use App\Models\Customer;
use App\Models\LandMeasurement;
use App\Models\Driver;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceTypes = ['ploughing', 'harrowing', 'seeding', 'harvesting', 'leveling'];
        
        return [
            'organization_id' => Organization::factory(),
            'customer_id' => Customer::factory(),
            'land_measurement_id' => LandMeasurement::factory(),
            'driver_id' => Driver::factory(),
            'machine_id' => Machine::factory(),
            'service_type' => fake()->randomElement($serviceTypes),
            'status' => Job::STATUS_PENDING,
            'invoice_generated' => false,
            'scheduled_at' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'started_at' => null,
            'completed_at' => null,
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the job is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_PENDING,
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the job is assigned.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_ASSIGNED,
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the job is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_IN_PROGRESS,
            'started_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the job is completed.
     */
    public function completed(): static
    {
        $startedAt = fake()->dateTimeBetween('-1 month', '-1 week');
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_COMPLETED,
            'started_at' => $startedAt,
            'completed_at' => fake()->dateTimeBetween($startedAt, 'now'),
        ]);
    }

    /**
     * Indicate that the job is billed.
     */
    public function billed(): static
    {
        $startedAt = fake()->dateTimeBetween('-2 months', '-1 month');
        $completedAt = fake()->dateTimeBetween($startedAt, '-1 week');
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_BILLED,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'invoice_generated' => true,
        ]);
    }

    /**
     * Indicate that the job is paid.
     */
    public function paid(): static
    {
        $startedAt = fake()->dateTimeBetween('-3 months', '-2 months');
        $completedAt = fake()->dateTimeBetween($startedAt, '-1 month');
        return $this->state(fn (array $attributes) => [
            'status' => Job::STATUS_PAID,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'invoice_generated' => true,
        ]);
    }
}
