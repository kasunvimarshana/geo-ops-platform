# Project Structure - GeoOps Platform

## Root Directory

```
geo-ops-platform/
├── backend/                    # Laravel REST API
├── frontend/                   # React Native (Expo) Mobile App
├── docs/                       # Documentation
│   ├── ARCHITECTURE.md         # System architecture
│   ├── API_SPECIFICATION.md    # API documentation
│   ├── DATABASE_SCHEMA.md      # Database design
│   ├── DEPLOYMENT.md           # Deployment guide
│   ├── SETUP_GUIDE.md          # Development setup
│   ├── SEED_DATA.md            # Sample data
│   └── PROJECT_STRUCTURE.md    # This file
├── .gitignore
└── README.md                   # Main project README
```

## Backend Structure (Laravel)

```
backend/
├── app/
│   ├── Console/                # Artisan commands
│   │   ├── Commands/
│   │   │   ├── CheckSubscriptionExpiry.php
│   │   │   └── CleanupTrackingLogs.php
│   │   └── Kernel.php
│   │
│   ├── DTOs/                   # Data Transfer Objects
│   │   ├── Auth/
│   │   │   ├── LoginDTO.php
│   │   │   └── RegisterDTO.php
│   │   ├── Measurement/
│   │   │   ├── CreateMeasurementDTO.php
│   │   │   └── UpdateMeasurementDTO.php
│   │   ├── Job/
│   │   │   ├── CreateJobDTO.php
│   │   │   └── UpdateJobStatusDTO.php
│   │   └── Invoice/
│   │       ├── CreateInvoiceDTO.php
│   │       └── GenerateInvoiceDTO.php
│   │
│   ├── Exceptions/             # Custom exceptions
│   │   ├── Handler.php
│   │   ├── SubscriptionLimitException.php
│   │   ├── UnauthorizedException.php
│   │   └── ValidationException.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── MeasurementController.php
│   │   │       ├── JobController.php
│   │   │       ├── DriverController.php
│   │   │       ├── CustomerController.php
│   │   │       ├── MachineController.php
│   │   │       ├── TrackingController.php
│   │   │       ├── InvoiceController.php
│   │   │       ├── ExpenseController.php
│   │   │       ├── PaymentController.php
│   │   │       ├── ReportController.php
│   │   │       ├── SyncController.php
│   │   │       └── SubscriptionController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── CheckSubscription.php
│   │   │   ├── CheckRole.php
│   │   │   ├── OrganizationScope.php
│   │   │   └── LogApiRequests.php
│   │   │
│   │   ├── Requests/           # Form request validation
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Measurement/
│   │   │   │   ├── StoreMeasurementRequest.php
│   │   │   │   └── UpdateMeasurementRequest.php
│   │   │   ├── Job/
│   │   │   │   ├── StoreJobRequest.php
│   │   │   │   └── UpdateJobRequest.php
│   │   │   └── Invoice/
│   │   │       └── StoreInvoiceRequest.php
│   │   │
│   │   └── Resources/          # API response resources
│   │       ├── UserResource.php
│   │       ├── MeasurementResource.php
│   │       ├── JobResource.php
│   │       ├── InvoiceResource.php
│   │       └── CustomerResource.php
│   │
│   ├── Jobs/                   # Background jobs
│   │   ├── GenerateInvoicePdf.php
│   │   ├── SendInvoiceEmail.php
│   │   ├── ProcessOfflineSync.php
│   │   └── GenerateMonthlyReport.php
│   │
│   ├── Models/                 # Eloquent models
│   │   ├── User.php
│   │   ├── Organization.php
│   │   ├── Driver.php
│   │   ├── Customer.php
│   │   ├── Machine.php
│   │   ├── LandMeasurement.php
│   │   ├── Job.php
│   │   ├── TrackingLog.php
│   │   ├── Invoice.php
│   │   ├── Payment.php
│   │   ├── Expense.php
│   │   ├── Subscription.php
│   │   └── AuditLog.php
│   │
│   ├── Repositories/           # Repository pattern
│   │   ├── Contracts/          # Interfaces
│   │   │   ├── MeasurementRepositoryInterface.php
│   │   │   ├── JobRepositoryInterface.php
│   │   │   ├── InvoiceRepositoryInterface.php
│   │   │   └── UserRepositoryInterface.php
│   │   │
│   │   └── Eloquent/           # Implementations
│   │       ├── MeasurementRepository.php
│   │       ├── JobRepository.php
│   │       ├── InvoiceRepository.php
│   │       └── UserRepository.php
│   │
│   ├── Services/               # Business logic
│   │   ├── AuthService.php
│   │   ├── MeasurementService.php
│   │   ├── JobService.php
│   │   ├── InvoiceService.php
│   │   ├── PaymentService.php
│   │   ├── ExpenseService.php
│   │   ├── TrackingService.php
│   │   ├── SyncService.php
│   │   ├── SubscriptionService.php
│   │   ├── ReportService.php
│   │   └── PdfService.php
│   │
│   └── Providers/
│       ├── AppServiceProvider.php
│       ├── AuthServiceProvider.php
│       ├── RouteServiceProvider.php
│       └── RepositoryServiceProvider.php
│
├── config/                     # Configuration files
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── jwt.php
│   ├── queue.php
│   └── services.php
│
├── database/
│   ├── factories/              # Model factories
│   │   ├── UserFactory.php
│   │   ├── OrganizationFactory.php
│   │   └── CustomerFactory.php
│   │
│   ├── migrations/             # Database migrations
│   │   ├── 2024_01_01_000001_create_organizations_table.php
│   │   ├── 2024_01_01_000002_create_users_table.php
│   │   ├── 2024_01_01_000003_create_customers_drivers_machines_tables.php
│   │   ├── 2024_01_01_000004_create_land_measurements_table.php
│   │   ├── 2024_01_01_000005_create_jobs_tracking_tables.php
│   │   ├── 2024_01_01_000006_create_invoices_payments_expenses_tables.php
│   │   └── 2024_01_01_000007_create_subscriptions_audit_logs_tables.php
│   │
│   └── seeders/                # Database seeders
│       ├── DatabaseSeeder.php
│       ├── OrganizationSeeder.php
│       ├── UserSeeder.php
│       ├── CustomerSeeder.php
│       └── SampleDataSeeder.php
│
├── routes/
│   ├── api.php                 # API routes
│   └── web.php                 # Web routes
│
├── storage/
│   ├── app/
│   │   ├── invoices/           # Generated PDF invoices
│   │   └── receipts/           # Expense receipts
│   ├── framework/
│   └── logs/
│
├── tests/
│   ├── Feature/                # Feature tests
│   │   ├── AuthTest.php
│   │   ├── MeasurementTest.php
│   │   ├── JobTest.php
│   │   └── InvoiceTest.php
│   │
│   └── Unit/                   # Unit tests
│       ├── MeasurementServiceTest.php
│       ├── AreaCalculatorTest.php
│       └── SubscriptionTest.php
│
├── .env.example
├── .gitignore
├── composer.json
├── phpunit.xml
└── README.md
```

