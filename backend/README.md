# Geo Ops Platform - Backend API

Production-ready backend API for GPS land measurement and field service management.

## Features

- ✅ RESTful API with Express & TypeScript
- ✅ JWT authentication & role-based authorization
- ✅ PostgreSQL database with spatial data support
- ✅ GPS land measurement with area calculation
- ✅ Job management & tracking
- ✅ Invoice generation & payment tracking
- ✅ Expense management
- ✅ Clean architecture (Controller → Service → Repository)

## Technology Stack

- **Runtime**: Node.js 18+
- **Language**: TypeScript
- **Framework**: Express.js
- **Database**: PostgreSQL
- **Authentication**: JWT
- **Validation**: Joi
- **Testing**: Jest

## Prerequisites

- Node.js >= 18.0.0
- PostgreSQL >= 13
- npm or yarn

## Getting Started

### 1. Install Dependencies

```bash
npm install
```

### 2. Environment Setup

Copy the example environment file:

```bash
cp .env.example .env
```

Update the `.env` file with your configuration.

### 3. Database Setup

Create a PostgreSQL database:

```bash
createdb geo_ops_platform
```

Run migrations:

```bash
psql -d geo_ops_platform -f src/database/migrations/001_initial_schema.sql
```

### 4. Run Development Server

```bash
npm run dev
```

The API will be available at `http://localhost:3000/api/v1`

## API Endpoints

### Authentication

- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `GET /api/v1/auth/profile` - Get user profile (requires authentication)

### Land Measurements

- `POST /api/v1/land-measurements` - Create measurement
- `GET /api/v1/land-measurements` - Get all measurements
- `GET /api/v1/land-measurements/:id` - Get measurement by ID
- `PATCH /api/v1/land-measurements/:id` - Update measurement
- `DELETE /api/v1/land-measurements/:id` - Delete measurement

All land measurement endpoints require authentication.

## Scripts

- `npm run dev` - Start development server with hot reload
- `npm run build` - Build for production
- `npm start` - Start production server
- `npm test` - Run tests
- `npm run test:watch` - Run tests in watch mode
- `npm run test:cov` - Run tests with coverage
- `npm run lint` - Lint code

## Project Structure

```
backend/
├── src/
│   ├── config/           # Configuration files
│   ├── controllers/      # Request handlers
│   ├── database/         # Migrations & seeders
│   ├── middleware/       # Express middleware
│   ├── routes/           # API routes
│   ├── services/         # Business logic
│   ├── types/            # TypeScript types
│   ├── utils/            # Helper functions
│   ├── app.ts            # Express app setup
│   └── main.ts           # Entry point
├── dist/                 # Compiled JavaScript
├── tests/                # Test files
└── package.json
```

## Authentication

The API uses JWT tokens for authentication. Include the token in the `Authorization` header:

```
Authorization: Bearer <your-token>
```

## User Roles

- **Admin**: Full system access
- **Owner**: Organization owner, can manage machines, drivers, jobs
- **Driver**: Can view and update assigned jobs
- **Broker**: Can create jobs and manage customers
- **Accountant**: Can manage invoices and expenses

## Database Schema

The application uses PostgreSQL with the following main tables:

- `organizations` - Organization details
- `users` - User accounts
- `machines` - Agricultural machines/equipment
- `customers` - Customer information
- `land_measurements` - GPS measurements with polygons
- `jobs` - Field service jobs
- `invoices` - Billing and invoices
- `payments` - Payment records
- `expenses` - Expense tracking
- `tracking_logs` - GPS tracking data

## Error Handling

The API returns consistent error responses:

```json
{
  "status": "error",
  "message": "Error description"
}
```

HTTP status codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `500` - Internal Server Error

## Security

- Passwords hashed with bcrypt
- JWT tokens for authentication
- Input validation with Joi
- CORS protection
- Helmet security headers
- SQL injection protection with parameterized queries

## License

Proprietary
