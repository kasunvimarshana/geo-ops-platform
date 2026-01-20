# Getting Started with GeoOps Platform

> Quick start guide to get the GPS-based land measurement and agricultural field-service management platform running locally.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Quick Setup](#quick-setup)
- [Backend Setup](#backend-setup)
- [Mobile App Setup](#mobile-app-setup)
- [Verification](#verification)
- [Next Steps](#next-steps)

## Prerequisites

### Backend Requirements
- **PHP**: 8.3 or higher
- **Composer**: 2.x
- **Database**: MySQL 8.0+ or PostgreSQL 14+ with Spatial Extensions
- **Cache**: Redis 6.0+
- **PHP Extensions**: pdo, pdo_mysql, mbstring, xml, bcmath, gd, zip

### Mobile Requirements
- **Node.js**: 20+
- **Package Manager**: npm or yarn
- **Expo CLI**: `npm install -g expo-cli`
- **Development Tools**: Android Studio (for Android) or Xcode (for iOS)
- **Testing Device**: Physical device or emulator with GPS capability

### Development Tools (Recommended)
- **Editor**: Visual Studio Code
- **Git**: Latest version
- **Postman**: For API testing
- **React Native Debugger**: For mobile debugging

## Quick Setup

### 1. Clone Repository

```bash
git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform
```

### 2. Backend Setup (5 minutes)

```bash
cd backend

# Install dependencies
composer install

# Configure environment
cp .env.example .env

# Edit .env and set your database credentials
# DB_DATABASE=geo_ops_platform
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Generate keys
php artisan key:generate
php artisan jwt:secret

# Create database
mysql -u root -p -e "CREATE DATABASE geo_ops_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations and seeders
php artisan migrate --seed

# Start server
php artisan serve
```

Backend API will be available at: **http://localhost:8000**

### 3. Mobile App Setup (5 minutes)

```bash
cd ../mobile

# Install dependencies
npm install

# Configure environment
cp .env.example .env

# Edit .env and set API URL
# EXPO_PUBLIC_API_URL=http://localhost:8000/api/v1

# Start Expo development server
npm start
```

### 4. Run Mobile App

After starting Expo:
- Press **a** for Android emulator
- Press **i** for iOS simulator
- Scan QR code with Expo Go app on your phone

## Backend Setup (Detailed)

### Database Configuration

#### MySQL
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo_ops_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### PostgreSQL (with PostGIS)
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=geo_ops_platform
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

For PostGIS support:
```sql
CREATE EXTENSION postgis;
```

### Redis Configuration

```env
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

### JWT Configuration

```env
JWT_SECRET=your_jwt_secret_here
JWT_TTL=60
JWT_REFRESH_TTL=20160
```

### Queue Worker

For background jobs (PDF generation, sync processing):

```bash
php artisan queue:work --tries=3
```

### Database Seeding

The default seeder creates:
- **5 Roles**: Admin, Owner, Driver, Broker, Accountant
- **1 Demo Organization**: "Demo Farms"
- **3 Demo Users**: admin@demo.com, owner@demo.com, driver@demo.com
- **Subscription Limits**: Free, Basic, Pro tiers

Default credentials:
```
Email: admin@demo.com
Password: password
```

## Mobile App Setup (Detailed)

### Environment Variables

Create `.env` file in mobile directory:

```env
# API Configuration
EXPO_PUBLIC_API_URL=http://localhost:8000/api/v1

# Maps API Keys
EXPO_PUBLIC_GOOGLE_MAPS_KEY=your_google_maps_key
EXPO_PUBLIC_MAPBOX_KEY=your_mapbox_key

# App Configuration
EXPO_PUBLIC_APP_ENV=development
EXPO_PUBLIC_DEFAULT_LANGUAGE=en
```

### Expo Configuration

The `app.json` is pre-configured with:
- Location permissions
- Camera permissions
- Bluetooth permissions
- Background location
- File system access

### Dependencies

All required packages are listed in `package.json`:
- React Native 0.74
- Expo 51
- React Navigation
- Zustand (state management)
- SQLite (offline storage)
- MMKV (key-value storage)
- React Native Maps
- i18next (localization)

## Verification

### Test Backend API

```bash
# Health check
curl http://localhost:8000/api/v1/health

# Expected response:
# {"status":"ok","database":"connected","cache":"connected","timestamp":"..."}

# Test authentication
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@demo.com","password":"password"}'
```

### Test Mobile App

1. Open app in Expo Go
2. See splash screen
3. Navigate to Login screen
4. Login with demo credentials:
   - Email: `admin@demo.com`
   - Password: `password`
5. Access main dashboard

### Common Issues

#### Backend Issues

**"Extension pdo_mysql not installed"**
```bash
# Ubuntu/Debian
sudo apt-get install php8.3-mysql

# macOS
brew install php@8.3
```

**"Redis connection refused"**
```bash
# Start Redis
redis-server

# Or use database queue driver in .env
QUEUE_CONNECTION=database
```

**"Access denied for user"**
- Check database credentials in `.env`
- Ensure database exists
- Grant proper permissions

#### Mobile Issues

**"Metro bundler failed to start"**
```bash
# Clear cache
npm start -- --reset-cache
```

**"Unable to resolve module"**
```bash
# Reinstall dependencies
rm -rf node_modules
npm install
```

**"Network request failed"**
- Ensure backend is running
- Check API URL in `.env`
- For physical device, use computer's IP address instead of localhost

## Next Steps

### Development Workflow

1. **Read Documentation**
   - [Architecture Guide](architecture.md)
   - [API Documentation](api-reference.md)
   - [Database Schema](database-schema.md)

2. **Understand Structure**
   - Backend: Clean Architecture with Services/Repositories
   - Mobile: Feature-based modular architecture

3. **Start Coding**
   - Follow SOLID principles
   - Write tests as you code
   - Use existing patterns

4. **Testing**
   - Backend: `php artisan test`
   - Mobile: `npm test`

5. **Deployment**
   - See [Deployment Guide](deployment.md)

### Learning Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Expo Documentation**: https://docs.expo.dev
- **React Native**: https://reactnative.dev
- **Clean Architecture**: See `architecture.md`

### Getting Help

- **Documentation**: Check `/documents` directory
- **GitHub Issues**: Open an issue for bugs or questions
- **Code Examples**: Review implementation files in `backend/app/` and `mobile/src/`

## Development Commands

### Backend

```bash
# List all Artisan commands
php artisan list

# List all routes
php artisan route:list

# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# Format code
./vendor/bin/pint

# Create new migration
php artisan make:migration create_something_table

# Create new controller
php artisan make:controller SomethingController

# Create new model
php artisan make:model Something
```

### Mobile

```bash
# Run on Android
npm run android

# Run on iOS
npm run ios

# Run tests
npm test

# Type checking
npm run type-check

# Lint code
npm run lint

# Clear all caches
npm start -- --reset-cache
```

## Project Structure

```
geo-ops-platform/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/   # API endpoints
│   │   ├── Services/           # Business logic
│   │   ├── Repositories/       # Data access
│   │   ├── Models/             # Eloquent models
│   │   └── DTOs/               # Data transfer objects
│   ├── database/
│   │   ├── migrations/         # Database schema
│   │   └── seeders/            # Sample data
│   └── routes/api.php          # API routes
│
├── mobile/                     # React Native App
│   ├── src/
│   │   ├── features/           # Feature modules
│   │   ├── services/           # API, GPS, Storage, Sync
│   │   ├── stores/             # State management
│   │   ├── components/         # UI components
│   │   ├── navigation/         # App navigation
│   │   ├── utils/              # Helpers
│   │   └── i18n/               # Translations
│   └── app.json                # Expo configuration
│
└── documents/                  # Documentation
    ├── getting-started.md      # This file
    ├── architecture.md         # System architecture
    ├── api-reference.md        # API documentation
    ├── database-schema.md      # Database design
    └── deployment.md           # Production deployment
```

## Ready to Code! 🚀

You now have:
- ✅ Backend API running locally
- ✅ Mobile app in development mode
- ✅ Database configured with sample data
- ✅ Understanding of project structure
- ✅ Development tools ready

**Start building amazing features for the agricultural community!** 🌾

---

**Next**: Read the [Architecture Guide](architecture.md) to understand the system design.
