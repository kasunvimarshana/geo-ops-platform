<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Role;
use App\Models\SubscriptionLimit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample organization
        $organization = Organization::firstOrCreate(
            ['slug' => 'demo-org'],
            [
                'name' => 'Demo Organization',
                'subscription_package' => 'pro',
                'subscription_expires_at' => now()->addYear(),
                'status' => 'active',
                'settings' => [
                    'currency' => 'LKR',
                    'timezone' => 'Asia/Colombo',
                    'date_format' => 'Y-m-d',
                    'default_language' => 'si',
                ],
            ]
        );

        // Create subscription limits for the organization
        SubscriptionLimit::firstOrCreate(
            ['organization_id' => $organization->id],
            [
                'measurements_count' => 0,
                'measurements_limit' => 1000,
                'drivers_count' => 0,
                'drivers_limit' => 50,
                'exports_count' => 0,
                'exports_limit' => 500,
                'reset_at' => now()->addMonth(),
            ]
        );

        // Get roles
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $ownerRole = Role::where('slug', 'owner')->firstOrFail();
        $driverRole = Role::where('slug', 'driver')->firstOrFail();

        // Create admin user for the organization
        User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'organization_id' => $organization->id,
                'role_id' => $adminRole->id,
                'name' => 'Admin User',
                'phone' => '+94771234567',
                'password' => Hash::make('password'),
                'language' => 'en',
                'is_active' => true,
            ]
        );

        // Create owner user
        User::firstOrCreate(
            ['email' => 'owner@demo.com'],
            [
                'organization_id' => $organization->id,
                'role_id' => $ownerRole->id,
                'name' => 'Organization Owner',
                'phone' => '+94771234568',
                'password' => Hash::make('password'),
                'language' => 'si',
                'is_active' => true,
            ]
        );

        // Create driver user
        User::firstOrCreate(
            ['email' => 'driver@demo.com'],
            [
                'organization_id' => $organization->id,
                'role_id' => $driverRole->id,
                'name' => 'Demo Driver',
                'phone' => '+94771234569',
                'password' => Hash::make('password'),
                'language' => 'si',
                'is_active' => true,
            ]
        );
    }
}
