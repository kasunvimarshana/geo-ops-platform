<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'System administrator with full access to all features',
                'permissions' => [
                    'manage_users',
                    'manage_roles',
                    'manage_organization',
                    'manage_subscription',
                    'view_reports',
                    'manage_lands',
                    'manage_machines',
                    'manage_jobs',
                    'manage_invoices',
                    'manage_expenses',
                    'manage_payments',
                ],
            ],
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'description' => 'Organization owner with access to business operations',
                'permissions' => [
                    'view_dashboard',
                    'view_reports',
                    'manage_lands',
                    'manage_machines',
                    'manage_jobs',
                    'manage_invoices',
                    'manage_expenses',
                    'manage_payments',
                    'view_users',
                ],
            ],
            [
                'name' => 'Driver',
                'slug' => 'driver',
                'description' => 'Equipment driver with access to job management',
                'permissions' => [
                    'view_dashboard',
                    'view_jobs',
                    'update_job_status',
                    'record_job_tracking',
                    'view_machines',
                    'record_expenses',
                ],
            ],
            [
                'name' => 'Broker',
                'slug' => 'broker',
                'description' => 'Land broker with access to land measurement features',
                'permissions' => [
                    'view_dashboard',
                    'measure_lands',
                    'view_lands',
                    'create_jobs',
                    'view_jobs',
                    'view_machines',
                ],
            ],
            [
                'name' => 'Accountant',
                'slug' => 'accountant',
                'description' => 'Financial manager with access to invoicing and payments',
                'permissions' => [
                    'view_dashboard',
                    'view_reports',
                    'manage_invoices',
                    'manage_payments',
                    'view_expenses',
                    'view_jobs',
                    'view_lands',
                ],
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
