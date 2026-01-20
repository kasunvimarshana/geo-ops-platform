#!/bin/bash

# Navigate to the mobile app directory
cd ../mobile

# Install dependencies
npm install

# Build the mobile app for production
eas build --platform all --profile production

# Notify user of successful deployment
echo "Mobile application deployed successfully!"