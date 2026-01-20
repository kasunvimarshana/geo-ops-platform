# Backend Project Structure

## GeoOps Platform - Laravel Backend

### Clean Architecture Structure

```
backend/
├── app/
│   ├── Console/
│   │   └── Commands/              # Custom Artisan commands
│   ├── DTOs/                       # Data Transfer Objects
│   │   ├── Auth/
│   │   │   ├── LoginDTO.php
│   │   │   └── RegisterDTO.php
│   │   ├── Land/
│   │   │   ├── CreateLandDTO.php
│   │   │   └── UpdateLandDTO.php
│   │   ├── Measurement/
│   │   │   ├── CreateMeasurementDTO.php
│   │   │   └── BatchMeasurementDTO.php
│   │   ├── Job/
│   │   │   ├── CreateJobDTO.php
│   │   │   └── UpdateJobStatusDTO.php
│   │   ├── Invoice/
│   │   │   └── CreateInvoiceDTO.php
│   │   ├── Payment/
│   │   │   └── CreatePaymentDTO.php
│   │   └── Expense/
│   │       └── CreateExpenseDTO.php
│   │
│   ├── Exceptions/
│   │   ├── Handler.php
│   │   ├── BusinessException.php
│   │   ├── UnauthorizedException.php
│   │   └── SubscriptionLimitException.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── LandController.php
│   │   │   │   ├── MeasurementController.php
│   │   │   │   ├── JobController.php
│   │   │   │   ├── TrackingController.php
│   │   │   │   ├── InvoiceController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── ExpenseController.php
│   │   │   │   ├── SubscriptionController.php
│   │   │   │   └── ReportController.php
│   │   │   └── Controller.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── OrganizationContext.php
│   │   │   ├── RoleAuthorization.php
│   │   │   ├── SubscriptionCheck.php
│   │   │   └── ApiLogger.php
│   │   │
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Land/
│   │   │   │   ├── CreateLandRequest.php
│   │   │   │   └── UpdateLandRequest.php
│   │   │   ├── Measurement/
│   │   │   │   ├── CreateMeasurementRequest.php
│   │   │   │   └── BatchMeasurementRequest.php
│   │   │   ├── Job/
│   │   │   │   ├── CreateJobRequest.php
│   │   │   │   └── UpdateJobStatusRequest.php
│   │   │   ├── Invoice/
│   │   │   │   └── CreateInvoiceRequest.php
│   │   │   ├── Payment/
│   │   │   │   └── CreatePaymentRequest.php
│   │   │   └── Expense/
│   │   │       └── CreateExpenseRequest.php
│   │   │
│   │   └── Resources/
│   │       ├── UserResource.php
│   │       ├── LandResource.php
│   │       ├── MeasurementResource.php
│   │       ├── JobResource.php
│   │       ├── InvoiceResource.php
│   │       ├── PaymentResource.php
│   │       └── ExpenseResource.php
│   │
│   ├── Jobs/
│   │   ├── GenerateInvoicePdfJob.php
│   │   ├── GenerateReportJob.php
│   │   ├── SyncOfflineDataJob.php
│   │   └── CleanupOldTrackingLogsJob.php
│   │
│   ├── Models/
│   │   ├── Organization.php
│   │   ├── User.php
│   │   ├── Land.php
│   │   ├── Measurement.php
│   │   ├── Job.php
│   │   ├── TrackingLog.php
│   │   ├── Invoice.php
│   │   ├── Payment.php
│   │   ├── Expense.php
│   │   ├── SubscriptionPackage.php
│   │   └── AuditLog.php
│   │
│   ├── Repositories/
│   │   ├── Contracts/                    # Repository interfaces
│   │   │   ├── OrganizationRepositoryInterface.php
│   │   │   ├── UserRepositoryInterface.php
│   │   │   ├── LandRepositoryInterface.php
│   │   │   ├── MeasurementRepositoryInterface.php
│   │   │   ├── JobRepositoryInterface.php
│   │   │   ├── TrackingLogRepositoryInterface.php
│   │   │   ├── InvoiceRepositoryInterface.php
│   │   │   ├── PaymentRepositoryInterface.php
│   │   │   └── ExpenseRepositoryInterface.php
│   │   │
│   │   ├── OrganizationRepository.php
│   │   ├── UserRepository.php
│   │   ├── LandRepository.php
│   │   ├── MeasurementRepository.php
│   │   ├── JobRepository.php
│   │   ├── TrackingLogRepository.php
│   │   ├── InvoiceRepository.php
│   │   ├── PaymentRepository.php
│   │   └── ExpenseRepository.php
│   │
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── UserService.php
│   │   ├── LandService.php
│   │   ├── MeasurementService.php
│   │   ├── GeoCalculationService.php    # GPS area calculations
│   │   ├── JobService.php
│   │   ├── TrackingService.php
│   │   ├── InvoiceService.php
│   │   ├── PaymentService.php
│   │   ├── ExpenseService.php
│   │   ├── SubscriptionService.php
│   │   ├── ReportService.php
│   │   └── PdfService.php
│   │
│   ├── Traits/
│   │   ├── HasAuditFields.php
│   │   ├── BelongsToOrganization.php
│   │   └── HasSoftDeletes.php
│   │
│   └── Providers/
│       ├── AppServiceProvider.php
│       ├── AuthServiceProvider.php
│       ├── RepositoryServiceProvider.php
│       └── RouteServiceProvider.php
│
├── bootstrap/
│   ├── app.php
│   └── cache/
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── jwt.php                     # JWT configuration
│   ├── subscription.php            # Subscription limits
│   └── services.php
│
├── database/
│   ├── factories/
│   │   ├── OrganizationFactory.php
│   │   ├── UserFactory.php
│   │   ├── LandFactory.php
│   │   ├── JobFactory.php
│   │   └── InvoiceFactory.php
│   │
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_organizations_table.php
│   │   ├── 2024_01_01_000002_create_users_table.php
│   │   ├── 2024_01_01_000003_create_lands_table.php
│   │   ├── 2024_01_01_000004_create_measurements_table.php
│   │   ├── 2024_01_01_000005_create_jobs_table.php
│   │   ├── 2024_01_01_000006_create_tracking_logs_table.php
│   │   ├── 2024_01_01_000007_create_invoices_table.php
│   │   ├── 2024_01_01_000008_create_payments_table.php
│   │   ├── 2024_01_01_000009_create_expenses_table.php
│   │   ├── 2024_01_01_000010_create_subscription_packages_table.php
│   │   └── 2024_01_01_000011_create_audit_logs_table.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── SubscriptionPackageSeeder.php
│       ├── OrganizationSeeder.php
│       ├── UserSeeder.php
│       └── SampleDataSeeder.php
│
├── public/
│   ├── index.php
│   └── storage/                    # Symlink to storage/app/public
│
├── resources/
│   ├── lang/
│   │   ├── en/
│   │   └── si/                     # Sinhala translations
│   └── views/
│       ├── pdf/
│       │   ├── invoice.blade.php
│       │   └── report.blade.php
│       └── emails/
│
├── routes/
│   ├── api.php                     # API routes
│   ├── web.php
│   ├── console.php
│   └── channels.php
│
├── storage/
│   ├── app/
│   │   ├── public/                 # Public files (PDFs, receipts)
│   │   └── private/                # Private files
│   ├── framework/
│   ├── logs/
│   └── testing/
│
├── tests/
│   ├── Feature/
│   │   ├── AuthTest.php
│   │   ├── LandTest.php
│   │   ├── MeasurementTest.php
│   │   ├── JobTest.php
│   │   └── InvoiceTest.php
│   ├── Unit/
│   │   ├── GeoCalculationServiceTest.php
│   │   ├── SubscriptionServiceTest.php
│   │   └── ReportServiceTest.php
│   └── TestCase.php
│
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── phpunit.xml
└── README.md
```

