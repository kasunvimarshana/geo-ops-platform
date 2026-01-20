# Seed Data

## GeoOps Platform - Sample Data for Testing and Development

---

## Subscription Packages

### Free Package

```json
{
  "name": "free",
  "display_name": "Free Plan",
  "description": "Perfect for getting started with basic features",
  "max_measurements": 10,
  "max_drivers": 2,
  "max_jobs": 20,
  "max_lands": 5,
  "max_storage_mb": 100,
  "price_monthly": 0,
  "price_yearly": 0,
  "features": [
    "Basic land measurement",
    "GPS tracking",
    "Job management",
    "Basic reporting"
  ]
}
```

### Basic Package

```json
{
  "name": "basic",
  "display_name": "Basic Plan",
  "description": "For small farming operations",
  "max_measurements": 100,
  "max_drivers": 5,
  "max_jobs": 200,
  "max_lands": 25,
  "max_storage_mb": 500,
  "price_monthly": 2500,
  "price_yearly": 25000,
  "features": [
    "All Free features",
    "Invoice generation",
    "PDF exports",
    "Payment tracking",
    "Expense management",
    "Email support"
  ]
}
```

### Pro Package

```json
{
  "name": "pro",
  "display_name": "Pro Plan",
  "description": "For professional agricultural service providers",
  "max_measurements": -1,
  "max_drivers": -1,
  "max_jobs": -1,
  "max_lands": -1,
  "max_storage_mb": 5000,
  "price_monthly": 5000,
  "price_yearly": 50000,
  "features": [
    "All Basic features",
    "Unlimited measurements",
    "Unlimited drivers",
    "Unlimited jobs",
    "Advanced reports",
    "Financial analytics",
    "API access",
    "Priority support",
    "Custom branding"
  ]
}
```

---

## Sample Organizations

### Organization 1: Green Farm Services

```json
{
  "name": "Green Farm Services",
  "contact_name": "Kasun Perera",
  "contact_email": "kasun@greenfarm.lk",
  "contact_phone": "+94771234567",
  "address": "123 Main Street, Gampaha, Western Province, Sri Lanka",
  "package_tier": "basic",
  "package_expires_at": "2025-12-31 23:59:59",
  "is_active": true
}
```

### Organization 2: Lanka Plow Operators

```json
{
  "name": "Lanka Plow Operators",
  "contact_name": "Nimal Fernando",
  "contact_email": "nimal@lankaplow.lk",
  "contact_phone": "+94772345678",
  "address": "456 Temple Road, Kurunegala, North Western Province, Sri Lanka",
  "package_tier": "pro",
  "package_expires_at": "2025-12-31 23:59:59",
  "is_active": true
}
```

---

## Sample Users

### Admin User

```json
{
  "organization_id": 1,
  "role": "admin",
  "first_name": "Kasun",
  "last_name": "Perera",
  "email": "kasun@greenfarm.lk",
  "phone": "+94771234567",
  "password": "password123",
  "is_active": true
}
```

### Owner User

```json
{
  "organization_id": 1,
  "role": "owner",
  "first_name": "Sunil",
  "last_name": "Silva",
  "email": "sunil@greenfarm.lk",
  "phone": "+94773456789",
  "password": "password123",
  "is_active": true
}
```

### Driver User 1

```json
{
  "organization_id": 1,
  "role": "driver",
  "first_name": "Nimal",
  "last_name": "Jayawardena",
  "email": "nimal.driver@greenfarm.lk",
  "phone": "+94774567890",
  "password": "password123",
  "is_active": true
}
```

### Driver User 2

```json
{
  "organization_id": 1,
  "role": "driver",
  "first_name": "Chaminda",
  "last_name": "Kumara",
  "email": "chaminda.driver@greenfarm.lk",
  "phone": "+94775678901",
  "password": "password123",
  "is_active": true
}
```

### Accountant User

```json
{
  "organization_id": 1,
  "role": "accountant",
  "first_name": "Chamari",
  "last_name": "Fernando",
  "email": "chamari@greenfarm.lk",
  "phone": "+94776789012",
  "password": "password123",
  "is_active": true
}
```

---

## Sample Lands

### Land 1: Rice Field A - Gampaha

