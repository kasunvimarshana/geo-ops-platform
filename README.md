# Geo Ops Platform

**GPS Land Measurement & Field Service Management Application**

A production-ready, full-stack platform for GPS land measurement and field service management. Built with Node.js, TypeScript, PostgreSQL, React Native, and Expo.

---

## 🎯 Project Status: ✅ Core Implementation Complete

This repository contains a **fully functional, production-ready foundation** for a GPS land measurement and field service management platform.

### What's Implemented

✅ **Backend API** (Node.js/Express/TypeScript)

- Complete authentication system with JWT
- Land measurement API with GPS coordinates
- Improved area calculation (spherical excess formula)
- PostgreSQL database with complete schema
- Clean architecture with SOLID principles
- Docker deployment ready

✅ **Mobile App** (React Native/Expo)

- Authentication screens (login/register)
- GPS measurement interface
- Measurement history
- User profile management
- State management with Zustand
- API integration with Axios

✅ **Database** (PostgreSQL)

- 10 tables with proper relationships
- Support for organizations, users, measurements, jobs, invoices, payments, expenses
- Spatial data storage with JSONB
- Comprehensive indexes

✅ **Infrastructure**

- Docker Compose for easy deployment
- Environment configuration
- Database migrations
- Comprehensive documentation

---

## 🚀 Quick Start

### Using Docker (Recommended)

```bash
# Clone the repository
git clone <repository-url>
cd geo-ops-platform

# Start all services
docker-compose up -d

# Backend API: http://localhost:3000
# Database: localhost:5432
```

### Manual Setup

**Backend:**

```bash
cd backend
npm install
cp .env.example .env
createdb geo_ops_platform
psql -d geo_ops_platform -f src/database/migrations/001_initial_schema.sql
npm run dev
```

**Mobile:**

```bash
cd mobile
npm install
npm start
# Scan QR code or press 'i' for iOS, 'a' for Android
```

See [`QUICKSTART.md`](QUICKSTART.md) for detailed setup instructions.

---

## 📚 Documentation

| Document                               | Description                                   |
| -------------------------------------- | --------------------------------------------- |
| [QUICKSTART.md](QUICKSTART.md)         | Step-by-step setup guide                      |
| [PROJECT_README.md](PROJECT_README.md) | Comprehensive project documentation           |
| [ARCHITECTURE.md](ARCHITECTURE.md)     | System architecture and design patterns       |
| [API_REFERENCE.md](API_REFERENCE.md)   | Complete API documentation with examples      |
| [SUMMARY.md](SUMMARY.md)               | Implementation summary and status             |
| [NOTES.md](NOTES.md)                   | Known issues, accuracy notes, recommendations |
| [backend/README.md](backend/README.md) | Backend-specific documentation                |
| [mobile/README.md](mobile/README.md)   | Mobile app documentation                      |

---

## 🏗️ Architecture

### Technology Stack

**Backend:**

- Node.js 18+ with TypeScript
- Express.js framework
- PostgreSQL 15+ database
- JWT authentication
- Joi validation
- Jest testing framework

**Mobile:**

- React Native with Expo
- TypeScript
- Expo Router navigation
- Zustand state management
- Axios for API calls
- AsyncStorage for offline data

**Infrastructure:**

- Docker & Docker Compose
- PostgreSQL with spatial extensions
- RESTful API architecture

### System Components

```
┌─────────────────────────────────────┐
│     Mobile App (React Native)       │
│  ┌─────────────────────────────┐   │
│  │  Auth, Measure, History,     │   │
│  │  Profile Screens             │   │
│  └─────────────────────────────┘   │
└──────────────┬──────────────────────┘
               │ REST API (HTTPS)
┌──────────────▼──────────────────────┐
│    Backend API (Express/Node.js)    │
│  ┌─────────────────────────────┐   │
│  │  Controllers → Services      │   │
│  │  → Repository Pattern        │   │
│  └─────────────────────────────┘   │
└──────────────┬──────────────────────┘
               │ SQL
┌──────────────▼──────────────────────┐
│      PostgreSQL Database            │
│  ┌─────────────────────────────┐   │
│  │  10 Tables with Relations    │   │
│  │  Spatial Data (JSONB)        │   │
│  └─────────────────────────────┘   │
└─────────────────────────────────────┘
```

---

## 🔐 Features

### Implemented ✅

#### Authentication & Authorization

- User registration and login
- JWT token-based authentication
- Role-based access control (Admin, Owner, Driver, Broker, Accountant)
- Organization-based data isolation
- Secure password hashing with bcrypt

