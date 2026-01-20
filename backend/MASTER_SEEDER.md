# Master Seeder Documentation

## Overview

The `MasterSeeder` is a comprehensive database seeder that populates the GeoOps Platform database with realistic Sri Lankan agricultural data. It creates a complete working environment with multiple organizations, users, lands, jobs, invoices, payments, expenses, and tracking logs.

## What Gets Seeded

### 1. Subscription Packages (3 packages)

- **Free Plan**: 5 lands, 100 measurements, 50 jobs, 2 drivers
- **Basic Plan**: 20 lands, 500 measurements, 200 jobs, 5 drivers
- **Professional Plan**: 100 lands, 2000 measurements, 1000 jobs, 20 drivers

### 2. Organizations (3 organizations)

1. **AgriTech Solutions Lanka** (Pro tier)
    - Contact: Sunil Perera
    - Location: Colombo 03
    - Package expires: 1 year from now

2. **Green Valley Farms** (Basic tier)
    - Contact: Nimal Fernando
    - Location: Kurunegala
    - Package expires: 6 months from now

3. **Fresh Harvest Co** (Free tier)
    - Contact: Kumari Silva
    - Location: Galle
    - No expiration

### 3. Users (18 users total)

#### AgriTech Solutions (8 users)

- 1 Admin: Sunil Perera
- 1 Owner: Anura Dissanayake
- 4 Drivers: Kamal, Pradeep, Ruwan, Lahiru
- 2 Viewers: Sanduni, Tharindu

#### Green Valley Farms (6 users)

- 1 Admin: Nimal Fernando
- 1 Owner: Chaminda Rodrigo
- 3 Drivers: Saman, Asanka, Nuwan
- 1 Viewer: Dilini Perera

#### Fresh Harvest Co (4 users)

- 1 Admin: Kumari Silva
- 1 Owner: Ravi Mendis
- 2 Drivers: Janaka, Buddika

**All users have password:** `Password123!`

### 4. Lands (24 lands with realistic GPS coordinates)

#### Sri Lankan Locations Covered:

- **Western Province**: Colombo, Gampaha
- **Central Province**: Kandy, Nuwara Eliya, Matale
- **North Central**: Anuradhapura, Polonnaruwa
- **North Western**: Kurunegala
- **Southern Province**: Galle, Matara, Hambantota
- **Sabaragamuwa**: Ratnapura
- **Uva Province**: Badulla

**Land Types:**

- Paddy fields
- Tea estates
- Vegetable farms
- Coconut plantations
- Fruit orchards
- Spice gardens
- Rubber estates
- Mixed crop fields

### 5. Measurements (30+ measurements)

- 1-3 measurements per land
- Various measurement methods: GPS, manual, drone
- Historical data (1-90 days old)
- Accurate area calculations in acres, hectares, and square meters

### 6. Field Jobs (25+ jobs)

**Distribution:**

- AgriTech Solutions: 12-15 jobs
- Green Valley Farms: 8-10 jobs
- Fresh Harvest Co: 3-5 jobs

**Service Types:**

- Plowing
- Planting
- Harvesting
- Spraying
- Fertilizing
- Irrigation

**Job Statuses:**

- Pending
- Assigned
- In Progress
- Completed
- Cancelled

**Job Details:**

- Realistic scheduling (0-60 days old)
- Assigned drivers
- Location coordinates
- Area and rate calculations
- Duration and distance tracking
- Completion notes

### 7. Invoices (15+ invoices)

- Generated for ~70% of completed jobs
- 1-5 days after job completion
- Payment terms: 15-30 days
- 60% are paid, 40% pending
- LKR currency
- Detailed line items

### 8. Payments (10+ payments)

- For all paid invoices
- Payment methods: cash, bank transfer, cheque, mobile payment
- Payment dates: 1-20 days after invoice
- Reference numbers
- Completed status

### 9. Expenses (15+ expenses)

**Categories:**

- Fuel: LKR 2,000-8,000
- Maintenance: LKR 3,000-15,000
- Parts: LKR 5,000-25,000
- Salary: LKR 1,000-5,000
- Transport: LKR 500-3,000
- Food: LKR 300-1,500
- Other: LKR 1,000-10,000

**Linked to:**

- Completed and in-progress jobs
- Assigned drivers
- 1-3 expenses per job

### 10. Tracking Logs (100+ logs)

- GPS tracking for in-progress and completed jobs
- Log entries every 5-15 minutes
- Realistic movement patterns around land locations
- Complete metadata:
    - Latitude/longitude
    - Altitude, accuracy, speed, heading
    - Battery level
    - Movement status
    - Activity type (still, walking, in_vehicle)

## Usage

### Run the Seeder

```bash
# Run all seeders (calls MasterSeeder)
php artisan db:seed

# Run only MasterSeeder
php artisan db:seed --class=MasterSeeder

# Fresh migration with seed
php artisan migrate:fresh --seed
```

### Test Data Access

#### Login Credentials

Use any of these accounts to test:

```
# Admin accounts
Email: sunil@agritech.lk (Pro org)
Email: nimal@greenvalley.lk (Basic org)
Email: kumari@freshharvest.lk (Free org)

# Owner accounts
Email: anura@agritech.lk
Email: chaminda@greenvalley.lk
Email: ravi@freshharvest.lk

# Driver accounts
Email: kamal@agritech.lk
Email: saman@greenvalley.lk
Email: janaka@freshharvest.lk

Password for all: Password123!
```

