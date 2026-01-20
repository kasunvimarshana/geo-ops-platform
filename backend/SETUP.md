# Laravel 11 Backend Setup Guide

## ✓ What's Already Done

The complete Laravel 11 application structure has been created with all essential files:

### Core Framework Files

- ✓ `artisan` - CLI entry point
- ✓ `bootstrap/app.php` - Application bootstrap
- ✓ `public/index.php` - Web entry point
- ✓ `phpunit.xml` - Test configuration

### Application Structure

- ✓ `app/Http/Kernel.php` - HTTP kernel with middleware configuration
- ✓ `app/Console/Kernel.php` - Console kernel
- ✓ `app/Exceptions/Handler.php` - Exception handler
- ✓ `app/Providers/AppServiceProvider.php` - Service provider
- ✓ 7 HTTP Middleware files in `app/Http/Middleware/`
- ✓ 16 Configuration files in `config/`

### Testing Setup

- ✓ `tests/TestCase.php` - Base test case
- ✓ `tests/Feature/ExampleTest.php` - Example feature test
- ✓ `tests/Unit/ExampleTest.php` - Example unit test
- ✓ PHPUnit configuration in `phpunit.xml`

### Storage Directories

- ✓ `storage/app/` - File storage
- ✓ `storage/framework/cache/` - Application cache
- ✓ `storage/framework/sessions/` - Session storage
- ✓ `storage/framework/views/` - Compiled views
- ✓ `storage/logs/` - Application logs

### Preserved Existing Files

- ✓ `app/Models/` (User, Organization, LandMeasurement)
- ✓ `app/Http/Controllers/Api/AuthController.php`
- ✓ `database/migrations/` (all 7 migrations)
- ✓ `routes/api.php`
- ✓ `composer.json` (with Laravel 11, JWT, Sanctum, etc.)
- ✓ `.env.example`

## 📋 Next Steps to Complete Setup

### 1. Install PHP Dependencies

```bash
cd backend
composer install
```

### 2. Create Environment File

```bash
cp .env.example .env
```

### 3. Generate Application Encryption Key

```bash
php artisan key:generate
```

### 4. Configure Database Connection

Edit `.env` file and set your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Generate JWT Secret Key

```bash
php artisan jwt:secret
```

This will generate and add `JWT_SECRET` to your `.env` file.

### 6. Create Database and Run Migrations

```bash
php artisan migrate
```

### 7. (Optional) Seed Database with Sample Data

```bash
php artisan db:seed
```

### 8. Start Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## 🔧 Available Commands

### Development

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Create database backup
php artisan migrate:refresh

# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# View all routes
php artisan route:list
```

### Testing

```bash
# Run all tests
php artisan test

# Run feature tests only
php artisan test --filter Feature

# Run unit tests only
php artisan test --filter Unit

# Run with coverage report
php artisan test --coverage
```

### Tinker (Interactive Shell)

```bash
# Start interactive shell for testing code
php artisan tinker

# Example: In tinker shell
> $users = App\Models\User::all();
> $users->count();
```

### Code Quality

```bash
# Format code with Pint
php artisan pint

# Analyze code
php artisan tinker  # For quick checks
```

## 📁 Project Structure

```
backend/
├── app/
│   ├── Console/Kernel.php
│   ├── Exceptions/Handler.php
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   ├── Resources/
│   │   └── Kernel.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Organization.php
│   │   └── LandMeasurement.php
│   └── Providers/
├── bootstrap/
│   └── app.php
├── config/                    # 16 configuration files
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/             # Database seeders
├── public/
│   └── index.php
├── resources/
│   ├── views/
│   └── lang/
├── routes/
│   ├── api.php
│   └── console.php
├── storage/                   # File storage & logs
├── tests/
│   ├── Feature/
│   └── Unit/
├── artisan
├── composer.json
├── phpunit.xml
└── .env.example
```

## 🔐 Environment Variables

Key environment variables in `.env`:

```
# Application
APP_NAME="GeoOps API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Colombo
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=root
DB_PASSWORD=

# Cache & Queue
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# JWT
JWT_SECRET=
JWT_TTL=60
JWT_ALGO=HS256

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@geo-ops.lk"

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

## 🗄️ Database Connection Options

The application supports multiple databases:

### MySQL (Default)

```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=root
DB_PASSWORD=
```

### SQLite (For Testing/Development)

```php
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### PostgreSQL

```php
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=geo-ops
DB_USERNAME=postgres
DB_PASSWORD=
```

## 🎯 Key Features Configured

✓ **Authentication**

- JWT Token Authentication (tymon/jwt-auth)
- Sanctum API Tokens
- Session-based Authentication

✓ **Database**

- MySQL, SQLite, PostgreSQL support
- Eloquent ORM
- Query Builder
- Migrations & Seeders

✓ **Caching**

- Redis
- Memcached
- File-based
- Array (testing)

✓ **File Storage**

- Local filesystem
- AWS S3 support
- Public/Private storage

✓ **Queues**

- Redis queues
- Database queues
- Sync (testing)

✓ **Testing**

- PHPUnit test runner
- Feature & Unit tests
- Test database (SQLite in-memory)

✓ **API Features**

- CORS support
- Request validation
- API resources
- Rate limiting
- Error handling

## 🚀 Development Workflow

1. **Feature Development**
   - Create controllers in `app/Http/Controllers/`
   - Create routes in `routes/api.php`
   - Create models in `app/Models/`
   - Create migrations for new tables

2. **Testing**
   - Write tests in `tests/Feature/` and `tests/Unit/`
   - Run tests with `php artisan test`

3. **API Resources**
   - Create API resources in `app/Http/Resources/`
   - Transform database models to JSON

4. **Request Validation**
   - Create form requests in `app/Http/Requests/`
   - Validate incoming data

## 📖 Documentation

For detailed Laravel 11 documentation:

- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Eloquent ORM](https://laravel.com/docs/11.x/eloquent)
- [API Resources](https://laravel.com/docs/11.x/eloquent-resources)
- [Testing](https://laravel.com/docs/11.x/testing)

For JWT Authentication:

- [tymon/jwt-auth Documentation](https://jwt-auth.readthedocs.io/)

For Sanctum API:

- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)

## 🆘 Troubleshooting

### Composer install errors

```bash
# Clear composer cache
composer clear-cache

# Update composer
composer self-update

# Retry install
composer install
```

### Permission issues

```bash
# Set proper permissions
chmod -R 775 storage bootstrap/cache
```

### JWT not working

```bash
# Generate JWT secret (if missing)
php artisan jwt:secret

# Check JWT configuration
php artisan config:show jwt
```

### Database connection issues

```bash
# Check .env file
cat .env

# Test database connection
php artisan tinker
> DB::connection()->getPdo();
```

## ✅ Verification Checklist

Before starting development, verify:

- [ ] `composer install` completed successfully
- [ ] `.env` file created and configured
- [ ] `APP_KEY` generated
- [ ] Database connection tested
- [ ] Migrations run successfully with `php artisan migrate`
- [ ] JWT secret generated with `php artisan jwt:secret`
- [ ] `php artisan serve` starts without errors
- [ ] API is accessible at `http://localhost:8000`
- [ ] Tests run successfully with `php artisan test`

## 📞 Support

For issues or questions:

1. Check Laravel documentation
2. Review existing models and migrations
3. Check application logs in `storage/logs/`
4. Run `php artisan tinker` to debug issues

---

**Status**: ✓ Complete Laravel 11 structure created
**Ready for**: Development and testing
**Last Updated**: January 2024
