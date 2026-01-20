# Geo Ops Platform

## GPS Land Measurement & Agricultural Field-Service Management Application

A production-ready, enterprise-grade GPS land measurement and agricultural field-service management platform built with Laravel backend and React Native (Expo) mobile frontend.

### 🎯 Project Overview

This platform provides comprehensive solutions for:
- **GPS Land Measurement**: Walk-around tracking and point-based polygon drawing with accurate area calculations
- **Job Management**: Complete field work lifecycle from assignment to completion
- **GPS Tracking**: Real-time driver/operator tracking with historical movement logs
- **Billing & Invoicing**: Automated invoice generation with PDF support
- **Expense Management**: Track fuel, maintenance, and operational costs
- **Payment & Ledger**: Income/expense tracking with financial reporting
- **Offline-First**: Full functionality without internet connectivity
- **Multi-tenancy**: Organization-level data isolation

### 🏗️ Architecture

- **Backend**: Laravel 11.x with Clean Architecture
- **Frontend**: React Native (Expo) with TypeScript
- **Database**: MySQL/PostgreSQL with spatial data support
- **Authentication**: JWT-based token authentication
- **API**: RESTful APIs with comprehensive documentation

### 📚 Documentation

- [INDEX.md](./INDEX.md) - Complete documentation index
- [ARCHITECTURE.md](./ARCHITECTURE.md) - System architecture and design patterns
- [DATABASE.md](./DATABASE.md) - Database schema, ERD, and relationships
- [API.md](./API.md) - Complete API endpoint documentation
- [DEPLOYMENT.md](./DEPLOYMENT.md) - Production deployment guide
- [IMPLEMENTATION_GUIDE.md](./IMPLEMENTATION_GUIDE.md) - Step-by-step implementation guide
- [BLUETOOTH_PRINTER.md](./BLUETOOTH_PRINTER.md) - Bluetooth printer integration guide ⭐ NEW
- [backend/STRUCTURE.md](./backend/STRUCTURE.md) - Backend folder structure
- [frontend/STRUCTURE.md](./frontend/STRUCTURE.md) - Frontend folder structure

### 🚀 Quick Start

#### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan db:seed
php artisan serve
```

#### Frontend Setup
```bash
cd frontend
npm install
cp .env.example .env
npx expo start
```

### 📋 Features

#### Core Features

##### 1. GPS Land Measurement
- Walk-around GPS tracking with automatic boundary detection
- Point-based polygon drawing for precise measurements
- Real-time area calculation in acres and hectares
- Measurement history and editing capabilities
- Polygon coordinate storage with spatial indexing

##### 2. Job Management
- Complete job lifecycle management (Pending → Assigned → In Progress → Completed)
- Driver and machine assignment
- Job scheduling and priority management
- Integration with land measurements
- Status tracking and notifications

##### 3. GPS Tracking
- Real-time driver location tracking
- Historical movement logs with route playback
- Distance and duration calculations
- Battery-optimized tracking
- Geofencing capabilities

##### 4. Billing & Invoicing
- Automated invoice generation from completed jobs
- Configurable rate cards per service type
- PDF invoice generation with background jobs
- Payment tracking and balance management
- Multi-currency support

##### 5. Expense Management
- Fuel consumption tracking
- Maintenance and spare parts logging
- Expense categorization and tagging
- Machine-wise expense allocation
- Receipt image uploads

##### 6. Financial Management
- Complete ledger system with income/expense tracking
- Payment recording (cash, bank transfer, mobile money)
- Financial reports and analytics
- Customer balance tracking
- Profit/loss statements

##### 7. Subscription Management
- Multi-tier packages (Free, Basic, Pro, Enterprise)
- Usage limit enforcement
- Expiry handling and renewal
- Feature gating based on subscription
- Upgrade/downgrade flows

##### 8. Offline-First Architecture
- Full functionality without internet
- SQLite local database
- Background synchronization
- Conflict resolution
- Optimistic UI updates

### 🛠️ Technology Stack

#### Backend
- **Framework**: Laravel 11.x (PHP 8.2+)
- **Architecture**: Clean Architecture with SOLID principles
- **Authentication**: JWT tokens
- **Database**: MySQL 8.0+ or PostgreSQL 14+ with PostGIS
- **Cache**: Redis
- **Queue**: Redis-based job queue
- **Storage**: AWS S3 or compatible

#### Frontend
- **Framework**: React Native with Expo SDK 50+
- **Language**: TypeScript 5.x
- **State Management**: Zustand
- **Offline Storage**: SQLite + MMKV
- **Maps**: Google Maps / Mapbox
- **Location**: Expo Location API
- **Navigation**: React Navigation

#### DevOps
- **Containerization**: Docker & Docker Compose
- **CI/CD**: GitHub Actions
- **Monitoring**: Sentry for error tracking
- **Logging**: ELK Stack or similar

### 👥 User Roles

| Role | Permissions |
|------|-------------|
| **Admin** | Full system access, user management, system configuration |
| **Owner** | Manage organization, jobs, billing, view reports |
| **Driver** | View assigned jobs, track GPS, log expenses |
| **Broker** | Create jobs, manage clients, view commissions |
| **Accountant** | View financial data, manage payments, generate reports |

### 🔒 Security Features

- JWT-based authentication with refresh tokens
- Role-based access control (RBAC)
- Organization-level data isolation
- API rate limiting (100 req/min per user)
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- CORS configuration
- Encrypted sensitive data
- Audit trails (created_by, updated_by)
- Soft deletes for data recovery

### 📊 Performance & Scalability

- Horizontal scaling support
- Database query optimization with proper indexing
- Redis caching for frequently accessed data
- Background job processing for heavy tasks
- CDN for static assets
- Pagination for large datasets
- Lazy loading and code splitting
- Optimized GPS tracking intervals

### 🌐 Localization

- **Languages**: English and Sinhala
- Dynamic language switching
- Date/time localization (Asia/Colombo timezone)
- Currency formatting (LKR)
- Right-to-left (RTL) support ready

### 📱 Mobile App Features

- Intuitive UI suitable for rural users
- Offline-first with background sync
- Battery-optimized GPS tracking
- **Bluetooth Thermal Printer Support** ⭐ NEW
  - ESC/POS compatible printers
  - Direct invoice printing
  - Receipt generation and printing
  - Job summary printing
  - Offline print queue with retry
  - PDF fallback when printer unavailable
- Push notifications
- Photo capture for receipts
- PDF viewing and sharing
- Dark mode support
- Biometric authentication

### 🧪 Testing

- **Backend**: PHPUnit for unit and feature tests
- **Frontend**: Jest + React Native Testing Library
- **E2E**: Detox for mobile app testing
- **API**: Postman collections for endpoint testing

### 📈 Monitoring & Analytics

- Application performance monitoring (APM)
- Error tracking with Sentry
- User analytics
- Server monitoring (CPU, RAM, Disk)
- Database query performance
- API response time tracking

### 🔄 CI/CD Pipeline

- Automated testing on pull requests
- Code quality checks (PHPStan, ESLint)
- Automated deployment to staging
- Manual approval for production deployment
- Database migration validation
- Rollback capabilities

### 📝 Project Status

This repository contains the complete architecture, database schema, API documentation, and implementation guides for the Geo Ops Platform. The structure follows enterprise-grade best practices with Clean Architecture, SOLID principles, and production-ready configurations.

### 🤝 Contributing

Contributions are welcome! Please follow the established architecture patterns and coding standards outlined in the documentation.

### 📄 License

[Specify your license here]

### 📞 Support

For questions or support, please [create an issue](https://github.com/kasunvimarshana/geo-ops-platform/issues) or contact the development team.

---

**Built with ❤️ for agricultural field service management in Sri Lanka**
