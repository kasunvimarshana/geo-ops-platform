# Deployment Guide for GeoOps Platform

This document provides instructions for deploying the GeoOps Platform Platform, which consists of a Laravel backend and an Expo-based mobile frontend. Follow the steps below to ensure a successful deployment.

## Prerequisites

Before deploying, ensure you have the following:

- A server running Ubuntu 22.04 LTS or similar.
- Access to a MySQL or PostgreSQL database.
- PHP 8.2+ and Composer installed on the server.
- Node.js and npm installed for the mobile application.
- Nginx web server installed.
- SSL certificate (Let's Encrypt recommended) for secure connections.
- Supervisor installed for managing background processes.

## Backend Deployment

### Step 1: Clone the Repository

```bash
git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform/backend
```

### Step 2: Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

### Step 3: Configure Environment

Copy the example environment file and set your environment variables:

```bash
cp .env.example .env
```

Edit the `.env` file to configure your database and other settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
JWT_SECRET=your_jwt_secret
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Run Migrations and Seed Database

```bash
php artisan migrate --seed
```

### Step 6: Configure Nginx

Create a new Nginx configuration file for the application:

```nginx
server {
    listen 80;
    server_name your_domain.com;

    root /path/to/geo-ops-platform/backend/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Restart Nginx to apply the changes:

```bash
sudo systemctl restart nginx
```

### Step 7: Start Queue Workers

Configure Supervisor to manage queue workers. Create a new configuration file for the workers:

```ini
[program:geo-ops-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/geo-ops-platform/backend/artisan queue:work
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/geo-ops-platform/backend/storage/logs/worker.log
```

Start the Supervisor service:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start geo-ops-worker:*
```

## Mobile Deployment

### Step 1: Clone the Repository

If you haven't already, clone the repository:

```bash
git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform/mobile
```

### Step 2: Install Dependencies

```bash
npm install
```

### Step 3: Configure Environment

Copy the example environment file and set your environment variables:

```bash
cp .env.example .env
```

Edit the `.env` file to configure your API endpoint:

```env
API_URL=https://your_domain.com/api/v1
```

### Step 4: Build the Application

For production builds, use the following commands:

```bash
# For Android
eas build --platform android --profile production

# For iOS
eas build --platform ios --profile production
```

### Step 5: Deploy to App Stores

Follow the respective guidelines for deploying your Android and iOS applications to the Google Play Store and Apple App Store.

## Conclusion

Your GeoOps Platform is now deployed and ready for use. Ensure to monitor the application logs and performance regularly to maintain reliability and scalability. For further assistance, refer to the documentation or contact support.