---

## Layer Responsibilities

### Controllers (Thin)

- Accept HTTP requests
- Validate input using Form Requests
- Call service layer methods
- Return formatted JSON responses
- NO business logic

**Example:**

```php
public function store(CreateLandRequest $request): JsonResponse
{
    $dto = CreateLandDTO::fromRequest($request);
    $land = $this->landService->createLand($dto);

    return response()->json([
        'success' => true,
        'data' => new LandResource($land)
    ], 201);
}
```

### Services (Business Logic)

- All business workflows
- Coordinate between multiple repositories
- Transaction management
- Complex calculations
- Event dispatching
- Job queuing

**Example:**

```php
public function createLand(CreateLandDTO $dto): Land
{
    return DB::transaction(function () use ($dto) {
        $land = $this->landRepository->create($dto->toArray());

        // Calculate area
        $area = $this->geoService->calculateArea($dto->coordinates);
        $land->update($area);

        // Create initial measurement
        $this->measurementService->createFromLand($land);

        return $land;
    });
}
```

### Repositories (Data Access)

- All database queries
- Eloquent interactions
- Query optimization
- Organization filtering
- Interface contracts

**Example:**

```php
public function findByOrganization(int $organizationId, array $filters = []): Collection
{
    return Land::where('organization_id', $organizationId)
        ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
        ->with(['owner', 'measurements'])
        ->get();
}
```

