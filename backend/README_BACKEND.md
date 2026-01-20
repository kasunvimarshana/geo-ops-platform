# GeoOps Platform - Laravel Backend

## Overview

Production-ready Laravel 11.x backend implementing Clean Architecture principles for a GPS Field Management Platform.

## Features

- ✅ JWT Authentication
- ✅ Clean Architecture (Domain, Application, Infrastructure, Presentation)
- ✅ Repository Pattern
- ✅ Service Layer for Business Logic
- ✅ GPS/Spatial Data Support (Laravel Eloquent Spatial)
- ✅ Invoice PDF Generation (DomPDF)
- ✅ RESTful API with Resource Transformers
- ✅ Form Request Validation
- ✅ Organization-scoped Data

## Tech Stack

- **Framework**: Laravel 11.x
- **PHP**: 8.2+
- **Authentication**: JWT (tymon/jwt-auth)
- **Spatial Data**: Laravel Eloquent Spatial
- **PDF Generation**: DomPDF
- **Database**: MySQL/PostgreSQL with PostGIS support

## Architecture

### Clean Architecture Layers

```
app/
├── Domain/
│   └── Repositories/          # Repository interfaces (contracts)
├── Application/
│   ├── DTOs/                  # Data Transfer Objects
│   └── Services/              # Business logic services
├── Infrastructure/
│   └── Repositories/          # Repository implementations
├── Presentation/
│   ├── Controllers/Api/       # API Controllers (thin layer)
│   ├── Resources/             # API Resource transformers
│   ├── Requests/              # Form Request validation
│   └── Middleware/            # Custom middleware
└── Models/                    # Eloquent models
```

## Database Schema

### Core Tables

- **organizations**: Company/organization data
- **users**: User accounts with roles (admin, owner, driver, broker, accountant)
- **packages**: Subscription packages (Free, Basic, Pro)
- **subscriptions**: Organization subscriptions

### Business Tables

- **land_plots**: GPS-measured land parcels with spatial data
- **field_jobs**: Agricultural job tracking
- **gps_tracking**: Real-time GPS tracking data
- **invoices**: Invoice generation
- **payments**: Payment tracking
- **expenses**: Expense management

## Setup Instructions

### 1. Install Dependencies

```bash
composer install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Configure database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Seed Database

```bash
php artisan db:seed
```

This will create:

- 3 packages (Free, Basic, Pro)
- Demo organization
- Admin user (admin@geo-ops.com / password)

### 5. Start Server

```bash
php artisan serve
```

API will be available at: `http://localhost:8000/api`

## API Endpoints

### Authentication

```
POST   /api/auth/register     - Register new user
POST   /api/auth/login        - Login
POST   /api/auth/refresh      - Refresh JWT token
POST   /api/auth/logout       - Logout
GET    /api/auth/me           - Get current user
```

### Land Plots

```
GET    /api/land-plots         - List all land plots
POST   /api/land-plots         - Create land plot
GET    /api/land-plots/{id}    - Get land plot
PUT    /api/land-plots/{id}    - Update land plot
DELETE /api/land-plots/{id}    - Delete land plot
```

### Field Jobs

```
GET    /api/field-jobs         - List all jobs
POST   /api/field-jobs         - Create job
GET    /api/field-jobs/{id}    - Get job
PUT    /api/field-jobs/{id}    - Update job
DELETE /api/field-jobs/{id}    - Delete job
POST   /api/field-jobs/{id}/start     - Start job
POST   /api/field-jobs/{id}/complete  - Complete job
POST   /api/field-jobs/{id}/cancel    - Cancel job
```

### Invoices

```
GET    /api/invoices           - List all invoices
POST   /api/invoices           - Create invoice
GET    /api/invoices/{id}      - Get invoice
PUT    /api/invoices/{id}      - Update invoice
GET    /api/invoices/{id}/generate-pdf  - Generate PDF
GET    /api/invoices/{id}/download-pdf  - Download PDF
```

## Authentication

All protected endpoints require JWT token in Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

### Login Example

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@geo-ops.com",
    "password": "password"
  }'
```

Response:

```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

## Code Standards

- PSR-12 coding standards
- Strict types enabled (`declare(strict_types=1)`)
- Type hints for all parameters and return types
- Comprehensive PHPDoc blocks
- SOLID principles

## Key Services

### AuthService

Handles user registration, login, logout, and JWT token management.

### LandPlotService

- GPS area calculations (acres, hectares, square meters)
- Perimeter calculations
- Center point calculations
- Spatial data management

### JobService

- Job lifecycle management (create → assign → start → complete)
- Duration tracking
- Driver assignment

### InvoiceService

- Invoice generation with auto-number
- PDF generation and storage
- Payment status tracking

## Spatial Data

Land plots and GPS tracking use PostGIS geometry types. Coordinates are stored as:

- **Point**: For GPS tracking locations
- **Geometry**: For land plot boundaries
- **JSON**: For coordinate arrays

Example coordinate format:

```json
{
    "coordinates": [
        { "latitude": 6.9271, "longitude": 79.8612 },
        { "latitude": 6.9272, "longitude": 79.8613 },
        { "latitude": 6.9273, "longitude": 79.8614 }
    ]
}
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper database
- [ ] Set up queue workers
- [ ] Configure file storage (S3, etc.)
- [ ] Enable HTTPS
- [ ] Set up monitoring (Sentry, etc.)
- [ ] Configure backups

### Optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

## Contributing

1. Follow PSR-12 standards
2. Write tests for new features
3. Update documentation
4. Keep controllers thin (max 5-7 lines per method)
5. Business logic in Services, not Controllers

## License

Proprietary - All rights reserved

## Support

For issues or questions, contact the development team.
