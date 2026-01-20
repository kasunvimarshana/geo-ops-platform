# Geo Ops Platform

A production-ready, end-to-end **GPS land measurement and field-service management application**. This platform includes a mobile frontend (React Native/Expo), backend APIs (Node.js/Express), PostgreSQL database, JWT authentication, billing, reporting, and deployment readiness.

## 🎯 Overview

Geo Ops Platform is designed for agricultural operations, machine rental businesses, and field service management. It enables:

- **GPS Land Measurement**: Walk-around and point-based polygon measurement
- **Field Work Management**: Job tracking, driver assignment, machine management
- **Financial Management**: Invoicing, expense tracking, payment management
- **Real-time Tracking**: Driver/operator GPS tracking
- **Subscription Management**: Package-based access control
- **Offline Support**: Work without internet, sync when online

## 🏗️ Architecture

### Technology Stack

#### Backend

- **Runtime**: Node.js 18+
- **Language**: TypeScript
- **Framework**: Express.js
- **Database**: PostgreSQL 15+
- **Authentication**: JWT
- **Validation**: Joi
- **Testing**: Jest

#### Mobile App

- **Framework**: React Native with Expo
- **Language**: TypeScript
- **Navigation**: Expo Router
- **State**: Zustand
- **Maps**: React Native Maps
- **Offline**: SQLite, AsyncStorage

### Architecture Principles

- Client-Server architecture
- RESTful APIs
- Stateless backend
- Role-based access control (RBAC)
- Offline-first mobile design
- Clean architecture (Controller → Service → Repository)

## 👥 User Roles

- **Admin**: Full system access
- **Owner**: Organization owner, manages machines, drivers, jobs
- **Driver/Operator**: View and update assigned jobs
- **Broker/Agent**: Create jobs, manage customers
- **Accountant**: Manage invoices and expenses

## 🗺️ Core Features

### 1. GPS Land Measurement

- Walk-around measurement using live GPS
- Point-to-point polygon drawing
- Auto area calculation (acres/hectares/square meters)
- Save measurement history
- View land plots on map
- Edit & re-measure

### 2. Map Visualization

- Google Maps / Mapbox integration
- Display measured lands
- Show completed jobs
- Track active drivers
- Color-coded layers

### 3. Job & Field Work Management

- Create and assign jobs
- Track job status (Pending, In-Progress, Completed)
- Assign driver & machine
- Attach measured land to job
- Job completion tracking

### 4. Driver/Broker Tracking

- Live GPS tracking
- Daily movement history
- Job-based tracking
- Distance & time calculation

### 5. Billing & Invoicing

- Auto invoice generation from land area
- Custom rate per acre/hectare
- PDF invoice export
- Invoice status tracking (Paid/Pending)
- Download & share invoices

### 6. Expense Management

- Fuel expenses
- Spare parts tracking
- Maintenance records
- Expense categorization
- Reports per machine/driver

### 7. Payments & Ledger

- Multiple payment methods (Cash, Bank, Digital)
- Customer balance tracking
- Income vs expense summary
- Monthly financial reports

### 8. Subscription & Packages

- Package-based access (Free/Basic/Pro)
- Limits on measurements, drivers, exports
- Package expiry handling

### 9. Offline Support

- Measure land without internet
- Store data locally
- Auto sync when online
- Conflict resolution

## 🚀 Getting Started

### Prerequisites

- Node.js >= 18.0.0
- PostgreSQL >= 13
- Docker & Docker Compose (optional)
- Expo CLI (for mobile development)

### Quick Start with Docker

```bash
# Clone the repository
git clone <repository-url>
cd geo-ops-platform

# Start all services
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f
```

The backend API will be available at `http://localhost:3000`

### Manual Setup

#### Backend Setup

```bash
cd backend

# Install dependencies
npm install

# Setup environment
cp .env.example .env
# Edit .env with your configuration

# Create database
createdb geo_ops_platform

# Run migrations
psql -d geo_ops_platform -f src/database/migrations/001_initial_schema.sql

# Start development server
npm run dev
```

