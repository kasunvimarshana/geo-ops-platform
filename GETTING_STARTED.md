# Getting Started Guide

## Welcome to Geo Ops Platform

This guide will help you get started with the GPS Land Measurement & Agricultural Field-Service Management Platform.

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

### For Backend Development
- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0+ or PostgreSQL 14+ (with PostGIS extension recommended)
- Redis 6.0+
- Node.js 18+ (for asset compilation)
- Git

### For Frontend Development
- Node.js 18+ and npm
- Expo CLI (`npm install -g expo-cli`)
- iOS Simulator (Mac) or Android Studio (for Android development)
- Expo Go app on your phone (for testing)

## 🚀 Quick Start

### Backend Setup (5 minutes)

1. **Install Laravel (if starting from scratch)**
```bash
cd backend
composer create-project --prefer-dist laravel/laravel .
```

2. **Copy environment file**
```bash
cp .env.example .env
```

3. **Configure database in .env**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geoops
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

4. **Install JWT package**
```bash
composer require tymon/jwt-auth
```

5. **Generate keys**
```bash
php artisan key:generate
php artisan jwt:secret
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Seed database**
```bash
php artisan db:seed
```

8. **Start development server**
```bash
php artisan serve
```

Your API will be available at `http://localhost:8000`

### Frontend Setup (5 minutes)

1. **Initialize Expo project (if starting from scratch)**
```bash
cd frontend
npx create-expo-app . --template blank-typescript
```

2. **Install dependencies**
```bash
npm install
# or
yarn install
```

3. **Install required packages**
```bash
npx expo install expo-location expo-secure-store @react-navigation/native
npm install zustand axios react-native-maps
```

4. **Copy environment file**
```bash
cp .env.example .env
```

5. **Configure API URL in .env**
```env
EXPO_PUBLIC_API_URL=http://localhost:8000/api/v1
```

6. **Start Expo development server**
```bash
npx expo start
```

7. **Run on device/simulator**
- Press `i` for iOS simulator
- Press `a` for Android emulator
- Scan QR code with Expo Go app on your phone

## 📁 Project Structure Overview

### Backend Structure
```
backend/
├── app/
│   ├── Http/Controllers/Api/V1/  # API controllers
│   ├── Services/                  # Business logic
│   ├── Repositories/              # Data access
│   ├── Models/                    # Eloquent models
│   ├── Http/Middleware/           # Middleware
│   └── Http/Requests/             # Validation
├── database/
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Data seeders
├── routes/
│   └── api.php                    # API routes
└── .env                           # Environment config
```

### Frontend Structure
```
frontend/
├── src/
│   ├── api/                       # API clients
│   ├── stores/                    # State management
│   ├── features/                  # Feature modules
│   │   ├── auth/
│   │   ├── measurement/
│   │   ├── jobs/
│   │   └── billing/
│   ├── components/                # Reusable components
│   ├── navigation/                # Navigation
│   └── services/                  # Business services
└── .env                           # Environment config
```

## 🎯 First Steps After Setup

### 1. Test the Backend API

```bash
# Register a user
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+94771234567",
    "password": "password123",
    "password_confirmation": "password123",
    "organization_name": "Johns Farm"
  }'

# Login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### 2. Test the Mobile App

1. Open the app in Expo Go
2. You should see the login screen
3. Use the credentials you created to log in
4. Navigate through the app features

### 3. Create Your First Measurement

#### Via API:
```bash
curl -X POST http://localhost:8000/api/v1/measurements \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Farmer John",
    "customer_phone": "+94771111111",
    "location_name": "Paddy Field A",
    "measurement_method": "walk_around",
    "measurement_date": "2024-01-15T08:30:00Z",
    "polygon_points": [
      {"latitude": 7.8731, "longitude": 80.7718, "timestamp": "2024-01-15T08:30:00Z"},
      {"latitude": 7.8735, "longitude": 80.7720, "timestamp": "2024-01-15T08:31:00Z"},
      {"latitude": 7.8733, "longitude": 80.7722, "timestamp": "2024-01-15T08:32:00Z"}
    ]
  }'
