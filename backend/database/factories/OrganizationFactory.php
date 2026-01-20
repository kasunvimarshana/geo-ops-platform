<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->company();
        
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'subscription_package' => fake()->randomElement(['free', 'basic', 'pro']),
            'subscription_expires_at' => fake()->dateTimeBetween('now', '+1 year'),
            'status' => fake()->randomElement(['active', 'suspended', 'cancelled']),
            'settings' => [
                'currency' => 'LKR',
                'timezone' => 'Asia/Colombo',
                'date_format' => 'Y-m-d',
                'default_language' => fake()->randomElement(['en', 'si']),
            ],
        ];
    }
}
