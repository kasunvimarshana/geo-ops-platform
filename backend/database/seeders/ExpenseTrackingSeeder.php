<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\TrackingLog;
use App\Models\Organization;
use App\Models\User;
use App\Models\FieldJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds sample expenses and tracking logs for testing.
     */
    public function run(): void
    {
        $this->command->info('Seeding Expenses and Tracking Logs...');

        // Get test organization and users
        $organization = Organization::where('name', 'GeoOps Test Farm')->first();

        if (!$organization) {
            $this->command->warn('Test organization not found. Run TestAuthSeeder first.');
            return;
        }

        // Get users
        $owner = User::where('email', 'owner@geo-ops.test')->first();
        $driver1 = User::where('email', 'driver@geo-ops.test')->first();
        $driver2 = User::where('email', 'john@geo-ops.test')->first();

        if (!$owner || !$driver1 || !$driver2) {
            $this->command->warn('Test users not found. Run TestAuthSeeder first.');
            return;
        }

        // Get test jobs
        $jobs = FieldJob::where('organization_id', $organization->id)
            ->limit(3)
            ->get();

        if ($jobs->isEmpty()) {
            $this->command->warn('No jobs found. Run FieldJobSeeder first.');
            return;
        }

        $this->command->info('Creating expenses...');

        // Create expenses with different categories
        $expenses = [];
        $expenseCategories = ['fuel', 'maintenance', 'parts', 'salary', 'transport', 'food', 'other'];

        // Recent expenses (last 7 days)
        for ($i = 0; $i < 5; $i++) {
            $category = $expenseCategories[array_rand($expenseCategories)];
            $date = Carbon::now()->subDays(rand(0, 7));

            $expenses[] = [
                'organization_id' => $organization->id,
                'job_id' => $jobs->random()->id,
                'driver_id' => collect([$driver1, $driver2])->random()->id,
                'category' => $category,
                'expense_number' => $this->generateExpenseNumber($organization->id, $date),
                'amount' => $this->getAmountForCategory($category),
                'currency' => 'USD',
                'expense_date' => $date->toDateString(),
                'vendor_name' => $this->getVendorForCategory($category),
                'description' => $this->getDescriptionForCategory($category),
                'receipt_path' => rand(0, 1) ? '/receipts/receipt_' . uniqid() . '.jpg' : null,
                'attachments' => rand(0, 1) ? ['/receipts/attachment_' . uniqid() . '.pdf'] : null,
                'is_synced' => true,
                'created_by' => $owner->id,
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }

        // Older expenses (last 30 days)
        for ($i = 0; $i < 5; $i++) {
            $category = $expenseCategories[array_rand($expenseCategories)];
            $date = Carbon::now()->subDays(rand(8, 30));

            $expenses[] = [
                'organization_id' => $organization->id,
                'job_id' => rand(0, 1) ? $jobs->random()->id : null,
                'driver_id' => rand(0, 1) ? collect([$driver1, $driver2])->random()->id : null,
                'category' => $category,
                'expense_number' => $this->generateExpenseNumber($organization->id, $date),
                'amount' => $this->getAmountForCategory($category),
                'currency' => 'USD',
                'expense_date' => $date->toDateString(),
                'vendor_name' => $this->getVendorForCategory($category),
                'description' => $this->getDescriptionForCategory($category),
                'receipt_path' => rand(0, 1) ? '/receipts/receipt_' . uniqid() . '.jpg' : null,
                'attachments' => null,
                'is_synced' => true,
                'created_by' => $owner->id,
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }

        Expense::insert($expenses);
        $this->command->info('Created ' . count($expenses) . ' expenses.');

        $this->command->info('Creating tracking logs...');

        // Create tracking logs
        $trackingLogs = [];
        $drivers = [$driver1, $driver2];

        // Recent tracking logs (last 3 days) - simulating real-world tracking
        foreach ($drivers as $driver) {
            // Today's tracking
            $this->createTrackingRoute(
                $trackingLogs,
                $driver,
                $organization,
                $jobs->random(),
                Carbon::now()->startOfDay()->addHours(8),
                10,
                -1.2921, // Nairobi area
                36.8219
            );

            // Yesterday's tracking
            $this->createTrackingRoute(
                $trackingLogs,
                $driver,
                $organization,
                $jobs->random(),
                Carbon::yesterday()->addHours(9),
                15,
                -1.2921,
                36.8219
            );

            // 2 days ago tracking
            $this->createTrackingRoute(
                $trackingLogs,
                $driver,
                $organization,
                $jobs->random(),
                Carbon::now()->subDays(2)->addHours(8),
                12,
                -1.2921,
                36.8219
            );
        }

        // Older tracking logs (last week) - fewer logs
        foreach ($drivers as $driver) {
            for ($day = 3; $day <= 7; $day++) {
                $this->createTrackingRoute(
                    $trackingLogs,
                    $driver,
                    $organization,
                    $jobs->random(),
                    Carbon::now()->subDays($day)->addHours(rand(8, 10)),
                    rand(5, 8),
                    -1.2921,
                    36.8219
                );
            }
        }

        // Insert tracking logs in batches
        $chunks = array_chunk($trackingLogs, 100);
        foreach ($chunks as $chunk) {
            DB::table('tracking_logs')->insert($chunk);
        }

        $this->command->info('Created ' . count($trackingLogs) . ' tracking logs.');
        $this->command->info('Expense and Tracking seeding completed!');
    }

    /**
     * Create a simulated tracking route
     */
    private function createTrackingRoute(
        array &$trackingLogs,
        User $driver,
        Organization $organization,
        FieldJob $job,
        Carbon $startTime,
        int $pointCount,
        float $baseLat,
        float $baseLon
    ): void {
        $platforms = ['android', 'ios'];
        $platform = $platforms[array_rand($platforms)];

        for ($i = 0; $i < $pointCount; $i++) {
            $time = $startTime->copy()->addMinutes($i * 5);

            // Simulate movement by slightly changing coordinates
            $lat = $baseLat + (($i * 0.001) + (rand(-10, 10) / 10000));
            $lon = $baseLon + (($i * 0.001) + (rand(-10, 10) / 10000));

            $trackingLogs[] = [
                'organization_id' => $organization->id,
                'user_id' => $driver->id,
                'job_id' => $job->id,
                'latitude' => $lat,
                'longitude' => $lon,
                'accuracy_meters' => rand(5, 20),
                'altitude_meters' => rand(1500, 1700),
                'speed_mps' => rand(0, 15) + (rand(0, 99) / 100),
                'heading_degrees' => rand(0, 360),
                'recorded_at' => $time,
                'device_id' => 'device_' . $driver->id,
                'platform' => $platform,
                'metadata' => json_encode(['battery' => rand(20, 100), 'signal_strength' => rand(1, 5)]),
                'is_synced' => true,
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }
    }

    /**
     * Generate expense number
     */
    private function generateExpenseNumber(int $organizationId, Carbon $date): string
    {
        static $sequence = [];

        $dateKey = $date->format('Ymd');
        $prefix = "EXP-{$dateKey}-";

        if (!isset($sequence[$dateKey])) {
            $sequence[$dateKey] = 1;
        } else {
            $sequence[$dateKey]++;
        }

        return $prefix . str_pad($sequence[$dateKey], 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get amount based on category
     */
    private function getAmountForCategory(string $category): float
    {
        $ranges = [
            'fuel' => [30, 150],
            'maintenance' => [100, 500],
            'parts' => [50, 300],
            'salary' => [500, 2000],
            'transport' => [20, 100],
            'food' => [10, 50],
            'other' => [20, 200],
        ];

        $range = $ranges[$category] ?? [10, 100];
        return rand($range[0], $range[1]) + (rand(0, 99) / 100);
    }

    /**
     * Get vendor name based on category
     */
    private function getVendorForCategory(string $category): string
    {
        $vendors = [
            'fuel' => ['Shell Station', 'Total Petrol', 'Engen', 'Rubis'],
            'maintenance' => ['AutoCare Workshop', 'Mech Masters', 'QuickFix Garage'],
            'parts' => ['AutoParts Ltd', 'Spare Parts Hub', 'Vehicle Parts Co'],
            'salary' => ['Payroll Department', 'HR Department'],
            'transport' => ['City Bus', 'Metro Transport', 'Taxi Service'],
            'food' => ['Restaurant ABC', 'Food Court', 'Cafe Express'],
            'other' => ['General Store', 'Office Supplies', 'Equipment Rental'],
        ];

        $vendorList = $vendors[$category] ?? ['General Vendor'];
        return $vendorList[array_rand($vendorList)];
    }

    /**
     * Get description based on category
     */
    private function getDescriptionForCategory(string $category): string
    {
        $descriptions = [
            'fuel' => ['Diesel refill for tractor', 'Fuel for field operations', 'Petrol for equipment'],
            'maintenance' => ['Regular vehicle maintenance', 'Oil change and service', 'Brake system repair'],
            'parts' => ['Replacement air filter', 'New tires', 'Battery replacement'],
            'salary' => ['Monthly driver salary', 'Field worker wages', 'Operator payment'],
            'transport' => ['Transportation to site', 'Delivery charges', 'Commute expenses'],
            'food' => ['Lunch for field crew', 'Team refreshments', 'Staff meal'],
            'other' => ['Office supplies', 'Miscellaneous expense', 'Equipment rental'],
        ];

        $descList = $descriptions[$category] ?? ['General expense'];
        return $descList[array_rand($descList)];
    }
}
