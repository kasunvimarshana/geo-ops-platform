<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Farmer User',
            'email' => 'farmer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'farmer',
        ]);

        User::create([
            'name' => 'Driver User',
            'email' => 'driver@example.com',
            'password' => Hash::make('password123'),
            'role' => 'driver',
        ]);

        User::create([
            'name' => 'Accountant User',
            'email' => 'accountant@example.com',
            'password' => Hash::make('password123'),
            'role' => 'accountant',
        ]);
    }
}