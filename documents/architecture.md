# System Architecture

> Comprehensive guide to the GeoOps Platform architecture, design patterns, and technical decisions.

## Table of Contents
- [Overview](#overview)
- [Technology Stack](#technology-stack)
- [Architecture Patterns](#architecture-patterns)
- [Core Modules](#core-modules)
- [Data Flow](#data-flow)
- [Security Architecture](#security-architecture)
- [Performance Optimization](#performance-optimization)
- [Scalability](#scalability)

## Overview

The GeoOps Platform is a production-ready GPS-based land measurement and agricultural field-service management application built with Clean Architecture principles. The system implements SOLID, DRY, and KISS design patterns for maximum scalability, maintainability, and long-term extensibility.

### Key Characteristics

- **Clean Architecture**: Clear separation of concerns across layers
- **Offline-First**: Works seamlessly without internet connectivity
- **Multi-Tenant**: Organization-level data isolation
- **Bilingual**: English and Sinhala (සිංහල) support
- **Cross-Platform**: Web API + Mobile (iOS & Android)
- **Production-Ready**: Security hardened, optimized, and scalable

## Technology Stack

### Backend (Laravel 11)

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Framework** | Laravel 11 LTS | Web application framework |
| **Language** | PHP 8.3+ | Server-side programming |
| **Database** | MySQL 8.0+ / PostgreSQL 14+ | Primary data store with spatial support |
| **Cache** | Redis 6.0+ | Caching and queue management |
| **Authentication** | JWT (tymon/jwt-auth) | Stateless token-based auth |
| **PDF Generation** | DomPDF | Invoice and report generation |
| **Excel Export** | Maatwebsite/Excel | Data export functionality |
| **Queue** | Redis/Database | Asynchronous job processing |
| **Storage** | S3-compatible | PDF and file storage |

### Mobile App (React Native + Expo)

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Framework** | React Native 0.74 | Cross-platform mobile framework |
| **Build Tool** | Expo 51 | Development and build infrastructure |
| **Language** | TypeScript 5.3 | Type-safe JavaScript |
| **State Management** | Zustand | Lightweight state management |
| **Offline Storage** | SQLite + MMKV | Local database and key-value store |
| **Maps** | React Native Maps | Google Maps/Mapbox integration |
| **Navigation** | React Navigation 6 | Screen navigation |
| **Localization** | i18next | Multi-language support |
| **API Client** | Axios | HTTP requests with interceptors |
| **Bluetooth** | react-native-bluetooth-escpos-printer | Thermal printer integration |

## Architecture Patterns

### Backend Architecture (Clean Architecture)

The backend follows Clean Architecture with four distinct layers:

```
┌─────────────────────────────────────────────────────────┐
│                 Presentation Layer                      │
│         (Controllers, Middleware, Requests)             │
│  - Thin controllers (routing only)                      │
│  - Request validation                                   │
│  - Response formatting                                  │
├─────────────────────────────────────────────────────────┤
│                 Application Layer                       │
│            (Services, Business Logic)                   │
│  - LandMeasurementService                              │
│  - JobService, InvoiceService                          │
│  - SyncService, SubscriptionService                    │
├─────────────────────────────────────────────────────────┤
│                   Domain Layer                          │
│          (Models, DTOs, Policies, Rules)                │
│  - Eloquent Models                                      │
│  - Data Transfer Objects                                │
│  - Business rules and policies                          │
├─────────────────────────────────────────────────────────┤
│               Infrastructure Layer                      │
│       (Repositories, Database, External APIs)           │
│  - Repository implementations                           │
│  - Database queries                                     │
│  - External service integrations                        │
└─────────────────────────────────────────────────────────┘
```

#### Directory Structure

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/      # Presentation Layer
│   │   │   ├── AuthController.php
│   │   │   ├── LandController.php
│   │   │   ├── JobController.php
│   │   │   └── ...
│   │   ├── Middleware/           # Authentication, RBAC
│   │   └── Requests/             # Form validation
│   │
│   ├── Services/                 # Application Layer
│   │   ├── AuthService.php
│   │   ├── LandMeasurementService.php
│   │   ├── JobService.php
│   │   ├── InvoiceService.php
│   │   └── SyncService.php
│   │
│   ├── Models/                   # Domain Layer
│   │   ├── Land.php
│   │   ├── Job.php
│   │   ├── Invoice.php
│   │   └── ...
│   │
│   ├── DTOs/                     # Domain Layer
│   │   └── LandMeasurementDTO.php
│   │
│   ├── Policies/                 # Domain Layer
│   │   └── JobPolicy.php
│   │
│   └── Repositories/             # Infrastructure Layer
│       ├── Interfaces/
│       │   └── LandRepositoryInterface.php
│       └── Eloquent/
│           └── LandRepository.php
│
├── database/
│   ├── migrations/               # Schema definitions
│   └── seeders/                  # Sample data
│
└── routes/
    └── api.php                   # API routing
```

### Mobile Architecture (Feature-Based)

The mobile app uses a feature-based modular architecture:

```
mobile/
├── src/
│   ├── features/                 # Feature Modules
│   │   ├── auth/                 # Authentication feature
│   │   │   ├── LoginScreen.tsx
│   │   │   ├── RegisterScreen.tsx
│   │   │   └── ProfileScreen.tsx
│   │   ├── measurement/          # GPS measurement feature
│   │   │   ├── MeasurementScreen.tsx
│   │   │   └── MeasurementListScreen.tsx
│   │   ├── maps/                 # Maps visualization
│   │   ├── jobs/                 # Job management
│   │   ├── billing/              # Invoicing
│   │   └── sync/                 # Offline sync
│   │
│   ├── components/               # Reusable UI Components
│   │   ├── Button.tsx
│   │   ├── Input.tsx
│   │   ├── Card.tsx
│   │   └── ...
│   │
│   ├── services/                 # Core Services
│   │   ├── api/                  # API communication
│   │   │   └── client.ts
│   │   ├── gps/                  # GPS tracking
│   │   │   └── gpsService.ts
│   │   ├── storage/              # SQLite & MMKV
│   │   │   └── database.ts
│   │   ├── sync/                 # Background sync
│   │   │   └── syncService.ts
│   │   └── printer/              # Bluetooth printing
│   │       └── printerService.ts
│   │
│   ├── stores/                   # State Management (Zustand)
│   │   ├── authStore.ts
│   │   ├── measurementStore.ts
│   │   └── syncStore.ts
│   │
│   ├── navigation/               # App Navigation
│   │   ├── AppNavigator.tsx
│   │   ├── AuthNavigator.tsx
│   │   └── MainNavigator.tsx
│   │
│   ├── utils/                    # Helper Functions
│   │   └── helpers.ts
│   │
│   ├── types/                    # TypeScript Definitions
│   │   └── index.ts
│   │
│   ├── constants/                # App Constants
│   │   └── index.ts
│   │
│   └── i18n/                     # Localization
│       ├── en.ts                 # English
│       └── si.ts                 # Sinhala
│
└── App.tsx                       # Application Entry
```

## Core Modules

### 1. Authentication & Authorization

**Backend:**
- JWT token-based authentication with refresh tokens
- Role-based access control (RBAC) using Spatie Laravel Permission
- 5 roles: Admin, Owner, Driver, Broker, Accountant
- Organization-level data isolation (multi-tenancy)

**Mobile:**
- Secure token storage using Expo SecureStore
- Auto token refresh on API calls
- Biometric authentication ready

**Flow:**
```
Login → JWT Token → Secure Storage → Auto-refresh → Logout
```

### 2. GPS Land Measurement

**Features:**
- Walk-around GPS tracking mode
- Point-based manual marking mode
- Real-time area calculation using Shoelace formula
- Measurement history with editing capability
- Offline measurement with background sync

**Algorithm:**
```javascript
// Shoelace Formula for polygon area
function calculateArea(coordinates) {
  let area = 0;
  for (let i = 0; i < coordinates.length; i++) {
    const j = (i + 1) % coordinates.length;
    area += coordinates[i].latitude * coordinates[j].longitude;
    area -= coordinates[j].latitude * coordinates[i].longitude;
  }
  return Math.abs(area / 2);
}
```

### 3. Maps & Visualization

- Display measured lands with color-coded polygons
- Show active jobs and driver locations
- Real-time GPS tracking during jobs
- Historical movement visualization
- Spatial queries for nearby lands

### 4. Job Management

**Lifecycle:**
```
Pending → In Progress → Completed → Invoiced → Paid
```

**Features:**
- Job creation with land and machine assignment
- Driver assignment and tracking
- Duration and distance calculation
- Job history and performance reports

### 5. Billing & Invoicing

- Automated invoice generation based on measured area
- Configurable rates per acre/hectare
- PDF invoice generation with professional templates
- Bluetooth thermal printer support (ESC/POS)
- Invoice status tracking

### 6. Expense Management

- Fuel tracking and consumption analysis
- Spare parts and maintenance logging
- Expense categorization by type, machine, driver
- Financial reporting with income vs expense

### 7. Payments & Ledger

- Multiple payment methods: Cash, Bank, Digital, Check
- Customer balance tracking
- Payment history
- Financial summaries with date ranges
- Ledger reports per customer/driver/machine

### 8. Subscription Management

**Tiers:**
- **Free**: 10 measurements, 1 driver, basic features
- **Basic**: 100 measurements, 5 drivers, advanced reports
- **Pro**: Unlimited measurements, unlimited drivers, all features

**Enforcement:**
- Feature gating based on subscription level
- Usage limit tracking
- Automatic expiry handling with grace periods

### 9. Offline-First Functionality

**Architecture:**
```
┌──────────────┐     Online      ┌────────────┐
│  Mobile App  │ ←─────────────→ │  Backend   │
└──────┬───────┘                  └────────────┘
       │
       ↓ Always
┌──────────────┐
│ Local SQLite │
│    MMKV      │
└──────┬───────┘
       │
       ↓ When online
  Background Sync
```

**Features:**
- Local SQLite database for core entities
- MMKV for fast key-value storage
- Background synchronization every 5 minutes
- Conflict resolution (last-write-wins with timestamp)
- Retry mechanism with exponential backoff
- Batch processing for efficiency

### 10. Bluetooth Printing

- ESC/POS command support for thermal printers
- Device discovery and pairing management
- Invoice and receipt printing templates
- Print queue with retry mechanism
- Graceful fallback to PDF

## Data Flow

### Online Request Flow

```
Mobile App
    ↓
API Client (with auth headers)
    ↓
Backend API (Laravel)
    ↓
Middleware (Auth, Organization, Rate Limit)
    ↓
Controller (Validation)
    ↓
Service (Business Logic)
    ↓
Repository (Data Access)
    ↓
Database (MySQL/PostgreSQL)
    ↓
Response (JSON)
    ↓
Mobile App (Update UI + Local Storage)
```

### Offline Flow

```
Mobile App
    ↓
Local Storage (SQLite/MMKV)
    ↓
Mark as "pending sync"
    ↓
Background Sync Service (When online)
    ↓
Batch Upload to API
    ↓
Conflict Resolution
    ↓
Update Local Storage with server IDs
```

### Sync Conflict Resolution

```
1. Check timestamps: local_updated_at vs server_updated_at
2. If local is newer → Upload to server
3. If server is newer → Download from server
4. If equal → No action needed
5. Mark sync status: pending, synced, failed
```

## Security Architecture

### Backend Security

1. **Authentication**
   - JWT tokens with short TTL (60 minutes)
   - Refresh tokens with longer TTL (2 weeks)
   - Token blacklisting on logout
   - Password hashing with bcrypt

2. **Authorization**
   - Role-based access control (RBAC)
   - Policy-based authorization
   - Organization-level data isolation
   - Route middleware protection

3. **Input Validation**
   - Form Request classes for validation
   - SQL injection prevention via Eloquent ORM
   - XSS protection through output encoding
   - CSRF protection for web routes

4. **API Security**
   - Rate limiting (60 requests/minute)
   - CORS configuration
   - HTTPS/TLS enforcement
   - Secure headers (CSP, HSTS, etc.)

5. **Data Security**
   - Database encryption at rest
   - Sensitive data masking in logs
   - Secure file storage (S3 with signed URLs)

### Mobile Security

1. **Storage Security**
   - Secure token storage (Expo SecureStore)
   - Encrypted SQLite database
   - Biometric authentication ready
   - Secure key-value storage (MMKV)

2. **Network Security**
   - HTTPS only communication
   - Certificate pinning ready
   - Request/response encryption
   - Timeout configurations

3. **Code Security**
   - No sensitive data in source code
   - Environment variables for secrets
   - Code obfuscation ready
   - Secure random ID generation

## Performance Optimization

### Backend Optimizations

1. **Database**
   - Indexes on frequently queried columns
   - Spatial indexes for GPS data
   - Query optimization with eager loading
   - Connection pooling
   - Read replicas for scaling

2. **Caching**
   - Redis caching for frequently accessed data
   - Query result caching
   - Route caching
   - Config caching
   - View caching

3. **Queue Processing**
   - Async PDF generation
   - Async email sending
   - Batch processing for sync
   - Priority queues

4. **Response Optimization**
   - Pagination for large datasets
   - Partial resource loading
   - Compressed responses (gzip)
   - HTTP caching headers

### Mobile Optimizations

1. **Rendering**
   - React Native optimization
   - Lazy loading of screens
   - Image optimization and caching
   - Virtual lists for large datasets

2. **GPS & Location**
   - Adaptive GPS tracking intervals
   - Battery-efficient location updates
   - Background location optimization
   - Location caching

3. **Storage**
   - MMKV for fast key-value access
   - SQLite query optimization
   - Batch database operations
   - Lazy data loading

4. **Network**
   - Request batching
   - Response caching
   - Optimistic UI updates
   - Retry with exponential backoff

## Scalability

### Horizontal Scaling

```
        ┌─────────────┐
        │Load Balancer│
        └──────┬──────┘
               │
    ┌──────────┼──────────┐
    │          │          │
┌───▼───┐  ┌──▼────┐  ┌──▼────┐
│App 1  │  │App 2  │  │App 3  │  (Stateless Laravel instances)
└───┬───┘  └───┬───┘  └───┬───┘
    │          │          │
    └──────────┼──────────┘
               │
        ┌──────▼──────┐
        │   Redis     │  (Shared cache & sessions)
        └─────────────┘
               │
        ┌──────▼──────┐
        │  Database   │  (Primary)
        │   Cluster   │
        └──────┬──────┘
               │
        ┌──────▼──────┐
        │  Read       │  (Replicas)
        │  Replicas   │
        └─────────────┘
```

### Key Scalability Features

1. **Stateless API**: No session state in application
2. **Database Read Replicas**: Distribute read queries
3. **CDN**: Static assets and media files
4. **Queue Workers**: Separate worker servers
5. **Microservices Ready**: Modular architecture allows easy separation

### Monitoring & Observability

- Error tracking (Sentry integration ready)
- Performance monitoring (New Relic ready)
- API analytics
- User behavior tracking
- Crash reporting
- Custom metrics and alerts

## Design Principles

### SOLID Principles

1. **Single Responsibility**: Each class has one reason to change
2. **Open/Closed**: Open for extension, closed for modification
3. **Liskov Substitution**: Subtypes must be substitutable
4. **Interface Segregation**: Many specific interfaces over one general
5. **Dependency Inversion**: Depend on abstractions, not concretions

### Other Principles

- **DRY** (Don't Repeat Yourself): Reusable components and services
- **KISS** (Keep It Simple, Stupid): Avoid over-engineering
- **Separation of Concerns**: Clear boundaries between layers
- **Dependency Injection**: Loose coupling for testability

## Conclusion

The GeoOps Platform architecture is designed for:
- ✅ **Maintainability**: Clear structure and patterns
- ✅ **Scalability**: Horizontal and vertical scaling ready
- ✅ **Security**: Defense in depth approach
- ✅ **Performance**: Optimized at every layer
- ✅ **Reliability**: Offline-first with sync
- ✅ **Extensibility**: Easy to add new features

The architecture supports both current requirements and future growth, making it a solid foundation for a production-ready agricultural service platform.

---

**Next**: See [API Reference](api-reference.md) for detailed endpoint documentation.
