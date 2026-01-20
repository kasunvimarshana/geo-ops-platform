# GeoOps Platform - System Architecture

## Overview

GeoOps is a production-ready GPS land measurement and agricultural field-service management application designed for farmers, machine owners, drivers, and brokers in Sri Lanka. The system follows a client-server architecture with offline-first mobile capabilities.

## Technology Stack

### Backend

- **Framework**: Laravel 11.x (Latest LTS)
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+ / PostgreSQL 14+
- **Authentication**: JWT (tymon/jwt-auth)
- **API Style**: RESTful
- **Queue System**: Redis/Database
- **Cache**: Redis
- **Storage**: Local/S3 for PDFs and exports

### Frontend

- **Framework**: React Native with Expo SDK 50+
- **Language**: TypeScript 5.x
- **State Management**: Zustand
- **Offline Storage**: SQLite + MMKV
- **Maps**: Google Maps / Mapbox
- **Navigation**: Expo Router
- **Localization**: i18n (Sinhala/English)

## Architecture Patterns

### Backend Architecture (Clean Architecture)

```
┌─────────────────────────────────────────────────────────────┐
│                        Presentation Layer                    │
│  (Controllers - Thin, Validation, Request/Response Mapping)  │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│                        Application Layer                     │
│    (Services - Business Logic, Orchestration, DTOs)         │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│                         Domain Layer                         │
│           (Models, Entities, Business Rules)                 │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│                      Infrastructure Layer                    │
│  (Repositories - Data Access, External Services, Queue)      │
└─────────────────────────────────────────────────────────────┘
```

### Frontend Architecture (Feature-Based)

```
src/
├── features/              # Feature modules
│   ├── auth/
│   ├── measurement/
│   ├── jobs/
│   ├── billing/
│   ├── expenses/
│   ├── tracking/
│   └── subscriptions/
├── shared/                # Shared components
│   ├── components/
│   ├── hooks/
│   ├── utils/
│   └── types/
├── services/              # API and external services
├── store/                 # Global state
└── navigation/            # App navigation
```

## System Components

### 1. Authentication & Authorization

**JWT-Based Authentication**

- Stateless token-based authentication
- Refresh token mechanism
- Token expiry and renewal
- Secure password hashing (bcrypt)

**Role-Based Access Control (RBAC)**

- Admin: Full system access
- Owner: Organization management
- Driver: Job and tracking access
- Broker: Client and job management
- Accountant: Financial access

### 2. GPS Measurement Module

**Features**:

- Walk-around GPS tracking with continuous location updates
- Point-based polygon drawing
- Area calculation (acres/hectares) using Haversine formula
- Polygon coordinate storage (GeoJSON format)
- Measurement history and editing
- Accuracy indicators

**Technical Implementation**:

- Backend: Spatial data types (POINT, POLYGON)
- Frontend: Expo Location API with background updates
- Algorithm: Shoelace formula for polygon area
- Coordinate system: WGS84 (EPSG:4326)

### 3. Map Visualization

**Layers**:

- Measured land polygons (color-coded by status)
- Active job markers
- Driver/vehicle real-time positions
- Historical tracks and routes

**Features**:

- Interactive map controls
- Layer toggling
- Custom markers and polygons
- Clustering for performance
- Offline map tiles (optional)

### 4. Job Management

**Job Lifecycle**:

1. **Pending**: Job created, awaiting assignment
2. **Assigned**: Driver assigned, not started
3. **In Progress**: Active field work
4. **Completed**: Work finished, awaiting billing
5. **Billed**: Invoice generated
6. **Paid**: Payment received

**Features**:

- Job creation with land selection
- Driver and machine assignment
- Real-time job status updates
- Job history and logs
- Job-based expense tracking

### 5. Tracking System

**Driver Tracking**:

- Periodic GPS location updates (configurable interval)
- Battery-optimized tracking
- Job-based tracking activation
- Historical movement logs
- Distance and duration calculation

**Technical Details**:

- Background location updates (Expo TaskManager)
- Geofencing for job sites
- Track storage optimization
- Privacy controls

### 6. Billing & Invoicing

**Auto-Billing**:

- Rate-based calculation (per acre/hectare)
- Custom rates per customer/job type
- Tax and discount application
- Multi-currency support (LKR primary)

**Invoice Generation**:

- PDF generation (backend: Laravel PDF / DomPDF)
- Invoice numbering system
- Email and download options
- Payment status tracking
- Invoice history

### 7. Expense Management

**Expense Categories**:

- Fuel
- Spare parts
- Maintenance
- Labor
- Other operational costs

**Features**:

