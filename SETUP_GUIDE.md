# SETUP GUIDE for GeoOps Platform

## Overview

This guide provides step-by-step instructions for setting up the GeoOps Platform, which consists of a Laravel backend and a React Native mobile application. Follow the instructions carefully to ensure a successful setup.

---

## Prerequisites

Before you begin, ensure you have the following installed on your machine:

- PHP 8.2 or higher
- Composer
- Node.js (version 16 or higher)
- npm or Yarn
- MySQL 8.0 or PostgreSQL 15
- Git
- Expo CLI

---

## Backend Setup

### Step 1: Clone the Repository

```bash
git clone https://github.com/kasunvimarshana/geo-ops-platform.git
cd geo-ops-platform/backend
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Configure Environment Variables

Copy the example environment file and update the necessary configurations:

```bash
cp .env.example .env
```

Edit the `.env` file to set your database connection details and other environment variables.

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Set Up Database

Run the migrations to create the necessary tables in your database:

```bash
php artisan migrate --seed
```

### Step 6: Start the Backend Server

```bash
php artisan serve
```

The backend API will be available at `http://localhost:8000`.

---

## Mobile Setup

### Step 1: Navigate to the Mobile Directory

```bash
cd ../mobile
```

### Step 2: Install Dependencies

```bash
npm install
```

### Step 3: Configure Environment Variables

Copy the example environment file and update the necessary configurations:

```bash
cp .env.example .env
```

Edit the `.env` file to set your API base URL and other environment variables.

### Step 4: Start the Mobile Application

```bash
npm start
```

This will start the Expo development server. You can run the app on an emulator or a physical device using the Expo Go app.

---

## Additional Notes

- For detailed API documentation, refer to [API_SPECIFICATION.md](API_SPECIFICATION.md).
- For deployment instructions, see [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md).
- If you encounter any issues, please check the [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines on reporting problems.

---

## Conclusion

You have successfully set up the GeoOps Platform. You can now start using the application and contribute to its development. Happy coding!