## Frontend Structure (React Native - Expo)

```
frontend/
├── app/                        # Expo Router (file-based routing)
│   ├── (auth)/                 # Authentication routes
│   │   ├── _layout.tsx
│   │   ├── login.tsx
│   │   ├── register.tsx
│   │   └── forgot-password.tsx
│   │
│   ├── (tabs)/                 # Main app tabs
│   │   ├── _layout.tsx
│   │   ├── index.tsx           # Dashboard/Home
│   │   ├── measurements.tsx    # Measurements list
│   │   ├── jobs.tsx            # Jobs list
│   │   ├── tracking.tsx        # Driver tracking
│   │   └── profile.tsx         # User profile
│   │
│   ├── measurements/           # Measurement screens
│   │   ├── [id].tsx            # View measurement
│   │   ├── create.tsx          # Create measurement
│   │   └── edit/[id].tsx       # Edit measurement
│   │
│   ├── jobs/                   # Job screens
│   │   ├── [id].tsx
│   │   ├── create.tsx
│   │   └── edit/[id].tsx
│   │
│   ├── invoices/               # Invoice screens
│   │   ├── [id].tsx
│   │   └── list.tsx
│   │
│   ├── expenses/               # Expense screens
│   │   ├── [id].tsx
│   │   ├── create.tsx
│   │   └── list.tsx
│   │
│   ├── _layout.tsx             # Root layout
│   └── +not-found.tsx
│
├── src/
│   ├── components/             # Reusable UI components
│   │   ├── ui/                 # Base UI components
│   │   │   ├── Button.tsx
│   │   │   ├── Input.tsx
│   │   │   ├── Card.tsx
│   │   │   ├── Badge.tsx
│   │   │   ├── Modal.tsx
│   │   │   ├── Loading.tsx
│   │   │   └── ErrorBoundary.tsx
│   │   │
│   │   ├── forms/              # Form components
│   │   │   ├── FormField.tsx
│   │   │   ├── DatePicker.tsx
│   │   │   ├── Select.tsx
│   │   │   └── ImagePicker.tsx
│   │   │
│   │   ├── maps/               # Map components
│   │   │   ├── MapView.tsx
│   │   │   ├── MeasurementMap.tsx
│   │   │   ├── TrackingMap.tsx
│   │   │   └── Marker.tsx
│   │   │
│   │   └── lists/              # List components
│   │       ├── MeasurementItem.tsx
│   │       ├── JobItem.tsx
│   │       └── InvoiceItem.tsx
│   │
│   ├── features/               # Feature modules
│   │   ├── auth/
│   │   │   ├── hooks/
│   │   │   │   ├── useAuth.ts
│   │   │   │   └── useLogin.ts
│   │   │   ├── screens/
│   │   │   │   ├── LoginScreen.tsx
│   │   │   │   └── RegisterScreen.tsx
│   │   │   └── types.ts
│   │   │
│   │   ├── measurements/
│   │   │   ├── hooks/
│   │   │   │   ├── useMeasurements.ts
│   │   │   │   ├── useGPSTracking.ts
│   │   │   │   └── useAreaCalculation.ts
│   │   │   ├── screens/
│   │   │   │   ├── MeasurementListScreen.tsx
│   │   │   │   ├── CreateMeasurementScreen.tsx
│   │   │   │   └── MeasurementDetailScreen.tsx
│   │   │   ├── utils/
│   │   │   │   ├── areaCalculator.ts
│   │   │   │   └── coordinateUtils.ts
│   │   │   └── types.ts
│   │   │
│   │   ├── jobs/
│   │   │   ├── hooks/
│   │   │   │   └── useJobs.ts
│   │   │   ├── screens/
│   │   │   └── types.ts
│   │   │
│   │   ├── billing/
│   │   │   ├── hooks/
│   │   │   ├── screens/
│   │   │   └── types.ts
│   │   │
│   │   ├── expenses/
│   │   │   ├── hooks/
│   │   │   ├── screens/
│   │   │   └── types.ts
│   │   │
│   │   ├── tracking/
│   │   │   ├── hooks/
│   │   │   ├── screens/
│   │   │   └── types.ts
│   │   │
│   │   └── subscriptions/
│   │       ├── hooks/
│   │       ├── screens/
│   │       └── types.ts
│   │
│   ├── services/               # External services
│   │   ├── api/                # API client
│   │   │   ├── client.ts
│   │   │   ├── auth.ts
│   │   │   ├── measurements.ts
│   │   │   ├── jobs.ts
│   │   │   ├── invoices.ts
│   │   │   ├── expenses.ts
│   │   │   ├── tracking.ts
│   │   │   └── sync.ts
│   │   │
│   │   ├── storage/            # Local storage
│   │   │   ├── database.ts     # SQLite operations
│   │   │   ├── cache.ts        # MMKV cache
│   │   │   └── fileSystem.ts
│   │   │
│   │   ├── location/           # GPS services
│   │   │   ├── tracker.ts
│   │   │   ├── calculator.ts
│   │   │   └── permissions.ts
│   │   │
│   │   └── sync/               # Offline sync
│   │       ├── syncManager.ts
│   │       ├── conflictResolver.ts
│   │       └── queueManager.ts
│   │
│   ├── store/                  # State management (Zustand)
│   │   ├── authStore.ts
│   │   ├── measurementStore.ts
│   │   ├── jobStore.ts
│   │   ├── invoiceStore.ts
│   │   ├── syncStore.ts
│   │   └── settingsStore.ts
│   │
│   ├── hooks/                  # Custom hooks
│   │   ├── useLocation.ts
│   │   ├── useOfflineSync.ts
│   │   ├── usePermissions.ts
│   │   ├── useNetworkStatus.ts
│   │   └── useTheme.ts
│   │
│   ├── utils/                  # Utility functions
│   │   ├── calculations.ts
│   │   ├── validators.ts
│   │   ├── formatters.ts
│   │   ├── constants.ts
│   │   └── helpers.ts
│   │
│   ├── locales/                # i18n translations
│   │   ├── i18n.ts
│   │   ├── en.json
│   │   └── si.json
│   │
│   └── types/                  # TypeScript types
│       ├── api.ts
│       ├── models.ts
│       ├── navigation.ts
│       └── store.ts
│
├── assets/                     # Static assets
│   ├── images/
│   ├── icons/
│   └── fonts/
│
├── .env.example
├── .gitignore
├── app.json
├── babel.config.js
├── eas.json
├── package.json
├── tsconfig.json
└── README.md
```

