# GeoOps Platform - Setup Guide

## Quick Start Guide

This guide will help you set up the GeoOps Platform for development.

## Prerequisites

Before you begin, ensure you have the following installed:

### Backend Requirements

- **PHP 8.2+**: [Download PHP](https://www.php.net/downloads)
- **Composer 2.x**: [Download Composer](https://getcomposer.org/download/)
- **MySQL 8.0+** or **PostgreSQL 14+**: [MySQL](https://dev.mysql.com/downloads/) | [PostgreSQL](https://www.postgresql.org/download/)
- **Redis**: [Download Redis](https://redis.io/download)
- **Node.js 18+**: [Download Node.js](https://nodejs.org/)

### Frontend Requirements

- **Node.js 18+** and npm
- **Expo CLI**: `npm install -g expo-cli`
- **Git**: [Download Git](https://git-scm.com/downloads)

### Optional

- **Docker** and **Docker Compose** for containerized development

## Step 1: Clone the Repository

```bash
git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform
```

## Step 2: Backend Setup

### 2.1 Install Dependencies

```bash
cd backend
composer install
```

### 2.2 Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` file with your configuration:

```env
APP_NAME="GeoOps API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=root
DB_PASSWORD=your_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 2.3 Create Database

For MySQL:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE geo-ops CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

For PostgreSQL:

```bash
psql -U postgres
```

```sql
CREATE DATABASE geo-ops;
CREATE EXTENSION postgis; -- For spatial data support
\q
```

### 2.4 Generate Application Keys

```bash
php artisan key:generate
php artisan jwt:secret
```

### 2.5 Run Database Migrations

```bash
php artisan migrate
```

### 2.6 (Optional) Seed Database

```bash
php artisan db:seed
```

### 2.7 Create Storage Link

```bash
php artisan storage:link
```

### 2.8 Start Development Server

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Queue Worker
php artisan queue:work

# Terminal 3: (Optional) Start Scheduler
php artisan schedule:work
```

The API will be available at: `http://localhost:8000/api`

## Step 3: Frontend Setup

### 3.1 Install Dependencies

```bash
cd ../frontend
npm install
```

### 3.2 Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` file:

```env
EXPO_PUBLIC_API_URL=http://localhost:8000/api
EXPO_PUBLIC_GOOGLE_MAPS_API_KEY=your_google_maps_api_key
EXPO_PUBLIC_APP_ENV=development
```

**Note**: For Android emulator, use `http://10.0.2.2:8000/api` instead of `localhost`

### 3.3 Get Google Maps API Key

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable "Maps SDK for Android" and "Maps SDK for iOS"
4. Create credentials (API Key)
5. Add the API key to your `.env` file

### 3.4 Start Expo Development Server

```bash
npm start
```

This will open Expo Dev Tools in your browser.

### 3.5 Run on Device/Emulator

**Option 1: Physical Device**

1. Install "Expo Go" app from Play Store (Android) or App Store (iOS)
2. Scan QR code from terminal/browser
3. Ensure device is on same network as development machine

**Option 2: iOS Simulator (macOS only)**

```bash
npm run ios
```

**Option 3: Android Emulator**

1. Install Android Studio and set up an emulator
2. Start the emulator
3. Run:

```bash
npm run android
```

**Option 4: Web Browser**

```bash
npm run web
```

## Step 4: Verify Installation

### Test Backend API

```bash
# Test health endpoint
curl http://localhost:8000/api/health

# Expected response:
# {"status":"healthy","timestamp":"2024-01-15T10:30:00.000000Z"}
```

### Test Mobile App

1. Open the app on your device/emulator
2. You should see the login screen
3. Try creating a new account

## Step 5: Sample Data (Optional)

To populate the database with sample data for testing:

```bash
cd backend
php artisan db:seed --class=DatabaseSeeder
```

This will create:

- Sample organizations
- Sample users (admin, owners, drivers)
- Sample customers
- Sample land measurements
- Sample jobs and invoices

## Development Workflow

### Backend Development

1. **Create a new feature**:

   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Create migrations**:

   ```bash
   php artisan make:migration create_your_table
   ```

3. **Create models**:

   ```bash
   php artisan make:model YourModel
   ```

4. **Create controllers**:

   ```bash
   php artisan make:controller Api/YourController --api
   ```

5. **Run tests**:
   ```bash
   php artisan test
   ```

### Frontend Development

1. **Project structure**: Follow feature-based architecture

   ```
   src/features/your-feature/
   ├── hooks/
   ├── screens/
   ├── components/
   └── types.ts
   ```

2. **Add new screen**: Create in `app/(tabs)/` or `app/(auth)/`

3. **State management**: Use Zustand stores in `src/store/`

4. **API calls**: Add to `src/services/api/`

5. **Run tests**:
   ```bash
   npm test
   ```

## Troubleshooting

### Common Backend Issues

**Database connection error**:

```bash
# Check MySQL is running
sudo systemctl status mysql

# Check Redis is running
redis-cli ping
# Should return: PONG
```

**Permission errors**:

```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

**Composer memory limit**:

```bash
php -d memory_limit=-1 /usr/local/bin/composer install
```

### Common Frontend Issues

**Metro bundler cache issues**:

```bash
expo start -c
```

**Node modules issues**:

```bash
rm -rf node_modules
npm install
```

**iOS CocoaPods issues**:

```bash
cd ios
pod install
cd ..
```

**Android build issues**:

```bash
cd android
./gradlew clean
cd ..
```

## Environment-Specific Configuration

### Development

- Debug mode enabled
- Detailed error messages
- Local database
- Local file storage

### Staging

- Debug mode disabled
- Error logging
- Staging database
- Cloud storage (optional)

### Production

- Debug mode disabled
- Error logging to external service
- Production database with replicas
- Cloud storage (S3/similar)
- HTTPS enforced
- Rate limiting enabled
- Caching enabled

## Docker Setup (Alternative)

If you prefer Docker, create `docker-compose.yml`:

```yaml
version: "3.8"
services:
  app:
    build: ./backend
    ports:
      - "8000:8000"
    environment:
      - DB_HOST=db
      - REDIS_HOST=redis
    depends_on:
      - db
      - redis

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: geo-ops
      MYSQL_ROOT_PASSWORD: secret
    ports:
      - "3306:3306"

  redis:
    image: redis:alpine
    ports:
      - "6379:6379"
```

Run with:

```bash
docker-compose up -d
```

## Next Steps

1. **Read the documentation**:
   - [Architecture Overview](./ARCHITECTURE.md)
   - [API Specification](./API_SPECIFICATION.md)
   - [Database Schema](./DATABASE_SCHEMA.md)
   - [Deployment Guide](./DEPLOYMENT.md)

2. **Explore the code**:
   - Backend: `backend/app/`
   - Frontend: `frontend/src/`

3. **Run the tests**:
   - Backend: `php artisan test`
   - Frontend: `npm test`

4. **Start developing**:
   - Pick a feature from the roadmap
   - Create a branch
   - Implement the feature
   - Write tests
   - Submit a pull request

## Support

For issues and questions:

- Email: dev@geo-ops.lk
- GitHub Issues: [Create an issue](https://github.com/kasunvimarshana/geo-ops-platform/issues)

## License

Proprietary - All rights reserved
