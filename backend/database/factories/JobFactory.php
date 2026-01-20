<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\Organization;
use App\Models\Field;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'organization_id' => Organization::factory(),
            'field_id' => Field::factory(),
            'created_by' => User::factory(),
            'assigned_to' => $this->faker->optional()->randomElement([User::factory(), null]),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+3 months'),
            'started_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'location' => [
                'type' => 'Point',
                'coordinates' => [$this->faker->longitude(), $this->faker->latitude()]
            ],
        ];
    }
}
