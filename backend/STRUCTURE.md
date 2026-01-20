# Backend - Laravel Clean Architecture

## Directory Structure

```
backend/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/          # Thin controllers
│   │   │   ├── Api/
│   │   │   │   ├── V1/
│   │   │   │   │   ├── AuthController.php
│   │   │   │   │   ├── UserController.php
│   │   │   │   │   ├── MeasurementController.php
│   │   │   │   │   ├── JobController.php
│   │   │   │   │   ├── TrackingController.php
│   │   │   │   │   ├── InvoiceController.php
│   │   │   │   │   ├── PaymentController.php
│   │   │   │   │   ├── ExpenseController.php
│   │   │   │   │   ├── MachineController.php
│   │   │   │   │   ├── ReportController.php
│   │   │   │   │   └── SyncController.php
│   │   ├── Middleware/
│   │   │   ├── AuthenticateJWT.php
│   │   │   ├── RoleMiddleware.php
│   │   │   ├── OrganizationMiddleware.php
│   │   │   ├── SubscriptionMiddleware.php
│   │   │   └── RateLimitMiddleware.php
│   │   ├── Requests/            # Form request validators
│   │   │   ├── Auth/
│   │   │   ├── Measurement/
│   │   │   ├── Job/
│   │   │   ├── Invoice/
│   │   │   └── ...
│   │   └── Resources/           # API Resources (DTOs)
│   │       ├── UserResource.php
│   │       ├── MeasurementResource.php
│   │       ├── JobResource.php
│   │       └── ...
│   ├── Models/                   # Eloquent models
│   │   ├── User.php
│   │   ├── Organization.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── Subscription.php
│   │   ├── Machine.php
│   │   ├── Measurement.php
│   │   ├── MeasurementPolygon.php
│   │   ├── Job.php
│   │   ├── JobAssignment.php
│   │   ├── GpsTracking.php
│   │   ├── Invoice.php
│   │   ├── InvoiceItem.php
│   │   ├── Payment.php
│   │   ├── Expense.php
│   │   ├── Ledger.php
│   │   ├── SyncQueue.php
│   │   └── RateCard.php
│   ├── Services/                 # Business logic layer
│   │   ├── Auth/
│   │   │   ├── AuthService.php
│   │   │   └── JWTService.php
│   │   ├── User/
│   │   │   └── UserService.php
│   │   ├── Measurement/
│   │   │   ├── MeasurementService.php
│   │   │   └── AreaCalculationService.php
│   │   ├── Job/
│   │   │   └── JobService.php
│   │   ├── Tracking/
│   │   │   └── TrackingService.php
│   │   ├── Billing/
│   │   │   ├── InvoiceService.php
│   │   │   └── PaymentService.php
│   │   ├── Expense/
│   │   │   └── ExpenseService.php
│   │   ├── Report/
│   │   │   └── ReportService.php
│   │   ├── Subscription/
│   │   │   └── SubscriptionService.php
│   │   └── Sync/
│   │       └── SyncService.php
│   ├── Repositories/             # Data access layer
│   │   ├── Contracts/           # Repository interfaces
│   │   │   ├── UserRepositoryInterface.php
│   │   │   ├── MeasurementRepositoryInterface.php
│   │   │   ├── JobRepositoryInterface.php
│   │   │   └── ...
│   │   └── Eloquent/            # Eloquent implementations
│   │       ├── UserRepository.php
│   │       ├── MeasurementRepository.php
│   │       ├── JobRepository.php
│   │       ├── InvoiceRepository.php
│   │       ├── PaymentRepository.php
│   │       ├── ExpenseRepository.php
│   │       └── ...
│   ├── DTOs/                     # Data Transfer Objects
│   │   ├── Auth/
│   │   │   ├── LoginDTO.php
│   │   │   └── RegisterDTO.php
│   │   ├── Measurement/
│   │   │   └── CreateMeasurementDTO.php
│   │   └── ...
│   ├── Jobs/                     # Queue jobs
│   │   ├── GenerateInvoicePDF.php
│   │   ├── ProcessSyncQueue.php
│   │   ├── SendInvoiceEmail.php
│   │   ├── GenerateReport.php
│   │   └── UpdateLedger.php
│   ├── Events/                   # Domain events
│   │   ├── JobCompleted.php
│   │   ├── InvoiceCreated.php
│   │   ├── PaymentReceived.php
│   │   └── MeasurementCompleted.php
│   ├── Listeners/               # Event listeners
│   │   ├── CreateInvoiceOnJobCompletion.php
│   │   ├── UpdateLedgerOnPayment.php
│   │   └── NotifyJobAssignment.php
│   ├── Traits/                   # Reusable traits
│   │   ├── HasOrganization.php
│   │   ├── HasAuditFields.php
│   │   └── Searchable.php
│   └── Helpers/                  # Helper functions
│       ├── ResponseHelper.php
│       ├── GeoHelper.php
│       └── DateHelper.php
├── bootstrap/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── auth.php
│   ├── jwt.php
│   ├── cors.php
│   └── subscription.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_organizations_table.php
│   │   ├── 2024_01_01_000002_create_roles_table.php
│   │   ├── 2024_01_01_000003_create_permissions_table.php
│   │   ├── 2024_01_01_000004_create_users_table.php
│   │   ├── 2024_01_01_000005_create_role_permissions_table.php
│   │   ├── 2024_01_01_000006_create_subscriptions_table.php
│   │   ├── 2024_01_01_000007_create_machines_table.php
│   │   ├── 2024_01_01_000008_create_measurements_table.php
│   │   ├── 2024_01_01_000009_create_measurement_polygons_table.php
│   │   ├── 2024_01_01_000010_create_jobs_table.php
│   │   ├── 2024_01_01_000011_create_job_assignments_table.php
│   │   ├── 2024_01_01_000012_create_gps_tracking_table.php
│   │   ├── 2024_01_01_000013_create_invoices_table.php
│   │   ├── 2024_01_01_000014_create_invoice_items_table.php
│   │   ├── 2024_01_01_000015_create_payments_table.php
│   │   ├── 2024_01_01_000016_create_expenses_table.php
│   │   ├── 2024_01_01_000017_create_ledger_table.php
│   │   ├── 2024_01_01_000018_create_sync_queue_table.php
│   │   └── 2024_01_01_000019_create_rate_cards_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── PermissionSeeder.php
│   │   ├── OrganizationSeeder.php
│   │   ├── UserSeeder.php
│   │   └── DemoDataSeeder.php
│   └── factories/
│       ├── OrganizationFactory.php
│       ├── UserFactory.php
│       ├── MeasurementFactory.php
│       └── ...
├── routes/
│   ├── api.php                   # API routes
│   └── web.php
├── storage/
│   ├── app/
│   │   ├── public/
│   │   │   ├── invoices/
│   │   │   ├── reports/
│   │   │   └── receipts/
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── Measurement/
│   │   ├── Job/
│   │   └── Invoice/
│   └── Unit/
│       ├── Services/
│       └── Repositories/
├── .env.example
├── .gitignore
├── composer.json
├── phpunit.xml
├── artisan
└── README.md
```

