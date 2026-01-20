# System Architecture Overview

## GPS Field Management Platform
**Version:** 1.0.0  
**Last Updated:** January 2026

---

## Table of Contents
1. [System Overview](#system-overview)
2. [Technology Stack](#technology-stack)
3. [Architecture Principles](#architecture-principles)
4. [System Components](#system-components)
5. [Backend Architecture](#backend-architecture)
6. [Mobile Architecture](#mobile-architecture)
7. [Data Flow](#data-flow)
8. [Security Architecture](#security-architecture)
9. [Offline-First Strategy](#offline-first-strategy)
10. [Scalability & Performance](#scalability--performance)

---

## System Overview

The GPS Field Management Platform is an enterprise-grade agricultural field service management system designed for farmers, machine owners, drivers, and brokers in Sri Lanka. The system provides comprehensive functionality for GPS-based land measurement, job management, driver tracking, billing, expense management, and financial reporting with robust offline-first capabilities.

### Key Features
- **GPS Land Measurement**: Walk-around tracking and polygon-based area calculation
- **Job Management**: Complete lifecycle management with assignments
- **Driver Tracking**: Real-time and historical GPS tracking
- **Billing & Invoicing**: Automated invoice generation with PDF support
- **Expense Management**: Fuel, parts, and maintenance tracking
- **Financial Ledger**: Income/expense tracking with comprehensive reporting
- **Subscription Management**: Free, Basic, and Pro tier enforcement
- **Offline-First**: Full functionality without internet connectivity
- **Multi-language**: Sinhala and English support

---

## Technology Stack

### Backend
- **Framework**: Laravel 11.x (LTS)
- **Language**: PHP 8.3+
- **Database**: MySQL 8.0+ / PostgreSQL 15+ with spatial extensions
- **Authentication**: JWT (tymon/jwt-auth)
- **Queue System**: Redis / Database
- **Cache**: Redis
- **Storage**: Local / S3-compatible cloud storage
- **PDF Generation**: DomPDF / Snappy
- **API Documentation**: Swagger/OpenAPI

### Mobile Frontend
- **Framework**: React Native via Expo SDK 51+
- **Language**: TypeScript 5.x
- **State Management**: Zustand
- **Offline Storage**: SQLite (expo-sqlite) + MMKV
- **Maps**: Google Maps / Mapbox GL
- **GPS**: expo-location with background tracking
- **PDF**: expo-print
- **API Client**: Axios with interceptors
- **Navigation**: React Navigation 6.x
- **UI**: React Native Paper / custom components

### DevOps & Tools
- **Version Control**: Git / GitHub
- **CI/CD**: GitHub Actions
- **API Testing**: Postman / Insomnia
- **Code Quality**: PHPStan, ESLint, Prettier
- **Package Management**: Composer, npm

---

## Architecture Principles

### SOLID Principles
- **Single Responsibility**: Each class has one reason to change
- **Open/Closed**: Open for extension, closed for modification
- **Liskov Substitution**: Subtypes must be substitutable for their base types
- **Interface Segregation**: Many client-specific interfaces over one general-purpose
- **Dependency Inversion**: Depend on abstractions, not concretions

### Additional Principles
- **DRY** (Don't Repeat Yourself): Eliminate code duplication
- **KISS** (Keep It Simple, Stupid): Simple solutions over complex ones
- **Separation of Concerns**: Different responsibilities in different layers
- **Clean Code**: Readable, maintainable, and testable code
- **Domain-Driven Design**: Business logic focused on domain models

---

## System Components

```
┌─────────────────────────────────────────────────────────────┐
│                    Mobile Application                        │
│                   (React Native Expo)                        │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  GPS &   │  │   Job    │  │ Billing  │  │  Offline │   │
│  │   Map    │  │Management│  │ & Invoice│  │  Storage │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ REST API (HTTPS)
                            │ JWT Authentication
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      API Gateway                             │
│                   (Laravel Backend)                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  Auth &  │  │ Business │  │   Data   │  │   Queue  │   │
│  │   RBAC   │  │  Logic   │  │  Access  │  │   Jobs   │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Database Layer                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  MySQL/  │  │  Redis   │  │  Cloud   │  │  Backup  │   │
│  │PostgreSQL│  │  Cache   │  │ Storage  │  │ Storage  │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

## Backend Architecture

### Clean Architecture Layers

```
┌─────────────────────────────────────────────────────────────┐
│                   Presentation Layer                         │
│  • Controllers (thin, route handling only)                   │
│  • Middleware (auth, validation, rate limiting)              │
│  • Request DTOs (validation)                                 │
│  • Response Resources (formatting)                           │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                   Application Layer                          │
│  • Use Cases (application-specific business rules)           │
│  • Service Classes (orchestration)                           │
│  • DTOs (data transfer between layers)                       │
│  • Interfaces (contracts)                                    │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Domain Layer                            │
│  • Entities (business objects)                               │
│  • Domain Services (pure business logic)                     │
│  • Repository Interfaces (data contracts)                    │
│  • Value Objects                                             │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                  Infrastructure Layer                        │
│  • Repository Implementations (Eloquent)                     │
│  • External Services (email, SMS, storage)                   │
│  • Database Migrations                                       │
│  • Seeders                                                   │
└─────────────────────────────────────────────────────────────┘
```

### Directory Structure

```
backend/
├── app/
│   ├── Domain/                    # Core business logic
│   │   ├── Entities/              # Business entities
│   │   ├── Repositories/          # Repository interfaces
│   │   └── Services/              # Domain services
│   ├── Application/               # Application layer
│   │   ├── DTOs/                  # Data transfer objects
│   │   ├── Services/              # Application services
│   │   └── UseCases/              # Use case implementations
│   ├── Infrastructure/            # External concerns
│   │   ├── Repositories/          # Repository implementations
│   │   ├── Services/              # External service implementations
│   │   └── Persistence/           # Database migrations, seeders
│   └── Presentation/              # HTTP layer
│       ├── Controllers/           # API controllers
│       ├── Middleware/            # HTTP middleware
│       ├── Requests/              # Form requests
│       └── Resources/             # API resources
├── config/                        # Configuration files
├── database/                      # Database files
├── routes/                        # Route definitions
└── tests/                         # Automated tests
```

---

## Mobile Architecture

### Feature-Based Structure

```
mobile/
├── src/
│   ├── features/                  # Feature modules
│   │   ├── auth/                  # Authentication
│   │   │   ├── components/
│   │   │   ├── screens/
│   │   │   ├── services/
│   │   │   └── store/
│   │   ├── gps/                   # GPS & Measurement
│   │   ├── jobs/                  # Job Management
│   │   ├── billing/               # Billing & Invoices
│   │   ├── expenses/              # Expense Management
│   │   └── tracking/              # Driver Tracking
│   ├── shared/                    # Shared code
│   │   ├── components/            # Reusable UI components
│   │   ├── services/              # API, storage, sync
│   │   ├── utils/                 # Helper functions
│   │   ├── hooks/                 # Custom React hooks
│   │   ├── constants/             # App constants
│   │   └── types/                 # TypeScript types
│   ├── navigation/                # Navigation config
│   ├── store/                     # Global state (Zustand)
│   ├── locales/                   # i18n translations
│   └── theme/                     # Styling & theming
├── assets/                        # Images, fonts, etc.
└── App.tsx                        # Root component
```

### State Management

```
┌─────────────────────────────────────────────────────────────┐
│                       Global State (Zustand)                 │
│  • Auth State        • User Profile       • Subscriptions    │
│  • Sync Queue        • Network Status     • App Settings     │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Local Storage (SQLite + MMKV)             │
│  • Measurements      • Jobs              • Expenses          │
│  • Invoices          • Tracking Logs     • User Data         │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                       Sync Service                           │
│  • Background Sync   • Conflict Resolution  • Queue Mgmt     │
└─────────────────────────────────────────────────────────────┘
```

---

## Data Flow

### Measurement Flow (Online)

```
1. User walks around field → GPS coordinates captured
2. App calculates polygon area → Display on map
3. User saves measurement → Store locally + Queue for sync
4. Sync service uploads → Backend validates & stores
5. Backend generates invoice → Returns PDF URL
6. App downloads PDF → Store locally
```

### Measurement Flow (Offline)

```
1. User walks around field → GPS coordinates captured (no internet)
2. App calculates polygon area → Display on map
3. User saves measurement → Store in SQLite with sync_pending flag
4. Internet restored → Sync service detects pending items
5. Sync service uploads → Backend validates & stores
6. Backend generates invoice → Returns PDF URL
7. App downloads PDF → Store locally + Update sync status
```

---

## Security Architecture

### Authentication & Authorization

```
┌─────────────────────────────────────────────────────────────┐
│                    Client (Mobile App)                       │
│  Login → Email + Password                                    │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                   JWT Token Generation                       │
│  • Access Token (15 min)                                     │
│  • Refresh Token (7 days)                                    │
│  • Token includes: user_id, role, organization_id            │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                   Request Authorization                      │
│  • Token validation                                          │
│  • Role verification                                         │
│  • Organization isolation                                    │
│  • Permission checks                                         │
└─────────────────────────────────────────────────────────────┘
```

### Security Measures

1. **Data Encryption**: HTTPS/TLS for all API communications
2. **Token Security**: JWT with short expiration, refresh token rotation
3. **Input Validation**: Strict validation on all API endpoints
4. **SQL Injection Prevention**: Parameterized queries via Eloquent ORM
5. **XSS Protection**: Output escaping, Content Security Policy
6. **CSRF Protection**: Token-based CSRF for web endpoints
7. **Rate Limiting**: Per-user and per-IP rate limits
8. **Organization Isolation**: All queries scoped to organization
9. **Audit Logging**: All sensitive operations logged
10. **File Security**: Signed URLs for file access

---

## Offline-First Strategy

### Core Principles

1. **Local-First Operations**: All user actions work offline immediately
2. **Background Synchronization**: Sync happens automatically when online
3. **Conflict Resolution**: Last-write-wins with user notification
4. **Queue Management**: FIFO queue with retry logic
5. **Data Consistency**: Eventual consistency model

### Sync Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Mobile App                                │
│  ┌────────────┐     ┌────────────┐     ┌────────────┐      │
│  │   User     │────▶│   Local    │────▶│   Sync     │      │
│  │  Action    │     │  Storage   │     │   Queue    │      │
│  └────────────┘     └────────────┘     └────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                   Network Available?
                            │
                     ┌──────┴──────┐
                    YES            NO
                     │              │
                     ▼              ▼
              ┌────────────┐  ┌────────────┐
              │   Sync     │  │   Wait &   │
              │   Now      │  │   Retry    │
              └────────────┘  └────────────┘
                     │
                     ▼
              ┌────────────┐
              │  Backend   │
              │   API      │
              └────────────┘
                     │
                     ▼
              ┌────────────┐
              │  Success?  │
              └────────────┘
                     │
              ┌──────┴──────┐
             YES            NO
              │              │
              ▼              ▼
        ┌────────────┐  ┌────────────┐
        │  Mark      │  │  Retry     │
        │  Synced    │  │  Later     │
        └────────────┘  └────────────┘
```

### Conflict Resolution Strategy

1. **Measurements**: Last-write-wins, server timestamp is authoritative
2. **Job Status**: Server state takes precedence
3. **Payments**: Server validation required, no client override
4. **User Profile**: Merge strategy with last-write-wins per field
5. **Settings**: Client preference preserved unless server override

---

## Scalability & Performance

### Backend Scalability

1. **Horizontal Scaling**: Stateless API servers behind load balancer
2. **Database Optimization**: Proper indexing, query optimization
3. **Caching Strategy**: Redis for session, frequent queries
4. **Queue Workers**: Multiple workers for background jobs
5. **CDN**: Static assets and PDFs served via CDN
6. **Database Replication**: Read replicas for reporting queries

### Mobile Performance

1. **Lazy Loading**: Components and data loaded on demand
2. **Image Optimization**: Compressed, cached images
3. **Map Clustering**: Marker clustering for many points
4. **Pagination**: Infinite scroll with virtual lists
5. **Memory Management**: Proper cleanup of listeners and timers
6. **Battery Optimization**: Intelligent GPS sampling rates

### Monitoring & Observability

1. **Application Monitoring**: Error tracking (Sentry)
2. **Performance Monitoring**: Response times, slow queries
3. **User Analytics**: Feature usage, crash reports
4. **Server Metrics**: CPU, memory, disk, network
5. **Logs**: Centralized logging (ELK Stack)
6. **Alerts**: Automated alerts for critical issues

---

## Deployment Architecture

### Production Environment

```
┌─────────────────────────────────────────────────────────────┐
│                      Load Balancer                           │
│                    (NGINX / AWS ELB)                         │
└─────────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  API Server  │    │  API Server  │    │  API Server  │
│      #1      │    │      #2      │    │      #3      │
└──────────────┘    └──────────────┘    └──────────────┘
        │                   │                   │
        └───────────────────┼───────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Database   │    │    Redis     │    │   Storage    │
│    Master    │    │    Cache     │    │     S3       │
└──────────────┘    └──────────────┘    └──────────────┘
        │
        ▼
┌──────────────┐
│   Database   │
│   Replica    │
└──────────────┘
```

---

## Conclusion

This architecture provides a robust, scalable, and maintainable foundation for the GPS Field Management Platform. By adhering to Clean Architecture principles, SOLID principles, and industry best practices, the system is designed to handle thousands of concurrent users while maintaining high performance, reliability, and code quality.

The offline-first approach ensures that users in rural areas with poor connectivity can still use the application effectively, while the subscription-based model provides a sustainable revenue stream for the business.