- Machine-wise expense tracking
- Driver-wise expenses
- Receipt photo uploads
- Expense approval workflow
- Category-based reporting

### 8. Payment & Ledger

**Payment Methods**:

- Cash
- Bank transfer
- Mobile payment (Dialog/Hutch)
- Credit

**Ledger Features**:

- Customer balance tracking
- Payment history
- Outstanding invoices
- Income vs expense reports
- Profit/loss calculations

### 9. Subscription Management

**Package Tiers**:

| Feature                 | Free      | Basic    | Pro       |
| ----------------------- | --------- | -------- | --------- |
| Land Measurements/month | 10        | 100      | Unlimited |
| Drivers                 | 1         | 3        | Unlimited |
| PDF Exports/month       | 5         | 50       | Unlimited |
| Historical Data         | 30 days   | 180 days | Unlimited |
| Support                 | Community | Email    | Priority  |

**Enforcement**:

- Usage limit checks at API level
- Upgrade prompts in mobile app
- Grace period for expired subscriptions
- Feature toggles based on package

### 10. Offline-First System

**Offline Capabilities**:

- Land measurement without internet
- Job creation and updates
- Expense logging
- Invoice viewing
- Read-only access to synced data

**Sync Strategy**:

- Background sync when online
- Conflict resolution (last-write-wins with timestamps)
- Retry mechanism with exponential backoff
- Sync queue management
- User notification on sync status

**Technical Implementation**:

```typescript
// Offline Storage Structure
- Local SQLite: Structured data (measurements, jobs, invoices)
- MMKV: Settings, cache, quick access data
- File System: PDFs, images, offline maps
```

## Data Flow

### Land Measurement Flow

```
1. User starts measurement on mobile app
2. App requests GPS permission
3. Location updates stored locally (SQLite)
4. User completes measurement
5. App calculates area using coordinates
6. Measurement saved locally with sync flag
7. Background sync pushes to backend when online
8. Backend validates and stores in database
9. Backend returns confirmation
10. App updates local record with server ID
```

### Job Creation & Execution Flow

```
1. Owner creates job (offline-capable)
2. Job stored locally and queued for sync
3. Sync pushes job to backend
4. Backend assigns unique job ID
5. Driver receives job assignment (push notification)
6. Driver starts job (status: In Progress)
7. GPS tracking activated
8. Driver completes job
9. System auto-generates invoice based on measured area
10. Invoice PDF created and stored
11. Owner reviews and sends invoice to customer
```

## Database Schema

### Core Tables

**users**

- id, name, email, password, phone, role, organization_id
- email_verified_at, created_at, updated_at, deleted_at

**organizations**

- id, name, owner_id, subscription_package, subscription_expires_at
- settings (JSON), created_at, updated_at

**land_measurements**

- id, organization_id, name, coordinates (POLYGON), area_acres, area_hectares
- measured_by, measured_at, created_at, updated_at, deleted_at

**jobs**

- id, organization_id, customer_id, land_measurement_id, driver_id
- machine_id, status, scheduled_at, started_at, completed_at
- notes, created_by, created_at, updated_at, deleted_at

**drivers**

- id, user_id, organization_id, license_number, vehicle_info
- status, created_at, updated_at, deleted_at

**tracking_logs**

- id, driver_id, job_id, latitude, longitude, accuracy
- speed, heading, recorded_at

**invoices**

- id, organization_id, job_id, customer_id, invoice_number
- amount, tax, discount, total, status, pdf_path
- issued_at, due_at, paid_at, created_at, updated_at

**expenses**

- id, organization_id, job_id, driver_id, machine_id
- category, amount, description, receipt_path
- expense_date, created_by, created_at, updated_at, deleted_at

**payments**

- id, organization_id, invoice_id, customer_id, amount
- payment_method, reference_number, payment_date
- created_by, created_at, updated_at

**subscriptions**

- id, organization_id, package, amount, starts_at, expires_at
- status, created_at, updated_at

## API Endpoints

### Authentication

- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/refresh` - Refresh token
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user

### Land Measurements

- `GET /api/measurements` - List measurements
- `POST /api/measurements` - Create measurement
- `GET /api/measurements/{id}` - Get measurement details
- `PUT /api/measurements/{id}` - Update measurement
- `DELETE /api/measurements/{id}` - Delete measurement

### Jobs

- `GET /api/jobs` - List jobs
- `POST /api/jobs` - Create job
- `GET /api/jobs/{id}` - Get job details
- `PUT /api/jobs/{id}` - Update job
- `PUT /api/jobs/{id}/status` - Update job status
- `DELETE /api/jobs/{id}` - Delete job

### Drivers

- `GET /api/drivers` - List drivers
- `POST /api/drivers` - Create driver
- `GET /api/drivers/{id}` - Get driver details
- `PUT /api/drivers/{id}` - Update driver
- `DELETE /api/drivers/{id}` - Delete driver

### Tracking

- `POST /api/tracking/locations` - Submit location updates (batch)
- `GET /api/tracking/drivers/{id}/history` - Get driver location history
- `GET /api/tracking/jobs/{id}/route` - Get job route

### Billing

- `GET /api/invoices` - List invoices
- `POST /api/invoices` - Create invoice
- `GET /api/invoices/{id}` - Get invoice details
- `GET /api/invoices/{id}/pdf` - Download invoice PDF
- `PUT /api/invoices/{id}` - Update invoice
- `DELETE /api/invoices/{id}` - Delete invoice

### Expenses

- `GET /api/expenses` - List expenses
- `POST /api/expenses` - Create expense
- `GET /api/expenses/{id}` - Get expense details
- `PUT /api/expenses/{id}` - Update expense
- `DELETE /api/expenses/{id}` - Delete expense

### Payments

- `GET /api/payments` - List payments
- `POST /api/payments` - Record payment
- `GET /api/payments/{id}` - Get payment details

### Reports

- `GET /api/reports/financial` - Financial summary
- `GET /api/reports/jobs` - Job reports
- `GET /api/reports/expenses` - Expense reports

### Sync

- `POST /api/sync/push` - Push offline data
- `GET /api/sync/pull` - Pull server updates

## Security Measures

1. **Authentication**: JWT tokens with expiry
2. **Authorization**: Role-based middleware on all routes
3. **Data Isolation**: Organization-level filtering on all queries
4. **Input Validation**: Request validation classes
5. **Rate Limiting**: API throttling (60 requests/minute)
6. **SQL Injection**: Eloquent ORM and parameter binding
7. **XSS Protection**: Output escaping
8. **CORS**: Configured for mobile app origins
9. **Encryption**: Sensitive data encrypted at rest
10. **Audit Logs**: User action logging

## Performance Optimization

### Backend

- Database indexing on foreign keys and search fields
- Query optimization with eager loading
- Response caching for static data
- Background jobs for heavy operations
- Connection pooling

### Frontend

- Lazy loading of screens
- Image optimization and caching
- Virtualized lists for large data
- Debouncing for search inputs
- Memoization of expensive calculations
- Batch API requests

## Deployment Architecture

### Production Environment

```
┌─────────────────────────────────────────────────────────┐
│                     Load Balancer                        │
│                    (Nginx/CloudFlare)                    │
└────────────────┬──────────────────┬─────────────────────┘
                 │                  │
        ┌────────▼──────┐  ┌───────▼────────┐
        │ Laravel App 1 │  │ Laravel App 2  │
        │   (PHP-FPM)   │  │   (PHP-FPM)    │
        └────────┬──────┘  └────────┬───────┘
                 │                  │
                 └─────────┬────────┘
                           │
          ┌────────────────▼───────────────┐
          │         MySQL/PostgreSQL        │
          │      (Master-Slave Replication) │
          └────────────────────────────────┘
                           │
          ┌────────────────▼───────────────┐
          │        Redis (Cache/Queue)      │
          └────────────────────────────────┘
                           │
          ┌────────────────▼───────────────┐
          │    S3/Local Storage (Files)     │
          └────────────────────────────────┘
```

### Scaling Strategy

- **Horizontal Scaling**: Multiple Laravel app servers behind load balancer
- **Database**: Read replicas for reporting queries
- **Caching**: Redis for session and query cache
- **Queue Workers**: Separate servers for background jobs
- **CDN**: Static assets and PDFs
- **Mobile**: Over-the-air updates via Expo

## Monitoring & Logging

- **Application Logs**: Laravel Log
- **Error Tracking**: Sentry/Bugsnag
- **Performance**: New Relic/DataDog
- **Uptime**: Pingdom/StatusCake
- **Mobile Analytics**: Firebase Analytics

## Development Workflow

1. Local development with Docker
2. Git feature branches
3. Code review process
4. Automated testing (PHPUnit, Jest)
5. CI/CD pipeline (GitHub Actions)
6. Staging environment testing
7. Production deployment

## Future Enhancements

1. Weather integration for job scheduling
2. Machine learning for area estimation validation
3. IoT integration for machine diagnostics
4. Multi-language support (Tamil, Hindi)
5. Advanced analytics dashboard
6. Customer mobile app
7. WhatsApp integration for notifications
8. Voice-based data entry (Sinhala)