```json
{
  "organization_id": 1,
  "owner_user_id": 2,
  "name": "Rice Field A - Gampaha",
  "description": "Main rice cultivation field near Gampaha town",
  "coordinates": [
    { "lat": 7.0917, "lng": 79.995 },
    { "lat": 7.092, "lng": 79.9955 },
    { "lat": 7.0922, "lng": 79.996 },
    { "lat": 7.0925, "lng": 79.9957 },
    { "lat": 7.0924, "lng": 79.9952 },
    { "lat": 7.092, "lng": 79.9948 }
  ],
  "area_acres": 2.52,
  "area_hectares": 1.02,
  "area_square_meters": 10200,
  "center_latitude": 7.0921,
  "center_longitude": 79.9954,
  "location_address": "Near Gampaha Town",
  "location_district": "Gampaha",
  "location_province": "Western",
  "status": "active"
}
```

### Land 2: Vegetable Farm B - Kurunegala

```json
{
  "organization_id": 1,
  "owner_user_id": 2,
  "name": "Vegetable Farm B - Kurunegala",
  "description": "Mixed vegetable cultivation area",
  "coordinates": [
    { "lat": 7.4867, "lng": 80.3647 },
    { "lat": 7.487, "lng": 80.365 },
    { "lat": 7.4872, "lng": 80.3655 },
    { "lat": 7.4869, "lng": 80.3658 },
    { "lat": 7.4865, "lng": 80.3654 },
    { "lat": 7.4864, "lng": 80.3649 }
  ],
  "area_acres": 1.83,
  "area_hectares": 0.74,
  "area_square_meters": 7400,
  "center_latitude": 7.4868,
  "center_longitude": 80.3652,
  "location_address": "Kurunegala - Dambulla Road",
  "location_district": "Kurunegala",
  "location_province": "North Western",
  "status": "active"
}
```

### Land 3: Paddy Field C - Anuradhapura

```json
{
  "organization_id": 1,
  "owner_user_id": 2,
  "name": "Paddy Field C - Anuradhapura",
  "description": "Large paddy field for cultivation",
  "coordinates": [
    { "lat": 8.3114, "lng": 80.4037 },
    { "lat": 8.3118, "lng": 80.4042 },
    { "lat": 8.3122, "lng": 80.4047 },
    { "lat": 8.3126, "lng": 80.4045 },
    { "lat": 8.3125, "lng": 80.404 },
    { "lat": 8.312, "lng": 80.4035 }
  ],
  "area_acres": 3.75,
  "area_hectares": 1.52,
  "area_square_meters": 15200,
  "center_latitude": 8.312,
  "center_longitude": 80.4041,
  "location_address": "Anuradhapura - Medawachchiya Road",
  "location_district": "Anuradhapura",
  "location_province": "North Central",
  "status": "active"
}
```

---

## Sample Jobs

### Job 1: Plowing - Rice Field A

```json
{
  "organization_id": 1,
  "land_id": 1,
  "assigned_driver_id": 3,
  "created_by_user_id": 1,
  "job_number": "JOB-2024-001",
  "job_type": "plowing",
  "status": "completed",
  "scheduled_at": "2024-01-15 08:00:00",
  "started_at": "2024-01-15 08:15:00",
  "completed_at": "2024-01-15 10:30:00",
  "customer_name": "Sunil Silva",
  "customer_phone": "+94773456789",
  "customer_address": "123 Main Street, Gampaha",
  "rate_per_acre": 6000,
  "estimated_area_acres": 2.5,
  "estimated_cost": 15000,
  "actual_area_acres": 2.52,
  "actual_cost": 15120,
  "notes": "Job completed successfully. Weather was good."
}
```

### Job 2: Harvesting - Vegetable Farm B

```json
{
  "organization_id": 1,
  "land_id": 2,
  "assigned_driver_id": 4,
  "created_by_user_id": 1,
  "job_number": "JOB-2024-002",
  "job_type": "harvesting",
  "status": "in_progress",
  "scheduled_at": "2024-01-20 07:00:00",
  "started_at": "2024-01-20 07:10:00",
  "completed_at": null,
  "customer_name": "Ranjith Bandara",
  "customer_phone": "+94778901234",
  "customer_address": "Kurunegala - Dambulla Road",
  "rate_per_acre": 5000,
  "estimated_area_acres": 1.83,
  "estimated_cost": 9150,
  "actual_area_acres": null,
  "actual_cost": null,
  "notes": "Harvesting in progress"
}
```

### Job 3: Plowing - Paddy Field C

