# Deployment Guide

## Production Deployment

### Prerequisites

- Server: Ubuntu 20.04+ or similar Linux distribution
- PHP 8.2+
- Composer 2.x
- Node.js 18+ (for building frontend)
- MySQL 8.0+ or PostgreSQL 14+ with PostGIS extension
- Redis 6.0+
- Nginx or Apache
- SSL certificate (Let's Encrypt recommended)

### Backend Deployment (Laravel)

#### 1. Server Setup

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-mysql \
  php8.2-pgsql php8.2-redis php8.2-xml php8.2-mbstring \
  php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx

# Install MySQL or PostgreSQL
sudo apt install -y mysql-server
# OR
sudo apt install -y postgresql postgresql-contrib postgis

# Install Redis
sudo apt install -y redis-server

# Install Supervisor (for queue workers)
sudo apt install -y supervisor
```

#### 2. Clone and Configure Backend

```bash
# Clone repository
cd /var/www
sudo git clone <repository-url> geoops
cd geoops/backend

# Set permissions
sudo chown -R www-data:www-data /var/www/geoops
sudo chmod -R 755 /var/www/geoops

# Install dependencies
composer install --optimize-autoloader --no-dev

# Configure environment
cp .env.example .env
nano .env  # Edit with production values

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=PermissionSeeder

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Create storage link
php artisan storage:link
```

#### 3. Database Setup

**For PostgreSQL with PostGIS:**
```sql
-- Create database
CREATE DATABASE geoops;

-- Create user
CREATE USER geoops_user WITH PASSWORD 'secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE geoops TO geoops_user;

-- Enable PostGIS extension
\c geoops
CREATE EXTENSION IF NOT EXISTS postgis;
```

**For MySQL:**
```sql
-- Create database
CREATE DATABASE geoops CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'geoops_user'@'localhost' IDENTIFIED BY 'secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON geoops.* TO 'geoops_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 4. Nginx Configuration

Create `/etc/nginx/sites-available/geoops`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name api.geoops.lk;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.geoops.lk;
    
    root /var/www/geoops/backend/public;
    index index.php;
    
    # SSL Configuration (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/api.geoops.lk/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.geoops.lk/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Increase body size for file uploads
    client_max_body_size 20M;
    
    # Logging
    access_log /var/log/nginx/geoops-access.log;
    error_log /var/log/nginx/geoops-error.log;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/geoops /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 5. SSL Certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d api.geoops.lk
```

#### 6. Queue Workers (Supervisor)

Create `/etc/supervisor/conf.d/geoops-worker.conf`:

```ini
[program:geoops-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/geoops/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/geoops/backend/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start geoops-worker:*
```

#### 7. Scheduled Tasks (Cron)

Add to crontab:
```bash
sudo crontab -e -u www-data
```

Add line:
```
* * * * * cd /var/www/geoops/backend && php artisan schedule:run >> /dev/null 2>&1
```

#### 8. Production Environment Variables

Update `.env` with production values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.geoops.lk

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=geoops
DB_USERNAME=geoops_user
DB_PASSWORD=<secure_password>

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=<redis_password>
REDIS_PORT=6379

# Configure real mail provider
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=<email>
MAIL_PASSWORD=<password>
MAIL_ENCRYPTION=tls

# AWS S3 for file storage
AWS_ACCESS_KEY_ID=<key>
AWS_SECRET_ACCESS_KEY=<secret>
AWS_DEFAULT_REGION=ap-south-1
AWS_BUCKET=geoops-production

# Production API keys
GOOGLE_MAPS_API_KEY=<key>
MAPBOX_ACCESS_TOKEN=<token>
SMS_API_KEY=<key>
STRIPE_SECRET=<key>

# Security
JWT_SECRET=<generate_secure_secret>
API_RATE_LIMIT=100

# Monitoring
SENTRY_LARAVEL_DSN=<dsn>
```

---

### Frontend Deployment (React Native Expo)

#### 1. Development Setup

```bash
cd frontend

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Update with production API URL
EXPO_PUBLIC_API_URL=https://api.geoops.lk/api/v1
```

#### 2. Build for Production

**Install EAS CLI:**
```bash
npm install -g eas-cli
eas login
```

**Configure EAS:**
```bash
eas build:configure
```

**Build for Android:**
```bash
# Build APK for testing
eas build --platform android --profile preview

# Build AAB for Play Store
eas build --platform android --profile production
```

**Build for iOS:**
```bash
# Build for TestFlight
eas build --platform ios --profile preview

# Build for App Store
eas build --platform ios --profile production
```

#### 3. App Store Submission

**Google Play Store:**
1. Go to Google Play Console
2. Create new app
3. Upload AAB file
4. Fill app details, screenshots, descriptions
5. Set pricing and distribution
6. Submit for review

**Apple App Store:**
1. Go to App Store Connect
2. Create new app
3. Upload IPA via Xcode or Transporter
4. Fill app details, screenshots, descriptions
5. Submit for review

#### 4. Over-The-Air (OTA) Updates

```bash
# Publish update
eas update --branch production --message "Bug fixes and improvements"
```

---

### Docker Deployment (Alternative)

#### Docker Compose Setup

Create `docker-compose.yml`:

```yaml
version: '3.8'

services:
  app:
    build:
      context: ./backend
      dockerfile: Dockerfile
    container_name: geoops-app
    restart: unless-stopped
    working_dir: /var/www/backend
    volumes:
      - ./backend:/var/www/backend
    environment:
      - APP_ENV=production
    networks:
      - geoops-network
    depends_on:
      - db
      - redis

  nginx:
    image: nginx:alpine
    container_name: geoops-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./backend:/var/www/backend
      - ./nginx/conf.d:/etc/nginx/conf.d
      - ./nginx/ssl:/etc/nginx/ssl
    networks:
      - geoops-network
    depends_on:
      - app

  db:
    image: postgis/postgis:14-3.3
    container_name: geoops-db
    restart: unless-stopped
    environment:
      POSTGRES_DB: geoops
      POSTGRES_USER: geoops_user
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - dbdata:/var/lib/postgresql/data
    networks:
      - geoops-network

  redis:
    image: redis:7-alpine
    container_name: geoops-redis
    restart: unless-stopped
    command: redis-server --requirepass ${REDIS_PASSWORD}
    volumes:
      - redisdata:/data
    networks:
      - geoops-network

  queue:
    build:
      context: ./backend
      dockerfile: Dockerfile
    container_name: geoops-queue
    restart: unless-stopped
    command: php artisan queue:work --tries=3
    volumes:
      - ./backend:/var/www/backend
    networks:
      - geoops-network
    depends_on:
      - app
      - redis

networks:
  geoops-network:
    driver: bridge

volumes:
  dbdata:
  redisdata:
```

**Deploy with Docker:**
```bash
# Build and start containers
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Seed database
docker-compose exec app php artisan db:seed
```

---

### Monitoring & Maintenance

#### 1. Application Monitoring

**Install Sentry:**
```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=<your-dsn>
```

**Server Monitoring:**
- Install monitoring tools (New Relic, Datadog, or custom)
- Monitor CPU, RAM, Disk usage
- Set up alerts for downtime

#### 2. Backup Strategy

**Database Backup:**
```bash
# PostgreSQL
pg_dump -U geoops_user geoops > backup_$(date +%Y%m%d).sql

# MySQL
mysqldump -u geoops_user -p geoops > backup_$(date +%Y%m%d).sql
```

**Automated Backups:**
Add to cron:
```bash
0 2 * * * /usr/local/bin/backup-database.sh
0 3 * * * /usr/local/bin/backup-files.sh
```

#### 3. Log Management

**Rotate logs:**
Create `/etc/logrotate.d/geoops`:
```
/var/www/geoops/backend/storage/logs/*.log {
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

#### 4. Performance Optimization

```bash
# Enable OPcache
sudo nano /etc/php/8.2/fpm/php.ini

# Add/uncomment:
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.validate_timestamps=0
```

#### 5. Security Hardening

```bash
# Firewall rules
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Disable root SSH login
sudo nano /etc/ssh/sshd_config
# Set: PermitRootLogin no

# Install fail2ban
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

---

### CI/CD Pipeline (GitHub Actions)

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Deploy to Server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.SERVER_HOST }}
        username: ${{ secrets.SERVER_USER }}
        key: ${{ secrets.SSH_PRIVATE_KEY }}
        script: |
          cd /var/www/geoops
          git pull origin main
          cd backend
          composer install --no-dev --optimize-autoloader
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo supervisorctl restart geoops-worker:*
```

---

## Development Setup

### Backend Local Setup

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate keys
php artisan key:generate
php artisan jwt:secret

# Setup database
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

### Frontend Local Setup

```bash
cd frontend

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Start Expo development server
npx expo start
```

---

## Production Checklist

- [ ] Environment variables configured
- [ ] Database migrations run
- [ ] SSL certificate installed
- [ ] Queue workers running
- [ ] Cron jobs configured
- [ ] File storage (S3) configured
- [ ] Email service configured
- [ ] SMS service configured
- [ ] Payment gateway configured
- [ ] Monitoring tools installed
- [ ] Backup system configured
- [ ] Firewall rules set
- [ ] Security headers configured
- [ ] Error tracking enabled
- [ ] Performance optimization applied
- [ ] Documentation updated

---

This deployment guide ensures a secure, scalable, and maintainable production environment for the Geo Ops Platform.
