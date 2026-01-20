#!/bin/bash

# This script automates the setup of the backend application for the GeoOps Platform.

# Exit on any error
set -e

# Define the backend directory
BACKEND_DIR="./backend"

# Navigate to the backend directory
cd $BACKEND_DIR

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install

# Copy the environment file
echo "Setting up environment file..."
cp .env.example .env

# Generate application key
echo "Generating application key..."
php artisan key:generate

# Generate JWT secret
echo "Generating JWT secret..."
php artisan jwt:secret

# Run migrations and seed the database
echo "Running migrations and seeding the database..."
php artisan migrate --seed

# Start the Laravel development server
echo "Starting the Laravel development server..."
php artisan serve &

echo "Backend setup completed successfully!"