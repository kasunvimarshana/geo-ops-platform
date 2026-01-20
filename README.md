# GeoOps Platform

> Production-ready GPS-based land measurement and agricultural field-service management platform

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Backend: Laravel 11](https://img.shields.io/badge/Backend-Laravel%2011-red.svg)](https://laravel.com)
[![Mobile: React Native](https://img.shields.io/badge/Mobile-React%20Native-blue.svg)](https://reactnative.dev)
[![Expo](https://img.shields.io/badge/Expo-~51.0-000020.svg)](https://expo.dev)

## 🎯 Overview

GeoOps Platform is a comprehensive GPS-based land measurement and agricultural field-service management application designed for farmers, machine owners, drivers, and brokers in Sri Lanka. The platform follows Clean Architecture principles and implements SOLID, DRY, and KISS design patterns for scalability, maintainability, and long-term extensibility.

## ✨ Key Features

### 📍 GPS Land Measurement

- **Walk-around GPS tracking** with real-time polygon drawing
- **Point-based measurement** for manual land marking
- **Accurate area calculation** in acres and hectares using Shoelace formula
- **Measurement history** with editable records
- **Offline measurement** capability with background sync

### 🗺️ Maps & Visualization

- **Interactive maps** (Google Maps/Mapbox integration)
- **Real-time visualization** of measured lands, jobs, and drivers
- **Color-coded status** indicators for easy identification
- **Spatial queries** for nearby lands and active jobs
- **Historical tracking** visualization

### 💼 Job & Field Work Management

- **Job creation and assignment** to drivers and machines
- **Lifecycle management** (Pending → In Progress → Completed)
- **Driver GPS tracking** during active jobs
- **Duration and distance** calculation
- **Job history** and performance reports

### 💰 Billing & Invoicing

- **Automated invoice generation** based on measured area
- **Configurable rates** per acre/hectare
- **PDF invoice generation** with professional templates
- **Bluetooth thermal printer** support (ESC/POS compatible)
- **Invoice status tracking** (Draft → Sent → Paid → Overdue)

### 💳 Expense Management

- **Fuel tracking** and consumption analysis
- **Spare parts and maintenance** logging
- **Expense categorization** by type, machine, and driver
- **Financial reporting** with income vs expense analysis

### 📊 Payments & Ledger

- **Multiple payment methods** (Cash, Bank, Digital, Check)
- **Customer balance tracking** and payment history
- **Financial summaries** with customizable date ranges
- **Ledger reports** per customer, driver, and machine

### 📦 Subscription Management

- **Package tiers**: Free, Basic, Pro
- **Usage limits enforcement** (measurements, drivers, exports)
- **Automatic expiry handling** with grace periods
- **Feature gating** based on subscription level

### 🔄 Offline-First Architecture

- **Local SQLite database** for offline data persistence
- **MMKV storage** for fast key-value data
- **Background synchronization** with conflict resolution
- **Retry mechanism** with exponential backoff
- **Idempotent sync** to prevent duplicates

### 🖨️ Bluetooth Printing

- **ESC/POS thermal printer** integration
- **Device discovery and pairing** management
- **Print queue** with retry mechanism
- **PDF fallback** when printer unavailable
- **Receipt and invoice** printing support

### 🔐 Security & Access Control

- **JWT authentication** with refresh tokens
- **Role-based access control** (RBAC)
- **Organization-level data isolation** (multi-tenancy)
- **Encrypted local storage** for sensitive data
- **API rate limiting** to prevent abuse

### 🌍 Localization

- **Sinhala (සිංහල)** - Primary language
- **English** - Secondary language
- **RTL support** not required
- **Number and date formatting** per locale

## 🏗️ Architecture

### Technology Stack

**Backend (Laravel 11 LTS)**

- PHP 8.3+
- MySQL 8.0+ / PostgreSQL 14+ with Spatial Extensions
- Redis for caching and queues
- JWT Authentication
- Clean Architecture with Service/Repository pattern

**Mobile App (React Native + Expo)**

- TypeScript
- Expo 51
- Zustand for state management
- SQLite + MMKV for offline storage
- React Native Maps
- Bluetooth ESC/POS Printer integration

### Clean Architecture Layers

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│  (Controllers/UI Components)            │
├─────────────────────────────────────────┤
│         Application Layer               │
│  (Services/Business Logic)              │
├─────────────────────────────────────────┤
│         Domain Layer                    │
│  (Models/DTOs/Policies)                 │
├─────────────────────────────────────────┤
│         Infrastructure Layer            │
│  (Repositories/Database/External APIs)  │
└─────────────────────────────────────────┘
```

### Design Principles

- **SOLID**: Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion
- **DRY**: Don't Repeat Yourself - reusable components and services
- **KISS**: Keep It Simple, Stupid - avoid over-engineering
- **Separation of Concerns**: Clear boundaries between layers
- **Dependency Injection**: Loose coupling for testability

## 📁 Project Structure

```
geo-ops-platform/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/   # Thin controllers (routing only)
│   │   │   ├── Middleware/    # Auth, RBAC, Rate limiting
│   │   │   └── Requests/      # Form validation
│   │   ├── Services/          # Business logic layer
│   │   ├── Repositories/      # Data access layer
│   │   ├── Models/            # Eloquent models
│   │   ├── DTOs/              # Data Transfer Objects
│   │   ├── Jobs/              # Queue jobs
│   │   └── Policies/          # Authorization policies
│   ├── database/
│   │   ├── migrations/        # Database schema
│   │   └── seeders/           # Sample data
│   └── routes/
│       └── api.php            # API endpoints
│
├── mobile/                     # React Native Expo App
│   ├── src/
│   │   ├── features/          # Feature-based modules
│   │   │   ├── auth/
│   │   │   ├── measurement/
│   │   │   ├── maps/
│   │   │   ├── jobs/
│   │   │   ├── billing/
│   │   │   └── sync/
│   │   ├── services/          # API, GPS, Storage, Sync, Printer
│   │   ├── stores/            # Zustand state management
│   │   ├── components/        # Reusable UI components
│   │   ├── utils/             # Helper functions
│   │   └── i18n/              # Localization
│   └── app.json
│
└── docs/                       # Documentation
    ├── ARCHITECTURE.md         # System architecture
    ├── DATABASE.md             # Database schema and ERD
    ├── API.md                  # API endpoints documentation
    └── DEPLOYMENT.md           # Deployment guide
```

## 🚀 Getting Started

### Prerequisites

**Backend**

- PHP 8.3 or higher
- Composer 2.x
- MySQL 8.0+ or PostgreSQL 14+
- Redis 6.0+

**Mobile**

- Node.js 20+
- npm or yarn
- Expo CLI
- Android Studio / Xcode (for building)

### Backend Setup

1. Clone the repository:

```bash
git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform/backend
```

2. Install dependencies:

```bash
composer install
```

3. Configure environment:

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

4. Update `.env` with your database and service credentials

5. Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

6. Start the development server:

```bash
php artisan serve
```

### Mobile App Setup

1. Navigate to mobile directory:

```bash
cd mobile
```

2. Install dependencies:

```bash
npm install
```

3. Configure environment:

```bash
cp .env.example .env
```

4. Update `.env` with API URL and service keys

5. Start Expo development server:

```bash
npm start
```

6. Run on device/simulator:

```bash
npm run android  # For Android
npm run ios      # For iOS
```

## 📚 Documentation

**Complete documentation is available in the [/documents](documents/) directory.**

### Quick Links

- **[📖 Getting Started](documents/getting-started.md)** - Quick setup guide (5 minutes)
- **[🏗️ Architecture](documents/architecture.md)** - System design and patterns
- **[🔌 API Reference](documents/api-reference.md)** - Complete REST API documentation
- **[💾 Database Schema](documents/database-schema.md)** - ERD and table definitions
- **[🚀 Deployment Guide](documents/deployment.md)** - Production deployment instructions
- **[🧪 Testing Guide](documents/testing-guide.md)** - Testing strategy and examples
- **[📊 Implementation Status](documents/implementation-status.md)** - Project completion status

**📁 [View Complete Documentation Index](documents/README.md)**

## 🧪 Testing

### Backend Tests

```bash
cd backend
php artisan test
```

### Mobile Tests

```bash
cd mobile
npm test
```

## 🔒 Security

- JWT authentication with refresh tokens
- HTTPS/TLS encryption for all API communications
- SQL injection prevention through ORM
- CSRF protection
- Rate limiting on API endpoints
- Secure local storage encryption
- Organization-level data isolation

## 📈 Scalability

- Horizontal scaling support
- Database read replicas
- Redis caching layer
- Queue workers for async processing
- CDN for static assets
- Microservices-ready architecture

## 🤝 Contributing

Contributions are welcome! Please read our contributing guidelines before submitting pull requests.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 User Roles

- **Admin** - System-wide control and management
- **Owner (Farmer/Machine Owner)** - Organization management
- **Driver/Operator** - Field work execution
- **Broker/Agent** - Customer relationship management
- **Accountant** - Financial reporting and management

## 🌟 Core Modules

1. **Authentication & Authorization** - JWT + RBAC
2. **GPS Land Measurement** - Walk-around & point-based
3. **Maps & Visualization** - Real-time tracking
4. **Job Management** - Lifecycle and assignment
5. **Billing & Invoicing** - Automated generation
6. **Expense Management** - Categorized tracking
7. **Payments & Ledger** - Financial management
8. **Subscription Management** - Package enforcement
9. **Offline-First Sync** - Background synchronization
10. **Bluetooth Printing** - Thermal printer integration

## 🎯 Target Users

- Farmers and landowners in Sri Lanka
- Agricultural machinery owners
- Tractor and equipment operators
- Brokers and field agents
- Agricultural service businesses

## 📞 Support

For support, email support@geo-ops.lk or open an issue in the GitHub repository.

## 🔄 Version

Current Version: **1.0.0**

## 📅 Roadmap

- [ ] Multi-language support expansion
- [ ] Weather integration for field planning
- [ ] IoT sensor integration
- [ ] AI-powered yield prediction
- [ ] Satellite imagery integration
- [ ] Drone mapping support
- [ ] Marketplace for equipment rental
- [ ] Community features for knowledge sharing

---

**Built with ❤️ for the agricultural community of Sri Lanka**

## GPS Land Measurement & Field Service Management Platform

_(Laravel Backend + React Native (Expo) Mobile App)_