## Documentation Structure

```
docs/
├── ARCHITECTURE.md             # System architecture overview
├── API_SPECIFICATION.md        # Detailed API documentation
├── DATABASE_SCHEMA.md          # Database design and ERD
├── DEPLOYMENT.md               # Production deployment guide
├── SETUP_GUIDE.md              # Development setup instructions
├── SEED_DATA.md                # Sample data documentation
└── PROJECT_STRUCTURE.md        # This file
```

## Key Design Patterns

### Backend (Laravel)

1. **Clean Architecture**:
   - Controllers: Handle HTTP requests/responses
   - Services: Contain business logic
   - Repositories: Abstract data access
   - DTOs: Type-safe data transfer

2. **Organization-Level Data Isolation**:
   - Global scopes on models
   - Middleware for filtering
   - Query builder scoping

3. **JWT Authentication**:
   - Stateless authentication
   - Token refresh mechanism
   - Role-based authorization

### Frontend (React Native)

1. **Feature-Based Architecture**:
   - Self-contained feature modules
   - Clear separation of concerns
   - Easy to test and maintain

2. **Offline-First**:
   - Local SQLite database
   - MMKV for caching
   - Background sync
   - Conflict resolution

3. **State Management**:
   - Zustand for global state
   - React hooks for local state
   - Async state handling