#### Mobile App Setup

```bash
cd mobile

# Install dependencies
npm install

# Start Expo development server
npm start

# Run on iOS simulator
npm run ios

# Run on Android emulator
npm run android
```

## 📚 Documentation

### API Documentation

The backend provides the following API endpoints:

**Authentication**

- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `GET /api/v1/auth/profile` - Get user profile

**Land Measurements**

- `POST /api/v1/land-measurements` - Create measurement
- `GET /api/v1/land-measurements` - List all measurements
- `GET /api/v1/land-measurements/:id` - Get measurement by ID
- `PATCH /api/v1/land-measurements/:id` - Update measurement
- `DELETE /api/v1/land-measurements/:id` - Delete measurement

### Database Schema

The application uses the following main tables:

- `organizations` - Organization details
- `users` - User accounts with roles
- `machines` - Agricultural machines/equipment
- `customers` - Customer information
- `land_measurements` - GPS measurements with polygons
- `jobs` - Field service jobs
- `invoices` - Billing and invoices
- `payments` - Payment records
- `expenses` - Expense tracking
- `tracking_logs` - GPS tracking data

See `/backend/src/database/migrations/001_initial_schema.sql` for full schema.

## 🔐 Security

- Passwords hashed with bcrypt
- JWT token-based authentication
- Input validation with Joi
- SQL injection protection (parameterized queries)
- CORS protection
- Helmet security headers
- Rate limiting
- Role-based authorization

## 📦 Project Structure

```
geo-ops-platform/
├── backend/                 # Node.js/Express backend
│   ├── src/
│   │   ├── config/         # Configuration files
│   │   ├── controllers/    # Request handlers
│   │   ├── database/       # Migrations & seeders
│   │   ├── middleware/     # Express middleware
│   │   ├── routes/         # API routes
│   │   ├── services/       # Business logic
│   │   ├── types/          # TypeScript types
│   │   └── utils/          # Helper functions
│   ├── Dockerfile
│   └── package.json
├── mobile/                  # React Native/Expo mobile app
│   ├── app/                # Expo Router pages
│   ├── src/
│   │   ├── components/     # UI components
│   │   ├── services/       # API services
│   │   ├── store/          # State management
│   │   └── types/          # TypeScript types
│   └── package.json
├── docker-compose.yml       # Docker services
└── README.md               # This file
```

## 🧪 Testing

### Backend Tests

```bash
cd backend
npm test
npm run test:cov
```

### Mobile Tests

```bash
cd mobile
npm run lint
npm run type-check
```

## 🚢 Deployment

### Backend Deployment

1. Build Docker image:

```bash
cd backend
docker build -t geo-ops-backend .
```

2. Deploy to cloud (AWS, GCP, Azure, etc.)

### Mobile App Deployment

1. Build for iOS:

```bash
cd mobile
expo build:ios
```

2. Build for Android:

```bash
expo build:android
```

3. Submit to App Store / Play Store

## 🌍 Environment Variables

### Backend (.env)

```
NODE_ENV=production
PORT=3000
DB_HOST=localhost
DB_NAME=geo_ops_platform
DB_USER=postgres
DB_PASSWORD=your-password
JWT_SECRET=your-secret-key
```

### Mobile (.env)

```
EXPO_PUBLIC_API_URL=https://api.yourserver.com/api/v1
```

## 🤝 Contributing

This is a production application. Follow these guidelines:

1. Follow SOLID principles
2. Write clean, maintainable code
3. Add tests for new features
4. Update documentation
5. Follow existing code style

## 📄 License

Proprietary - All rights reserved

## 🆘 Support

For issues and questions:

- Check documentation in `/backend/README.md` and `/mobile/README.md`
- Review API documentation
- Check database schema

## 🎓 Credits

Built with best practices for:

- Mobile GIS applications
- GPS tracking systems
- Financial management
- Scalable SaaS platforms
- Agricultural field management

Designed for farmers and machine owners with reliability, offline usability, and long-term scalability in mind.
