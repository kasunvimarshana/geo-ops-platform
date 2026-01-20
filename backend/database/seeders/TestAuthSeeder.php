<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test organization
        $organization = Organization::firstOrCreate(
            ['name' => 'Test Organization'],
            [
                'contact_name' => 'Test Contact',
                'contact_email' => 'contact@test.com',
                'package_tier' => 'basic',
                'is_active' => true,
            ]
        );

        // Create test users
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'organization_id' => $organization->id,
                'role' => 'admin',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('Password123!'),
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'owner@test.com'],
            [
                'organization_id' => $organization->id,
                'role' => 'owner',
                'first_name' => 'Owner',
                'last_name' => 'User',
                'password' => Hash::make('Password123!'),
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'driver@test.com'],
            [
                'organization_id' => $organization->id,
                'role' => 'driver',
                'first_name' => 'Driver',
                'last_name' => 'User',
                'password' => Hash::make('Password123!'),
                'is_active' => true,
            ]
        );
    }
}
