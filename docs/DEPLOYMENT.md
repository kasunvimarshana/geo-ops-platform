# Deployment Guide

## GeoOps Platform - Production Deployment

---

## Backend Deployment (Laravel API)

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Redis
- Node.js & NPM (for asset compilation)
- SSL certificate
- Domain name

---

### Server Requirements

**Recommended Stack:**

- **OS**: Ubuntu 22.04 LTS
- **Web Server**: Nginx
- **PHP**: 8.2 with extensions (mbstring, xml, curl, gd, pdo_mysql, redis)
- **Database**: MySQL 8.0 or PostgreSQL 13
- **Cache/Queue**: Redis 7.0
- **Supervisor**: For queue workers
- **Certbot**: For SSL certificates

---

### Step 1: Server Setup

#### Install Required Packages

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
  php8.2-mysql php8.2-pgsql php8.2-xml php8.2-curl php8.2-gd \
  php8.2-mbstring php8.2-zip php8.2-redis php8.2-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Install Nginx
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx

# Install Supervisor
sudo apt install -y supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor

# Install Certbot for SSL
sudo apt install -y certbot python3-certbot-nginx
```

---

### Step 2: Database Setup

#### MySQL Setup

```bash
# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE geo-ops CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'geo-ops_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON geo-ops.* TO 'geo-ops_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### PostgreSQL Setup (Alternative)

```bash
# Install PostgreSQL and PostGIS
sudo apt install -y postgresql postgresql-contrib postgis

# Create database and user
sudo -u postgres psql

CREATE DATABASE geo-ops;
CREATE USER geo-ops_user WITH PASSWORD 'strong_password_here';
GRANT ALL PRIVILEGES ON DATABASE geo-ops TO geo-ops_user;

# Connect to database and enable PostGIS
\c geo-ops
CREATE EXTENSION postgis;
\q
```

---

### Step 3: Clone and Configure Application

```bash
# Create application directory
sudo mkdir -p /var/www/geo-ops
sudo chown -R $USER:$USER /var/www/geo-ops

# Clone repository
cd /var/www/geo-ops
git clone https://github.com/yourusername/geo-ops-platform.git .

# Go to backend directory
cd backend

# Install dependencies
composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

---

### Step 4: Configure Environment (.env)

Edit `/var/www/geo-ops/backend/.env`:

```env
APP_NAME="GeoOps Platform"
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_URL=https://api.geo-ops.lk

LOG_CHANNEL=daily
LOG_LEVEL=warning

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=geo-ops_user
DB_PASSWORD=strong_password_here

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Cache
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Queue
QUEUE_CONNECTION=redis

# JWT
JWT_SECRET=your_jwt_secret_key_here
JWT_TTL=60
JWT_REFRESH_TTL=43200

# AWS S3 (for file storage)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=geo-ops-files
AWS_USE_PATH_STYLE_ENDPOINT=false

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=noreply@geo-ops.lk
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@geo-ops.lk
MAIL_FROM_NAME="${APP_NAME}"

