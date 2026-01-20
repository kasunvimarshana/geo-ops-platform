# Deployment Guide

## GPS Field Management Platform - Deployment Instructions

**Version:** 1.0.0  
**Last Updated:** January 2026

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Backend Deployment](#backend-deployment)
3. [Mobile App Deployment](#mobile-app-deployment)
4. [Environment Configuration](#environment-configuration)
5. [Database Setup](#database-setup)
6. [Security Considerations](#security-considerations)
7. [Monitoring & Maintenance](#monitoring--maintenance)

---

## Prerequisites

### Backend Requirements

- **Server**: Ubuntu 22.04 LTS or later
- **PHP**: 8.3 or higher
- **Composer**: Latest version
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **Database**: MySQL 8.0+ or PostgreSQL 15+ with spatial extensions
- **Redis**: 6.0+ (for caching and queues)
- **Node.js**: 18+ (for asset compilation)
- **SSL Certificate**: Let's Encrypt or commercial SSL

### Mobile Requirements

- **Development**: macOS (for iOS) or Windows/Linux/macOS (for Android)
- **Node.js**: 18+ with npm/yarn
- **Expo CLI**: Latest version
- **iOS**: Xcode 14+ (macOS only)
- **Android**: Android Studio with Android SDK

### Third-Party Services

- **Email**: SMTP server or service (SendGrid, Mailgun, SES)
- **SMS**: Optional (Twilio, Vonage)
- **Storage**: AWS S3 or compatible service
- **Maps**: Google Maps API key or Mapbox token
- **Push Notifications**: Firebase Cloud Messaging

---

## Backend Deployment

### 1. Server Setup

#### Update System Packages

```bash
sudo apt update && sudo apt upgrade -y
```

#### Install PHP 8.3

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-pgsql \
    php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-gd \
    php8.3-bcmath php8.3-redis php8.3-intl
```

#### Install Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

#### Install MySQL 8.0

```bash
sudo apt install -y mysql-server mysql-client
sudo mysql_secure_installation
```

#### Install Redis

```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

#### Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

---

### 2. Deploy Laravel Application

#### Clone Repository

```bash
cd /var/www
sudo git clone https://github.com/your-org/geo-ops-platform.git
cd geo-ops-platform/backend
```

#### Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/geo-ops-platform
sudo chmod -R 755 /var/www/geo-ops-platform
sudo chmod -R 775 /var/www/geo-ops-platform/backend/storage
sudo chmod -R 775 /var/www/geo-ops-platform/backend/bootstrap/cache
```

#### Install Dependencies

```bash
cd /var/www/geo-ops-platform/backend
composer install --optimize-autoloader --no-dev
```

#### Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with production settings:

```env
APP_NAME="GeoOps Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.geo-ops.lk

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops_manager
DB_USERNAME=geo-ops_user
DB_PASSWORD=your_secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=s3
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@geo-ops.lk"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

JWT_SECRET=your_jwt_secret_key_here
JWT_TTL=15
JWT_REFRESH_TTL=10080

GOOGLE_MAPS_API_KEY=your_google_maps_key
```

#### Run Migrations

```bash
php artisan migrate --force
php artisan db:seed --force
```

#### Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Set Up Queue Worker

Create systemd service file:

```bash
sudo nano /etc/systemd/system/geo-ops-worker.service
```

Content:

```ini
[Unit]
Description=GeoOps Platform Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/geo-ops-platform/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl daemon-reload
sudo systemctl enable geo-ops-worker
sudo systemctl start geo-ops-worker
```

#### Set Up Cron Jobs

```bash
sudo crontab -e -u www-data
```

Add:

```
* * * * * cd /var/www/geo-ops-platform/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

### 3. Nginx Configuration

Create site configuration:

```bash
sudo nano /etc/nginx/sites-available/geo-ops-api
```

Content:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name api.geo-ops.lk;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.geo-ops.lk;

    root /var/www/geo-ops-platform/backend/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/api.geo-ops.lk/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.geo-ops.lk/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/geo-ops-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### 4. SSL Certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d api.geo-ops.lk
sudo systemctl reload nginx
```

Auto-renewal:

```bash
sudo certbot renew --dry-run
```

---

## Mobile App Deployment

### 1. Development Setup

#### Clone Repository

```bash
git clone https://github.com/your-org/geo-ops-platform.git
cd geo-ops-platform/mobile
```

#### Install Dependencies

```bash
npm install
# or
yarn install
```

#### Configure Environment

Create `.env` file:

```env
API_URL=https://api.geo-ops.lk/api/v1
GOOGLE_MAPS_API_KEY=your_android_key
GOOGLE_MAPS_API_KEY_IOS=your_ios_key
SENTRY_DSN=your_sentry_dsn
```

Update `app.json`:

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
      "image": "./assets/splash-icon.png",
      "resizeMode": "contain",
      "backgroundColor": "#ffffff"
    },
    "assetBundlePatterns": ["**/*"],
    "ios": {
      "supportsTablet": true,
      "bundleIdentifier": "lk.geo-ops.app",
      "infoPlist": {
        "NSLocationWhenInUseUsageDescription": "We need your location to measure land area accurately.",
        "NSLocationAlwaysAndWhenInUseUsageDescription": "We need background location access for GPS tracking during jobs.",
        "UIBackgroundModes": ["location"]
      }
    },
    "android": {
      "adaptiveIcon": {
        "foregroundImage": "./assets/adaptive-icon.png",
        "backgroundColor": "#ffffff"
      },
      "package": "lk.geo-ops.app",
      "permissions": [
        "ACCESS_COARSE_LOCATION",
        "ACCESS_FINE_LOCATION",
        "ACCESS_BACKGROUND_LOCATION",
        "CAMERA",
        "READ_EXTERNAL_STORAGE",
        "WRITE_EXTERNAL_STORAGE"
      ],
      "config": {
        "googleMaps": {
          "apiKey": "your_android_key"
        }
      }
    },
    "web": {
      "favicon": "./assets/favicon.png"
    },
    "extra": {
      "eas": {
        "projectId": "your-project-id"
      }
    }
  }
}
```

---

### 2. Build for Production

#### Install EAS CLI

```bash
npm install -g eas-cli
eas login
```

#### Configure EAS Build

```bash
eas build:configure
```

#### Build Android APK/AAB

```bash
# Development build
eas build --platform android --profile development

# Production build
eas build --platform android --profile production
```

#### Build iOS App

```bash
# Development build
eas build --platform ios --profile development

# Production build (requires Apple Developer account)
eas build --platform ios --profile production
```

---

### 3. Publish to Stores

#### Google Play Store

1. **Prepare Assets:**
   - App icon (512x512 PNG)
   - Feature graphic (1024x500 PNG)
   - Screenshots (min 2, various devices)
   - Privacy policy URL

2. **Create Release:**
   - Go to Google Play Console
   - Create new release
   - Upload AAB file
   - Fill in release notes
   - Set pricing and distribution

3. **Submit for Review**

#### Apple App Store

1. **Prepare Assets:**
   - App icon (1024x1024 PNG)
   - Screenshots for all supported devices
   - App preview video (optional)
   - Privacy policy URL

2. **App Store Connect:**
   - Create app listing
   - Upload build via Xcode or Transporter
   - Fill in app information
   - Submit for review

---

## Environment Configuration

### Backend (.env)

Complete `.env` template:

```env
# Application
APP_NAME="GeoOps Platform"
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_TIMEZONE=Asia/Colombo
APP_URL=https://api.geo-ops.lk
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops_manager
DB_USERNAME=geo-ops_user
DB_PASSWORD=secure_password

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@geo-ops.lk"
MAIL_FROM_NAME="GeoOps Platform"

# AWS S3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=geo-ops-storage
AWS_URL=https://geo-ops-storage.s3.amazonaws.com

# JWT
JWT_SECRET=your_jwt_secret_here
JWT_TTL=15
JWT_REFRESH_TTL=10080

# API Keys
GOOGLE_MAPS_API_KEY=your_server_key
SENTRY_LARAVEL_DSN=your_sentry_dsn

# Rate Limiting
RATE_LIMIT_FREE=100
RATE_LIMIT_BASIC=500
RATE_LIMIT_PRO=0

# App Settings
DEFAULT_CURRENCY=LKR
DEFAULT_TIMEZONE=Asia/Colombo
INVOICES_PREFIX=INV
```

### Mobile (.env)

```env
# API
API_URL=https://api.geo-ops.lk/api/v1
API_TIMEOUT=30000

# Maps
GOOGLE_MAPS_API_KEY=your_android_key
GOOGLE_MAPS_API_KEY_IOS=your_ios_key
MAPBOX_ACCESS_TOKEN=your_mapbox_token

# Analytics
SENTRY_DSN=your_sentry_dsn

# Features
ENABLE_OFFLINE_MODE=true
ENABLE_GPS_TRACKING=true
GPS_ACCURACY_THRESHOLD=10

# Sync Settings
SYNC_INTERVAL_MINUTES=15
MAX_RETRY_ATTEMPTS=3
```

---

## Database Setup

### MySQL Configuration

#### Create Database and User

```sql
CREATE DATABASE geo-ops_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'geo-ops_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON geo-ops_manager.* TO 'geo-ops_user'@'localhost';
FLUSH PRIVILEGES;
```

#### Enable Spatial Support

Ensure MySQL 8.0+ with InnoDB spatial index support.

#### Optimize Configuration

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
query_cache_size = 0
query_cache_type = 0
```

Restart MySQL:

```bash
sudo systemctl restart mysql
```

### PostgreSQL Configuration (Alternative)

#### Create Database and User

```sql
CREATE DATABASE geo-ops_manager;
CREATE USER geo-ops_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE geo-ops_manager TO geo-ops_user;

-- Enable PostGIS extension
\c geo-ops_manager
CREATE EXTENSION IF NOT EXISTS postgis;
```

---

## Security Considerations

### 1. Server Hardening

#### Firewall Configuration

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

#### Disable Root Login

```bash
sudo nano /etc/ssh/sshd_config
```

Set: `PermitRootLogin no`

#### Regular Updates

```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Application Security

- **HTTPS Only**: Enforce SSL/TLS
- **JWT Secrets**: Use strong, random secrets
- **Database Passwords**: Use complex passwords
- **API Rate Limiting**: Implemented per package tier
- **Input Validation**: All inputs validated
- **SQL Injection**: Protected via Eloquent ORM
- **XSS Protection**: Output escaping enabled
- **CSRF Protection**: Token-based protection

### 3. Backup Strategy

#### Automated Database Backups

```bash
#!/bin/bash
# /usr/local/bin/backup-db.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/geo-ops"
DB_NAME="geo-ops_manager"
DB_USER="geo-ops_user"
DB_PASS="secure_password"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Delete backups older than 30 days
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete

# Upload to S3
aws s3 cp $BACKUP_DIR/db_$DATE.sql.gz s3://geo-ops-backups/database/
```

Add to cron:

```bash
0 2 * * * /usr/local/bin/backup-db.sh
```

---

## Monitoring & Maintenance

### 1. Application Monitoring

#### Laravel Telescope (Development)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

#### Error Tracking (Sentry)

```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=your_dsn
```

### 2. Server Monitoring

#### Install Monitoring Tools

```bash
sudo apt install -y htop iotop nethogs
```

#### Log Monitoring

```bash
# Laravel logs
tail -f /var/www/geo-ops-platform/backend/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.3-fpm.log
```

### 3. Performance Optimization

#### OPcache Configuration

Edit `/etc/php/8.3/fpm/php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

#### Redis Persistence

Edit `/etc/redis/redis.conf`:

```
save 900 1
save 300 10
save 60 10000
```

---

## Troubleshooting

### Common Issues

#### 1. Permission Denied

```bash
sudo chown -R www-data:www-data /var/www/geo-ops-platform
sudo chmod -R 755 /var/www/geo-ops-platform
sudo chmod -R 775 /var/www/geo-ops-platform/backend/storage
```

#### 2. Queue Not Processing

```bash
sudo systemctl status geo-ops-worker
sudo systemctl restart geo-ops-worker
php artisan queue:work --tries=3
```

#### 3. Cache Issues

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### 4. Database Connection Failed

- Check credentials in `.env`
- Verify MySQL is running: `sudo systemctl status mysql`
- Test connection: `mysql -u geo-ops_user -p`

---

## Conclusion

This deployment guide provides comprehensive instructions for deploying the GPS Field Management Platform to production. Follow security best practices, monitor your application regularly, and keep all dependencies up to date.

For additional support, refer to:

- Laravel Documentation: https://laravel.com/docs
- Expo Documentation: https://docs.expo.dev
- System Architecture: `docs/ARCHITECTURE.md`
- API Documentation: `docs/API_DOCUMENTATION.md`