## File Naming Conventions

### Backend

- **Models**: PascalCase (e.g., `LandMeasurement.php`)
- **Controllers**: PascalCase with suffix (e.g., `MeasurementController.php`)
- **Migrations**: snake_case with timestamp (e.g., `2024_01_01_000001_create_users_table.php`)
- **Services**: PascalCase with suffix (e.g., `MeasurementService.php`)

### Frontend

- **Components**: PascalCase (e.g., `MeasurementMap.tsx`)
- **Screens**: PascalCase with suffix (e.g., `LoginScreen.tsx`)
- **Hooks**: camelCase with prefix (e.g., `useMeasurements.ts`)
- **Utilities**: camelCase (e.g., `areaCalculator.ts`)
- **Types**: camelCase (e.g., `types.ts`)

## Code Organization Best Practices

1. **Single Responsibility**: Each file should have one clear purpose
2. **DRY (Don't Repeat Yourself)**: Avoid code duplication
3. **KISS (Keep It Simple, Stupid)**: Prefer simple solutions
4. **SOLID Principles**: Follow object-oriented design principles
5. **Feature Folders**: Group related files by feature, not by type
6. **Clear Naming**: Use descriptive, self-documenting names
7. **Small Files**: Keep files focused and manageable (<300 lines)
8. **Comments**: Document complex logic and business rules