# Subscription Package Limits
FREE_MAX_MEASUREMENTS=10
FREE_MAX_DRIVERS=2
FREE_MAX_JOBS=20
BASIC_MAX_MEASUREMENTS=100
BASIC_MAX_DRIVERS=5
BASIC_MAX_JOBS=200
```

---

### Step 5: Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate --force

# Seed subscription packages
php artisan db:seed --class=SubscriptionPackageSeeder

# (Optional) Seed sample data for testing
php artisan db:seed --class=SampleDataSeeder

# Create storage symlink
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Step 6: Configure Nginx

Create `/etc/nginx/sites-available/geo-ops`:

```nginx
server {
    listen 80;
    server_name api.geo-ops.lk;
    root /var/www/geo-ops/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Increase upload size for receipts/images
    client_max_body_size 20M;
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/geo-ops /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### Step 7: Configure SSL with Let's Encrypt

```bash
# Generate SSL certificate
sudo certbot --nginx -d api.geo-ops.lk

# Auto-renewal is set up automatically
# Test renewal
sudo certbot renew --dry-run
```

---

### Step 8: Configure Queue Workers (Supervisor)

Create `/etc/supervisor/conf.d/geo-ops-worker.conf`:

```ini
[program:geo-ops-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/geo-ops/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/geo-ops/backend/storage/logs/worker.log
stopwaitsecs=3600
```

Start workers:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start geo-ops-worker:*
```

---

### Step 9: Configure Cron for Scheduled Tasks

```bash
sudo crontab -e -u www-data

# Add this line:
* * * * * cd /var/www/geo-ops/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

### Step 10: Security Hardening

```bash
# Restrict access to sensitive files
sudo chmod 600 /var/www/geo-ops/backend/.env

# Disable directory listing
sudo vi /etc/nginx/nginx.conf
# Add inside http block:
autoindex off;

# Configure firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Secure MySQL
sudo mysql_secure_installation

# Fail2ban (optional)
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

### Step 11: Monitoring & Logging

#### Set up Log Rotation

Create `/etc/logrotate.d/geo-ops`:

```
/var/www/geo-ops/backend/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

#### Monitor Queue Workers

```bash
# Check queue worker status
sudo supervisorctl status geo-ops-worker:*

# View worker logs
tail -f /var/www/geo-ops/backend/storage/logs/worker.log
```

---

## Mobile App Deployment (React Native Expo)

### Prerequisites

- Node.js 18+ and npm
- Expo CLI
- EAS CLI (Expo Application Services)
- Apple Developer Account (for iOS)
- Google Play Developer Account (for Android)

---

### Step 1: Install EAS CLI

```bash
npm install -g eas-cli
```

---

### Step 2: Configure Environment Variables

Create `/mobile/.env.production`:

```env
API_BASE_URL=https://api.geo-ops.lk/api/v1
GOOGLE_MAPS_API_KEY=your_google_maps_api_key
MAPBOX_ACCESS_TOKEN=your_mapbox_token
SENTRY_DSN=your_sentry_dsn
APP_VERSION=1.0.0
```

---

### Step 3: Configure EAS Build

Create `/mobile/eas.json`:

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
        "serviceAccountKeyPath": "./service-account-key.json",
        "track": "internal"
      },
      "ios": {
        "appleId": "your-apple-id@example.com",
        "ascAppId": "1234567890",
        "appleTeamId": "ABCDEFGHIJ"
      }
    }
  }
}
```

---

### Step 4: Update app.json

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
      "bundleIdentifier": "lk.geo-ops.platform",
      "buildNumber": "1",
      "infoPlist": {
        "NSLocationWhenInUseUsageDescription": "GeoOps needs your location to measure land accurately.",
        "NSLocationAlwaysUsageDescription": "GeoOps needs background location to track jobs.",
        "NSLocationAlwaysAndWhenInUseUsageDescription": "GeoOps needs your location for land measurement and job tracking."
      }
    },
    "android": {
      "adaptiveIcon": {
        "foregroundImage": "./assets/adaptive-icon.png",
        "backgroundColor": "#ffffff"
      },
      "package": "lk.geo-ops.platform",
      "versionCode": 1,
      "permissions": [
        "ACCESS_COARSE_LOCATION",
        "ACCESS_FINE_LOCATION",
        "ACCESS_BACKGROUND_LOCATION"
      ]
    },
    "plugins": [
      [
        "expo-location",
        {
          "locationAlwaysAndWhenInUsePermission": "Allow GeoOps to use your location."
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

### Step 5: Build for Android

```bash
cd mobile

# Login to Expo
eas login

# Configure EAS
eas build:configure

# Build production APK/AAB
eas build --platform android --profile production

# Download build when complete
# Build will be available in Expo dashboard
```

---

### Step 6: Build for iOS

```bash
# Build for iOS (requires Apple Developer account)
eas build --platform ios --profile production

# The build will be available in Expo dashboard
```

---

### Step 7: Submit to App Stores

#### Android (Google Play Store)

```bash
# Submit to Google Play
eas submit --platform android --profile production

# Or manually:
# 1. Go to https://play.google.com/console
# 2. Create new app
# 3. Upload AAB file
# 4. Complete store listing
# 5. Submit for review
```

#### iOS (Apple App Store)

```bash
# Submit to App Store
eas submit --platform ios --profile production