### Example Queries

```php
// Get all organizations with their usage
$orgs = Organization::with(['users', 'lands', 'fieldJobs', 'measurements'])->get();

// Get jobs with tracking logs
$jobs = FieldJob::with(['trackingLogs', 'driver', 'land'])
    ->where('status', 'in_progress')
    ->get();

// Get invoices with payments
$invoices = Invoice::with(['payments', 'job', 'customer'])->get();

// Get pro organization data
$proOrg = Organization::where('package_tier', 'pro')
    ->with(['users', 'lands', 'fieldJobs', 'invoices'])
    ->first();
```

## Data Relationships

```
Organization
├── Users (admin, owner, driver, viewer)
├── Lands
│   └── Measurements
├── FieldJobs
│   ├── Assigned Driver (User)
│   ├── Land
│   ├── Invoice
│   ├── Expenses
│   └── TrackingLogs
├── Invoices
│   ├── Job
│   ├── Customer (User)
│   └── Payments
├── Payments
│   ├── Invoice
│   └── Customer (User)
├── Expenses
│   ├── Job
│   └── Driver (User)
└── TrackingLogs
    ├── User (Driver)
    └── Job
```

## Realistic Data Features

### Sri Lankan Context

- Local names (Sinhala/Tamil names)
- Local phone numbers (+94)
- Actual GPS coordinates from Sri Lankan locations
- LKR currency for financial transactions
- Local agricultural practices (paddy, tea, coconut, spices)

### Business Logic

- Jobs are properly linked to lands
- Invoices are generated from completed jobs
- Payments match invoice amounts
- Expenses are realistic for job types
- Tracking logs show realistic movement patterns
- Usage respects subscription tier limits

### Time-based Data

- Historical data spread over 0-90 days
- Proper job lifecycle (scheduled → started → completed)
- Invoice dates after job completion
- Payment dates after invoice dates
- Tracking logs during job execution

## Customization

### Modify Organization Count

```php
// In seedOrganizations() method
$orgs = [
    // Add more organizations here
    [
        'name' => 'Your Organization',
        'contact_email' => 'contact@yourorg.lk',
        'package_tier' => 'basic',
        // ...
    ],
];
```

### Adjust Data Volume

```php
// In seedFieldJobs() method
$numJobs = match($org->package_tier) {
    'pro' => rand(20, 30),    // Increase range
    'basic' => rand(15, 20),
    'free' => rand(5, 10),
};
```

### Add More Land Locations

```php
// In seedLands() method
$landsData = [
    // Add more lands with different GPS coordinates
    [
        'org_idx' => 0,
        'name' => 'New Land Name',
        'coords' => [[lat, lng], [lat, lng], ...],
        'district' => 'District Name',
        'province' => 'Province Name'
    ],
];
```

## Performance

### Seeding Time

- Full seed: ~30-60 seconds
- Creates 200+ records across 10 tables
- Efficient bulk operations

### Optimization Tips

1. Disable model events if not needed:

    ```php
    Model::withoutEvents(function () {
        // Seeding code
    });
    ```

2. Use chunks for large datasets:

    ```php
    foreach ($items as $chunk) {
        Model::insert($chunk);
    }
    ```

3. Disable query log in production:
    ```php
    DB::connection()->disableQueryLog();
    ```

## Verification

### Check Seeded Data

```bash
# Count records
php artisan tinker
>>> Organization::count()
=> 3
>>> User::count()
=> 18
>>> Land::count()
=> 24
>>> FieldJob::count()
=> 25+

# Check subscription usage
>>> $org = Organization::first()
>>> $org->lands()->count()
>>> $org->fieldJobs()->count()
>>> $org->users()->where('role', 'driver')->count()
```

### Verify Relationships

```bash
php artisan tinker
>>> $job = FieldJob::with(['driver', 'land', 'invoice', 'expenses', 'trackingLogs'])->first()
>>> $job->driver->email
>>> $job->invoice->invoice_number
>>> $job->expenses->count()
>>> $job->trackingLogs->count()
```

## Troubleshooting

### Foreign Key Errors

- Ensure migrations are run first: `php artisan migrate:fresh`
- Check that subscription_packages table is seeded first

### Duplicate Entry Errors

- Use `firstOrCreate()` instead of `create()`
- Clear database before re-seeding: `php artisan migrate:fresh --seed`

### Memory Issues

- Reduce data volume in configuration
- Use chunk processing for large datasets
- Increase PHP memory limit: `ini_set('memory_limit', '512M')`

## Notes

- Seeder is idempotent using `firstOrCreate()` - safe to run multiple times
- All dates are relative to current time for realistic testing
- GPS coordinates are from actual Sri Lankan locations
- Phone numbers use Sri Lankan format (+94)
- Currency is LKR (Sri Lankan Rupees)
- All relationships are properly maintained
- Soft deletes are respected

## Future Enhancements

Potential additions:

- Equipment/machinery tracking
- Crop yield data
- Weather data integration
- Soil analysis records
- Pesticide/fertilizer usage logs
- Customer feedback/ratings
- Multi-year historical data
- Seasonal variations
