# GeoOps Backend - Laravel API

Production-ready Laravel backend for GeoOps Platform following Clean Architecture principles.

## Features

- 🏗️ Clean Architecture (Controllers, Services, Repositories, DTOs)
- 🔐 JWT Authentication with role-based authorization
- 🗺️ Spatial data support for GPS measurements
- 📄 Automated PDF invoice generation
- 🚀 Background job processing
- 📊 Comprehensive reporting
- 🔄 Offline sync support
- 🏢 Multi-tenancy (organization-level data isolation)

## Requirements

- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0+ or PostgreSQL 14+ (with spatial extensions)
- Redis (for queue and cache)
- Node.js & NPM (for asset compilation)

## Installation

### 1. Clone and Install Dependencies

```bash
cd backend
composer install
npm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database and Redis credentials:

```env
APP_NAME="GeoOps API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

JWT_SECRET=
JWT_TTL=60
```

### 3. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 4. JWT Setup

```bash
php artisan jwt:secret
```

### 5. Storage Setup

```bash
php artisan storage:link
```

### 6. Start Development Server

```bash
# Start Laravel server
php artisan serve

# In another terminal, start queue worker
php artisan queue:work

# (Optional) Start scheduler
php artisan schedule:work
```

## Project Structure

```
backend/
├── app/
│   ├── Console/              # Artisan commands
│   ├── DTOs/                 # Data Transfer Objects
│   │   ├── Auth/
│   │   ├── Measurement/
│   │   ├── Job/
│   │   └── Invoice/
│   ├── Exceptions/           # Custom exceptions
│   ├── Http/
│   │   ├── Controllers/      # Thin controllers
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── MeasurementController.php
│   │   │   │   ├── JobController.php
│   │   │   │   ├── InvoiceController.php
│   │   │   │   └── ...
│   │   ├── Middleware/       # Custom middleware
│   │   ├── Requests/         # Form requests (validation)
│   │   └── Resources/        # API resources (response formatting)
│   ├── Jobs/                 # Background jobs
│   │   ├── GenerateInvoicePdf.php
│   │   ├── SyncOfflineData.php
│   │   └── ...
│   ├── Models/               # Eloquent models
│   │   ├── User.php
│   │   ├── Organization.php
│   │   ├── LandMeasurement.php
│   │   ├── Job.php
│   │   └── ...
│   ├── Repositories/         # Data access layer
│   │   ├── Contracts/        # Repository interfaces
│   │   └── Eloquent/         # Eloquent implementations
│   │       ├── MeasurementRepository.php
│   │       ├── JobRepository.php
│   │       └── ...
│   ├── Services/             # Business logic layer
│   │   ├── AuthService.php
│   │   ├── MeasurementService.php
│   │   ├── JobService.php
│   │   ├── InvoiceService.php
│   │   └── ...
│   └── Providers/
├── config/                   # Configuration files
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── routes/
│   ├── api.php               # API routes
│   └── web.php
├── storage/
│   ├── app/
│   │   ├── invoices/         # Generated invoices
│   │   └── receipts/         # Expense receipts
│   └── logs/
└── tests/
    ├── Feature/              # Feature tests
    └── Unit/                 # Unit tests
```

## Architecture Layers

### 1. Controllers (Presentation Layer)

Thin controllers that handle HTTP requests and responses:

```php
class MeasurementController extends Controller
{
    public function __construct(
        private MeasurementService $measurementService
    ) {}

    public function index(Request $request)
    {
        $measurements = $this->measurementService->list($request->all());
        return MeasurementResource::collection($measurements);
    }
}
```

### 2. Services (Business Logic Layer)

Business logic and orchestration:

```php
class MeasurementService
{
    public function __construct(
        private MeasurementRepository $repository,
        private AreaCalculator $calculator
    ) {}

