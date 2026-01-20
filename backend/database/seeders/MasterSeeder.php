<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Models\Land;
use App\Models\Measurement;
use App\Models\FieldJob;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\TrackingLog;
use App\Models\SubscriptionPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MasterSeeder extends Seeder
{
    private array $organizations = [];
    private array $users = [];
    private array $lands = [];
    private array $jobs = [];
    private array $invoices = [];

    public function run(): void
    {
        $this->seedSubscriptionPackages();
        $this->seedOrganizations();
        $this->seedUsers();
        $this->seedLands();
        $this->seedMeasurements();
        $this->seedFieldJobs();
        $this->seedInvoices();
        $this->seedPayments();
        $this->seedExpenses();
        $this->seedTrackingLogs();

        $this->command->info('Master seeder completed successfully!');
    }

    private function seedSubscriptionPackages(): void
    {
        $packages = [
            [
                'name' => 'free',
                'display_name' => 'Free Plan',
                'description' => 'Perfect for getting started with basic features',
                'max_measurements' => 100,
                'max_drivers' => 2,
                'max_jobs' => 50,
                'max_lands' => 5,
                'max_storage_mb' => 500,
                'price_monthly' => 0.00,
                'price_yearly' => null,
                'features' => ['Basic tracking', 'Limited reporting', 'Email support'],
                'is_active' => true,
            ],
            [
                'name' => 'basic',
                'display_name' => 'Basic Plan',
                'description' => 'Ideal for small farming operations',
                'max_measurements' => 500,
                'max_drivers' => 5,
                'max_jobs' => 200,
                'max_lands' => 20,
                'max_storage_mb' => 2048,
                'price_monthly' => 29.99,
                'price_yearly' => 299.00,
                'features' => ['Advanced tracking', 'Comprehensive reporting', 'Priority support', 'Mobile app'],
                'is_active' => true,
            ],
            [
                'name' => 'pro',
                'display_name' => 'Professional Plan',
                'description' => 'Complete solution for professional agricultural businesses',
                'max_measurements' => 2000,
                'max_drivers' => 20,
                'max_jobs' => 1000,
                'max_lands' => 100,
                'max_storage_mb' => 10240,
                'price_monthly' => 99.99,
                'price_yearly' => 999.00,
                'features' => ['Unlimited tracking', 'Custom reports', '24/7 support', 'API access', 'White label', 'Advanced analytics'],
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::firstOrCreate(
                ['name' => $package['name']],
                $package
            );
        }

        $this->command->info('Subscription packages seeded.');
    }

    private function seedOrganizations(): void
    {
        $orgs = [
            [
                'name' => 'AgriTech Solutions Lanka',
                'contact_name' => 'Sunil Perera',
                'contact_email' => 'sunil@agritech.lk',
                'contact_phone' => '+94771234567',
                'address' => 'No 45, Galle Road, Colombo 03',
                'package_tier' => 'pro',
                'package_expires_at' => Carbon::now()->addYear(),
                'is_active' => true,
            ],
            [
                'name' => 'Green Valley Farms',
                'contact_name' => 'Nimal Fernando',
                'contact_email' => 'nimal@greenvalley.lk',
                'contact_phone' => '+94712345678',
                'address' => 'Kandy Road, Kurunegala',
                'package_tier' => 'basic',
                'package_expires_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Harvest Co',
                'contact_name' => 'Kumari Silva',
                'contact_email' => 'kumari@freshharvest.lk',
                'contact_phone' => '+94723456789',
                'address' => 'Matara Road, Galle',
                'package_tier' => 'free',
                'package_expires_at' => null,
                'is_active' => true,
            ],
        ];

        foreach ($orgs as $org) {
            $this->organizations[] = Organization::firstOrCreate(
                ['contact_email' => $org['contact_email']],
                $org
            );
        }

        $this->command->info('Organizations seeded: ' . count($this->organizations));
    }

    private function seedUsers(): void
    {
        $usersData = [
            // AgriTech Solutions Lanka (Pro) - 8 users
            ['org_idx' => 0, 'role' => 'admin', 'first_name' => 'Sunil', 'last_name' => 'Perera', 'email' => 'sunil@agritech.lk', 'phone' => '+94771234567'],
            ['org_idx' => 0, 'role' => 'owner', 'first_name' => 'Anura', 'last_name' => 'Dissanayake', 'email' => 'anura@agritech.lk', 'phone' => '+94771234568'],
            ['org_idx' => 0, 'role' => 'driver', 'first_name' => 'Kamal', 'last_name' => 'Jayasinghe', 'email' => 'kamal@agritech.lk', 'phone' => '+94771234569'],
            ['org_idx' => 0, 'role' => 'driver', 'first_name' => 'Pradeep', 'last_name' => 'Wijeratne', 'email' => 'pradeep@agritech.lk', 'phone' => '+94771234570'],
            ['org_idx' => 0, 'role' => 'driver', 'first_name' => 'Ruwan', 'last_name' => 'Bandara', 'email' => 'ruwan@agritech.lk', 'phone' => '+94771234571'],
            ['org_idx' => 0, 'role' => 'driver', 'first_name' => 'Lahiru', 'last_name' => 'Gunasekara', 'email' => 'lahiru@agritech.lk', 'phone' => '+94771234572'],
            ['org_idx' => 0, 'role' => 'viewer', 'first_name' => 'Sanduni', 'last_name' => 'Rathnayake', 'email' => 'sanduni@agritech.lk', 'phone' => '+94771234573'],
            ['org_idx' => 0, 'role' => 'viewer', 'first_name' => 'Tharindu', 'last_name' => 'Lakmal', 'email' => 'tharindu@agritech.lk', 'phone' => '+94771234574'],

            // Green Valley Farms (Basic) - 6 users
            ['org_idx' => 1, 'role' => 'admin', 'first_name' => 'Nimal', 'last_name' => 'Fernando', 'email' => 'nimal@greenvalley.lk', 'phone' => '+94712345678'],
            ['org_idx' => 1, 'role' => 'owner', 'first_name' => 'Chaminda', 'last_name' => 'Rodrigo', 'email' => 'chaminda@greenvalley.lk', 'phone' => '+94712345679'],
            ['org_idx' => 1, 'role' => 'driver', 'first_name' => 'Saman', 'last_name' => 'Kumara', 'email' => 'saman@greenvalley.lk', 'phone' => '+94712345680'],
            ['org_idx' => 1, 'role' => 'driver', 'first_name' => 'Asanka', 'last_name' => 'Dias', 'email' => 'asanka@greenvalley.lk', 'phone' => '+94712345681'],
            ['org_idx' => 1, 'role' => 'driver', 'first_name' => 'Nuwan', 'last_name' => 'Priyantha', 'email' => 'nuwan@greenvalley.lk', 'phone' => '+94712345682'],
            ['org_idx' => 1, 'role' => 'viewer', 'first_name' => 'Dilini', 'last_name' => 'Perera', 'email' => 'dilini@greenvalley.lk', 'phone' => '+94712345683'],

            // Fresh Harvest Co (Free) - 4 users
            ['org_idx' => 2, 'role' => 'admin', 'first_name' => 'Kumari', 'last_name' => 'Silva', 'email' => 'kumari@freshharvest.lk', 'phone' => '+94723456789'],
            ['org_idx' => 2, 'role' => 'owner', 'first_name' => 'Ravi', 'last_name' => 'Mendis', 'email' => 'ravi@freshharvest.lk', 'phone' => '+94723456790'],
            ['org_idx' => 2, 'role' => 'driver', 'first_name' => 'Janaka', 'last_name' => 'Samaraweera', 'email' => 'janaka@freshharvest.lk', 'phone' => '+94723456791'],
            ['org_idx' => 2, 'role' => 'driver', 'first_name' => 'Buddika', 'last_name' => 'Pathirana', 'email' => 'buddika@freshharvest.lk', 'phone' => '+94723456792'],
        ];

        foreach ($usersData as $userData) {
            $org = $this->organizations[$userData['org_idx']];
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'organization_id' => $org->id,
                    'role' => $userData['role'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'phone' => $userData['phone'],
                    'password' => Hash::make('Password123!'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $this->users[] = $user;
        }

        $this->command->info('Users seeded: ' . count($this->users));
    }

    private function seedLands(): void
    {
        // Sri Lankan GPS coordinates (realistic locations)
        $landsData = [
            // AgriTech Solutions (Pro) - 12 lands
            ['org_idx' => 0, 'name' => 'Paddy Field Colombo North', 'coords' => [[6.9271, 79.8612], [6.9280, 79.8612], [6.9280, 79.8622], [6.9271, 79.8622]], 'district' => 'Colombo', 'province' => 'Western'],
            ['org_idx' => 0, 'name' => 'Tea Estate Kandy Hills', 'coords' => [[7.2906, 80.6337], [7.2920, 80.6337], [7.2920, 80.6352], [7.2906, 80.6352]], 'district' => 'Kandy', 'province' => 'Central'],
            ['org_idx' => 0, 'name' => 'Vegetable Farm Nuwara Eliya', 'coords' => [[6.9497, 80.7891], [6.9510, 80.7891], [6.9510, 80.7905], [6.9497, 80.7905]], 'district' => 'Nuwara Eliya', 'province' => 'Central'],
            ['org_idx' => 0, 'name' => 'Rice Field Anuradhapura', 'coords' => [[8.3114, 80.4037], [8.3125, 80.4037], [8.3125, 80.4050], [8.3114, 80.4050]], 'district' => 'Anuradhapura', 'province' => 'North Central'],
            ['org_idx' => 0, 'name' => 'Coconut Plantation Kurunegala', 'coords' => [[7.4863, 80.3623], [7.4875, 80.3623], [7.4875, 80.3638], [7.4863, 80.3638]], 'district' => 'Kurunegala', 'province' => 'North Western'],
            ['org_idx' => 0, 'name' => 'Fruit Orchard Matale', 'coords' => [[7.4675, 80.6234], [7.4688, 80.6234], [7.4688, 80.6248], [7.4675, 80.6248]], 'district' => 'Matale', 'province' => 'Central'],
            ['org_idx' => 0, 'name' => 'Spice Garden Matara', 'coords' => [[5.9549, 80.5550], [5.9560, 80.5550], [5.9560, 80.5563], [5.9549, 80.5563]], 'district' => 'Matara', 'province' => 'Southern'],
            ['org_idx' => 0, 'name' => 'Rubber Estate Ratnapura', 'coords' => [[6.6828, 80.4014], [6.6840, 80.4014], [6.6840, 80.4028], [6.6828, 80.4028]], 'district' => 'Ratnapura', 'province' => 'Sabaragamuwa'],
            ['org_idx' => 0, 'name' => 'Paddy Field Polonnaruwa', 'coords' => [[7.9403, 81.0188], [7.9415, 81.0188], [7.9415, 81.0202], [7.9403, 81.0202]], 'district' => 'Polonnaruwa', 'province' => 'North Central'],
            ['org_idx' => 0, 'name' => 'Vegetable Field Badulla', 'coords' => [[6.9934, 81.0550], [6.9946, 81.0550], [6.9946, 81.0564], [6.9934, 81.0564]], 'district' => 'Badulla', 'province' => 'Uva'],
            ['org_idx' => 0, 'name' => 'Banana Plantation Gampaha', 'coords' => [[7.0840, 80.0098], [7.0852, 80.0098], [7.0852, 80.0112], [7.0840, 80.0112]], 'district' => 'Gampaha', 'province' => 'Western'],
            ['org_idx' => 0, 'name' => 'Paddy Field Hambantota', 'coords' => [[6.1429, 81.1212], [6.1440, 81.1212], [6.1440, 81.1226], [6.1429, 81.1226]], 'district' => 'Hambantota', 'province' => 'Southern'],

            // Green Valley Farms (Basic) - 8 lands
            ['org_idx' => 1, 'name' => 'Main Rice Field', 'coords' => [[7.4863, 80.3700], [7.4875, 80.3700], [7.4875, 80.3715], [7.4863, 80.3715]], 'district' => 'Kurunegala', 'province' => 'North Western'],
            ['org_idx' => 1, 'name' => 'Vegetable Plot A', 'coords' => [[7.4880, 80.3720], [7.4892, 80.3720], [7.4892, 80.3735], [7.4880, 80.3735]], 'district' => 'Kurunegala', 'province' => 'North Western'],
            ['org_idx' => 1, 'name' => 'Fruit Garden', 'coords' => [[7.4900, 80.3740], [7.4912, 80.3740], [7.4912, 80.3755], [7.4900, 80.3755]], 'district' => 'Kurunegala', 'province' => 'North Western'],
            ['org_idx' => 1, 'name' => 'Paddy Field East', 'coords' => [[7.4920, 80.3760], [7.4932, 80.3760], [7.4932, 80.3775], [7.4920, 80.3775]], 'district' => 'Kurunegala', 'province' => 'North Western'],
            ['org_idx' => 1, 'name' => 'Tea Small Plot', 'coords' => [[7.2950, 80.6400], [7.2962, 80.6400], [7.2962, 80.6415], [7.2950, 80.6415]], 'district' => 'Kandy', 'province' => 'Central'],
            ['org_idx' => 1, 'name' => 'Coconut Grove', 'coords' => [[7.4940, 80.3780], [7.4952, 80.3780], [7.4952, 80.3795], [7.4940, 80.3795]], 'district' => 'Kurunegala', 'province' => 'North Western'],
            ['org_idx' => 1, 'name' => 'Spice Garden Plot', 'coords' => [[7.4960, 80.3800], [7.4972, 80.3800], [7.4972, 80.3815], [7.4960, 80.3815]], 'district' => 'Kurunegala', 'province' => 'North Western'],
            ['org_idx' => 1, 'name' => 'Mixed Crop Field', 'coords' => [[7.4980, 80.3820], [7.4992, 80.3820], [7.4992, 80.3835], [7.4980, 80.3835]], 'district' => 'Kurunegala', 'province' => 'North Western'],

            // Fresh Harvest (Free) - 4 lands
            ['org_idx' => 2, 'name' => 'Main Field', 'coords' => [[6.0535, 80.2210], [6.0547, 80.2210], [6.0547, 80.2225], [6.0535, 80.2225]], 'district' => 'Galle', 'province' => 'Southern'],
            ['org_idx' => 2, 'name' => 'Vegetable Patch', 'coords' => [[6.0550, 80.2230], [6.0562, 80.2230], [6.0562, 80.2245], [6.0550, 80.2245]], 'district' => 'Galle', 'province' => 'Southern'],
            ['org_idx' => 2, 'name' => 'Small Paddy Field', 'coords' => [[6.0570, 80.2250], [6.0582, 80.2250], [6.0582, 80.2265], [6.0570, 80.2265]], 'district' => 'Galle', 'province' => 'Southern'],
            ['org_idx' => 2, 'name' => 'Fruit Trees', 'coords' => [[6.0590, 80.2270], [6.0602, 80.2270], [6.0602, 80.2285], [6.0590, 80.2285]], 'district' => 'Galle', 'province' => 'Southern'],
        ];

        foreach ($landsData as $landData) {
            $org = $this->organizations[$landData['org_idx']];
            $owner = $this->users[array_search($org->id, array_column($this->users, 'organization_id'))];

            $coordinates = $landData['coords'];
            $centerLat = array_sum(array_column($coordinates, 0)) / count($coordinates);
            $centerLng = array_sum(array_column($coordinates, 1)) / count($coordinates);

            // Calculate area (approximate)
            $areaHectares = rand(5, 50) / 10; // 0.5 to 5 hectares
            $areaAcres = $areaHectares * 2.471;
            $areaSquareMeters = $areaHectares * 10000;

            $land = Land::create([
                'organization_id' => $org->id,
                'owner_user_id' => $owner->id,
                'name' => $landData['name'],
                'description' => 'Agricultural land in ' . $landData['district'],
                'coordinates' => $coordinates,
                'area_acres' => round($areaAcres, 2),
                'area_hectares' => round($areaHectares, 2),
                'area_square_meters' => round($areaSquareMeters, 2),
                'center_latitude' => $centerLat,
                'center_longitude' => $centerLng,
                'location_district' => $landData['district'],
                'location_province' => $landData['province'],
                'status' => 'active',
                'created_by' => $owner->id,
            ]);

            $this->lands[] = $land;
        }

        $this->command->info('Lands seeded: ' . count($this->lands));
    }

    private function seedMeasurements(): void
    {
        $measurementCount = 0;

        foreach ($this->lands as $land) {
            $numMeasurements = rand(1, 3);

            for ($i = 0; $i < $numMeasurements; $i++) {
                $measuredDate = Carbon::now()->subDays(rand(1, 90));

                Measurement::create([
                    'organization_id' => $land->organization_id,
                    'land_id' => $land->id,
                    'measured_by' => $land->owner_user_id,
                    'area_acres' => $land->area_acres + (rand(-10, 10) / 100),
                    'area_hectares' => $land->area_hectares + (rand(-10, 10) / 100),
                    'area_square_meters' => $land->area_square_meters + (rand(-100, 100)),
                    'perimeter_meters' => rand(300, 1200),
                    'measurement_method' => ['gps', 'manual', 'drone'][rand(0, 2)],
                    'coordinates' => $land->coordinates,
                    'notes' => 'Measurement taken on ' . $measuredDate->format('Y-m-d'),
                    'measured_at' => $measuredDate,
                    'created_by' => $land->owner_user_id,
                ]);

                $measurementCount++;
            }
        }

        $this->command->info('Measurements seeded: ' . $measurementCount);
    }

    private function seedFieldJobs(): void
    {
        $serviceTypes = ['plowing', 'planting', 'harvesting', 'spraying', 'fertilizing', 'irrigation'];
        $statuses = ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'];
        $jobCount = 0;

        foreach ($this->organizations as $orgIdx => $org) {
            // Get drivers for this org
            $orgDrivers = array_filter($this->users, fn($u) => $u->organization_id === $org->id && $u->role === 'driver');
            $orgLands = array_filter($this->lands, fn($l) => $l->organization_id === $org->id);

            $numJobs = match($org->package_tier) {
                'pro' => rand(12, 15),
                'basic' => rand(8, 10),
                'free' => rand(3, 5),
            };

            for ($i = 0; $i < $numJobs; $i++) {
                $status = $statuses[array_rand($statuses)];
                $serviceType = $serviceTypes[array_rand($serviceTypes)];
                $land = $orgLands[array_rand($orgLands)];
                $driver = $orgDrivers ? $orgDrivers[array_rand($orgDrivers)] : null;

                $scheduledDate = Carbon::now()->subDays(rand(0, 60));
                $startedAt = in_array($status, ['in_progress', 'completed']) ? $scheduledDate->copy()->addHours(rand(1, 3)) : null;
                $completedAt = $status === 'completed' ? $startedAt?->copy()->addHours(rand(2, 8)) : null;
                $durationMinutes = $completedAt ? $startedAt->diffInMinutes($completedAt) : null;

                $ratePerUnit = rand(5000, 25000);
                $estimatedAmount = $land->area_acres * $ratePerUnit;
                $actualAmount = $status === 'completed' ? $estimatedAmount + rand(-1000, 1000) : null;

                $job = FieldJob::create([
                    'organization_id' => $org->id,
                    'land_id' => $land->id,
                    'customer_id' => $land->owner_user_id,
                    'driver_id' => $driver?->id,
                    'job_number' => 'JOB-' . strtoupper(substr($org->name, 0, 3)) . '-' . str_pad($jobCount + 1, 5, '0', STR_PAD_LEFT),
                    'status' => $status,
                    'service_type' => $serviceType,
                    'customer_name' => $land->owner->first_name . ' ' . $land->owner->last_name,
                    'customer_phone' => $land->owner->phone,
                    'customer_address' => $land->location_district . ', ' . $land->location_province,
                    'location_coordinates' => [$land->center_latitude, $land->center_longitude],
                    'area_acres' => $land->area_acres,
                    'area_hectares' => $land->area_hectares,
                    'rate_per_unit' => $ratePerUnit,
                    'rate_unit' => 'acre',
                    'estimated_amount' => $estimatedAmount,
                    'actual_amount' => $actualAmount,
                    'scheduled_date' => $scheduledDate,
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                    'duration_minutes' => $durationMinutes,
                    'distance_km' => $completedAt ? rand(5, 50) : null,
                    'notes' => ucfirst($serviceType) . ' work for ' . $land->name,
                    'completion_notes' => $completedAt ? 'Work completed successfully' : null,
                    'is_synced' => true,
                    'created_by' => $land->owner_user_id,
                ]);

                $this->jobs[] = $job;
                $jobCount++;
            }
        }

        $this->command->info('Field jobs seeded: ' . $jobCount);
    }

    private function seedInvoices(): void
    {
        $invoiceCount = 0;

        foreach ($this->jobs as $job) {
            // Create invoice for 70% of completed jobs
            if ($job->status === 'completed' && rand(1, 10) <= 7) {
                $invoiceDate = $job->completed_at->copy()->addDays(rand(1, 5));
                $dueDate = $invoiceDate->copy()->addDays(rand(15, 30));
                $isPaid = rand(1, 10) <= 6; // 60% paid

                $invoice = Invoice::create([
                    'organization_id' => $job->organization_id,
                    'job_id' => $job->id,
                    'customer_id' => $job->customer_id,
                    'invoice_number' => 'INV-' . $invoiceDate->format('Ymd') . '-' . str_pad($invoiceCount + 1, 4, '0', STR_PAD_LEFT),
                    'invoice_date' => $invoiceDate,
                    'due_date' => $dueDate,
                    'customer_name' => $job->customer_name,
                    'customer_email' => $job->customer->email ?? null,
                    'customer_phone' => $job->customer_phone,
                    'customer_address' => $job->customer_address,
                    'subtotal' => $job->actual_amount ?? $job->estimated_amount,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $job->actual_amount ?? $job->estimated_amount,
                    'amount_paid' => $isPaid ? ($job->actual_amount ?? $job->estimated_amount) : 0,
                    'amount_due' => $isPaid ? 0 : ($job->actual_amount ?? $job->estimated_amount),
                    'status' => $isPaid ? 'paid' : 'pending',
                    'payment_status' => $isPaid ? 'paid' : 'unpaid',
                    'currency' => 'LKR',
                    'line_items' => [
                        [
                            'description' => ucfirst($job->service_type) . ' - ' . $job->area_acres . ' acres',
                            'quantity' => $job->area_acres,
                            'unit_price' => $job->rate_per_unit,
                            'amount' => $job->actual_amount ?? $job->estimated_amount,
                        ]
                    ],
                    'notes' => 'Invoice for job ' . $job->job_number,
                    'created_by' => $job->created_by,
                ]);

                $this->invoices[] = $invoice;
                $invoiceCount++;
            }
        }

        $this->command->info('Invoices seeded: ' . $invoiceCount);
    }

    private function seedPayments(): void
    {
        $paymentCount = 0;

        foreach ($this->invoices as $invoice) {
            if ($invoice->payment_status === 'paid') {
                $paymentMethods = ['cash', 'bank_transfer', 'cheque', 'mobile_payment'];
                $paymentDate = $invoice->invoice_date->copy()->addDays(rand(1, 20));

                Payment::create([
                    'organization_id' => $invoice->organization_id,
                    'invoice_id' => $invoice->id,
                    'customer_id' => $invoice->customer_id,
                    'payment_date' => $paymentDate,
                    'amount' => $invoice->total_amount,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'currency' => 'LKR',
                    'reference_number' => 'PAY-' . $paymentDate->format('Ymd') . '-' . rand(1000, 9999),
                    'notes' => 'Payment for invoice ' . $invoice->invoice_number,
                    'status' => 'completed',
                    'created_by' => $invoice->customer_id,
                ]);

                $paymentCount++;
            }
        }

        $this->command->info('Payments seeded: ' . $paymentCount);
    }

    private function seedExpenses(): void
    {
        $categories = ['fuel', 'maintenance', 'parts', 'salary', 'transport', 'food', 'other'];
        $expenseCount = 0;

        foreach ($this->jobs as $job) {
            // Add 1-3 expenses for completed or in-progress jobs
            if (in_array($job->status, ['completed', 'in_progress'])) {
                $numExpenses = rand(1, 3);

                for ($i = 0; $i < $numExpenses; $i++) {
                    $category = $categories[array_rand($categories)];
                    $expenseDate = $job->started_at ?? $job->scheduled_date;

                    $amount = match($category) {
                        'fuel' => rand(2000, 8000),
                        'maintenance' => rand(3000, 15000),
                        'parts' => rand(5000, 25000),
                        'salary' => rand(1000, 5000),
                        'transport' => rand(500, 3000),
                        'food' => rand(300, 1500),
                        default => rand(1000, 10000),
                    };

                    Expense::create([
                        'organization_id' => $job->organization_id,
                        'job_id' => $job->id,
                        'driver_id' => $job->driver_id,
                        'category' => $category,
                        'amount' => $amount,
                        'currency' => 'LKR',
                        'expense_date' => $expenseDate->copy()->addHours(rand(1, 8)),
                        'vendor_name' => 'Vendor ' . rand(1, 10),
                        'description' => ucfirst($category) . ' expense for job ' . $job->job_number,
                        'created_by' => $job->driver_id ?? $job->created_by,
                    ]);

                    $expenseCount++;
                }
            }
        }

        $this->command->info('Expenses seeded: ' . $expenseCount);
    }

    private function seedTrackingLogs(): void
    {
        $trackingCount = 0;

        foreach ($this->jobs as $job) {
            // Create tracking logs for in-progress and completed jobs
            if (in_array($job->status, ['in_progress', 'completed']) && $job->driver_id) {
                $startTime = $job->started_at;
                $endTime = $job->completed_at ?? now();

                // Create log entries every 5-15 minutes
                $currentTime = $startTime->copy();
                $locations = [];

                // Generate path around the land center
                $baseLat = $job->location_coordinates[0];
                $baseLng = $job->location_coordinates[1];

                while ($currentTime <= $endTime) {
                    // Add small random variations to simulate movement
                    $lat = $baseLat + (rand(-30, 30) / 10000);
                    $lng = $baseLng + (rand(-30, 30) / 10000);

                    TrackingLog::create([
                        'organization_id' => $job->organization_id,
                        'user_id' => $job->driver_id,
                        'job_id' => $job->id,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'altitude' => rand(50, 200),
                        'accuracy' => rand(5, 20),
                        'speed' => rand(0, 15),
                        'heading' => rand(0, 360),
                        'battery_level' => rand(20, 100),
                        'is_moving' => rand(0, 1) == 1,
                        'activity_type' => ['still', 'walking', 'in_vehicle'][rand(0, 2)],
                        'recorded_at' => $currentTime->copy(),
                    ]);

                    $trackingCount++;
                    $currentTime->addMinutes(rand(5, 15));
                }
            }
        }

        $this->command->info('Tracking logs seeded: ' . $trackingCount);
    }
}
