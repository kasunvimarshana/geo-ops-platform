# GeoOps Platform

> **Production-Ready GPS Land Measurement & Agricultural Field Service Management Application**

A comprehensive, scalable platform for GPS-based land measurement, agricultural service management, job tracking, invoicing, and financial reporting, built with **Laravel (Backend)** and **React Native Expo (Mobile)**, designed for thousands of users in Sri Lanka and similar markets.

---

## 🎯 Overview

GeoOps Platform is a full-stack, offline-first mobile and web application designed to help agricultural service providers manage their operations efficiently. The system supports:

- **GPS Land Measurement** (walk-around & point-based)
- **Job & Field Work Management**
- **Driver/Broker GPS Tracking**
- **Automated Billing & Invoice Generation (PDF)**
- **Expense Tracking & Payment Management**
- **Financial Reports & Analytics**
- **Subscription-Based Packages** (Free/Basic/Pro)
- **Offline-First Functionality** with Background Sync
- **Multilingual Support** (English/Sinhala)

---

## 🏗️ Architecture

### Technology Stack

#### **Backend (API Server)**

- **Framework**: Laravel 12.x (Latest LTS)
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+ or PostgreSQL 13+ (with PostGIS)
- **Cache/Queue**: Redis 7.0+
- **Authentication**: JWT
- **Architecture**: Clean Architecture (Controllers → Services → Repositories)

#### **Mobile App**

- **Framework**: React Native with Expo SDK 54+
- **Language**: TypeScript 5+
- **State Management**: Zustand + React Query
- **Offline Storage**: SQLite (expo-sqlite) + MMKV
- **Maps**: Google Maps / Mapbox
- **GPS**: expo-location
- **Architecture**: Feature-Based Modular Structure

---

## 📁 Project Structure

```
geo-ops-platform/
├── backend/                 # Laravel API backend
│   ├── app/
│   │   ├── DTOs/           # Data Transfer Objects
│   │   ├── Services/       # Business Logic Layer
│   │   ├── Repositories/   # Data Access Layer
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/
│   │   │   └── Resources/
│   │   ├── Models/
│   │   └── Jobs/           # Background Jobs
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   └── tests/
│
├── mobile/                  # React Native Expo app
│   ├── src/
│   │   ├── features/       # Feature modules
│   │   ├── services/       # API, Storage, GPS, Sync
│   │   ├── shared/         # Shared components
│   │   ├── navigation/
│   │   ├── store/          # Global state
│   │   └── i18n/          # Translations
│   └── assets/
│
└── docs/                    # Comprehensive documentation
    ├── ARCHITECTURE.md
    ├── DATABASE_SCHEMA.md
    ├── API_SPECIFICATION.md
    ├── BACKEND_STRUCTURE.md
    ├── MOBILE_STRUCTURE.md
    ├── DEPLOYMENT.md
    └── SEED_DATA.md
```

---

## 📚 Documentation

Comprehensive documentation is available in the `/docs` directory:

| Document                                                | Description                                                |
| ------------------------------------------------------- | ---------------------------------------------------------- |
| [**ARCHITECTURE.md**](./docs/ARCHITECTURE.md)           | Complete system architecture, design principles, data flow |
| [**DATABASE_SCHEMA.md**](./docs/DATABASE_SCHEMA.md)     | Full database schema, ERD, table definitions               |
| [**API_SPECIFICATION.md**](./docs/API_SPECIFICATION.md) | Complete REST API documentation with examples              |
| [**BACKEND_STRUCTURE.md**](./docs/BACKEND_STRUCTURE.md) | Laravel project structure and clean architecture guide     |
| [**MOBILE_STRUCTURE.md**](./docs/MOBILE_STRUCTURE.md)   | React Native feature-based architecture guide              |
| [**DEPLOYMENT.md**](./docs/DEPLOYMENT.md)               | Production deployment instructions for backend and mobile  |
| [**SEED_DATA.md**](./docs/SEED_DATA.md)                 | Sample data for testing and development                    |

