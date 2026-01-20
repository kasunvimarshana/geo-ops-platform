<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $startsAt = $this->faker->dateTimeBetween('-6 months', 'now');
        $endsAt = (clone $startsAt)->modify('+1 year');

        return [
            'organization_id' => Organization::factory(),
            'plan_name' => $this->faker->randomElement(['Free', 'Basic', 'Premium', 'Enterprise']),
            'plan_type' => $this->faker->randomElement(['monthly', 'yearly']),
            'price' => $this->faker->randomFloat(2, 0, 999.99),
            'status' => $this->faker->randomElement(['active', 'inactive', 'cancelled', 'expired']),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'cancelled_at' => $this->faker->optional(0.1)->dateTimeBetween($startsAt, 'now'),
            'features' => [
                'max_fields' => $this->faker->numberBetween(10, 1000),
                'max_users' => $this->faker->numberBetween(1, 100),
                'api_access' => $this->faker->boolean(),
                'support_level' => $this->faker->randomElement(['basic', 'standard', 'premium']),
            ],
        ];
    }
}
