# Production Deployment Guide

This guide provides comprehensive instructions for deploying the GeoOps Platform to production.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Docker Deployment (Recommended)](#docker-deployment-recommended)
3. [Manual Deployment](#manual-deployment)
4. [Mobile App Deployment](#mobile-app-deployment)
5. [Post-Deployment](#post-deployment)
6. [Monitoring and Maintenance](#monitoring-and-maintenance)

## Prerequisites

### Server Requirements

- Ubuntu 22.04 LTS or similar Linux distribution
- Minimum 2GB RAM (4GB+ recommended for production)
- 20GB+ storage
- Domain name with SSL certificate
- Docker and Docker Compose (for Docker deployment)
- PHP 8.3+ (for manual deployment)
- MySQL 8.0+ or PostgreSQL 13+
- Redis (recommended)
- Nginx or Apache (for manual deployment)
- Node.js 18+ (for mobile app builds)

### Services Required

- Expo Application Services (EAS) account for mobile builds
- Cloud storage (optional - AWS S3, Google Cloud Storage)
- Email service (optional - SendGrid, Mailgun)
- Payment gateway (optional - Stripe, PayPal)

---

## Docker Deployment (Recommended)

Docker provides the easiest and most reliable deployment method.

### 1. Install Docker

```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose -y

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker
```

### 2. Clone Repository

```bash
git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform
```

### 3. Configure Environment

```bash
# Copy and edit environment file
cp .env.docker.example .env.docker
nano .env.docker

# Generate secure passwords
MYSQL_ROOT_PASSWORD=$(openssl rand -base64 32)
DB_PASSWORD=$(openssl rand -base64 32)

# Update .env.docker with generated passwords
```

### 4. Configure Backend

```bash
cd backend
cp .env.example .env
nano .env

# Update these values:
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://your-domain.com
# DB_CONNECTION=mysql
# DB_HOST=mysql
# DB_DATABASE=geo-ops
# DB_USERNAME=geo-ops_user
# DB_PASSWORD=your-secure-password
# CORS_ALLOWED_ORIGINS=https://your-domain.com,https://app.your-domain.com
```

### 5. Start Services

```bash
# Return to root directory
cd ..

# Start all services
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f backend
```

### 6. Initialize Application

```bash
# Generate application key
docker-compose exec backend php artisan key:generate

# Generate JWT secret
docker-compose exec backend php artisan jwt:secret

# Run migrations
docker-compose exec backend php artisan migrate --force

# Create admin user (optional)
docker-compose exec backend php artisan tinker
>>> $user = App\Models\User::create([
...   'name' => 'Admin',
...   'email' => 'admin@example.com',
...   'password' => bcrypt('secure-password'),
...   'role' => 'admin'
... ]);
>>> exit
```

### 7. Configure SSL (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal is configured automatically
sudo certbot renew --dry-run
```

---

## Manual Deployment

### 1. Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3 and extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip \
  php8.3-gd php8.3-redis -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install Redis
sudo apt install redis-server -y
sudo systemctl enable redis-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install nginx -y
```

### 2. Configure MySQL

```bash
# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE geo-ops CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'geo-ops'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON geo-ops.* TO 'geo-ops'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Deploy Laravel Application

```bash
# Create application directory
sudo mkdir -p /var/www/geo-ops
cd /var/www/geo-ops

# Clone repository (or upload files)
git clone https://github.com/kasunvimarshana/geo-ops-platform.git .

# Navigate to backend
cd backend

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set permissions
sudo chown -R www-data:www-data /var/www/geo-ops
sudo chmod -R 755 /var/www/geo-ops
sudo chmod -R 775 /var/www/geo-ops/backend/storage
sudo chmod -R 775 /var/www/geo-ops/backend/bootstrap/cache

# Copy and configure .env
cp .env.example .env
nano .env  # Edit with production values
```

### 4. Configure .env for Production

```env
APP_NAME="GeoOps Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geo-ops
DB_USERNAME=geo-ops
DB_PASSWORD=strong_password_here

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Generate these keys
JWT_SECRET=  # Will be generated
APP_KEY=     # Will be generated
```

### 5. Run Setup Commands

```bash
# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Run migrations
php artisan migrate --force

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 6. Configure Nginx

Create Nginx configuration:

```bash
sudo nano /etc/nginx/sites-available/geo-ops
```

Add configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/geo-ops/backend/public;

    index index.php;

    # SSL configuration
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Logging
    access_log /var/log/nginx/geo-ops_access.log;
    error_log /var/log/nginx/geo-ops_error.log;

    # PHP-FPM configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site and restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/geo-ops /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 7. Setup SSL with Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### 8. Setup Queue Worker

Create systemd service:

```bash
sudo nano /etc/systemd/system/geo-ops-queue.service
```

Add configuration:

```ini
[Unit]
Description=GeoOps Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/geo-ops/backend
ExecStart=/usr/bin/php /var/www/geo-ops/backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always

[Install]
WantedBy=multi-user.target
```

Enable and start service:

```bash
sudo systemctl daemon-reload
sudo systemctl enable geo-ops-queue
sudo systemctl start geo-ops-queue
```

### 9. Setup Cron Jobs

```bash
sudo crontab -e -u www-data
```

Add Laravel scheduler:

```cron
* * * * * cd /var/www/geo-ops/backend && php artisan schedule:run >> /dev/null 2>&1
```

## Mobile App Deployment

### 1. Install EAS CLI

```bash
npm install -g eas-cli
eas login
```

### 2. Configure EAS

```bash
cd mobile
eas build:configure
```

### 3. Update Production API URL

Edit `mobile/.env.production`:

```env
EXPO_PUBLIC_API_URL=https://your-domain.com/api/v1
```

### 4. Build for iOS

```bash
# Development build
eas build --profile development --platform ios

# Production build
eas build --profile production --platform ios
```

### 5. Build for Android

```bash
# Development build
eas build --profile development --platform android

# Production build
eas build --profile production --platform android
```

### 6. Submit to App Stores

```bash
# iOS
eas submit --platform ios

# Android
eas submit --platform android
```

## Monitoring & Maintenance

### 1. Setup Log Rotation

```bash
sudo nano /etc/logrotate.d/geo-ops
```

```
/var/www/geo-ops/backend/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

### 2. Database Backups

Create backup script:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/geo-ops"
mkdir -p $BACKUP_DIR

mysqldump -u geo-ops -p geo-ops > $BACKUP_DIR/geo-ops_$DATE.sql
gzip $BACKUP_DIR/geo-ops_$DATE.sql

# Keep only last 30 days
find $BACKUP_DIR -type f -mtime +30 -delete
```

Schedule with cron:

```cron
0 2 * * * /path/to/backup-script.sh
```

### 3. Monitoring

- Setup uptime monitoring (UptimeRobot, Pingdom)
- Configure error tracking (Sentry, Bugsnag)
- Monitor server resources (New Relic, DataDog)

### 4. Performance Optimization

```bash
# Enable OPcache
sudo nano /etc/php/8.3/fpm/php.ini

# Add/update:
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

## Security Checklist

- [ ] SSL certificate installed and auto-renewal configured
- [ ] Firewall configured (UFW)
- [ ] SSH key-based authentication only
- [ ] Regular security updates
- [ ] Database password is strong and secure
- [ ] JWT secret is properly generated
- [ ] APP_DEBUG=false in production
- [ ] File permissions are correct (755/644)
- [ ] `.env` file is not accessible via web
- [ ] Database backups are automated
- [ ] Error reporting configured to log only
- [ ] Rate limiting enabled on API endpoints
- [ ] CORS properly configured
- [ ] Security headers added to Nginx

## Troubleshooting

### Queue Not Processing

```bash
sudo systemctl status geo-ops-queue
sudo systemctl restart geo-ops-queue
sudo journalctl -u geo-ops-queue -f
```

### 500 Internal Server Error

```bash
# Check Laravel logs
tail -f /var/www/geo-ops/backend/storage/logs/laravel.log

# Check Nginx logs
tail -f /var/log/nginx/geo-ops_error.log

# Check PHP-FPM logs
tail -f /var/log/php8.3-fpm.log
```

### Database Connection Issues

```bash
# Test database connection
mysql -u geo-ops -p geo-ops

# Restart MySQL
sudo systemctl restart mysql
```

## Rollback Procedure

If deployment fails:

```bash
# Revert to previous code version
git checkout <previous-commit>

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

## Support

For production support:

- Check documentation: https://github.com/kasunvimarshana/geo-ops-platform
- Report issues: https://github.com/kasunvimarshana/geo-ops-platform/issues
