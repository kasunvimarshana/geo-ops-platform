# 🚀 Quick Start Guide for GeoOps Platform

Welcome to the GeoOps Platform! This guide will help you quickly set up and run the application for both the backend and mobile frontend.

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- PHP 8.2 or higher
- Composer
- Node.js (version 16 or higher)
- npm or yarn
- MySQL 8.0 or PostgreSQL 15
- Expo CLI (for mobile development)

## 🛠️ Backend Setup

1. **Clone the Repository**

   ```bash
   git clone https://github.com/kasunvimarshana/geo-ops-platform.git
   cd geo-ops-platform/backend
   ```

2. **Install Dependencies**

   ```bash
   composer install
   ```

3. **Configure Environment Variables**
   Copy the example environment file and set your database credentials:

   ```bash
   cp .env.example .env
   ```

   Edit the `.env` file to configure your database connection and other settings.

4. **Generate Application Key**

   ```bash
   php artisan key:generate
   ```

5. **Run Migrations**

   ```bash
   php artisan migrate --seed
   ```

6. **Start the Backend Server**

   ```bash
   php artisan serve
   ```

   The backend API will be available at `http://localhost:8000`.

## 📱 Mobile Setup

1. **Navigate to the Mobile Directory**

   ```bash
   cd ../mobile
   ```

2. **Install Dependencies**

   ```bash
   npm install
   ```

3. **Configure Environment Variables**
   Copy the example environment file:

   ```bash
   cp .env.example .env
   ```

   Edit the `.env` file to set the API URL to your backend server (e.g., `http://localhost:8000`).

4. **Start the Mobile Application**

   ```bash
   npm start
   ```

   This will open the Expo developer tools in your browser. You can run the app on an emulator or a physical device using the Expo Go app.

## 🎉 You're Ready to Go!

You now have the GeoOps Platform up and running! You can start testing the features and functionalities of the application.

For more detailed instructions, refer to the [SETUP_GUIDE.md](SETUP_GUIDE.md) and [DOCUMENTATION](docs/user-guide.md).

Happy farming! 🌾
