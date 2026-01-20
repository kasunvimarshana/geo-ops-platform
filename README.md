# 🌾 GeoOps Platform

> **GPS Land Measurement & Agricultural Field Service Management System**

A production-ready, enterprise-grade platform for agricultural field service management with GPS land measurement, job tracking, billing, and offline-first mobile capabilities.

**Tech Stack:** Laravel (Backend) + React Native with Expo (Mobile) | Built for scale and reliability

---

## 📋 Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
- [Architecture](#architecture)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 Overview

The GeoOps Platform is a comprehensive solution for managing agricultural field services. It enables:

- **Farmers** to request and track field services
- **Service Providers** to manage drivers, machines, and operations
- **Drivers** to execute jobs with GPS tracking
- **Accountants** to manage billing, payments, and financial reports

The system is designed for **thousands of users across Sri Lanka** with:

- ✅ **Offline-first architecture** - Works without internet
- ✅ **Clean Architecture** - Maintainable and testable
- ✅ **Bilingual support** - Sinhala and English
- ✅ **Production-ready** - Scalable and secure

---

## ✨ Key Features

### 🗺️ GPS Land Measurement

- Walk-around GPS measurement with real-time polygon drawing
- Point-based land measurement
- Accurate area calculation (acres & hectares)
- Measurement history and editing
- Coordinate storage with spatial indexing

### 👷 Job Management

- Complete job lifecycle (Pending → In Progress → Completed)
- Driver and machine assignment
- Real-time GPS tracking during job execution
- Job history and reporting
- Work area validation against measured land

### 💰 Billing & Invoicing

- Automated invoice generation from measured area
- Configurable rates per acre/hectare
- PDF invoice generation (background jobs)
- Multiple invoice statuses (Draft, Sent, Paid, Overdue)
- Invoice sharing via email/WhatsApp

### 💳 Payments & Ledger

- Multiple payment methods (Cash, Bank Transfer, Mobile Money)
- Payment tracking against invoices
- Customer balance management
- Payment receipts
- Financial reports and analytics

### 📊 Expense Management

- Fuel tracking
- Parts and maintenance expenses
- Machine-wise and driver-wise expense categorization
- Receipt image uploads
- Expense reports

### 📈 Reports & Analytics

- Dashboard with key metrics
- Financial reports (Income vs Expenses)
- Job completion reports
- Customer balance reports
- Date-range filtering and export

### 🔒 Multi-tenancy & Subscriptions

- Organization-level data isolation
- Three subscription tiers (Free, Basic, Pro)
- Usage limit enforcement (measurements, drivers, machines)
- Subscription expiry handling

### 📡 Offline-First Mobile App

- Works completely offline
- Local SQLite database
- Background synchronization
- Conflict resolution
- Automatic retry logic

### 🔐 Security & Authentication

- JWT-based authentication
- Role-based access control (Admin, Owner, Driver, Broker, Accountant)
- API rate limiting
- Secure file storage
- Audit logging

---

## 🛠️ Technology Stack

### Backend (Laravel)

- **Framework**: Laravel 11.x (LTS)
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+ / PostgreSQL 15+
- **Cache/Queue**: Redis
- **Authentication**: JWT (tymon/jwt-auth)
- **PDF Generation**: DomPDF
- **Architecture**: Clean Architecture (Controllers → Services → Repositories)

### Mobile (React Native + Expo)

- **Framework**: Expo SDK 50+
- **Language**: TypeScript 5+
- **State Management**: Zustand
- **Offline Storage**: Expo SQLite
- **Maps**: react-native-maps (Google Maps / Mapbox)
- **GPS**: expo-location with background tracking
- **HTTP**: Axios with interceptors
- **i18n**: react-i18next (Sinhala/English)

### Infrastructure

- **Web Server**: Nginx
- **SSL**: Let's Encrypt
- **Storage**: AWS S3 / Local
- **Monitoring**: Sentry / Laravel Telescope
- **CI/CD**: GitHub Actions

---

## 📁 Project Structure

```
geo-ops-platform/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── DTOs/              # Data Transfer Objects
│   │   ├── Http/
│   │   │   ├── Controllers/   # API Controllers
│   │   │   ├── Requests/      # Form Request Validation
│   │   │   └── Resources/     # API Resources
│   │   ├── Models/            # Eloquent Models
│   │   ├── Repositories/      # Data Access Layer
│   │   ├── Services/          # Business Logic
│   │   └── Jobs/              # Background Jobs
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
│       └── api.php
├── mobile/                     # React Native Expo App
│   ├── src/
│   │   ├── api/               # API Client
│   │   ├── components/        # Reusable Components
│   │   ├── database/          # SQLite Database
│   │   ├── features/          # Feature Modules
│   │   ├── navigation/        # React Navigation
│   │   ├── services/          # Business Services
│   │   ├── store/             # Zustand Stores
│   │   └── utils/             # Utility Functions
│   └── assets/
│       └── locales/           # i18n Translations
├── docs/                       # Additional Documentation
├── ARCHITECTURE.md            # System Architecture
├── DATABASE_SCHEMA.md         # Database Design & ERD
├── API_SPECIFICATION.md       # API Endpoints
├── BACKEND_STRUCTURE.md       # Backend Code Structure
├── FRONTEND_STRUCTURE.md      # Mobile App Structure
├── SETUP_GUIDE.md             # Development Setup
├── DEPLOYMENT_GUIDE.md        # Production Deployment
├── SEED_DATA.md               # Sample Data
└── README.md                  # This file
```

---

## 🚀 Quick Start

### Automated Setup (Recommended)

We provide automated setup scripts to quickly initialize the project:

#### Backend Setup

```bash
# Run the backend setup script
./setup-backend.sh

# This will:
# - Create Laravel 11.x project
# - Install JWT Auth, DomPDF, and Telescope
# - Configure environment files
# - Generate application and JWT keys
```

#### Mobile Setup

```bash
# Run the mobile setup script
./setup-mobile.sh

# This will:
# - Create Expo TypeScript project
# - Install all required dependencies
# - Set up folder structure
# - Configure environment files
```

### Manual Setup

If you prefer manual setup:

#### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

#### Mobile

```bash
cd mobile
npm install
cp .env.example .env
npm start
```

**📖 For detailed step-by-step instructions, see [QUICKSTART.md](QUICKSTART.md)**

---

## 📚 Documentation

| Document                                       | Description                                           |
| ---------------------------------------------- | ----------------------------------------------------- |
| [ARCHITECTURE.md](ARCHITECTURE.md)             | System architecture, design patterns, and scalability |
| [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)       | Complete database schema with ERD                     |
| [API_SPECIFICATION.md](API_SPECIFICATION.md)   | RESTful API endpoint documentation                    |
| [BACKEND_STRUCTURE.md](BACKEND_STRUCTURE.md)   | Laravel backend code organization                     |
| [FRONTEND_STRUCTURE.md](FRONTEND_STRUCTURE.md) | React Native app structure                            |
| [SETUP_GUIDE.md](SETUP_GUIDE.md)               | Local development setup guide                         |
| [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)     | Production deployment instructions                    |
| [SEED_DATA.md](SEED_DATA.md)                   | Sample data for testing                               |

---

## 🏗️ Architecture

### Clean Architecture Layers

```
┌─────────────────────────────────────────┐
│        Presentation Layer               │
│     (Controllers, Resources)            │
├─────────────────────────────────────────┤
│       Application Layer                 │
│    (Services, Business Logic)           │
├─────────────────────────────────────────┤
│         Domain Layer                    │
│      (Models, DTOs)                     │
├─────────────────────────────────────────┤
│      Infrastructure Layer               │
│   (Repositories, External APIs)         │
└─────────────────────────────────────────┘
```

### Design Principles

- ✅ **SOLID** - Single Responsibility, Open/Closed, etc.
- ✅ **DRY** - Don't Repeat Yourself
- ✅ **KISS** - Keep It Simple, Stupid
- ✅ **Clean Code** - Readable and maintainable
- ✅ **Repository Pattern** - Data access abstraction
- ✅ **Service Layer** - Business logic isolation

**📖 For detailed architecture, see [ARCHITECTURE.md](ARCHITECTURE.md)**

---

## 🔑 Key Endpoints

### Authentication

```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
GET    /api/v1/auth/me
```

### Land Management

```
GET    /api/v1/lands
POST   /api/v1/lands
GET    /api/v1/lands/{id}
PUT    /api/v1/lands/{id}
DELETE /api/v1/lands/{id}
```

### Job Management

```
GET    /api/v1/jobs
POST   /api/v1/jobs
POST   /api/v1/jobs/{id}/start
POST   /api/v1/jobs/{id}/complete
POST   /api/v1/jobs/{id}/tracking
```

### Invoicing

```
GET    /api/v1/invoices
POST   /api/v1/invoices
GET    /api/v1/invoices/{id}/pdf
```

**📖 For complete API documentation, see [API_SPECIFICATION.md](API_SPECIFICATION.md)**

---

## 🌐 Deployment

### Production Requirements

- Ubuntu 22.04 LTS server
- 4+ CPU cores, 8GB+ RAM
- MySQL/PostgreSQL database
- Redis for cache & queues
- Nginx web server
- SSL certificate (Let's Encrypt)
- Supervisor for queue workers

### Quick Deploy

```bash
# Backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan migrate --force

# Setup queue workers
sudo supervisorctl start geo-ops-worker:*

# Configure Nginx
sudo systemctl restart nginx
```

**📖 For complete deployment guide, see [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)**

---

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

---

## 📱 Mobile App

### Features Highlight

- 📍 Real-time GPS tracking with battery optimization
- 🗺️ Interactive map with polygon drawing
- 💾 Offline-first with SQLite storage
- 🔄 Background sync with conflict resolution
- 🌐 Bilingual UI (Sinhala/English)
- 📊 Dashboard with statistics
- 📄 PDF generation and sharing

### Build for Production

```bash
# Android
eas build --platform android --profile production

# iOS
eas build --platform ios --profile production
```

---

## 👥 User Roles

- **Admin** - Full system access, organization management
- **Owner** - Business owner, manage customers and machines
- **Driver** - Execute jobs, track GPS, log expenses
- **Broker** - Coordinate between farmers and service providers
- **Accountant** - Handle billing, payments, and reports

---

## 💡 Use Cases

1. **Farmer** requests land measurement service
2. **Admin** creates customer record and assigns job
3. **Driver** measures land using GPS walk-around
4. **System** calculates area and creates land record
5. **Admin** creates job for plowing with pricing
6. **Driver** starts job and GPS tracking begins
7. **Driver** completes job
8. **System** generates invoice automatically
9. **Accountant** sends invoice to farmer
10. **Farmer** makes payment
11. **System** updates ledger and customer balance

---

## 🔐 Security Features

- JWT token authentication with refresh
- Role-based access control (RBAC)
- Organization-level data isolation
- SQL injection protection
- XSS protection headers
- CSRF token validation
- Rate limiting on API endpoints
- Secure file storage
- Audit logging for all actions

---

## 📊 Database Schema

### Core Tables

- `organizations` - Multi-tenant organizations
- `users` - System users with roles
- `customers` - Farmers/clients
- `lands` - Measured land parcels with GPS polygons
- `jobs` - Field work jobs
- `job_tracking` - GPS tracking history
- `invoices` - Billing invoices
- `payments` - Payment records
- `expenses` - Business expenses
- `machines` - Agricultural equipment
- `drivers` - Driver details

**📖 For complete schema, see [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)**

---

## 🌍 Localization

Supports Sinhala (සිංහල) and English:

```typescript
// Example
t("dashboard.welcome");
// English: "Welcome"
// Sinhala: "ආයුබෝවන්"
```

---

## 📈 Scalability

The system is designed to handle:

- **10,000+** active users
- **100,000+** land measurements
- **500,000+** job records
- **1M+** tracking points

### Scaling Strategies

- Horizontal scaling with load balancer
- Database read replicas
- Redis clustering for cache/queue
- CDN for static assets
- Background job processing
- API response caching

---

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines on:

- Development workflow
- Coding standards (PSR-12 for PHP, Airbnb for TypeScript)
- Testing requirements
- Pull request process

### Quick Contribution Guide

```bash
# Fork and clone the repository
git clone https://github.com/YOUR_USERNAME/geo-ops-platform.git

# Create feature branch
git checkout -b feature/your-feature

# Make changes and commit
git commit -m "feat: add your feature"

# Push and create PR
git push origin feature/your-feature
```

---

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## 📞 Support

- **Email**: support@geo-ops.lk
- **Issues**: GitHub Issues
- **Documentation**: See docs/ folder

---

## 🙏 Acknowledgments

Built for farmers and agricultural service providers in Sri Lanka 🇱🇰

---

**🚀 Ready to revolutionize agricultural field services!**
