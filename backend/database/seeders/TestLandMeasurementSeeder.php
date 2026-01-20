<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use App\Models\Land;
use App\Models\Measurement;
use Carbon\Carbon;

/**
 * Test Land and Measurement Seeder
 * 
 * Creates sample data for testing the Land and Measurement API endpoints.
 */
class TestLandMeasurementSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding test land and measurement data...');

        $organization = Organization::first();
        if (!$organization) {
            $this->command->error('No organization found. Please run TestAuthSeeder first.');
            return;
        }

        $user = User::where('organization_id', $organization->id)->first();
        if (!$user) {
            $this->command->error('No user found. Please run TestAuthSeeder first.');
            return;
        }

        // Sample GPS coordinates for different locations
        $sampleCoordinates = [
            // Sample 1: Small rectangular field
            [
                ['lat' => 13.7563, 'lng' => 100.5018],
                ['lat' => 13.7565, 'lng' => 100.5018],
                ['lat' => 13.7565, 'lng' => 100.5025],
                ['lat' => 13.7563, 'lng' => 100.5025],
                ['lat' => 13.7563, 'lng' => 100.5018],
            ],
            // Sample 2: Larger irregular field
            [
                ['lat' => 13.7600, 'lng' => 100.5100],
                ['lat' => 13.7610, 'lng' => 100.5095],
                ['lat' => 13.7615, 'lng' => 100.5110],
                ['lat' => 13.7608, 'lng' => 100.5120],
                ['lat' => 13.7600, 'lng' => 100.5115],
                ['lat' => 13.7600, 'lng' => 100.5100],
            ],
            // Sample 3: Medium triangular field
            [
                ['lat' => 13.7400, 'lng' => 100.4900],
                ['lat' => 13.7420, 'lng' => 100.4900],
                ['lat' => 13.7410, 'lng' => 100.4920],
                ['lat' => 13.7400, 'lng' => 100.4900],
            ],
        ];

        $landNames = [
            'North Field - Rice Cultivation',
            'South Valley - Vegetable Farm',
            'East Hill - Fruit Orchard',
        ];

        $districts = ['Pathum Wan', 'Bang Khen', 'Don Mueang'];
        $province = 'Bangkok';

        // Create lands
        $lands = [];
        foreach ($sampleCoordinates as $index => $coordinates) {
            $land = Land::create([
                'organization_id' => $organization->id,
                'owner_user_id' => $user->id,
                'name' => $landNames[$index],
                'description' => "Sample land parcel #{$index} for testing purposes",
                'coordinates' => $coordinates,
                'area_acres' => rand(10, 100) / 10,
                'area_hectares' => rand(10, 100) / 25,
                'area_square_meters' => rand(1000, 10000),
                'center_latitude' => $coordinates[0]['lat'],
                'center_longitude' => $coordinates[0]['lng'],
                'location_address' => "123 Sample Street, " . $districts[$index],
                'location_district' => $districts[$index],
                'location_province' => $province,
                'status' => $index === 2 ? 'inactive' : 'active',
                'created_by' => $user->id,
            ]);
            $lands[] = $land;
            
            $this->command->info("Created land: {$land->name}");
        }

        // Create measurements for each land
        $measurementTypes = ['walk_around', 'point_based'];
        
        foreach ($lands as $landIndex => $land) {
            $numMeasurements = rand(2, 5);
            
            for ($i = 0; $i < $numMeasurements; $i++) {
                $startTime = Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 23));
                $endTime = $startTime->copy()->addMinutes(rand(15, 120));
                
                $coordinates = $sampleCoordinates[$landIndex];
                // Add slight variation to coordinates
                foreach ($coordinates as &$coord) {
                    $coord['lat'] += (rand(-10, 10) / 10000);
                    $coord['lng'] += (rand(-10, 10) / 10000);
                }
                
                $measurement = Measurement::create([
                    'land_id' => $land->id,
                    'user_id' => $user->id,
                    'organization_id' => $organization->id,
                    'type' => $measurementTypes[array_rand($measurementTypes)],
                    'coordinates' => $coordinates,
                    'area_square_meters' => rand(1000, 10000),
                    'area_acres' => rand(10, 100) / 10,
                    'area_hectares' => rand(10, 100) / 25,
                    'perimeter_meters' => rand(200, 800),
                    'center_point' => [
                        'latitude' => $coordinates[0]['lat'],
                        'longitude' => $coordinates[0]['lng'],
                    ],
                    'point_count' => count($coordinates),
                    'accuracy_meters' => rand(3, 15) / 10,
                    'measurement_started_at' => $startTime,
                    'measurement_completed_at' => $endTime,
                    'duration_seconds' => $endTime->diffInSeconds($startTime),
                    'notes' => $i === 0 ? 'Initial boundary measurement' : null,
                    'is_synced' => $i < ($numMeasurements - 1),
                    'device_id' => 'DEVICE-' . strtoupper(substr(md5($user->id . $i), 0, 8)),
                    'created_by' => $user->id,
                ]);
                
                $measurementNum = $i + 1;
                $this->command->info("  Created measurement #{$measurementNum} for {$land->name}");
            }
        }

        $this->command->info('Test data seeded successfully!');
        $totalLands = count($lands);
        $this->command->info("Created {$totalLands} lands with multiple measurements each.");
    }
}
