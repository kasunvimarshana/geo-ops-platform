<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create user first without organization, then assign organization later
        return [
            'name' => fake()->company(),
            'owner_id' => User::factory()->state(['organization_id' => null]),
            'subscription_package' => fake()->randomElement(['free', 'basic', 'pro']),
            'subscription_expires_at' => now()->addMonths(fake()->numberBetween(1, 12)),
            'settings' => [
                'currency' => 'LKR',
                'language' => 'en',
                'timezone' => 'Asia/Colombo',
            ],
        ];
    }

    /**
     * Configure the factory to handle circular dependencies properly.
     */
    public function configure()
    {
        return $this->afterCreating(function (Organization $organization) {
            // Update the owner's organization_id to this organization
            if ($organization->owner_id) {
                User::where('id', $organization->owner_id)
                    ->update(['organization_id' => $organization->id]);
            }
        });
    }

    /**
     * Indicate that the organization has a free subscription.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_package' => 'free',
            'subscription_expires_at' => now()->addMonth(),
        ]);
    }

    /**
     * Indicate that the organization has a basic subscription.
     */
    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_package' => 'basic',
            'subscription_expires_at' => now()->addMonths(6),
        ]);
    }

    /**
     * Indicate that the organization has a pro subscription.
     */
    public function pro(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_package' => 'pro',
            'subscription_expires_at' => now()->addYear(),
        ]);
    }

    /**
     * Indicate that the subscription has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_expires_at' => now()->subMonth(),
        ]);
    }
}
