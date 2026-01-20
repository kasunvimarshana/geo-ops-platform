#!/bin/bash

# Navigate to the backend directory
cd backend

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Run database migrations
php artisan migrate --force

# Seed the database
php artisan db:seed --force

# Clear and cache the configuration
php artisan config:cache

# Clear and cache the routes
php artisan route:cache

# Restart the queue workers
sudo supervisorctl restart geo-ops-worker:*

# Restart the web server
sudo systemctl restart nginx

echo "Backend deployment completed successfully."