## Key Architectural Decisions

### 1. Clean Architecture Layers

**Presentation Layer (HTTP)**
- Controllers are thin and only handle HTTP concerns
- Request validation via Form Requests
- Response transformation via API Resources
- Middleware for cross-cutting concerns

**Application Layer (Services)**
- All business logic lives in Service classes
- Services orchestrate between repositories
- Single responsibility per service
- No direct database access from services

**Domain Layer (Models & Repositories)**
- Models contain domain logic and relationships
- Repository pattern abstracts data access
- Repositories return models or collections
- Interface segregation for testability

**Infrastructure Layer (External)**
- Queue jobs for background processing
- Events and listeners for decoupling
- External service integrations

### 2. Dependency Injection

All dependencies injected via constructor:
```php
class MeasurementController extends Controller
{
    public function __construct(
        private MeasurementService $measurementService
    ) {}
}

class MeasurementService
{
    public function __construct(
        private MeasurementRepositoryInterface $measurementRepository,
        private AreaCalculationService $areaCalculator
    ) {}
}
```

### 3. Repository Pattern

Interface-based repositories bound in service provider:
```php
// In AppServiceProvider
$this->app->bind(
    MeasurementRepositoryInterface::class,
    MeasurementRepository::class
);
```

### 4. Request/Response Flow

```
Request → Middleware → Controller → Service → Repository → Database
                                       ↓
                                    Response
```

### 5. Error Handling

Centralized exception handler with:
- Validation exceptions → 422
- Authentication exceptions → 401
- Authorization exceptions → 403
- Not found exceptions → 404
- Generic exceptions → 500

### 6. Authentication Flow

1. User logs in → JWT token generated
2. Token stored in response
3. Subsequent requests include token in header
4. Middleware validates token and loads user
5. Organization context set from user

### 7. Multi-tenancy

Organization-level isolation:
- Global scope on models to filter by organization
- Middleware sets organization context
- Foreign keys reference organization_id
- Prevents cross-organization data access

### 8. Background Jobs

Queue-based processing for:
- PDF generation (invoices, reports)
- Email/SMS notifications
- Sync processing
- Data exports
- Ledger updates

### 9. Event-Driven Architecture

Domain events trigger side effects:
```php
// When job completed
event(new JobCompleted($job));

// Listener creates invoice
class CreateInvoiceOnJobCompletion
{
    public function handle(JobCompleted $event)
    {
        $this->invoiceService->createFromJob($event->job);
    }
}
```

### 10. Testing Strategy

- Feature tests for API endpoints
- Unit tests for services and repositories
- Test database for isolation
- Factory pattern for test data
- Mock external dependencies

## SOLID Principles Applied

**Single Responsibility**
- Each service handles one domain
- Controllers only handle HTTP
- Repositories only handle data access

**Open/Closed**
- Services extend behavior via composition
- Repository interfaces allow implementation swaps
- Middleware pipeline extensible

**Liskov Substitution**
- Repository interfaces allow any implementation
- Service dependencies via interfaces

**Interface Segregation**
- Focused repository interfaces
- Specific service contracts

**Dependency Inversion**
- Controllers depend on service interfaces
- Services depend on repository interfaces
- No direct database access from business logic

## Performance Optimizations

1. **Database Queries**
   - Eager loading to prevent N+1
   - Proper indexing on foreign keys
   - Query optimization via repositories

2. **Caching**
   - Redis for session data
   - Cache subscription features
   - Cache organization settings

3. **Queue Workers**
   - Background processing for heavy tasks
   - Multiple queue priorities
   - Failed job handling

4. **API Response**
   - Pagination for large datasets
   - Resource transformation for consistent format
   - Minimal data transfer

## Security Measures

1. **Authentication**
   - JWT tokens with expiry
   - Refresh token rotation
   - Password hashing (bcrypt)

2. **Authorization**
   - Role-based access control
   - Permission checking in middleware
   - Organization-level isolation

3. **Input Validation**
   - Form request validation
   - Sanitization of user input
   - SQL injection prevention (Eloquent)

4. **Rate Limiting**
   - Per-user request limits
   - Per-organization limits
   - Throttle middleware

5. **Audit Trail**
   - Created_by, updated_by fields
   - Soft deletes for recoverability
   - Activity logging

---

This structure ensures maintainability, testability, and scalability for production use.
