# Quick Start Guide

**GPS Field Management Platform - Get Running in 10 Minutes**

This guide will help you set up and run both the backend API and mobile app locally.

---

## 🎯 What You'll Get

By following this guide, you'll have:

- ✅ Laravel backend API running on `http://localhost:8000`
- ✅ React Native mobile app running on your device/simulator
- ✅ Sample data (admin user, packages)
- ✅ Fully functional GPS field management system

---

## 📋 Prerequisites

### Required Software

1. **PHP 8.3+** - [Download](https://www.php.net/downloads)
2. **Composer 2.x** - [Download](https://getcomposer.org/download/)
3. **Node.js 18+** - [Download](https://nodejs.org/)
4. **MySQL 8.0+** or **PostgreSQL 15+** - [MySQL](https://dev.mysql.com/downloads/) | [PostgreSQL](https://www.postgresql.org/download/)
5. **Expo CLI** - Install via npm: `npm install -g expo-cli`

### Optional (Recommended)

- **Redis 6.0+** - For queues and caching
- **iOS Simulator** (macOS only) - Via Xcode
- **Android Emulator** - Via Android Studio

### Verify Installation

```bash
php --version        # Should be 8.3 or higher
composer --version   # Should be 2.x
node --version       # Should be 18 or higher
npm --version        # Should be installed with Node
mysql --version      # Or: psql --version
```

---

## 🚀 Backend Setup (5 minutes)

### Step 1: Clone Repository

```bash
git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform/backend
```

### Step 2: Install Dependencies

```bash
composer install
```

_This will take 2-3 minutes_

### Step 3: Configure Environment

```bash
cp .env.example .env
```

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo_ops_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 4: Generate Keys

```bash
php artisan key:generate
php artisan jwt:secret
```

### Step 5: Create Database

```bash
# MySQL
mysql -u root -p -e "CREATE DATABASE geo_ops_platform;"

# Or PostgreSQL
psql -U postgres -c "CREATE DATABASE geo_ops_platform;"
```

### Step 6: Run Migrations & Seed Data

```bash
php artisan migrate --seed
```

This creates:

- 12 database tables
- 3 subscription packages (Free, Basic, Pro)
- Admin user: `admin@geo-ops.com` / `password`

### Step 7: Start Backend Server

```bash
php artisan serve
```

✅ **Backend is now running at:** `http://localhost:8000`

Test it:

```bash
curl http://localhost:8000/api/auth/login
# Should return: {"message":"The email field is required. ..."}
```

---

## 📱 Mobile Setup (5 minutes)

### Step 1: Navigate to Mobile Directory

```bash
cd ../mobile
```

_(From repository root, or: `cd geo-ops-platform/mobile`)_

### Step 2: Install Dependencies

```bash
npm install
```

_This will take 3-4 minutes_

### Step 3: Configure API URL

Edit `src/shared/constants/config.ts`:

```typescript
export const API_CONFIG = {
  BASE_URL: "http://localhost:8000/api", // For iOS simulator
  // BASE_URL: 'http://10.0.2.2:8000/api',  // For Android emulator
  // BASE_URL: 'http://YOUR_IP:8000/api',   // For physical device
  TIMEOUT: 30000,
};
```

**Important for physical devices:**

- Find your computer's IP: `ipconfig` (Windows) or `ifconfig` (Mac/Linux)
- Replace `localhost` with your IP, e.g., `http://192.168.1.100:8000/api`

### Step 4: Start Expo Development Server

```bash
npx expo start
```

You'll see a QR code and options.

### Step 5: Run the App

**Option A: iOS Simulator (macOS only)**

```bash
Press "i" in the terminal
# Or: npx expo start --ios
```

**Option B: Android Emulator**

```bash
Press "a" in the terminal
# Or: npx expo start --android
```

**Option C: Physical Device**

1. Install **Expo Go** app from App Store or Play Store
2. Scan the QR code shown in terminal
3. Make sure your device is on the same WiFi network

✅ **Mobile app is now running!**

---

## 🎉 You're Ready!

### Test the Application

#### Backend API Test

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@geo-ops.com","password":"password"}'

# You should get a JWT token in response
```

#### Mobile App Test

1. Open the app on your device/simulator
2. You'll see the Login screen
3. Login with:
   - **Email:** `admin@geo-ops.com`
   - **Password:** `password`
4. You'll be redirected to the Jobs screen
5. Try creating a job, viewing measurements, etc.

---

## 📚 Next Steps

### Explore Features

- ✅ Create field jobs
- ✅ GPS land measurements
- ✅ View job details
- ✅ Test offline functionality (turn off WiFi)
- ✅ Check background sync

### Documentation

- **System Architecture**: `docs/ARCHITECTURE.md`
- **API Reference**: `docs/API_DOCUMENTATION.md`
- **Database Schema**: `docs/DATABASE_SCHEMA.md`
- **Backend Guide**: `backend/README_BACKEND.md`
- **Mobile Guide**: `mobile/README.md`

### Development

- Backend: `backend/app/` - Explore the code
- Mobile: `mobile/src/` - Explore the code
- Tests: Add your own tests
- Features: Implement from `mobile/IMPROVEMENTS.md`

---

## 🐛 Troubleshooting

### Backend Issues

**"Database connection failed"**

```bash
# Check database is running
mysql -u root -p -e "SHOW DATABASES;"

# Verify .env credentials
cat .env | grep DB_
```

**"JWT secret not set"**

```bash
php artisan jwt:secret
```

**"Class not found"**

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

**Port 8000 already in use**

```bash
php artisan serve --port=8001
# Update mobile config to use port 8001
```

### Mobile Issues

**"Unable to connect to backend"**

- Check backend is running: `http://localhost:8000`
- For Android emulator, use `http://10.0.2.2:8000/api`
- For physical device, use your computer's IP
- Check firewall isn't blocking port 8000

**"Expo Go app crashes"**

```bash
# Clear cache and restart
npx expo start -c
```

**"Module not found"**

```bash
rm -rf node_modules
npm install
```

**"Unable to resolve module"**

```bash
npx expo start -c
# Clear Metro bundler cache
```

### Network Issues

**Backend and mobile can't communicate**

1. Find your computer's IP:

   ```bash
   # Windows
   ipconfig

   # Mac/Linux
   ifconfig | grep "inet "
   ```

2. Update mobile config:
   ```typescript
   BASE_URL: "http://YOUR_IP:8000/api";
   ```
3. Restart both backend and mobile

---

## 🔧 Advanced Setup

### Running Queue Workers (Optional)

For background jobs (PDF generation, sync processing):

```bash
php artisan queue:work
```

### Running Redis (Optional)

For caching and session management:

```bash
# Install Redis
# Mac: brew install redis
# Ubuntu: sudo apt install redis-server

# Start Redis
redis-server

# Update .env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Production Build (Mobile)

```bash
# Configure EAS Build
npx eas build:configure

# Build for Android
npx eas build --platform android --profile production

# Build for iOS
npx eas build --platform ios --profile production
```

---

## 📞 Need Help?

### Resources

- **Documentation**: Check the `/docs` folder
- **Issues**: [GitHub Issues](https://github.com/kasunvimarshana/geo-ops-platform/issues)
- **Backend README**: `backend/README_BACKEND.md`
- **Mobile README**: `mobile/README.md`

### Common Commands Reference

**Backend:**

```bash
php artisan serve              # Start server
php artisan migrate           # Run migrations
php artisan migrate:fresh --seed  # Reset & seed
php artisan route:list        # List all routes
php artisan tinker            # Interactive console
php artisan config:clear      # Clear config cache
```

**Mobile:**

```bash
npx expo start               # Start dev server
npx expo start -c            # Clear cache
npx expo start --ios         # Run on iOS
npx expo start --android     # Run on Android
npm run lint                 # Run linter
```

---

## ✅ Verification Checklist

Before you start development, verify:

- [ ] Backend server running on `http://localhost:8000`
- [ ] Database created and migrated
- [ ] Admin user created (test login via curl)
- [ ] Mobile app opens without errors
- [ ] Can login from mobile app
- [ ] Can see jobs list (empty initially)
- [ ] Can create a new job
- [ ] Network requests appear in backend logs
- [ ] Offline mode works (turn off WiFi, app still functions)

---

## 🎓 Learning Resources

### Laravel

- [Laravel Documentation](https://laravel.com/docs)
- [JWT Auth Package](https://jwt-auth.readthedocs.io/)
- [Laravel Eloquent Spatial](https://github.com/matanyadaev/laravel-eloquent-spatial)

### React Native & Expo

- [Expo Documentation](https://docs.expo.dev/)
- [React Navigation](https://reactnavigation.org/)
- [Zustand](https://github.com/pmndrs/zustand)

---

**You're all set! Happy coding! 🚀**

_If you encounter any issues, please check the troubleshooting section or open an issue on GitHub._
