<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Machine;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo organization
        $organization = Organization::create([
            'name' => 'Demo Agri Services',
            'owner_id' => 1, // Temporary
            'subscription_package' => 'pro',
            'subscription_expires_at' => now()->addYear(),
            'settings' => [
                'currency' => 'LKR',
                'default_rate_per_acre' => 15000,
            ],
        ]);

        // Create owner user
        $owner = User::create([
            'name' => 'John Perera',
            'email' => 'owner@geo-ops.lk',
            'password' => Hash::make('password'),
            'phone' => '+94771234567',
            'role' => 'owner',
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);

        // Update organization owner
        $organization->update(['owner_id' => $owner->id]);

        // Create subscription
        Subscription::create([
            'organization_id' => $organization->id,
            'package' => 'pro',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'amount' => 50000.00,
            'payment_method' => 'bank',
        ]);

        // Create broker user
        $broker = User::create([
            'name' => 'Sunil Silva',
            'email' => 'broker@geo-ops.lk',
            'password' => Hash::make('password'),
            'phone' => '+94772234567',
            'role' => 'broker',
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);

        // Create accountant user
        $accountant = User::create([
            'name' => 'Chamari Fernando',
            'email' => 'accountant@geo-ops.lk',
            'password' => Hash::make('password'),
            'phone' => '+94773234567',
            'role' => 'accountant',
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);

        // Create driver users and profiles
        $driverUser1 = User::create([
            'name' => 'Nimal Perera',
            'email' => 'driver1@geo-ops.lk',
            'password' => Hash::make('password'),
            'phone' => '+94774234567',
            'role' => 'driver',
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);

        $driver1 = Driver::create([
            'organization_id' => $organization->id,
            'user_id' => $driverUser1->id,
            'license_number' => 'DL123456',
            'license_expiry' => now()->addYears(2),
            'is_active' => true,
        ]);

        $driverUser2 = User::create([
            'name' => 'Kamal Silva',
            'email' => 'driver2@geo-ops.lk',
            'password' => Hash::make('password'),
            'phone' => '+94775234567',
            'role' => 'driver',
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);

        $driver2 = Driver::create([
            'organization_id' => $organization->id,
            'user_id' => $driverUser2->id,
            'license_number' => 'DL234567',
            'license_expiry' => now()->addYears(3),
            'is_active' => true,
        ]);

        // Create machines
        $machine1 = Machine::create([
            'organization_id' => $organization->id,
            'name' => 'Tractor - John Deere 5055E',
            'type' => 'Tractor',
            'registration_number' => 'NP-1234',
            'model' => 'John Deere 5055E',
            'year' => 2020,
            'is_active' => true,
        ]);

        $machine2 = Machine::create([
            'organization_id' => $organization->id,
            'name' => 'Harvester - Kubota DC70',
            'type' => 'Harvester',
            'registration_number' => 'NP-5678',
            'model' => 'Kubota DC70',
            'year' => 2021,
            'is_active' => true,
        ]);

        $machine3 = Machine::create([
            'organization_id' => $organization->id,
            'name' => 'Rotavator - Kubota RL1401',
            'type' => 'Rotavator',
            'registration_number' => 'NP-9012',
            'model' => 'Kubota RL1401',
            'year' => 2019,
            'is_active' => true,
        ]);

        // Create customers
        $customers = [
            [
                'name' => 'Wijaya Farm',
                'phone' => '+94771234001',
                'email' => 'wijaya@example.lk',
                'address' => 'Gampaha, Western Province',
                'nic' => '851234567V',
            ],
            [
                'name' => 'Jayasinghe Paddy Fields',
                'phone' => '+94771234002',
                'email' => 'jayasinghe@example.lk',
                'address' => 'Anuradhapura, North Central Province',
                'nic' => '871234567V',
            ],
            [
                'name' => 'Gunasekara Agricultural Services',
                'phone' => '+94771234003',
                'email' => 'gunasekara@example.lk',
                'address' => 'Kurunegala, North Western Province',
                'nic' => '791234567V',
            ],
            [
                'name' => 'Fernando Estates',
                'phone' => '+94771234004',
                'email' => 'fernando@example.lk',
                'address' => 'Matara, Southern Province',
                'nic' => '821234567V',
            ],
            [
                'name' => 'Silva Rice Mills',
                'phone' => '+94771234005',
                'email' => 'silva@example.lk',
                'address' => 'Polonnaruwa, North Central Province',
                'nic' => '751234567V',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create([
                'organization_id' => $organization->id,
                'name' => $customerData['name'],
                'phone' => $customerData['phone'],
                'email' => $customerData['email'],
                'address' => $customerData['address'],
                'nic' => $customerData['nic'],
                'balance' => 0,
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Test credentials:');
        $this->command->info('Owner: owner@geo-ops.lk / password');
        $this->command->info('Broker: broker@geo-ops.lk / password');
        $this->command->info('Accountant: accountant@geo-ops.lk / password');
        $this->command->info('Driver 1: driver1@geo-ops.lk / password');
        $this->command->info('Driver 2: driver2@geo-ops.lk / password');
    }
}
