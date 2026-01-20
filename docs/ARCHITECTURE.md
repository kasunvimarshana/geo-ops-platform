# GeoOps Platform - Architecture Documentation

## Overview

GeoOps Platform is a production-ready GPS-based land measurement and agricultural field-service platform built with:

- **Frontend/Mobile**: React Native (Expo) with TypeScript
- **Backend**: Laravel 10 (LTS) with PHP 8.3
- **Architecture**: Clean Architecture with SOLID principles

## Project Structure

```
geo-ops-platform/
├── mobile/                 # React Native mobile app
│   ├── src/
│   │   ├── config/        # Configuration files (API, GPS, i18n, storage)
│   │   ├── domain/        # Domain layer (entities, repositories)
│   │   ├── application/   # Use cases and business logic
│   │   ├── infrastructure/# External services (API, GPS, storage)
│   │   └── presentation/  # UI components, screens, navigation
│   └── package.json
├── backend/               # Laravel API
│   ├── app/
│   │   ├── Domain/       # Domain entities and business logic
│   │   ├── Application/  # Use cases
│   │   ├── Infrastructure/# External services implementation
│   │   ├── Presentation/ # HTTP controllers
│   │   └── Models/       # Eloquent models
│   ├── database/
│   │   └── migrations/   # Database schema
│   └── routes/
│       └── api.php       # API routes (v1)
└── docs/                 # Documentation
```

## Core Features Implemented

### Mobile App

1. **Authentication**
   - JWT-based authentication with refresh tokens
   - Secure token storage using MMKV encryption
   - User registration and login

2. **GPS Services**
   - Optimized battery usage with configurable accuracy
   - Walk-around and polygon measurement
   - Distance, area, and perimeter calculations
   - Background location tracking with foreground service
   - Haversine formula for distance calculation
   - Shoelace formula for polygon area calculation

3. **Internationalization (i18n)**
   - Sinhala and English language support
   - Complete translations for UI elements
   - Configurable language switching

4. **Local Storage**
   - MMKV for encrypted key-value storage
   - SQLite for offline data persistence
   - Offline queue for data synchronization

5. **API Client**
   - Axios-based HTTP client
   - JWT token injection via interceptors
   - Automatic token refresh on 401 responses

### Backend API

1. **Authentication & Authorization**
   - JWT authentication with tymon/jwt-auth
   - Role-Based Access Control (RBAC) with spatie/laravel-permission
   - Organization-based data isolation

2. **Database Schema**
   - Users with roles and organizations
   - Organizations (multi-tenancy support)
   - Fields with GeoJSON boundary data
   - Jobs with assignment and tracking
   - Subscriptions with plan management
   - Invoices with payment tracking

3. **API Versioning**
   - RESTful API with v1 prefix
   - Proper HTTP status codes
   - JSON responses with error handling

4. **Resource Controllers**
   - Field management (CRUD operations)
   - Job management
   - Organization isolation middleware

## Clean Architecture Implementation

### Mobile App Layers

1. **Domain Layer** (`src/domain/`)
   - Entities: User, Field, Job, GeoPoint
   - Interfaces for repositories
   - Business rules and domain logic

2. **Application Layer** (`src/application/`)
   - Use cases for business operations
   - Orchestrates domain and infrastructure

3. **Infrastructure Layer** (`src/infrastructure/`)
   - API client implementation
   - GPS service implementation
   - Storage implementations (MMKV, SQLite)
   - External service integrations

4. **Presentation Layer** (`src/presentation/`)
   - React Native components
   - Navigation structure
   - Screen implementations

### Backend Layers

1. **Domain Layer**
   - Eloquent models with relationships
   - Business logic and rules
   - Domain events

2. **Application Layer**
   - Service classes
   - Use case implementations
   - DTOs (Data Transfer Objects)

3. **Infrastructure Layer**
   - Database migrations
   - External API integrations
   - Queue jobs
   - File storage

4. **Presentation Layer**
   - API controllers
   - Request validation
   - Response formatting
   - Middleware

## SOLID Principles

- **Single Responsibility**: Each class has one reason to change
- **Open/Closed**: Classes open for extension, closed for modification
- **Liskov Substitution**: Derived classes substitutable for base classes
- **Interface Segregation**: Clients not forced to depend on unused interfaces
- **Dependency Inversion**: Depend on abstractions, not concretions

## Security Features

1. **Authentication**
   - JWT tokens with expiration
   - Secure password hashing (bcrypt)
   - Token refresh mechanism

2. **Authorization**
   - Role-Based Access Control (RBAC)
   - Organization isolation
   - Permission checks per endpoint

3. **Data Protection**
   - MMKV encryption for mobile storage
   - HTTPS for API communication
   - SQL injection protection (Eloquent ORM)
   - XSS protection

## GPS Features

### Battery Optimization

- Configurable accuracy levels (HIGH, MEDIUM, LOW)
- Dynamic interval adjustment
- Background tracking with foreground service
- Distance filter to reduce updates

### Measurement Types

1. **Walk Around**: User walks around perimeter
2. **Polygon**: Manual point placement on map
3. **Manual**: Direct coordinate entry

### Calculations

- Distance: Haversine formula
- Area: Shoelace formula for polygons
- Perimeter: Sum of distances between consecutive points

## API Endpoints

### Authentication

- `POST /api/v1/auth/register` - User registration
- `POST /api/v1/auth/login` - User login
- `GET /api/v1/auth/me` - Get authenticated user
- `POST /api/v1/auth/logout` - Logout
- `POST /api/v1/auth/refresh` - Refresh token

### Fields

- `GET /api/v1/fields` - List fields (with pagination)
- `POST /api/v1/fields` - Create field
- `GET /api/v1/fields/{id}` - Get field details
- `PUT /api/v1/fields/{id}` - Update field
- `DELETE /api/v1/fields/{id}` - Delete field

### Jobs

- `GET /api/v1/jobs` - List jobs
- `POST /api/v1/jobs` - Create job
- `GET /api/v1/jobs/{id}` - Get job details
- `PUT /api/v1/jobs/{id}` - Update job
- `DELETE /api/v1/jobs/{id}` - Delete job

## Database Schema

### Organizations

- Multi-tenant support
- Organization types: farm, service_provider, cooperative
- Soft deletes

### Users

- Linked to organizations
- Roles: admin, manager, driver, field_worker
- JWT authentication support

### Fields

- GeoJSON boundary storage
- Area and perimeter in meters
- Measurement type tracking
- Linked to organizations and users

### Jobs

- Task management
- Assignment tracking
- Status workflow: pending → in_progress → completed
- Priority levels

### Subscriptions

- Plan management (basic, pro, enterprise)
- Monthly/yearly billing
- Feature limitations

### Invoices

- Multiple types (subscription, service, other)
- Payment tracking
- Due date management

## Development Setup

### Mobile App

```bash
cd mobile
npm install
npm start
```

### Backend API

```bash
cd backend
composer install
php artisan migrate
php artisan serve
```

## Environment Variables

### Mobile

- `EXPO_PUBLIC_API_URL`: Backend API URL

### Backend

- `DB_CONNECTION`: Database connection
- `JWT_SECRET`: JWT signing secret
- `QUEUE_CONNECTION`: Queue driver

## Next Steps

1. Implement remaining API controllers (Jobs, Subscriptions, Invoices)
2. Add map integration (Google Maps/Mapbox)
3. Implement Bluetooth ESC/POS printing
4. Add PDF generation for reports
5. Implement offline synchronization
6. Add comprehensive testing
7. Set up CI/CD pipeline
8. Deploy to production

## License

MIT License
