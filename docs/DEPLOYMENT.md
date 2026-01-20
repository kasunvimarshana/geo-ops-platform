# Deployment Guide - GeoOps Platform

## Overview

This guide covers deploying the GeoOps platform to production environments. The platform consists of two main components:

- **Laravel Backend API** (backend/)
- **React Native Mobile App** (frontend/)

## Prerequisites

### Backend Server Requirements

- Ubuntu 20.04 LTS or higher
- PHP 8.2 with extensions: mbstring, xml, curl, zip, gd, mysql/pgsql, redis
- MySQL 8.0+ or PostgreSQL 14+ (with PostGIS for spatial data)
- Redis 6.0+
- Nginx or Apache
- Composer 2.x
- Supervisor (for queue workers)
- SSL Certificate (Let's Encrypt recommended)

### Mobile App Requirements

- Expo Account (for OTA updates)
- Google Maps API Key / Mapbox Access Token
- Firebase Account (for push notifications - optional)

## Backend Deployment

### 1. Server Setup

#### Install PHP 8.2

```bash
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-xml php8.2-curl php8.2-gd php8.2-mbstring \
    php8.2-zip php8.2-bcmath php8.2-redis
```

#### Install MySQL

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Create database
sudo mysql -u root -p
CREATE DATABASE geo-ops CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'geo-ops'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON geo-ops.* TO 'geo-ops'@'localhost';
FLUSH PRIVILEGES;
EXIT;
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
```

#### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### Install Supervisor

```bash
sudo apt install -y supervisor
sudo systemctl enable supervisor
```

### 2. Deploy Laravel Application

#### Clone Repository

```bash
cd /var/www
sudo git clone https://github.com/yourusername/geo-ops-platform.git
sudo chown -R www-data:www-data geo-ops-platform
cd geo-ops-platform/backend
```

#### Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

#### Configure Environment

```bash
cp .env.example .env
nano .env
```

Update `.env` with production values:

```env
APP_NAME="GeoOps API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://api.geo-ops.lk

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=geo-ops
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

JWT_SECRET=your_generated_jwt_secret
JWT_TTL=60

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@geo-ops.lk
```

#### Generate Keys

```bash
php artisan key:generate
php artisan jwt:secret
```

#### Run Migrations

```bash
php artisan migrate --force
php artisan db:seed --force
```

#### Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/geo-ops-platform/backend
sudo chmod -R 775 storage bootstrap/cache
```

#### Create Storage Link

```bash
php artisan storage:link
```

### 3. Configure Nginx

Create Nginx configuration:

```bash
sudo nano /etc/nginx/sites-available/geo-ops-api
```

Add configuration:

```nginx
server {
    listen 80;
    server_name api.geo-ops.lk;
    root /var/www/geo-ops-platform/backend/public;

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

    client_max_body_size 10M;
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/geo-ops-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 4. Configure SSL with Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d api.geo-ops.lk
```

### 5. Configure Queue Workers

Create Supervisor configuration:

```bash
sudo nano /etc/supervisor/conf.d/geo-ops-worker.conf
```

Add configuration:

```ini
[program:geo-ops-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/geo-ops-platform/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/geo-ops-platform/backend/storage/logs/worker.log
stopwaitsecs=3600
```

Start workers:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start geo-ops-worker:*
```

### 6. Configure Scheduler

Add to crontab:

```bash
sudo crontab -e -u www-data
```

Add line:

```
* * * * * cd /var/www/geo-ops-platform/backend && php artisan schedule:run >> /dev/null 2>&1
```

### 7. Setup Monitoring

#### Install Laravel Telescope (Development only)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

#### Configure Log Rotation

```bash
sudo nano /etc/logrotate.d/geo-ops
```

Add:

```
/var/www/geo-ops-platform/backend/storage/logs/*.log {
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

## Mobile App Deployment

### 1. Environment Configuration

Create `.env` files:

```bash
cd frontend
cp .env.example .env
```

Update configuration:

```env
EXPO_PUBLIC_API_URL=https://api.geo-ops.lk/api
EXPO_PUBLIC_GOOGLE_MAPS_API_KEY=your_google_maps_key
EXPO_PUBLIC_APP_ENV=production
```

### 2. Install Dependencies

```bash
npm install
```

### 3. Build for Production

#### Android APK

```bash
# Build APK
eas build --platform android --profile production

# Or build AAB for Play Store
eas build --platform android --profile production --type app-bundle
```

#### iOS IPA

```bash
eas build --platform ios --profile production
```

### 4. Submit to App Stores

#### Google Play Store

1. Create app listing in Play Console
2. Upload AAB file
3. Complete store listing with screenshots
4. Submit for review

#### Apple App Store

1. Create app in App Store Connect
2. Upload IPA via Transporter or Xcode
3. Complete app metadata
4. Submit for review

### 5. Configure OTA Updates

```bash
# Publish update
eas update --branch production --message "Bug fixes and improvements"
```

## Database Backup

### Automated MySQL Backup

Create backup script:

```bash
sudo nano /usr/local/bin/backup-geo-ops-db.sh
```

Add script:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/mysql"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="geo-ops"
DB_USER="geo-ops"
DB_PASS="secure_password"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/geo-ops_$DATE.sql.gz

# Keep only last 7 days
find $BACKUP_DIR -name "geo-ops_*.sql.gz" -mtime +7 -delete
```

Make executable and schedule:

```bash
sudo chmod +x /usr/local/bin/backup-geo-ops-db.sh
sudo crontab -e
```

Add daily backup:

```
0 2 * * * /usr/local/bin/backup-geo-ops-db.sh
```

## Monitoring & Maintenance

### Health Checks

Create health check endpoint:

```php
// routes/api.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'cache' => Cache::has('health_check') ? 'working' : 'not working',
        'queue' => Queue::size() < 1000 ? 'healthy' : 'backlogged',
    ]);
});
```

### Monitor Logs

```bash
# Watch Laravel logs
tail -f /var/www/geo-ops-platform/backend/storage/logs/laravel.log

# Watch Nginx access logs
tail -f /var/log/nginx/access.log

# Watch Nginx error logs
tail -f /var/log/nginx/error.log

# Watch worker logs
tail -f /var/www/geo-ops-platform/backend/storage/logs/worker.log
```

### Performance Monitoring

Consider implementing:

- **New Relic** for application performance monitoring
- **Sentry** for error tracking
- **DataDog** for infrastructure monitoring
- **Pingdom** for uptime monitoring

## Scaling

### Database Optimization

1. **Read Replicas**: Set up MySQL read replicas for reporting queries
2. **Connection Pooling**: Use ProxySQL or similar
3. **Partitioning**: Partition large tables (tracking_logs) by date

### Application Scaling

1. **Horizontal Scaling**: Add more app servers behind load balancer
2. **Load Balancer**: Use Nginx, HAProxy, or AWS ELB
3. **Redis Cluster**: Scale Redis for caching and queues
4. **CDN**: Use CloudFlare or AWS CloudFront for static assets

### Queue Optimization

1. Multiple queue workers for different job types
2. Separate queue for high-priority jobs
3. Horizon for queue monitoring (Laravel package)

## Security Checklist

- [ ] Enable firewall (UFW)
- [ ] Configure fail2ban
- [ ] Regular security updates
- [ ] Strong database passwords
- [ ] JWT secret properly configured
- [ ] HTTPS enforced
- [ ] CORS properly configured
- [ ] Rate limiting enabled
- [ ] File upload validation
- [ ] SQL injection prevention (using ORM)
- [ ] XSS protection
- [ ] Regular backups verified
- [ ] Log monitoring in place

## Troubleshooting

### Common Issues

**Queue not processing:**

```bash
sudo supervisorctl restart geo-ops-worker:*
php artisan queue:restart
```

**Cache issues:**

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Permission errors:**

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Database connection issues:**

```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

## Update Procedure

### Backend Updates

```bash
cd /var/www/geo-ops-platform/backend

# Pull latest code
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart workers
sudo supervisorctl restart geo-ops-worker:*
```

### Mobile App Updates

```bash
cd frontend

# Pull latest code
git pull origin main

# Update dependencies
npm install

# Publish OTA update
eas update --branch production --message "Version x.x.x updates"
```

## Support Contacts

- **Development Team**: dev@geo-ops.lk
- **Server Admin**: admin@geo-ops.lk
- **Emergency**: +94 77 XXX XXXX

## Additional Resources

- Laravel Documentation: https://laravel.com/docs
- Expo Documentation: https://docs.expo.dev
- MySQL Documentation: https://dev.mysql.com/doc
- Nginx Documentation: https://nginx.org/en/docs
