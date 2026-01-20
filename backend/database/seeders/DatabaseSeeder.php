<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed packages first
        $this->call(PackageSeeder::class);

        // Create a demo organization
        $organization = Organization::create([
            'name' => 'Demo Organization',
            'email' => 'demo@geo-ops.com',
            'phone' => '+1234567890',
            'address' => '123 Main St, Demo City',
            'status' => 'active',
            'settings' => [
                'currency' => 'LKR',
                'timezone' => 'Asia/Colombo',
            ],
        ]);

        // Create demo admin user
        User::create([
            'organization_id' => $organization->id,
            'name' => 'Admin User',
            'email' => 'admin@geo-ops.com',
            'phone' => '+1234567890',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
