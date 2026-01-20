<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 10, 5000);
        $tax = $subtotal * 0.1; // 10% tax
        $discount = $this->faker->optional(0.3, 0)->randomFloat(2, 0, $subtotal * 0.2);
        $total = $subtotal + $tax - $discount;

        $issuedAt = $this->faker->dateTimeBetween('-6 months', 'now');
        $dueDate = (clone $issuedAt)->modify('+30 days');

        return [
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('######'),
            'organization_id' => Organization::factory(),
            'subscription_id' => $this->faker->optional(0.6)->randomElement([Subscription::factory(), null]),
            'job_id' => $this->faker->optional(0.4)->randomElement([Job::factory(), null]),
            'type' => $this->faker->randomElement(['subscription', 'service', 'one_time']),
            'description' => $this->faker->optional()->sentence(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'status' => $this->faker->randomElement(['pending', 'paid', 'overdue', 'cancelled']),
            'payment_method' => $this->faker->optional()->randomElement(['credit_card', 'bank_transfer', 'paypal', 'cash']),
            'issued_at' => $issuedAt,
            'due_date' => $dueDate,
            'paid_at' => $this->faker->optional(0.7)->dateTimeBetween($issuedAt, 'now'),
            'items' => [
                [
                    'description' => $this->faker->sentence(3),
                    'quantity' => $this->faker->numberBetween(1, 10),
                    'unit_price' => $this->faker->randomFloat(2, 5, 500),
                    'total' => $this->faker->randomFloat(2, 5, 500),
                ]
            ],
        ];
    }
}
