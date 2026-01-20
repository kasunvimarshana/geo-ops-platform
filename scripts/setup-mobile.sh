#!/bin/bash

# This script sets up the mobile application for the GeoOps Platform.

# Navigate to the mobile directory
cd mobile

# Install dependencies
npm install

# Create the necessary directories for the SQLite database
mkdir -p src/database/migrations
mkdir -p src/database/models

# Copy example environment file
cp .env.example .env

# Initialize the SQLite database
npm run setup:db

# Start the Expo development server
npm start

echo "Mobile setup completed successfully!"