---

## 🚀 Quick Start

### Prerequisites

**Backend:**

- PHP 8.2+
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Redis
- Node.js & npm (for Laravel Mix)

**Mobile:**

- Node.js 18+
- npm or yarn
- Expo CLI
- iOS Simulator (Mac) or Android Emulator

---

### Backend Setup

```bash
# Navigate to backend directory
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_DATABASE=geo-ops
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed subscription packages
php artisan db:seed --class=SubscriptionPackageSeeder

# (Optional) Seed sample data for testing
php artisan db:seed --class=SampleDataSeeder

# Start development server
php artisan serve
```

Backend API will be available at `http://localhost:8000`

---

### Mobile Setup

```bash
# Navigate to mobile directory
cd mobile

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Configure API URL in .env
# API_BASE_URL=http://localhost:8000/api/v1

# Start Expo development server
npm start

# OR use specific platform
npm run android    # For Android
npm run ios        # For iOS (Mac only)
npm run web        # For web browser
```

---

## 🔑 Key Features

### 1. GPS Land Measurement

- **Walk-Around Mode**: Track user movement with GPS
- **Point-Based Mode**: Tap to add polygon points
- **Accurate Calculations**: Area in acres, hectares, square meters
- **Real-Time Preview**: See polygon on map as you measure
- **Offline Support**: Measure without internet connection

### 2. Job Management

- **Lifecycle Tracking**: Pending → Assigned → In Progress → Completed
- **Driver Assignment**: Assign jobs to specific drivers
- **Customer Information**: Store customer details with each job
- **Rate Configuration**: Set rates per acre/hectare
- **Job History**: Complete job tracking and history

### 3. GPS Tracking

- **Real-Time Location**: Track drivers during active jobs
- **Historical Routes**: View past job routes
- **Distance Calculation**: Automatic distance and duration tracking
- **Background Tracking**: Continue tracking even when app is minimized

### 4. Invoicing & Billing

- **Automated Generation**: Create invoices from completed jobs
- **PDF Export**: Generate professional PDF invoices
- **Configurable Rates**: Set custom rates per service type
- **Payment Tracking**: Record and track payments
- **Outstanding Balances**: Track unpaid invoices

### 5. Expense Management

- **Category Tracking**: Fuel, maintenance, parts, salary, etc.
- **Job Association**: Link expenses to specific jobs
- **Receipt Storage**: Upload and store receipt images
- **Financial Reports**: Income vs. expenses

### 6. Subscription Packages

- **Free Plan**: 10 measurements, 2 drivers, 20 jobs
- **Basic Plan**: 100 measurements, 5 drivers, 200 jobs (LKR 2,500/mo)
- **Pro Plan**: Unlimited everything (LKR 5,000/mo)
- **Usage Enforcement**: Automatic limit checking
- **Upgrade Prompts**: In-app upgrade suggestions

### 7. Offline-First Architecture

- **Local Storage**: SQLite for all data
- **Sync Queue**: Background synchronization
- **Conflict Resolution**: Server-authoritative conflict handling
- **Optimistic Updates**: Instant UI updates
- **Reliable Sync**: Retry with exponential backoff

### 8. Multilingual Support

- **English**: Full translation
- **Sinhala (සිංහල)**: Complete Sinhala localization
- **Easy Switching**: In-app language selector
- **Rural-Friendly UX**: Simple, intuitive interface

---

## 🏛️ Clean Architecture Principles

### Backend (Laravel)

**Layered Architecture:**

```
Controllers (Thin)
    ↓
Services (Business Logic)
    ↓
Repositories (Data Access)
    ↓
Models (Eloquent ORM)
```

**Key Principles:**

- ✅ **SOLID**: Single responsibility, dependency injection
- ✅ **DRY**: No code duplication
- ✅ **KISS**: Keep it simple and maintainable
- ✅ **Separation of Concerns**: Clear layer boundaries
- ✅ **Testability**: Easy to unit test each layer