# Or manually:
# 1. Open Xcode and upload to App Store Connect
# 2. Complete app information in App Store Connect
# 3. Submit for review
```

---

### Step 8: Over-The-Air (OTA) Updates

For minor updates without rebuilding:

```bash
# Publish update
eas update --branch production --message "Bug fixes and improvements"

# Users will receive update automatically
```

---

## CI/CD with GitHub Actions

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  deploy-backend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/geo-ops
            git pull origin main
            cd backend
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            sudo supervisorctl restart geo-ops-worker:*

  build-mobile:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup Node
        uses: actions/setup-node@v3
        with:
          node-version: 18

      - name: Install dependencies
        run: |
          cd mobile
          npm ci

      - name: Build for Android
        run: |
          cd mobile
          eas build --platform android --non-interactive --no-wait
        env:
          EXPO_TOKEN: ${{ secrets.EXPO_TOKEN }}
```

---

## Backup Strategy

### Database Backup

Create `/usr/local/bin/backup-geo-ops-db.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/geo-ops"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/geo-ops_$DATE.sql.gz"

mkdir -p $BACKUP_DIR

# MySQL backup
mysqldump -u geo-ops_user -p'password' geo-ops | gzip > $BACKUP_FILE

# Upload to S3 (optional)
aws s3 cp $BACKUP_FILE s3://your-backup-bucket/database/

# Delete backups older than 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Backup completed: $BACKUP_FILE"
```

Make executable and add to cron:

```bash
sudo chmod +x /usr/local/bin/backup-geo-ops-db.sh
sudo crontab -e

# Add daily backup at 2 AM
0 2 * * * /usr/local/bin/backup-geo-ops-db.sh >> /var/log/geo-ops-backup.log 2>&1
```

---

## Monitoring & Alerts

### Application Monitoring (Laravel)

Install Laravel Telescope (development) or Sentry (production):

```bash
# Production: Sentry
composer require sentry/sentry-laravel

# Configure in config/sentry.php and .env
SENTRY_LARAVEL_DSN=your_sentry_dsn
```

### Server Monitoring

Use tools like:

- **UptimeRobot** - Uptime monitoring
- **New Relic** - Application performance
- **CloudWatch** - AWS metrics
- **Grafana + Prometheus** - Custom metrics

---

## Performance Optimization

### Backend

1. **Enable OPcache** (PHP optimization)
2. **Use CDN** for static assets
3. **Database indexing** on frequently queried columns
4. **Redis caching** for frequently accessed data
5. **Optimize images** before storage
6. **Enable gzip compression** in Nginx

### Mobile

1. **Code splitting** and lazy loading
2. **Image optimization** and caching
3. **Reduce bundle size** (remove unused code)
4. **Use production build** (minified)

---

## Troubleshooting

### Backend Issues

```bash
# View Laravel logs
tail -f /var/www/geo-ops/backend/storage/logs/laravel.log

# Check queue workers
sudo supervisorctl status geo-ops-worker:*

# Restart queue workers
sudo supervisorctl restart geo-ops-worker:*

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Issues

```bash
# Check MySQL status
sudo systemctl status mysql

# Check connections
mysql -u geo-ops_user -p -e "SHOW PROCESSLIST;"

# Optimize tables
php artisan db:optimize
```

---

## Scaling Considerations

### Horizontal Scaling

- Load balancer (Nginx/HAProxy)
- Multiple app servers
- Database read replicas
- Redis cluster

### Vertical Scaling

- Increase server resources (CPU, RAM)
- Optimize queries
- Add indexes
- Use caching aggressively

---

## Support & Maintenance

- Monitor error logs daily
- Review performance metrics weekly
- Update dependencies monthly
- Backup verification monthly
- Security patches immediately
- Feature updates quarterly

---

## Emergency Contacts

- System Administrator: admin@geo-ops.lk
- Database Administrator: dba@geo-ops.lk
- Development Team: dev@geo-ops.lk
- On-call Support: +94 XX XXX XXXX
