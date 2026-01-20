# Setup Instructions

## Prerequisites

### Mobile Development

- Node.js 18+ and npm
- Expo CLI
- iOS Simulator (macOS) or Android Studio (for Android development)
- Expo Go app on physical device (optional)

### Backend Development

- PHP 8.1+
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional, for queues and caching)

## Backend Setup

### 1. Install Dependencies

```bash
cd backend
composer install
```

### 2. Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` file with your configuration:

```env
APP_NAME="GeoOps"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=your_generated_secret

QUEUE_CONNECTION=database
CACHE_DRIVER=file
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Database Setup

Create the database:

```sql
CREATE DATABASE geo-ops CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations:

```bash
php artisan migrate
```

### 5. Publish Vendor Assets

```bash
# JWT Auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

# Permissions
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 6. Start Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### 7. Run Queue Worker (Optional)

```bash
php artisan queue:work
```

## Mobile App Setup

### 1. Install Dependencies

```bash
cd mobile
npm install
```

### 2. Environment Configuration

Create `.env` file:

```env
EXPO_PUBLIC_API_URL=http://localhost:8000
```

For physical devices, use your computer's IP address:

```env
EXPO_PUBLIC_API_URL=http://192.168.1.100:8000
```

### 3. Start Expo Development Server

```bash
npm start
```

### 4. Run on Platform

**iOS Simulator (macOS only):**

```bash
npm run ios
```

**Android Emulator:**

```bash
npm run android
```

**Web:**

```bash
npm run web
```

**Physical Device:**

1. Install Expo Go app from App Store or Google Play
2. Scan QR code from terminal
3. App will load on your device

## Testing

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

## Database Seeding (Optional)

Create seeders for test data:

```bash
php artisan make:seeder OrganizationSeeder
php artisan make:seeder UserSeeder
php artisan db:seed
```

## Troubleshooting

### Backend Issues

**Issue: JWT Secret Not Set**

```bash
php artisan jwt:secret
```

**Issue: Permission Denied on Storage**

```bash
chmod -R 775 storage bootstrap/cache
```

**Issue: Database Connection Failed**

- Check MySQL/PostgreSQL is running
- Verify database credentials in `.env`
- Ensure database exists

### Mobile Issues

**Issue: Metro Bundler Cache**

```bash
npx expo start -c
```

**Issue: Cannot Connect to API**

- Ensure backend server is running
- Check `EXPO_PUBLIC_API_URL` is correct
- For physical devices, use IP address not `localhost`
- Ensure firewall allows connections

**Issue: Location Permissions**

- Grant location permissions when prompted
- Check app settings if denied

## Production Deployment

### Backend

1. **Environment**
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Use strong `APP_KEY` and `JWT_SECRET`

2. **Optimize**

   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Database**
   - Use production database credentials
   - Run migrations: `php artisan migrate --force`

4. **Web Server**
   - Configure Nginx or Apache
   - Point to `public` directory
   - Enable HTTPS

### Mobile

1. **Build for iOS**

   ```bash
   eas build --platform ios
   ```

2. **Build for Android**

   ```bash
   eas build --platform android
   ```

3. **Configure App Store**
   - Update `app.json` with proper identifiers
   - Add required permissions
   - Submit to App Store/Play Store

## API Documentation

### Using Postman

Import the API collection:

1. Open Postman
2. Import > Raw text
3. Use endpoints from `docs/ARCHITECTURE.md`

### Using Swagger (Future)

```bash
composer require darkaonline/l5-swagger
php artisan l5-swagger:generate
```

Visit: `http://localhost:8000/api/documentation`

## Development Workflow

### 1. Create Feature Branch

```bash
git checkout -b feature/your-feature-name
```

### 2. Make Changes

- Follow Clean Architecture principles
- Write tests for new features
- Update documentation

### 3. Test Changes

```bash
# Backend
cd backend && php artisan test

# Mobile
cd mobile && npm test
```

### 4. Commit Changes

```bash
git add .
git commit -m "feat: description of changes"
```

### 5. Push and Create PR

```bash
git push origin feature/your-feature-name
```

## Support

For issues and questions:

- Check documentation in `docs/` directory
- Review architecture in `docs/ARCHITECTURE.md`
- Check GitHub issues

## License

MIT License
