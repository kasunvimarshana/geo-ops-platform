<?php

namespace Database\Seeders;

use App\Models\FieldJob;
use App\Models\Organization;
use App\Models\User;
use App\Models\Land;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FieldJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first organization
        $organization = Organization::first();
        
        if (!$organization) {
            $this->command->error('No organization found. Please create an organization first.');
            return;
        }

        // Get users from the organization
        $users = User::where('organization_id', $organization->id)->get();
        
        if ($users->isEmpty()) {
            $this->command->error('No users found in the organization.');
            return;
        }

        $owner = $users->first();
        
        // Get or create drivers (users with role driver)
        $drivers = User::where('organization_id', $organization->id)
            ->where('role', 'driver')
            ->get();
        
        if ($drivers->isEmpty()) {
            // Create sample drivers
            $drivers = collect([
                User::create([
                    'organization_id' => $organization->id,
                    'full_name' => 'John Driver',
                    'email' => 'john.driver@example.com',
                    'password' => bcrypt('password'),
                    'phone' => '555-0101',
                    'role' => 'driver',
                    'is_active' => true,
                ]),
                User::create([
                    'organization_id' => $organization->id,
                    'full_name' => 'Jane Smith',
                    'email' => 'jane.driver@example.com',
                    'password' => bcrypt('password'),
                    'phone' => '555-0102',
                    'role' => 'driver',
                    'is_active' => true,
                ]),
            ]);
        }

        // Get lands from the organization
        $lands = Land::where('organization_id', $organization->id)->get();

        // Sample job data
        $jobs = [
            // Pending jobs
            [
                'organization_id' => $organization->id,
                'job_number' => 'JOB-' . date('Ymd') . '-0001',
                'status' => 'pending',
                'service_type' => 'plowing',
                'customer_name' => 'Green Valley Farm',
                'customer_phone' => '555-1001',
                'customer_address' => '123 Farm Road, District A',
                'land_id' => $lands->isNotEmpty() ? $lands->first()->id : null,
                'location_coordinates' => [
                    ['lat' => 13.7563, 'lng' => 100.5018],
                    ['lat' => 13.7564, 'lng' => 100.5019],
                    ['lat' => 13.7565, 'lng' => 100.5018],
                ],
                'area_acres' => 5.5,
                'area_hectares' => 2.23,
                'rate_per_unit' => 100.00,
                'rate_unit' => 'acre',
                'estimated_amount' => 550.00,
                'scheduled_date' => Carbon::now()->addDays(2),
                'notes' => 'Need to plow before planting season',
                'is_synced' => false,
                'created_by' => $owner->id,
            ],
            [
                'organization_id' => $organization->id,
                'job_number' => 'JOB-' . date('Ymd') . '-0002',
                'status' => 'pending',
                'service_type' => 'spraying',
                'customer_name' => 'Sunrise Agriculture',
                'customer_phone' => '555-1002',
                'customer_address' => '456 Field Lane, District B',
                'area_acres' => 3.0,
                'area_hectares' => 1.21,
                'rate_per_unit' => 75.00,
                'rate_unit' => 'acre',
                'estimated_amount' => 225.00,
                'scheduled_date' => Carbon::now()->addDays(3),
                'notes' => 'Pest control spraying needed',
                'is_synced' => false,
                'created_by' => $owner->id,
            ],
            
            // Assigned jobs
            [
                'organization_id' => $organization->id,
                'job_number' => 'JOB-' . date('Ymd') . '-0003',
                'status' => 'assigned',
                'service_type' => 'harvesting',
                'customer_name' => 'Golden Harvest Co.',
                'customer_phone' => '555-1003',
                'customer_address' => '789 Crop Street, District C',
                'driver_id' => $drivers->first()->id,
                'land_id' => $lands->count() > 1 ? $lands->get(1)->id : null,
                'area_acres' => 10.0,
                'area_hectares' => 4.05,
                'rate_per_unit' => 150.00,
                'rate_unit' => 'acre',
                'estimated_amount' => 1500.00,
                'scheduled_date' => Carbon::now()->addDay(),
                'notes' => 'Rice harvesting - ready to go',
                'is_synced' => false,
                'created_by' => $owner->id,
            ],
            
            // In progress job
            [
                'organization_id' => $organization->id,
                'job_number' => 'JOB-' . date('Ymd') . '-0004',
                'status' => 'in_progress',
                'service_type' => 'seeding',
                'customer_name' => 'Blue Sky Farms',
                'customer_phone' => '555-1004',
                'customer_address' => '321 Seed Avenue, District D',
                'driver_id' => $drivers->count() > 1 ? $drivers->get(1)->id : $drivers->first()->id,
                'area_acres' => 7.5,
                'area_hectares' => 3.04,
                'rate_per_unit' => 120.00,
                'rate_unit' => 'acre',
                'estimated_amount' => 900.00,
                'scheduled_date' => Carbon::now(),
                'started_at' => Carbon::now()->subHours(2),
                'notes' => 'Corn planting in progress',
                'is_synced' => false,
                'created_by' => $owner->id,
            ],
            
            // Completed jobs
            [
                'organization_id' => $organization->id,
                'job_number' => 'JOB-' . date('Ymd', strtotime('-1 day')) . '-0001',
                'status' => 'completed',
                'service_type' => 'other',
                'customer_name' => 'Mountain View Agriculture',
                'customer_phone' => '555-1005',
                'customer_address' => '654 Nutrient Road, District E',
                'driver_id' => $drivers->first()->id,
                'area_acres' => 6.0,
                'area_hectares' => 2.43,
                'rate_per_unit' => 90.00,
                'rate_unit' => 'acre',
                'estimated_amount' => 540.00,
                'actual_amount' => 560.00,
                'scheduled_date' => Carbon::yesterday(),
                'started_at' => Carbon::yesterday()->setTime(8, 0),
                'completed_at' => Carbon::yesterday()->setTime(12, 30),
                'duration_minutes' => 270,
                'distance_km' => 45.5,
                'notes' => 'Regular fertilizer application',
                'completion_notes' => 'Job completed successfully. Extra area covered.',
                'is_synced' => false,
                'created_by' => $owner->id,
            ],
            [
                'organization_id' => $organization->id,
                'job_number' => 'JOB-' . date('Ymd', strtotime('-2 days')) . '-0001',
                'status' => 'completed',
                'service_type' => 'plowing',
                'customer_name' => 'Valley Enterprises',
                'customer_phone' => '555-1006',
                'customer_address' => '987 Tractor Lane, District F',
                'driver_id' => $drivers->count() > 1 ? $drivers->get(1)->id : $drivers->first()->id,
                'area_acres' => 12.0,
                'area_hectares' => 4.86,
                'rate_per_unit' => 110.00,
                'rate_unit' => 'acre',
                'estimated_amount' => 1320.00,
                'actual_amount' => 1320.00,
                'scheduled_date' => Carbon::now()->subDays(2),
                'started_at' => Carbon::now()->subDays(2)->setTime(7, 0),
                'completed_at' => Carbon::now()->subDays(2)->setTime(16, 0),
                'duration_minutes' => 540,
                'distance_km' => 62.3,
                'notes' => 'Large field plowing project',
                'completion_notes' => 'All areas completed as planned.',
                'attachments' => [
                    'https://example.com/photos/job1.jpg',
                    'https://example.com/photos/job1_2.jpg',
                ],
                'is_synced' => true,
                'created_by' => $owner->id,
            ],
        ];

        foreach ($jobs as $jobData) {
            FieldJob::create($jobData);
        }

        $this->command->info('Field jobs seeded successfully!');
        $this->command->info('Created ' . count($jobs) . ' sample jobs across different statuses.');
    }
}
