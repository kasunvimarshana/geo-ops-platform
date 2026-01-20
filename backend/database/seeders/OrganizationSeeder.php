<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Organization::create([
            'name' => 'Agro Services Ltd',
            'address' => '123 Agro Lane, Colombo, Sri Lanka',
            'contact_number' => '011-1234567',
            'email' => 'info@agroservices.lk',
        ]);

        Organization::create([
            'name' => 'Farmers Union',
            'address' => '456 Farmer St, Kandy, Sri Lanka',
            'contact_number' => '081-7654321',
            'email' => 'contact@farmersunion.lk',
        ]);

        Organization::create([
            'name' => 'Green Fields Co.',
            'address' => '789 Green Rd, Galle, Sri Lanka',
            'contact_number' => '091-2345678',
            'email' => 'support@greenfields.lk',
        ]);
    }
}