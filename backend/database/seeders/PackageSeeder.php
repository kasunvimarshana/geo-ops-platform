<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Free plan for small operations',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'GPS tracking',
                    'Basic land measurement',
                    'Job management',
                ],
                'limits' => [
                    'jobs_per_month' => 0,
                    'users' => 2,
                    'land_plots' => 5,
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for growing businesses',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'GPS tracking',
                    'Advanced land measurement',
                    'Job management',
                    'Invoice generation',
                    'Expense tracking',
                    'Email support',
                ],
                'limits' => [
                    'jobs_per_month' => 50,
                    'users' => 10,
                    'land_plots' => 50,
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For professional operations',
                'price' => 99.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'GPS tracking',
                    'Advanced land measurement',
                    'Unlimited job management',
                    'Invoice generation',
                    'Expense tracking',
                    'Custom reports',
                    'API access',
                    'Priority support',
                ],
                'limits' => [
                    'jobs_per_month' => -1, // unlimited
                    'users' => -1, // unlimited
                    'land_plots' => -1, // unlimited
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $package) {
            Package::updateOrCreate(
                ['slug' => $package['slug']],
                $package
            );
        }
    }
}
