# Development Setup Guide

## GPS Field Management Platform - Quick Start for Developers

**Version**: 1.0.0  
**Target Audience**: Backend & Mobile Developers  
**Prerequisites**: Basic knowledge of Laravel and React Native

---

## 📋 Table of Contents

1. [System Requirements](#system-requirements)
2. [Backend Setup](#backend-setup)
3. [Mobile Setup](#mobile-setup)
4. [Database Configuration](#database-configuration)
5. [Environment Variables](#environment-variables)
6. [Running the Application](#running-the-application)
7. [Development Tools](#development-tools)
8. [Troubleshooting](#troubleshooting)

---

## 🖥️ System Requirements

### For Backend Development

- **OS**: macOS, Linux (Ubuntu 22.04+), or Windows with WSL2
- **PHP**: 8.3 or higher
- **Composer**: 2.6+
- **MySQL**: 8.0+ or **PostgreSQL**: 15+
- **Redis**: 6.0+
- **Node.js**: 18+ (for asset compilation)
- **Git**: Latest version

### For Mobile Development

- **OS**: macOS (for iOS), Windows/Linux/macOS (for Android)
- **Node.js**: 18+
- **npm** or **yarn**: Latest version
- **Expo CLI**: Globally installed
- **iOS**: Xcode 14+ (macOS only)
- **Android**: Android Studio with Android SDK
- **Git**: Latest version

### Recommended Hardware

- **RAM**: 8GB minimum, 16GB recommended
- **Disk Space**: 10GB+ free
- **CPU**: Multi-core processor

---

## 🔧 Backend Setup

### 1. Install PHP 8.3

#### macOS (using Homebrew)

```bash
brew update
brew install php@8.3
brew link php@8.3
```

#### Ubuntu/Debian

```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.3 php8.3-cli php8.3-fpm php8.3-mysql \
    php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip \
    php8.3-gd php8.3-bcmath php8.3-redis php8.3-intl
```

#### Windows (using Laragon or XAMPP)

- Download Laragon: https://laragon.org/download/
- Or XAMPP: https://www.apachefriends.org/
- Ensure PHP 8.3+ is included

Verify installation:

```bash
php --version
# Should show: PHP 8.3.x
```

---

### 2. Install Composer

#### macOS/Linux

```bash
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
```

#### Windows

Download installer: https://getcomposer.org/Composer-Setup.exe

Verify installation:

```bash
composer --version
# Should show: Composer version 2.x.x
```

---

### 3. Install MySQL

#### macOS

```bash
brew install mysql
brew services start mysql
mysql_secure_installation
```

#### Ubuntu/Debian

```bash
sudo apt install mysql-server
sudo systemctl start mysql
sudo mysql_secure_installation
```

#### Windows

Download installer: https://dev.mysql.com/downloads/installer/

Verify installation:

```bash
mysql --version
# Should show: mysql Ver 8.0.x
```

---

### 4. Install Redis

#### macOS

```bash
brew install redis
brew services start redis
```

#### Ubuntu/Debian

```bash
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

#### Windows

Download Redis for Windows: https://github.com/microsoftarchive/redis/releases

Verify installation:

```bash
redis-cli ping
# Should return: PONG
```

---

### 5. Clone Repository

```bash
git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform
```

---

### 6. Backend Installation

```bash
cd backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret
```

---

### 7. Configure Environment

Edit `backend/.env`:

```env
# Application
APP_NAME="GeoOps Platform"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Colombo

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops_manager
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Mail (use Mailtrap for development)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls

# AWS S3 (optional, for file uploads)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

# API Keys
GOOGLE_MAPS_API_KEY=your_key_here
```

---

### 8. Database Setup

#### Create Database

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE geo-ops_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

#### Run Migrations

```bash
cd backend
php artisan migrate
```

#### Seed Database (when seeders are ready)

```bash
php artisan db:seed
```

---

### 9. Start Backend Server

```bash
cd backend
php artisan serve
```

Backend will be available at: http://localhost:8000

To run queue worker (in separate terminal):

```bash
php artisan queue:work
```

---

## 📱 Mobile Setup

### 1. Install Node.js

#### macOS

```bash
brew install node
```

#### Ubuntu/Debian

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

#### Windows

Download installer: https://nodejs.org/

Verify installation:

```bash
node --version
npm --version
# Should show v20.x.x and 10.x.x
```

---

### 2. Install Expo CLI

```bash
npm install -g expo-cli
# Or using npx (no global install needed)
npx expo --version
```

---

### 3. Mobile Installation

```bash
cd mobile

# Install dependencies
npm install
# or
yarn install
```

---

### 4. Configure Mobile Environment

Create `mobile/.env`:

```env
API_URL=http://localhost:8000/api/v1
GOOGLE_MAPS_API_KEY=your_android_key
GOOGLE_MAPS_API_KEY_IOS=your_ios_key
```

For local development on physical device, use your computer's IP:

```env
API_URL=http://192.168.1.100:8000/api/v1
```

To find your IP:

- macOS/Linux: `ifconfig | grep inet`
- Windows: `ipconfig`

---

### 5. Start Mobile App

```bash
cd mobile
npx expo start
```

Options:

- Press `i` for iOS simulator (macOS only)
- Press `a` for Android emulator
- Scan QR code with Expo Go app on physical device

---

## 🗄️ Database Configuration

### MySQL Configuration

#### Option 1: GUI Tool (Recommended for Beginners)

**TablePlus** (macOS/Windows/Linux): https://tableplus.com/
**DBeaver** (Free, cross-platform): https://dbeaver.io/

Connection settings:

- Host: 127.0.0.1
- Port: 3306
- Database: geo-ops_manager
- Username: root
- Password: your_password

#### Option 2: Command Line

```bash
# Connect to MySQL
mysql -u root -p

# Show databases
SHOW DATABASES;

# Use database
USE geo-ops_manager;

# Show tables
SHOW TABLES;

# Describe table
DESCRIBE users;
```

---

### PostgreSQL Configuration (Alternative)

#### Install PostgreSQL

**macOS:**

```bash
brew install postgresql@15
brew services start postgresql@15
```

**Ubuntu:**

```bash
sudo apt install postgresql postgresql-contrib
sudo systemctl start postgresql
```

#### Create Database

```bash
# Switch to postgres user
sudo -u postgres psql

# Create database
CREATE DATABASE geo-ops_manager;

# Create user
CREATE USER geo-ops_user WITH PASSWORD 'secure_password';

# Grant privileges
GRANT ALL PRIVILEGES ON DATABASE geo-ops_manager TO geo-ops_user;

# Enable PostGIS for spatial data
\c geo-ops_manager
CREATE EXTENSION IF NOT EXISTS postgis;
```

Update `backend/.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=geo-ops_manager
DB_USERNAME=geo-ops_user
DB_PASSWORD=secure_password
```

---

## 🔐 Environment Variables

### Backend (.env)

**Critical Variables:**

```env
APP_KEY=                    # Auto-generated by artisan key:generate
JWT_SECRET=                 # Auto-generated by artisan jwt:secret
DB_PASSWORD=                # Your database password
REDIS_PASSWORD=             # Usually null for local
```

**Optional but Recommended:**

```env
MAIL_HOST=smtp.mailtrap.io  # For testing emails
MAIL_USERNAME=              # Mailtrap credentials
MAIL_PASSWORD=              # Mailtrap credentials
GOOGLE_MAPS_API_KEY=        # For spatial features
```

### Mobile (.env)

```env
API_URL=http://localhost:8000/api/v1
GOOGLE_MAPS_API_KEY=your_key
```

---

## ▶️ Running the Application

### Full Stack Development

**Terminal 1 - Backend API:**

```bash
cd backend
php artisan serve
```

**Terminal 2 - Queue Worker:**

```bash
cd backend
php artisan queue:work
```

**Terminal 3 - Mobile App:**

```bash
cd mobile
npx expo start
```

**Terminal 4 - Logs (Optional):**

```bash
cd backend
tail -f storage/logs/laravel.log
```

---

## 🛠️ Development Tools

### Backend Tools

#### Laravel Telescope (Development Debugger)

```bash
cd backend
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Access at: http://localhost:8000/telescope

#### Laravel Tinker (REPL)

```bash
php artisan tinker
```

#### Code Quality

```bash
# PHP CodeSniffer
composer require --dev squizlabs/php_codesniffer
./vendor/bin/phpcs

# PHPStan
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse
```

---

### Mobile Tools

#### React Native Debugger

```bash
brew install --cask react-native-debugger
# or download from: https://github.com/jhen0409/react-native-debugger
```

#### Expo DevTools

Automatically opens when running `expo start`

#### TypeScript Checking

```bash
cd mobile
npm run tsc
```

---

### API Testing

#### Postman

- Download: https://www.postman.com/downloads/
- Import API collection from `docs/API_DOCUMENTATION.md`

#### Insomnia

- Download: https://insomnia.rest/download
- Lightweight alternative to Postman

#### HTTPie (Command Line)

```bash
brew install httpie

# Example request
http GET http://localhost:8000/api/v1/health
```

---

### Database Tools

#### TablePlus (Recommended)

- Download: https://tableplus.com/
- Beautiful UI, supports MySQL, PostgreSQL, Redis

#### DBeaver (Free)

- Download: https://dbeaver.io/
- Open source, feature-rich

#### MySQL Workbench

- Download: https://dev.mysql.com/downloads/workbench/

---

## 🐛 Troubleshooting

### Backend Issues

#### Issue: "Could not find driver" (MySQL)

```bash
# Check PHP modules
php -m | grep pdo_mysql

# If missing, install:
# Ubuntu
sudo apt install php8.3-mysql
# macOS
brew reinstall php@8.3
```

#### Issue: "Redis connection refused"

```bash
# Check if Redis is running
redis-cli ping

# If not running, start it:
# macOS
brew services start redis
# Ubuntu
sudo systemctl start redis-server
```

#### Issue: Storage permission denied

```bash
cd backend
chmod -R 775 storage bootstrap/cache
```

#### Issue: "Class 'JWT' not found"

```bash
cd backend
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

---

### Mobile Issues

#### Issue: Metro bundler cache errors

```bash
cd mobile
npx expo start --clear
```

#### Issue: "Unable to resolve module"

```bash
cd mobile
rm -rf node_modules
npm install
# or
yarn install
```

#### Issue: iOS build fails

```bash
cd mobile/ios
pod install
cd ..
npx expo start
```

#### Issue: Android build fails

```bash
cd mobile/android
./gradlew clean
cd ..
npx expo start
```

---

### Database Issues

#### Issue: "Access denied for user"

```bash
# Reset MySQL password
mysql -u root

ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
FLUSH PRIVILEGES;
```

#### Issue: "Can't connect to MySQL server"

```bash
# Check MySQL status
# macOS
brew services list
# Ubuntu
sudo systemctl status mysql

# Start if not running
brew services start mysql  # macOS
sudo systemctl start mysql  # Ubuntu
```

#### Issue: Migrations fail

```bash
# Reset database (⚠️ WARNING: Destroys data)
php artisan migrate:fresh

# Or rollback and re-run
php artisan migrate:rollback
php artisan migrate
```

---

## 📚 Additional Resources

### Documentation

- **Project Docs**: `/docs` directory
- **Laravel Docs**: https://laravel.com/docs
- **React Native Docs**: https://reactnative.dev/
- **Expo Docs**: https://docs.expo.dev/

### Community

- **Stack Overflow**: Tag questions with `laravel`, `react-native`, `expo`
- **Laravel Discord**: https://discord.gg/laravel
- **React Native Community**: https://www.reactiflux.com/

### Learning

- **Laravel**: Laracasts.com
- **React Native**: React Native School
- **Clean Architecture**: https://blog.cleancoder.com/

---

## ✅ Checklist for New Developers

- [ ] PHP 8.3+ installed
- [ ] Composer installed
- [ ] MySQL/PostgreSQL installed and running
- [ ] Redis installed and running
- [ ] Node.js 18+ installed
- [ ] Repository cloned
- [ ] Backend dependencies installed
- [ ] Backend `.env` configured
- [ ] Database created and migrated
- [ ] Backend server starts successfully
- [ ] Mobile dependencies installed
- [ ] Mobile `.env` configured
- [ ] Mobile app starts successfully
- [ ] API accessible from mobile app
- [ ] Development tools installed (optional)

---

## 🎯 Quick Commands Reference

### Backend

```bash
php artisan serve              # Start server
php artisan migrate            # Run migrations
php artisan db:seed            # Seed database
php artisan queue:work         # Start queue worker
php artisan tinker             # REPL
php artisan route:list         # List all routes
php artisan cache:clear        # Clear cache
php artisan config:clear       # Clear config cache
```

### Mobile

```bash
npx expo start                 # Start dev server
npx expo start --ios           # Start on iOS
npx expo start --android       # Start on Android
npx expo start --clear         # Clear cache
npm test                       # Run tests
npm run tsc                    # Type check
```

### Database

```bash
mysql -u root -p                      # Connect to MySQL
php artisan migrate:fresh --seed      # Reset and seed
php artisan db:show                   # Show database info
php artisan db:table users            # Show table info
```

---

**You're now ready to start developing! 🚀**

Refer to `docs/ARCHITECTURE.md` for system design and `docs/API_DOCUMENTATION.md` for API specifications.

---

_Last Updated: January 17, 2026_
