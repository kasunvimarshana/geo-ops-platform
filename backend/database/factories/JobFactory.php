<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'assigned_to' => $this->faker->numberBetween(1, 10), // Assuming there are 10 drivers
            'land_id' => $this->faker->numberBetween(1, 50), // Assuming there are 50 lands
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}