```json
{
  "organization_id": 1,
  "land_id": 3,
  "assigned_driver_id": 3,
  "created_by_user_id": 1,
  "job_number": "JOB-2024-003",
  "job_type": "plowing",
  "status": "pending",
  "scheduled_at": "2024-01-25 08:00:00",
  "started_at": null,
  "completed_at": null,
  "customer_name": "Mahinda Rajapaksa",
  "customer_phone": "+94779012345",
  "customer_address": "Anuradhapura - Medawachchiya Road",
  "rate_per_acre": 6500,
  "estimated_area_acres": 3.75,
  "estimated_cost": 24375,
  "actual_area_acres": null,
  "actual_cost": null,
  "notes": "Scheduled for next week"
}
```

---

## Sample Invoices

### Invoice 1: For Job 1

```json
{
  "organization_id": 1,
  "job_id": 1,
  "invoice_number": "INV-2024-001",
  "customer_name": "Sunil Silva",
  "customer_phone": "+94773456789",
  "customer_address": "123 Main Street, Gampaha",
  "line_items": [
    {
      "description": "Land plowing service - Rice Field A",
      "quantity": 2.52,
      "rate": 6000,
      "amount": 15120
    }
  ],
  "subtotal": 15120,
  "tax_amount": 0,
  "discount_amount": 0,
  "total_amount": 15120,
  "status": "paid",
  "issued_at": "2024-01-15 11:00:00",
  "due_at": "2024-01-22 23:59:59",
  "paid_at": "2024-01-15 14:30:00",
  "notes": "Thank you for your business!"
}
```

---

## Sample Payments

### Payment 1: For Invoice 1

```json
{
  "organization_id": 1,
  "invoice_id": 1,
  "payment_number": "PAY-2024-001",
  "amount": 15120,
  "payment_method": "cash",
  "reference_number": null,
  "paid_at": "2024-01-15 14:30:00",
  "notes": "Cash payment received in full"
}
```

---

## Sample Expenses

### Expense 1: Fuel for Job 1

```json
{
  "organization_id": 1,
  "job_id": 1,
  "driver_id": 3,
  "expense_number": "EXP-2024-001",
  "category": "fuel",
  "description": "Diesel for tractor - Job JOB-2024-001",
  "amount": 5000,
  "expense_date": "2024-01-15",
  "notes": "Full tank for plowing job"
}
```

### Expense 2: Maintenance

```json
{
  "organization_id": 1,
  "job_id": null,
  "driver_id": null,
  "expense_number": "EXP-2024-002",
  "category": "maintenance",
  "description": "Tractor oil change and filter replacement",
  "amount": 8500,
  "expense_date": "2024-01-10",
  "notes": "Regular maintenance service"
}
```

### Expense 3: Parts

```json
{
  "organization_id": 1,
  "job_id": null,
  "driver_id": null,
  "expense_number": "EXP-2024-003",
  "category": "parts",
  "description": "New plow blade",
  "amount": 12000,
  "expense_date": "2024-01-08",
  "notes": "Replacement part for worn blade"
}
```

---

## Sample Tracking Logs

GPS tracking data for Driver 1 during Job 1:

```json
[
  {
    "organization_id": 1,
    "driver_id": 3,
    "job_id": 1,
    "latitude": 7.0917,
    "longitude": 79.995,
    "altitude": 25.5,
    "accuracy": 5.0,
    "speed": 12.5,
    "heading": 45.0,
    "recorded_at": "2024-01-15 08:15:00"
  },
  {
    "organization_id": 1,
    "driver_id": 3,
    "job_id": 1,
    "latitude": 7.0918,
    "longitude": 79.9951,
    "altitude": 25.8,
    "accuracy": 4.8,
    "speed": 13.2,
    "heading": 46.5,
    "recorded_at": "2024-01-15 08:15:30"
  },
  {
    "organization_id": 1,
    "driver_id": 3,
    "job_id": 1,
    "latitude": 7.0919,
    "longitude": 79.9952,
    "altitude": 26.0,
    "accuracy": 5.2,
    "speed": 12.8,
    "heading": 45.8,
    "recorded_at": "2024-01-15 08:16:00"
  }
]
```

---

## Sample Measurements

### Measurement 1: Initial measurement for Rice Field A