```

#### Via Mobile App:
1. Tap the "+" button on the Measurement screen
2. Enter customer details
3. Choose "Walk Around" or "Point Based" method
4. Allow location permissions
5. Start tracking and walk around the land boundary
6. Save the measurement

## 📚 Learning Resources

### Understanding the Architecture

1. **Read the Architecture Document** - [ARCHITECTURE.md](./ARCHITECTURE.md)
   - Understand the system design
   - Learn about Clean Architecture layers
   - See how components interact

2. **Review Database Schema** - [DATABASE.md](./DATABASE.md)
   - Understand data relationships
   - See the complete ERD
   - Learn about indexes and optimization

3. **Explore API Endpoints** - [API.md](./API.md)
   - Full API documentation
   - Request/response examples
   - Authentication flow

### Code Examples

1. **Backend Examples** - `backend/examples/`
   - Controllers (thin, delegating to services)
   - Services (business logic)
   - Repositories (data access)
   - Models (with relationships)
   - Middleware (authentication, authorization)

2. **Frontend Examples** - `frontend/examples/`
   - API clients
   - State management stores
   - Screens with proper patterns
   - Custom hooks
   - GPS tracking implementation

### Key Concepts to Understand

#### Clean Architecture
- **Controllers** only handle HTTP concerns
- **Services** contain all business logic
- **Repositories** abstract data access
- Clear separation of concerns

#### Offline-First Architecture
- Data stored locally in SQLite
- Changes queued for sync
- Background synchronization
- Conflict resolution strategies

#### Multi-Tenancy
- Organization-level data isolation
- Global scopes on models
- Middleware enforces boundaries

#### Subscription Management
- Package-based feature access
- Usage limit enforcement
- Automatic restriction handling

## 🛠️ Development Workflow

### Backend Development

1. **Create a new feature**
   - Add migration: `php artisan make:migration create_xxx_table`
   - Create model: `php artisan make:model Xxx`
   - Create repository interface and implementation
   - Create service class
   - Create controller
   - Add routes
   - Create form requests for validation
   - Write tests

2. **Run tests**
```bash
php artisan test
```

3. **Check code style**
```bash
./vendor/bin/phpstan analyze
./vendor/bin/php-cs-fixer fix
```

### Frontend Development

1. **Create a new feature**
   - Create feature directory in `src/features/`
   - Add screens
   - Create components
   - Add API methods
   - Create store
   - Add to navigation
   - Write tests

2. **Run tests**
```bash
npm test
```

3. **Check types**
```bash
npx tsc --noEmit
```

4. **Lint code**
```bash
npm run lint
```

## 🔧 Common Tasks

### Adding a New API Endpoint

1. **Create controller method**
```php
public function store(CreateRequest $request): JsonResponse
{
    $result = $this->service->create($request->validated());
    return $this->successResponse(data: $result, statusCode: 201);
}
```

2. **Add route**
```php
Route::post('/items', [ItemController::class, 'store']);
```

3. **Create form request**
```bash
php artisan make:request CreateItemRequest
```

### Adding a New Mobile Screen

1. **Create screen component**
```typescript
export const NewScreen: React.FC = () => {
  // Screen logic
  return <View>...</View>;
};
```

2. **Add to navigation**
```typescript
<Stack.Screen name="NewScreen" component={NewScreen} />
```

3. **Create store if needed**
```typescript
export const useNewStore = create<NewState>((set) => ({
  // State and actions
}));
```

### Running Database Migrations

```bash
# Run all pending migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Reset and re-run all migrations
php artisan migrate:fresh

# Reset and seed
php artisan migrate:fresh --seed
```

## 🐛 Troubleshooting

### Backend Issues

**Problem:** "Class not found" error
```bash
# Solution: Clear and rebuild autoload
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
php artisan cache:clear
```

**Problem:** Database connection error
```bash
# Solution: Check .env database credentials
# Test connection:
php artisan tinker
>>> DB::connection()->getPdo();
```

**Problem:** JWT token error
```bash
# Solution: Generate new JWT secret
php artisan jwt:secret
```

### Frontend Issues

**Problem:** "Unable to resolve module" error
```bash
# Solution: Clear cache and reinstall
rm -rf node_modules
npm install
npx expo start -c
```

**Problem:** Location permissions not working
```bash
# Solution: Reinstall expo-location
npx expo install expo-location
```

**Problem:** API connection refused
```bash
# Solution: Check API URL in .env
# Make sure backend server is running
# Use your computer's local IP instead of localhost for physical device
```

## 📞 Getting Help

### Documentation
- [Architecture](./ARCHITECTURE.md) - System design
- [Database](./DATABASE.md) - Schema and ERD
- [API](./API.md) - API documentation
- [Deployment](./DEPLOYMENT.md) - Production setup

### Code Examples
- `backend/examples/` - Backend patterns
- `frontend/examples/` - Frontend patterns

### Community
- Create an issue on GitHub
- Check existing issues for solutions
- Review pull requests for examples

## 🎓 Next Steps

1. ✅ Complete the quick start setup
2. 📖 Read the architecture documentation
3. 💻 Review code examples
4. 🔨 Build a simple feature end-to-end
5. 📱 Test on physical device
6. 🚀 Deploy to staging environment
7. 🎯 Build your specific features
8. ✨ Launch to production

## 🔐 Security Checklist

Before deploying to production:

- [ ] Change all default passwords
- [ ] Use strong JWT secret
- [ ] Enable HTTPS/SSL
- [ ] Configure CORS properly
- [ ] Set up rate limiting
- [ ] Enable API authentication on all routes
- [ ] Review file upload restrictions
- [ ] Set up database backups
- [ ] Configure logging and monitoring
- [ ] Review subscription limits
- [ ] Test offline sync thoroughly
- [ ] Validate all user inputs
- [ ] Set up error tracking (Sentry)

## 📊 Performance Checklist

- [ ] Database indexes created
- [ ] Redis cache configured
- [ ] Queue workers running
- [ ] Image optimization enabled
- [ ] API response caching
- [ ] Frontend code splitting
- [ ] Map marker clustering
- [ ] GPS tracking optimized
- [ ] Bundle size optimized
- [ ] Lazy loading implemented

---

**Ready to build?** Start with the quick start guide above and refer to the comprehensive documentation as you progress. Happy coding! 🚀