### DTOs (Data Transfer Objects)

- Type-safe data containers
- Transform request data
- Decouple from Eloquent
- Validation-ready structures

**Example:**

```php
class CreateLandDTO
{
    public function __construct(
        public readonly string $name,
        public readonly array $coordinates,
        public readonly ?string $description = null,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            name: $request->input('name'),
            coordinates: $request->input('coordinates'),
            description: $request->input('description'),
        );
    }
}
```

---

## Key Services

### GeoCalculationService

- Calculate area from GPS coordinates (acres, hectares, sq meters)
- Calculate perimeter
- Calculate center point
- Validate polygon integrity
- Convert between coordinate systems

### SubscriptionService

- Check usage limits
- Enforce package restrictions
- Handle package upgrades
- Track usage metrics

### PdfService

- Generate invoice PDFs
- Generate reports
- Store files securely
- Manage PDF queue

### ReportService

- Financial summaries
- Job reports
- Driver performance
- Custom date ranges

---

## Middleware Flow

```
Request
  ↓
Authenticate (JWT validation)
  ↓
OrganizationContext (Set current org)
  ↓
RoleAuthorization (Check permissions)
  ↓
SubscriptionCheck (Validate limits)
  ↓
Controller
  ↓
Service
  ↓
Repository
  ↓
Response
```

---

## Database Connection

**Primary**: MySQL 8.0+ or PostgreSQL 13+
**Cache**: Redis
**Queue**: Redis
**Session**: Redis

---

## Background Jobs

### GenerateInvoicePdfJob

- Generate PDF from invoice data
- Store in storage/app/public/invoices
- Update invoice record with path
- Notify on completion

### SyncOfflineDataJob

- Process batch measurements
- Handle conflicts
- Update sync status
- Log errors

### CleanupOldTrackingLogsJob

- Archive logs older than 90 days
- Optimize database
- Run daily

---

## API Routes Structure

```php
// routes/api.php

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware(['auth:api', 'organization', 'subscription'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('lands', LandController::class);
        Route::apiResource('measurements', MeasurementController::class);
        Route::post('measurements/batch', [MeasurementController::class, 'batch']);
        Route::apiResource('jobs', JobController::class);
        Route::put('jobs/{id}/status', [JobController::class, 'updateStatus']);
        Route::post('jobs/{id}/complete', [JobController::class, 'complete']);
        // ... more routes
    });
});
```

---

## Environment Variables

```env
# Application
APP_NAME="GeoOps Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.geo-ops.lk

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# JWT
JWT_SECRET=your-secret-key
JWT_TTL=60
JWT_REFRESH_TTL=43200

# Queue
QUEUE_CONNECTION=redis

# Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=

# Subscription limits
FREE_MAX_MEASUREMENTS=10
FREE_MAX_DRIVERS=2
FREE_MAX_JOBS=20
BASIC_MAX_MEASUREMENTS=100
BASIC_MAX_DRIVERS=5
BASIC_MAX_JOBS=200
```

---

## Testing Strategy

### Feature Tests

- Test complete API endpoints
- Include authentication
- Test authorization
- Validate responses

### Unit Tests

- Test services in isolation
- Test calculations
- Test business logic
- Mock repositories

### Running Tests

```bash
php artisan test
php artisan test --filter LandTest
php artisan test --coverage
```

---

## Deployment Commands

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed data
php artisan db:seed --class=SubscriptionPackageSeeder

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link

# Queue worker
php artisan queue:work redis --tries=3
```

---

## Best Practices

1. **Always use DTOs** for data transfer between layers
2. **Keep controllers thin** - no business logic
3. **Use repositories** for all database access
4. **Use transactions** for multi-step operations
5. **Type hint everything** for better IDE support
6. **Use form requests** for validation
7. **Use resources** for response formatting
8. **Log all errors** with context
9. **Write tests** for critical paths
10. **Follow PSR-12** coding standards