#### GPS Land Measurement

- Create measurements from GPS coordinates
- Automatic area calculation with spherical excess formula
- Support for multiple units (acres, hectares, square meters)
- List, view, update, and delete measurements
- Search functionality
- Metadata storage for custom fields

#### Database Schema

- Organizations, users, machines, customers
- Land measurements with GPS polygons
- Jobs, invoices, payments, expenses
- GPS tracking logs
- Proper indexes and relationships

#### Security

- JWT authentication
- Input validation with Joi
- SQL injection protection
- CORS protection
- Helmet security headers
- Rate limiting

#### Mobile Experience

- Modern, clean UI design
- Tab-based navigation
- Authentication flow
- GPS measurement with **live map visualization**
- **Interactive maps with polygon rendering**
- **Map previews in history view**
- Real-time coordinate tracking
- History viewing
- Profile management

### Planned 🔜

- Job management API
- Invoice generation & PDF export
- Expense tracking API
- Payment processing
- Real-time GPS tracking
- ~~Map visualization with polygons~~ ✅ **COMPLETED**
- Offline sync with SQLite
- Multi-language support (Sinhala)
- Push notifications
- Advanced reporting

---

## 📊 API Endpoints

### Authentication

- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `GET /api/v1/auth/profile` - Get user profile (requires auth)

### Land Measurements

- `POST /api/v1/land-measurements` - Create measurement (requires auth)
- `GET /api/v1/land-measurements` - List measurements (requires auth)
- `GET /api/v1/land-measurements/:id` - Get by ID (requires auth)
- `PATCH /api/v1/land-measurements/:id` - Update (requires auth)
- `DELETE /api/v1/land-measurements/:id` - Delete (requires auth)

See [API_REFERENCE.md](API_REFERENCE.md) for complete documentation.

---

## 👥 User Roles

| Role           | Permissions                                  |
| -------------- | -------------------------------------------- |
| **Admin**      | Full system access                           |
| **Owner**      | Manage organization, machines, drivers, jobs |
| **Driver**     | View and update assigned jobs                |
| **Broker**     | Create jobs, manage customers                |
| **Accountant** | Manage invoices, expenses, payments          |

---

## 🗄️ Database Schema

### Main Tables

1. **organizations** - Company information
2. **users** - User accounts with roles
3. **machines** - Agricultural equipment
4. **customers** - Customer records
5. **land_measurements** - GPS polygons
6. **jobs** - Field service jobs
7. **invoices** - Billing records
8. **payments** - Payment transactions
9. **expenses** - Expense tracking
10. **tracking_logs** - GPS tracking data

See the migration file in `backend/src/database/migrations/001_initial_schema.sql`

---

## 🧪 Development

### Backend Development

```bash
cd backend
npm run dev        # Start dev server
npm test           # Run tests
npm run lint       # Lint code
npm run build      # Build for production
```

### Mobile Development

```bash
cd mobile
npm start          # Start Expo
npm run ios        # Run on iOS
npm run android    # Run on Android
npm run lint       # Lint code
```

---

## 🔒 Security

The platform implements industry-standard security practices:

- JWT token authentication with expiry
- Password hashing with bcrypt (10 rounds)
- Input validation and sanitization
- SQL injection protection (parameterized queries)
- CORS protection
- Security headers with Helmet
- Rate limiting
- Role-based authorization
- Organization data isolation

---

## 📈 Scalability

Designed for growth:

- Stateless backend (horizontal scaling ready)
- Database connection pooling
- Efficient indexes on all foreign keys
- Pagination on list endpoints
- JSONB for flexible schema evolution
- Docker containerization
- Clean architecture for maintainability

---

## 🤝 Contributing

This project follows industry best practices:

- TypeScript for type safety
- Clean architecture (SOLID principles)
- Comprehensive error handling
- Input validation
- Consistent code style (ESLint)
- Detailed documentation

---

## 📄 License

Proprietary - All rights reserved

---

## 🎓 Credits

Built following best practices for:

- Mobile GIS applications
- GPS tracking systems
- Financial management systems
- Scalable SaaS platforms
- Agricultural field management

Designed for reliability, offline usability, and long-term scalability.

---

## 🆘 Support

For help getting started:

1. Read [QUICKSTART.md](QUICKSTART.md) for setup
2. Check [API_REFERENCE.md](API_REFERENCE.md) for API docs
3. Review [ARCHITECTURE.md](ARCHITECTURE.md) for system design
4. See [NOTES.md](NOTES.md) for known issues and recommendations

---

**Built with ❤️ for farmers and agricultural operations**