### Mobile (React Native)

**Feature-Based Modules:**

```
Feature Module
├── screens/      # UI components
├── components/   # Reusable UI pieces
├── hooks/        # Custom hooks
├── services/     # API calls, business logic
├── store/        # Feature state
└── types/        # TypeScript definitions
```

**Key Principles:**

- ✅ **Modularity**: Self-contained features
- ✅ **Reusability**: Shared components
- ✅ **Type Safety**: Full TypeScript coverage
- ✅ **Predictable State**: Zustand + React Query
- ✅ **Offline-First**: Local persistence + sync

---

## 🧪 Testing

### Backend Tests

```bash
cd backend

# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter LandTest

# Run with coverage
php artisan test --coverage
```

### Mobile Tests

```bash
cd mobile

# Run unit tests
npm test

# Run with coverage
npm test -- --coverage

# Run e2e tests (if configured)
npm run test:e2e
```

---

## 📦 Deployment

See [DEPLOYMENT.md](./docs/DEPLOYMENT.md) for comprehensive deployment instructions.

### Backend Deployment (Quick)

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start queue workers
php artisan queue:work redis --tries=3
```

### Mobile Deployment (Quick)

```bash
# Install EAS CLI
npm install -g eas-cli

# Login to Expo
eas login

# Build for production
eas build --platform android --profile production
eas build --platform ios --profile production

# Submit to stores
eas submit --platform android
eas submit --platform ios
```

---

## 🔐 Security

- ✅ JWT-based authentication with refresh tokens
- ✅ Role-based authorization (Admin, Owner, Driver, Broker, Accountant)
- ✅ Organization-level data isolation
- ✅ Input validation on all endpoints
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection
- ✅ CORS configuration
- ✅ Rate limiting
- ✅ Secure password hashing (bcrypt)
- ✅ Audit logging for critical operations

---

## 📈 Scalability

**Designed to support thousands of users:**

- **Horizontal Scaling**: Stateless API, multiple app servers
- **Database Optimization**: Proper indexing, query optimization
- **Caching**: Redis for sessions and frequently accessed data
- **Queue Workers**: Background processing for PDFs, reports
- **CDN**: Static assets served via CDN
- **Load Balancing**: Nginx/HAProxy for traffic distribution

---

## 🤝 Contributing

This is a commercial project. For contribution guidelines, please contact the maintainers.

---

## 📄 License

Proprietary License. All rights reserved.

---

## 👥 Team

- **Architecture & Backend**: Laravel Clean Architecture specialists
- **Mobile Development**: React Native + Expo experts
- **GPS/GIS**: Geographical systems specialists
- **UX/UI**: Rural user experience designers
- **DevOps**: Deployment and scaling engineers

---

## 📞 Support

- **Email**: support@geo-ops.lk
- **Phone**: +94 XX XXX XXXX
- **Documentation**: See `/docs` directory
- **API Docs**: https://api.geo-ops.lk/docs

---

## 🗺️ Roadmap

### Phase 1 (Current)

- ✅ Core GPS measurement
- ✅ Job management
- ✅ Invoicing & payments
- ✅ Offline support
- ✅ Basic reporting

### Phase 2 (Upcoming)

- 🔄 Real-time WebSocket tracking
- 🔄 Advanced analytics dashboard
- 🔄 Bluetooth printer support
- 🔄 WhatsApp integration
- 🔄 Machine learning area prediction

### Phase 3 (Future)

- 📅 Satellite imagery overlay
- 📅 Drone measurement support
- 📅 Weather API integration
- 📅 Voice commands (Sinhala)
- 📅 Mobile web version

---

## 🙏 Acknowledgments

Built with modern tools and best practices for agricultural service providers in Sri Lanka and emerging markets.

---

**Made with ❤️ for farmers and agricultural service providers in Sri Lanka**
