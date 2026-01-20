# System Architecture Overview

## GeoOps Platform - GPS Land Measurement & Agricultural Field Service Management

### Architecture Type

**Client-Server Architecture** with RESTful API communication

### Key Architectural Principles

- **Clean Architecture** (Backend)
- **Feature-Based Modular Architecture** (Mobile)
- **Offline-First Design**
- **Multi-Tenancy** (Organization-level isolation)
- **SOLID, DRY, KISS principles**

---

## High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Mobile Application                        │
│              (React Native + Expo + TypeScript)              │
│                                                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Presentation Layer                                   │  │
│  │  - Feature-based UI Components                        │  │
│  │  - Screens (Measurement, Jobs, Invoices, etc.)       │  │
│  │  - Navigation                                         │  │
│  └──────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  State Management (Zustand / Redux Toolkit)          │  │
│  │  - Authentication State                               │  │
│  │  - Sync State                                         │  │
│  │  - Measurement State                                  │  │
│  └──────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Services Layer                                       │  │
│  │  - API Service (HTTP Client with interceptors)       │  │
│  │  - GPS Service (Location tracking)                   │  │
│  │  - Sync Service (Background sync)                    │  │
│  │  - Storage Service (SQLite/MMKV)                     │  │
│  └──────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Offline Storage (SQLite / MMKV)                     │  │
│  │  - Local measurements, jobs, invoices                │  │
│  │  - Sync queue                                         │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ HTTPS / REST API
                              │ JWT Authentication
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Backend API Server                      │
│                    (Laravel - Latest LTS)                    │
│                                                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  API Layer (Thin Controllers)                        │  │
│  │  - Request Validation                                 │  │
│  │  - Authorization Middleware                           │  │
│  │  - Response Formatting                                │  │
│  └──────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Service Layer (Business Logic)                      │  │
│  │  - MeasurementService                                 │  │
│  │  - JobService                                         │  │
│  │  - BillingService                                     │  │
│  │  - PaymentService                                     │  │
│  │  - SubscriptionService                                │  │
│  └──────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Repository Layer (Data Access)                      │  │
│  │  - Interface-based repositories                       │  │
│  │  - Query optimization                                 │  │
│  │  - Organization filtering                             │  │
│  └──────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Queue Jobs (Background Processing)                  │  │
│  │  - PDF Generation                                     │  │
│  │  - Report Generation                                  │  │
│  │  - Data Sync Processing                               │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Database Layer                          │
│                   (MySQL / PostgreSQL)                       │
│                                                               │
│  - Spatial Data Support (ST_GeomFromText, ST_Area)          │
│  - Migrations & Seeders                                      │
│  - Soft Deletes & Audit Fields                              │
│  - Proper Indexing                                           │
└─────────────────────────────────────────────────────────────┘
```

---

## Backend Architecture (Laravel Clean Architecture)

### Layer Responsibilities

#### 1. Controller Layer (Thin)

- HTTP request/response handling
- Input validation (using Form Requests)
- Call service layer methods
- Return standardized JSON responses
- NO business logic

#### 2. Service Layer (Business Logic)

- All business workflows
- Orchestrate multiple repositories
- Transaction management
- Complex calculations (e.g., area from GPS points)
- Event dispatching
- Call external services

#### 3. Repository Layer (Data Access)

- All database queries
- Eloquent model interactions
- Query optimization
- Organization-level filtering
- Interface contracts for testability

#### 4. DTO Layer (Data Transfer Objects)

- Type-safe data containers
- Request validation
- Response formatting
- Decoupling from Eloquent models

#### 5. Background Jobs

- PDF generation (invoices, reports)
- Email notifications
- Data aggregation
- Sync processing

---

## Mobile Architecture (React Native + Expo)

### Feature-Based Structure

```
src/
├── features/
│   ├── auth/
│   │   ├── screens/
│   │   ├── components/
│   │   ├── hooks/
│   │   ├── services/
│   │   └── store/
│   ├── measurement/
│   │   ├── screens/
│   │   ├── components/
│   │   ├── hooks/
│   │   ├── services/
│   │   └── store/
│   ├── jobs/
│   ├── billing/
│   ├── expenses/
│   └── tracking/
├── shared/
│   ├── components/
│   ├── utils/
│   ├── hooks/
│   ├── services/
│   └── types/
├── services/
│   ├── api/
│   ├── gps/
│   ├── storage/
│   └── sync/
├── navigation/
└── store/
```

### State Management Strategy

- **Zustand** for global app state
- **React Query** for server state caching
- Local component state for UI-only state

### Offline-First Design

1. All write operations save to local SQLite
2. Queue sync jobs in background
3. Background task processes sync queue
4. Conflict resolution: last-write-wins with server authority
5. Retry failed syncs with exponential backoff

---

## Authentication & Authorization

### JWT Flow

1. User logs in with credentials
2. Server validates and returns JWT token + refresh token
3. Mobile stores tokens securely (Expo SecureStore)
4. All API requests include Bearer token
5. Token refresh on expiry

### Role-Based Access Control (RBAC)

- **Admin**: Full system access
- **Owner**: Manage own organization, drivers, jobs
- **Driver**: View assigned jobs, log expenses
- **Broker**: Create jobs, view reports
- **Accountant**: Financial reports, payments

### Organization-Level Data Isolation

- All queries filtered by `organization_id`
- Middleware enforces organization context
- Super Admin can access all organizations

---

## Data Flow Examples

### GPS Land Measurement Flow

**Offline Mode:**

1. User starts measurement
2. GPS coordinates collected every X seconds
3. Coordinates stored in SQLite
4. Area calculated locally using Turf.js
5. Measurement saved to local DB with `sync_status: pending`
6. Background task queues sync

**Online Mode / Sync:**

1. Sync service reads pending measurements
2. POST to `/api/measurements` with coordinate array
3. Server validates, calculates area server-side
4. Returns measurement ID
5. Local record updated with server ID and `sync_status: synced`

### Invoice Generation Flow

1. User selects completed job
2. Request POST `/api/invoices` with job_id
3. Service layer:
   - Fetches job and measurement data
   - Calculates amount (area × rate)
   - Creates invoice record
   - Dispatches job to generate PDF
4. Returns invoice data (without PDF initially)
5. Background job generates PDF
6. Mobile polls or receives notification when PDF ready

---

## Scalability Considerations

### Backend

- **Queue Workers**: Multiple workers for background jobs
- **Database Indexing**: Proper indexes on frequently queried fields
- **Caching**: Redis for session, frequently accessed data
- **Load Balancing**: Horizontal scaling with stateless API
- **Database Optimization**: Query optimization, pagination

### Mobile

- **Lazy Loading**: Load data as needed
- **Image Optimization**: Compress before upload
- **Background Sync**: Batch operations
- **Local Caching**: Cache API responses
- **Memory Management**: Proper cleanup of GPS listeners

---

## Security Measures

1. **Input Validation**: All inputs validated (backend + frontend)
2. **SQL Injection Prevention**: Eloquent ORM, prepared statements
3. **XSS Prevention**: Output encoding
4. **CSRF Protection**: Token-based
5. **Rate Limiting**: Throttle API requests
6. **Encryption**: Sensitive data encrypted at rest
7. **HTTPS Only**: All communication over TLS
8. **JWT Expiry**: Short-lived access tokens
9. **Audit Logging**: Track all critical operations
10. **Role Verification**: Every endpoint checks permissions

---

## Technology Stack Summary

### Backend

- **Framework**: Laravel 10.x (LTS)
- **Language**: PHP 8.1+
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Authentication**: Laravel Sanctum / JWT
- **Queue**: Redis + Laravel Horizon
- **PDF Generation**: Laravel Dompdf / Snappy
- **Spatial**: Spatial MySQL extensions or PostGIS

### Mobile

- **Framework**: React Native (Expo SDK 49+)
- **Language**: TypeScript 5+
- **State Management**: Zustand 4+
- **API Client**: Axios
- **Offline Storage**: SQLite (expo-sqlite) + MMKV
- **Maps**: Google Maps (react-native-maps) or Mapbox
- **GPS**: expo-location
- **Background Tasks**: expo-background-fetch
- **i18n**: react-i18next
- **PDF Viewer**: react-native-pdf

### DevOps

- **Version Control**: Git
- **CI/CD**: GitHub Actions
- **Backend Hosting**: AWS/DigitalOcean/Heroku
- **Database**: Managed MySQL/PostgreSQL
- **File Storage**: S3-compatible storage
- **Mobile Distribution**: Expo EAS Build

---

## Performance Targets

- API response time: < 200ms (95th percentile)
- Mobile app launch: < 3s
- GPS measurement accuracy: ±5 meters
- Offline operation: Full functionality without internet
- Sync time: < 10s for typical batch
- PDF generation: < 5s per invoice
- Support: 10,000+ concurrent users

---

## Future Enhancements

1. Real-time GPS tracking with WebSockets
2. Machine learning for area prediction
3. Weather API integration
4. Satellite imagery overlay
5. Voice commands (Sinhala)
6. WhatsApp integration for invoices
7. Advanced analytics dashboard
8. Mobile web version
9. Integration with payment gateways (LankaPay, etc.)
10. Drone measurement support