    public function create(MeasurementDTO $dto): LandMeasurement
    {
        // Calculate area
        $area = $this->calculator->calculate($dto->coordinates);

        // Save measurement
        return $this->repository->create([
            'name' => $dto->name,
            'coordinates' => $dto->coordinates,
            'area_acres' => $area['acres'],
            'area_hectares' => $area['hectares'],
        ]);
    }
}
```

### 3. Repositories (Data Access Layer)

Database operations abstracted:

```php
class MeasurementRepository implements MeasurementRepositoryInterface
{
    public function create(array $data): LandMeasurement
    {
        return LandMeasurement::create($data);
    }

    public function findByOrganization(int $organizationId)
    {
        return LandMeasurement::where('organization_id', $organizationId)
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
```

### 4. DTOs (Data Transfer Objects)

Type-safe data containers:

```php
class MeasurementDTO
{
    public function __construct(
        public string $name,
        public array $coordinates,
        public DateTime $measuredAt,
        public int $organizationId,
        public int $measuredBy
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            coordinates: $data['coordinates'],
            measuredAt: new DateTime($data['measured_at']),
            organizationId: auth()->user()->organization_id,
            measuredBy: auth()->id()
        );
    }
}
```

## API Routes

All API routes are defined in `routes/api.php`:

```php
// Public routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // Measurements
    Route::apiResource('measurements', MeasurementController::class);

    // Jobs
    Route::apiResource('jobs', JobController::class);
    Route::put('jobs/{job}/status', [JobController::class, 'updateStatus']);

    // Invoices
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf']);

    // ... more routes
});
```

## Authentication & Authorization

### JWT Authentication

```php
// Authenticate user
$token = auth()->attempt($credentials);

// Get authenticated user
$user = auth()->user();

// Logout
auth()->logout();
```

### Role-Based Authorization

```php
// In middleware
if (!auth()->user()->hasRole(['owner', 'admin'])) {
    abort(403, 'Unauthorized');
}

// In policy
public function create(User $user): bool
{
    return in_array($user->role, ['owner', 'admin']);
}
```

### Organization-Level Data Isolation

```php
// Global scope on models
protected static function booted()
{
    static::addGlobalScope('organization', function ($query) {
        if (auth()->check()) {
            $query->where('organization_id', auth()->user()->organization_id);
        }
    });
}
```

## Background Jobs

### Generate Invoice PDF

```bash
php artisan queue:work
```

```php
GenerateInvoicePdf::dispatch($invoice);
```

### Scheduled Tasks

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Check subscription expiry daily
    $schedule->command('subscriptions:check-expiry')
        ->daily();

    // Clean old tracking logs monthly
    $schedule->command('tracking:cleanup')
        ->monthly();
}
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/MeasurementTest.php

# Run with coverage
php artisan test --coverage
```

## Database Seeding

```bash
# Seed everything
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=OrganizationSeeder

# Fresh migration with seed
php artisan migrate:fresh --seed
```

## API Documentation

API documentation is available at:

- Markdown: `/docs/API_SPECIFICATION.md`
- Postman Collection: `/docs/GeoOps.postman_collection.json`

## Deployment

### Production Checklist

1. Set environment to production:

   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Optimize application:

   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. Set up queue workers (Supervisor):

   ```bash
   sudo supervisorctl start geo-ops-worker:*
   ```

4. Set up scheduler (Cron):

   ```
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```

5. Configure database backups

6. Set up monitoring and error tracking

## Performance Optimization

- Database query optimization with eager loading
- Redis caching for frequently accessed data
- Queue jobs for heavy operations
- Database indexing on search fields
- API response caching

## Security Best Practices

- JWT token expiration and refresh
- Rate limiting on API endpoints
- Input validation and sanitization
- SQL injection prevention (Eloquent ORM)
- XSS protection
- CORS configuration
- HTTPS enforcement in production

## Troubleshooting

### Queue jobs not processing

```bash
# Restart queue worker
php artisan queue:restart

# Clear failed jobs
php artisan queue:flush
```

### Cache issues

```bash
# Clear all caches
php artisan optimize:clear
```

### Permission errors

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Support

For issues and questions, contact the development team.

## License

Proprietary - All rights reserved