```json
{
  "organization_id": 1,
  "land_id": 1,
  "measured_by_user_id": 1,
  "measurement_type": "walk_around",
  "coordinates": [
    {
      "lat": 7.0917,
      "lng": 79.995,
      "timestamp": "2024-01-10T10:00:00Z",
      "accuracy": 5.2
    },
    {
      "lat": 7.092,
      "lng": 79.9955,
      "timestamp": "2024-01-10T10:00:15Z",
      "accuracy": 4.8
    },
    {
      "lat": 7.0922,
      "lng": 79.996,
      "timestamp": "2024-01-10T10:00:30Z",
      "accuracy": 5.0
    },
    {
      "lat": 7.0925,
      "lng": 79.9957,
      "timestamp": "2024-01-10T10:00:45Z",
      "accuracy": 4.9
    },
    {
      "lat": 7.0924,
      "lng": 79.9952,
      "timestamp": "2024-01-10T10:01:00Z",
      "accuracy": 5.1
    },
    {
      "lat": 7.092,
      "lng": 79.9948,
      "timestamp": "2024-01-10T10:01:15Z",
      "accuracy": 5.3
    }
  ],
  "area_acres": 2.52,
  "area_hectares": 1.02,
  "area_square_meters": 10200,
  "perimeter_meters": 405.5,
  "center_latitude": 7.0921,
  "center_longitude": 79.9954,
  "measured_at": "2024-01-10 10:00:00",
  "duration_seconds": 75,
  "sync_status": "synced",
  "synced_at": "2024-01-10 10:02:00",
  "notes": "Clear weather, good GPS signal"
}
```

---

## Seeder Files

### SubscriptionPackageSeeder.php

```php
<?php

namespace Database\Seeders;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Seeder;

class SubscriptionPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'free',
                'display_name' => 'Free Plan',
                'description' => 'Perfect for getting started with basic features',
                'max_measurements' => 10,
                'max_drivers' => 2,
                'max_jobs' => 20,
                'max_lands' => 5,
                'max_storage_mb' => 100,
                'price_monthly' => 0,
                'price_yearly' => 0,
                'features' => json_encode([
                    'Basic land measurement',
                    'GPS tracking',
                    'Job management',
                    'Basic reporting'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'basic',
                'display_name' => 'Basic Plan',
                'description' => 'For small farming operations',
                'max_measurements' => 100,
                'max_drivers' => 5,
                'max_jobs' => 200,
                'max_lands' => 25,
                'max_storage_mb' => 500,
                'price_monthly' => 2500,
                'price_yearly' => 25000,
                'features' => json_encode([
                    'All Free features',
                    'Invoice generation',
                    'PDF exports',
                    'Payment tracking',
                    'Expense management',
                    'Email support'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'pro',
                'display_name' => 'Pro Plan',
                'description' => 'For professional agricultural service providers',
                'max_measurements' => -1,
                'max_drivers' => -1,
                'max_jobs' => -1,
                'max_lands' => -1,
                'max_storage_mb' => 5000,
                'price_monthly' => 5000,
                'price_yearly' => 50000,
                'features' => json_encode([
                    'All Basic features',
                    'Unlimited measurements',
                    'Unlimited drivers',
                    'Unlimited jobs',
                    'Advanced reports',
                    'Financial analytics',
                    'API access',
                    'Priority support',
                    'Custom branding'
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::create($package);
        }
    }
}
```

---

## Test Credentials

### Admin Account

- **Email**: kasun@greenfarm.lk
- **Password**: password123
- **Role**: Admin
- **Organization**: Green Farm Services

### Driver Account

- **Email**: nimal.driver@greenfarm.lk
- **Password**: password123
- **Role**: Driver
- **Organization**: Green Farm Services

### Accountant Account

- **Email**: chamari@greenfarm.lk
- **Password**: password123
- **Role**: Accountant
- **Organization**: Green Farm Services

---

## Running Seeders

```bash
# Seed subscription packages only
php artisan db:seed --class=SubscriptionPackageSeeder

# Seed all sample data
php artisan db:seed --class=SampleDataSeeder

# Reset and seed everything
php artisan migrate:fresh --seed
```

---

## Notes

1. **Passwords**: All sample users use `password123` - this should be changed in production
2. **Coordinates**: GPS coordinates are for actual locations in Sri Lanka
3. **Amounts**: Amounts are in Sri Lankan Rupees (LKR)
4. **Phone Numbers**: Sample phone numbers use Sri Lankan format (+94)
5. **Districts**: Based on actual Sri Lankan districts and provinces
6. **Job Types**: Common agricultural service types in Sri Lanka
7. **Expense Categories**: Typical operational expenses

---

## Production Considerations

When deploying to production:

1. **Remove Sample Data**: Don't seed sample organizations/users/jobs
2. **Keep Subscription Packages**: These are required for the system
3. **Secure Credentials**: Use strong, unique passwords
4. **Validate Data**: Ensure all data meets validation requirements
5. **Test First**: Always test seeders in staging environment first
