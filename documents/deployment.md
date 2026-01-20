# GeoOps Platform - Deployment Guide

**Version:** 1.0.0  
**Last Updated:** January 2026  
**Status:** Production Ready

---

## Table of Contents

1. [Prerequisites](#prerequisites)
   - [Backend Requirements](#backend-requirements)
   - [Mobile App Requirements](#mobile-app-requirements)
   - [Third-Party Services](#third-party-services)
2. [Backend Deployment](#backend-deployment)
   - [Server Setup](#1-server-setup-ubuntu-2204)
   - [Clone and Configure](#2-clone-and-configure-backend)
   - [Environment Configuration](#3-configure-environment)
   - [Database Setup](#4-database-setup)
   - [Permissions](#5-set-permissions)
   - [Web Server Configuration](#6-configure-nginx)
   - [SSL Setup](#7-setup-ssl-with-lets-encrypt)
   - [Queue Workers](#8-setup-queue-workers)
   - [Scheduler](#9-setup-scheduler)
   - [Production Optimization](#10-optimize-for-production)
3. [Mobile App Deployment](#mobile-app-deployment)
   - [Environment Configuration](#1-configure-environment-1)
   - [App Configuration](#2-update-app-configuration)
   - [Build Setup](#3-install-eas-cli)
   - [Build Configuration](#4-configure-eas-build)
   - [Building for Android](#5-build-for-android)
   - [Building for iOS](#6-build-for-ios)
   - [Store Submission](#7-submit-to-stores)
   - [OTA Updates](#8-setup-ota-updates)
4. [Monitoring and Maintenance](#monitoring-and-maintenance)
   - [Backend Monitoring](#backend-monitoring)
   - [Health Checks](#application-health-checks)
   - [Performance Monitoring](#performance-monitoring)
5. [Scaling](#scaling-considerations)
   - [Horizontal Scaling](#horizontal-scaling)
   - [Database Scaling](#database-scaling)
   - [Caching Strategy](#caching-strategy)
   - [Queue Workers](#queue-workers)
6. [Security](#security-best-practices)
7. [Troubleshooting](#troubleshooting)
8. [Rollback Procedures](#rollback-procedure)
9. [Support](#support-and-documentation)

---

## Prerequisites

### Backend Requirements

- **PHP:** 8.3 or higher
- **Composer:** 2.x
- **Database:** MySQL 8.0+ or PostgreSQL 14+ with spatial extensions
- **Cache/Queue:** Redis 6.0+
- **Node.js:** 18+ (for asset compilation)
- **Web Server:** Nginx or Apache
- **SSL:** Let's Encrypt certificate (recommended)

### Mobile App Requirements

- **Node.js:** 20+
- **Package Manager:** npm or yarn
- **Build Tools:** Expo CLI, EAS CLI
- **Platforms:** Android Studio (Android builds), Xcode (iOS builds, macOS only)

### Third-Party Services

| Service | Purpose | Options |
|---------|---------|---------|
| Cloud Storage | File uploads and storage | AWS S3, DigitalOcean Spaces, or compatible |
| Email Service | Email notifications | SMTP, SendGrid, or AWS SES |
| Maps API | Geolocation and mapping | Google Maps or Mapbox |
| Error Tracking | Application monitoring | Sentry (optional) |

---

## Backend Deployment

### 1. Server Setup (Ubuntu 22.04)

#### Install PHP and Extensions

```bash
sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-pgsql \
  php8.3-redis php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl \
  php8.3-zip php8.3-gd php8.3-intl
```

#### Install Database: MySQL

```bash
sudo apt install -y mysql-server-8.0
sudo mysql_secure_installation
```

#### Install Database: PostgreSQL (Alternative)

```bash
sudo apt install -y postgresql-14 postgresql-14-postgis-3
```

#### Install Redis

```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
```

#### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
```

---

### 2. Clone and Configure Backend

```bash
cd /var/www
sudo git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform/backend

# Install dependencies
composer install --no-dev --optimize-autoloader

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret
```

---

### 3. Configure Environment

Edit `.env` file with the following configuration:

```env
# Application
APP_NAME="GeoOps Platform"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://api.geo-ops.lk

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Database - MySQL (Option 1)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo_ops_platform
DB_USERNAME=geo_ops_user
DB_PASSWORD=strong_password_here

# Database - PostgreSQL (Option 2 - Uncomment to use)
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=geo_ops_platform
# DB_USERNAME=geo_ops_user
# DB_PASSWORD=strong_password_here

# Cache and Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# JWT Authentication
JWT_SECRET=your_jwt_secret_here
JWT_TTL=60
JWT_REFRESH_TTL=20160

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@geo-ops.lk
MAIL_FROM_NAME="${APP_NAME}"

# File Storage (AWS S3 or compatible)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=geo-ops-platform
AWS_USE_PATH_STYLE_ENDPOINT=false

# Maps Configuration
GOOGLE_MAPS_API_KEY=your_google_maps_key
# OR
MAPBOX_API_KEY=your_mapbox_key

# Error Tracking (Optional)
SENTRY_LARAVEL_DSN=your_sentry_dsn

# Subscription Limits
FREE_MEASUREMENTS_LIMIT=10
FREE_DRIVERS_LIMIT=1
FREE_EXPORTS_LIMIT=5
BASIC_MEASUREMENTS_LIMIT=100
BASIC_DRIVERS_LIMIT=5
BASIC_EXPORTS_LIMIT=50
PRO_MEASUREMENTS_LIMIT=1000
PRO_DRIVERS_LIMIT=50
PRO_EXPORTS_LIMIT=500
```

---

### 4. Database Setup

#### Create Database: MySQL

```bash
mysql -u root -p
CREATE DATABASE geo_ops_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'geo_ops_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON geo_ops_platform.* TO 'geo_ops_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Create Database: PostgreSQL

```bash
sudo -u postgres psql
CREATE DATABASE geo_ops_platform;
CREATE USER geo_ops_user WITH PASSWORD 'strong_password_here';
GRANT ALL PRIVILEGES ON DATABASE geo_ops_platform TO geo_ops_user;
\c geo_ops_platform
CREATE EXTENSION IF NOT EXISTS postgis;
\q
```

#### Run Migrations and Seeders

```bash
cd /var/www/geo-ops-platform/backend
php artisan migrate --force
php artisan db:seed --force
```

---

### 5. Set Permissions

```bash
cd /var/www/geo-ops-platform/backend
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

### 6. Configure Nginx

Create `/etc/nginx/sites-available/geo-ops-api`:

```nginx
# HTTP to HTTPS redirect
server {
    listen 80;
    listen [::]:80;
    server_name api.geo-ops.lk;
    return 301 https://$server_name$request_uri;
}

# HTTPS server block
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.geo-ops.lk;
    root /var/www/geo-ops-platform/backend/public;

    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/api.geo-ops.lk/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.geo-ops.lk/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    # Compression
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # URL routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
    limit_req zone=api burst=10 nodelay;
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/geo-ops-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### 7. Setup SSL with Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d api.geo-ops.lk
```

Auto-renewal is configured automatically by Certbot.

---

### 8. Setup Queue Workers

Create systemd service `/etc/systemd/system/geo-ops-worker.service`:

```ini
[Unit]
Description=GeoOps Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=5s
ExecStart=/usr/bin/php /var/www/geo-ops-platform/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Enable and start the service:

```bash
sudo systemctl enable geo-ops-worker
sudo systemctl start geo-ops-worker
```

---

### 9. Setup Scheduler

Add to crontab for the www-data user:

```bash
sudo crontab -e -u www-data
```

Add the following line:

```cron
* * * * * cd /var/www/geo-ops-platform/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

### 10. Optimize for Production

Run these optimization commands:

```bash
cd /var/www/geo-ops-platform/backend
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Mobile App Deployment

### 1. Configure Environment

Create `mobile/.env`:

```env
EXPO_PUBLIC_API_URL=https://api.geo-ops.lk/api/v1
EXPO_PUBLIC_GOOGLE_MAPS_KEY=your_google_maps_key
EXPO_PUBLIC_MAPBOX_KEY=your_mapbox_key
EXPO_PUBLIC_SENTRY_DSN=your_sentry_dsn
```

---

### 2. Update App Configuration

Edit `mobile/app.json`:

```json
{
  "expo": {
    "name": "GeoOps Platform",
    "slug": "geo-ops-platform",
    "version": "1.0.0",
    "orientation": "portrait",
    "icon": "./assets/icon.png",
    "userInterfaceStyle": "light",
    "splash": {
      "image": "./assets/splash.png",
      "resizeMode": "contain",
      "backgroundColor": "#ffffff"
    },
    "assetBundlePatterns": ["**/*"],
    "ios": {
      "supportsTablet": true,
      "bundleIdentifier": "lk.geoops.app",
      "config": {
        "googleMapsApiKey": "YOUR_IOS_MAPS_KEY"
      },
      "infoPlist": {
        "NSLocationWhenInUseUsageDescription": "This app needs access to your location for GPS land measurement.",
        "NSLocationAlwaysUsageDescription": "This app needs continuous access to track field work in the background.",
        "UIBackgroundModes": ["location", "fetch"]
      }
    },
    "android": {
      "adaptiveIcon": {
        "foregroundImage": "./assets/adaptive-icon.png",
        "backgroundColor": "#ffffff"
      },
      "package": "lk.geoops.app",
      "permissions": [
        "ACCESS_COARSE_LOCATION",
        "ACCESS_FINE_LOCATION",
        "ACCESS_BACKGROUND_LOCATION",
        "BLUETOOTH",
        "BLUETOOTH_ADMIN",
        "BLUETOOTH_CONNECT"
      ],
      "config": {
        "googleMaps": {
          "apiKey": "YOUR_ANDROID_MAPS_KEY"
        }
      }
    },
    "plugins": [
      "expo-location",
      "expo-secure-store",
      [
        "expo-build-properties",
        {
          "android": {
            "compileSdkVersion": 34,
            "targetSdkVersion": 34,
            "minSdkVersion": 23
          },
          "ios": {
            "deploymentTarget": "13.0"
          }
        }
      ]
    ],
    "extra": {
      "eas": {
        "projectId": "your-project-id"
      }
    }
  }
}
```

---

### 3. Install EAS CLI

```bash
npm install -g eas-cli
eas login
```

---

### 4. Configure EAS Build

Create `mobile/eas.json`:

```json
{
  "cli": {
    "version": ">= 5.0.0"
  },
  "build": {
    "development": {
      "developmentClient": true,
      "distribution": "internal",
      "android": {
        "gradleCommand": ":app:assembleDebug"
      },
      "ios": {
        "buildConfiguration": "Debug"
      }
    },
    "preview": {
      "distribution": "internal",
      "android": {
        "buildType": "apk"
      }
    },
    "production": {
      "android": {
        "buildType": "app-bundle"
      },
      "ios": {
        "buildConfiguration": "Release"
      }
    }
  },
  "submit": {
    "production": {
      "android": {
        "serviceAccountKeyPath": "./google-play-key.json",
        "track": "production"
      },
      "ios": {
        "appleId": "your-apple-id@example.com",
        "ascAppId": "1234567890",
        "appleTeamId": "ABCDE12345"
      }
    }
  }
}
```

---

### 5. Build for Android

```bash
cd mobile
eas build --platform android --profile production
```

---

### 6. Build for iOS

```bash
cd mobile
eas build --platform ios --profile production
```

---

### 7. Submit to Stores

#### Submit to Google Play Store:

```bash
eas submit --platform android
```

#### Submit to Apple App Store:

```bash
eas submit --platform ios
```

---

### 8. Setup OTA Updates

Update `mobile/eas.json` to include update configuration:

```json
{
  "update": {
    "production": {
      "channel": "production"
    },
    "preview": {
      "channel": "preview"
    }
  }
}
```

Publish an update:

```bash
eas update --branch production --message "Bug fixes and improvements"
```

---

## Monitoring and Maintenance

### Backend Monitoring

#### Setup Log Rotation

Create `/etc/logrotate.d/geo-ops`:

```
/var/www/geo-ops-platform/backend/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

#### Monitor Queue Workers

Check worker status:

```bash
sudo systemctl status geo-ops-worker
sudo journalctl -u geo-ops-worker -f
```

View failed jobs:

```bash
php artisan queue:failed
```

---

#### Database Backups

**MySQL backup:**

```bash
mysqldump -u geo_ops_user -p geo_ops_platform > backup_$(date +%Y%m%d).sql
```

**PostgreSQL backup:**

```bash
pg_dump -U geo_ops_user geo_ops_platform > backup_$(date +%Y%m%d).sql
```

---

### Application Health Checks

Add a health check endpoint to your Laravel routes:

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'database' => DB::connection()->getPdo() ? 'ok' : 'error',
        'redis' => Redis::ping() ? 'ok' : 'error',
        'storage' => Storage::disk()->exists('.health') ? 'ok' : 'error',
    ]);
});
```

Monitor this endpoint regularly to ensure system health.

---

### Performance Monitoring

Key metrics to monitor:

- **Response Times:** Track API response times for degradation
- **Database Performance:** Monitor query execution times and optimize slow queries
- **Queue Jobs:** Track job success/failure rates and processing times
- **API Usage:** Monitor endpoint usage patterns and identify bottlenecks
- **Mobile App:** Track crash rates and user session metrics

---

## Scaling Considerations

### Horizontal Scaling

- Deploy load balancer (Nginx, HAProxy, or cloud provider LB)
- Run multiple application server instances
- Use shared Redis and database infrastructure
- Store files centrally (AWS S3 or similar)

### Database Scaling

- Implement read replicas for read-heavy queries
- Keep master database for write operations
- Use connection pooling to manage connections efficiently
- Optimize queries and add strategic indexes

### Caching Strategy

- Use Redis for session storage and application cache
- Deploy CDN for static assets
- Implement API response caching with appropriate TTLs
- Cache frequently accessed database query results

### Queue Workers

- Run multiple worker processes for parallel job processing
- Consider dedicated worker servers for CPU-intensive jobs
- Implement priority queues for critical tasks
- Monitor queue depth and adjust workers accordingly

---

## Security Best Practices

1. **Keep Dependencies Updated:** Regularly update PHP, Composer packages, and Node.js packages
2. **Database Security:** Use strong passwords; restrict database access to localhost or VPN
3. **Firewall:** Enable UFW or equivalent firewall; allow only necessary ports
4. **Secrets Management:** Use environment variables for sensitive data; never commit secrets
5. **Rate Limiting:** Enable rate limiting on API endpoints
6. **Security Audits:** Conduct regular security audits and penetration testing
7. **Access Logs:** Monitor and review server access logs regularly
8. **HTTPS Only:** Enforce HTTPS; redirect all HTTP to HTTPS
9. **CORS Configuration:** Properly configure CORS to allow only trusted origins
10. **Input Validation:** Validate all user input server-side
11. **SQL Injection Prevention:** Use parameterized queries and Laravel's query builder
12. **CSRF Protection:** Maintain CSRF token protection on state-changing requests

---

## Troubleshooting

### 500 Internal Server Error

**Symptoms:** Application returns 500 error responses

**Resolution:**
1. Check Laravel logs: `tail -f storage/logs/laravel.log`
2. Check Nginx error log: `tail -f /var/log/nginx/error.log`
3. Verify file permissions on storage and cache directories
4. Ensure `.env` file has all required variables

---

### Queue Jobs Not Processing

**Symptoms:** Jobs are queued but not being processed

**Resolution:**
1. Check worker status: `sudo systemctl status geo-ops-worker`
2. Verify Redis connection: `redis-cli ping` (should return PONG)
3. Check failed jobs: `php artisan queue:failed`
4. Review worker logs: `sudo journalctl -u geo-ops-worker -f`
5. Restart worker: `sudo systemctl restart geo-ops-worker`

---

### Database Connection Errors

**Symptoms:** Cannot connect to database; migrations fail

**Resolution:**
1. Verify credentials in `.env` file
2. Check database service status:
   - MySQL: `sudo systemctl status mysql`
   - PostgreSQL: `sudo systemctl status postgresql`
3. Test connection using Tinker:
   ```bash
   php artisan tinker
   DB::connection()->getPdo()  # Should connect without error
   ```
4. Verify database exists and user has permissions
5. Check firewall rules if using remote database

---

### Mobile App Cannot Connect to API

**Symptoms:** Mobile app fails to authenticate or fetch data

**Resolution:**
1. Verify API URL in `mobile/.env`
2. Check CORS settings in Laravel backend
3. Verify SSL certificate is valid: `curl -I https://api.geo-ops.lk`
4. Test API endpoint with curl or Postman
5. Review mobile app logs for specific error messages
6. Ensure backend is running and accessible

---

## Rollback Procedure

### Backend Rollback

Revert to a previous stable version:

```bash
cd /var/www/geo-ops-platform/backend

# Checkout previous commit
git checkout <previous-stable-commit>

# Reinstall dependencies
composer install

# Rollback database migrations if needed
php artisan migrate:rollback

# Clear all caches
php artisan cache:clear
php artisan config:cache

# Restart queue workers
sudo systemctl restart geo-ops-worker
```

### Mobile App Rollback

Revert to previous mobile app version:

```bash
eas update --branch production --message "Rollback to previous version"
```

---

## Support and Documentation

For additional information and support:

- **API Documentation:** https://api.geo-ops.lk/docs
- **User Guide:** https://docs.geo-ops.lk
- **Support Email:** support@geo-ops.lk
- **GitHub Repository:** https://github.com/kasunvimarshana/geo-ops-platform
- **GitHub Issues:** https://github.com/kasunvimarshana/geo-ops-platform/issues

---

**Document Information**

This deployment guide provides comprehensive instructions for deploying the GeoOps Platform in a production environment. Follow these steps carefully and ensure all prerequisites are met before beginning the deployment process.

For questions or issues, please refer to the support resources listed above or open an issue on GitHub.
