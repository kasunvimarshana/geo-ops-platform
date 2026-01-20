# System Architecture

## Overview

Geo Ops Platform is a full-stack GPS land measurement and field-service management application designed with scalability, reliability, and offline-first capabilities.

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────┐              ┌──────────────────┐         │
│  │  React Native     │              │   Web App        │         │
│  │  Mobile App       │◄────────────►│   (Future)       │         │
│  │  (iOS/Android)    │              │                  │         │
│  └────────┬──────────┘              └──────────────────┘         │
│           │                                                       │
└───────────┼───────────────────────────────────────────────────────┘
            │
            │ HTTPS/REST API
            │
┌───────────▼───────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                            │
├───────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │              Express.js API Server                          │ │
│  │                                                             │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │ │
│  │  │ Auth         │  │ Measurement  │  │ Job          │    │ │
│  │  │ Controller   │  │ Controller   │  │ Controller   │    │ │
│  │  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘    │ │
│  │         │                  │                  │            │ │
│  │  ┌──────▼───────┐  ┌──────▼───────┐  ┌──────▼───────┐    │ │
│  │  │ Auth         │  │ Measurement  │  │ Job          │    │ │
│  │  │ Service      │  │ Service      │  │ Service      │    │ │
│  │  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘    │ │
│  │         │                  │                  │            │ │
│  │  ┌──────▼──────────────────▼──────────────────▼───────┐   │ │
│  │  │           Repository Layer                          │   │ │
│  │  │         (Database Access Layer)                     │   │ │
│  │  └─────────────────────────────────────────────────────┘   │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
└───────────┬───────────────────────────────────────────────────────┘
            │
            │ PostgreSQL Protocol
            │
┌───────────▼───────────────────────────────────────────────────────┐
│                       DATA LAYER                                  │
├───────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │              PostgreSQL Database                            │ │
│  │                                                             │ │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │ │
│  │  │  Users   │  │   Land   │  │   Jobs   │  │ Invoices │  │ │
│  │  │          │  │Measurem. │  │          │  │          │  │ │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │ │
│  │                                                             │ │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │ │
│  │  │Machines  │  │Customers │  │ Expenses │  │ Tracking │  │ │
│  │  │          │  │          │  │          │  │   Logs   │  │ │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                                                                   │
└───────────────────────────────────────────────────────────────────┘
```

## Component Architecture

### Backend Architecture

```
Backend (Node.js + Express)
│
├── API Layer (Express Router)
│   ├── Route Definitions
│   ├── Input Validation (Joi)
│   └── Authentication Middleware (JWT)
│
├── Controller Layer
│   ├── Request Handling
│   ├── Response Formatting
│   └── Error Handling
│
├── Service Layer (Business Logic)
│   ├── Authentication Service
│   ├── Land Measurement Service
│   ├── Job Management Service
│   ├── Invoice Service
│   ├── Payment Service
│   └── Expense Service
│
├── Repository Layer (Data Access)
│   └── PostgreSQL Queries
│
└── Database Layer
    └── PostgreSQL with Spatial Extensions
```

### Mobile Architecture

```
Mobile App (React Native + Expo)
│
├── Presentation Layer
│   ├── Screens (Expo Router)
│   │   ├── Auth Screens
│   │   ├── Dashboard
│   │   ├── Measurement Screen
│   │   ├── Job Management
│   │   └── Profile
│   │
│   └── Components
│       ├── UI Components
│       ├── Map Components
│       └── Form Components
│
├── State Management (Zustand)
│   ├── Auth Store
│   ├── Measurement Store
│   ├── Job Store
│   └── UI Store
│
├── Service Layer
│   ├── API Service (Axios)
│   ├── Auth Service
│   ├── Measurement Service
│   ├── GPS Service
│   └── Storage Service
│
└── Data Layer
    ├── AsyncStorage (Token, User Data)
    ├── SQLite (Offline Data)
    └── Local File System (PDFs, Images)
```

## Data Flow

### Authentication Flow

```
1. User enters credentials
   ↓
2. Mobile app sends to /api/v1/auth/login
   ↓
3. Backend validates credentials
   ↓
4. Backend generates JWT token
   ↓
5. Token returned to mobile app
   ↓
6. Token stored in AsyncStorage
   ↓
7. Token included in all subsequent requests
```

### Land Measurement Flow

```
1. User starts measurement
   ↓
2. GPS coordinates collected at intervals
   ↓
3. Coordinates stored locally (offline support)
   ↓
4. User stops measurement
   ↓
5. Area calculated using Shoelace formula
   ↓
6. Data sent to /api/v1/land-measurements (when online)
   ↓
7. Backend validates and saves to PostgreSQL
   ↓
8. Response sent back to mobile
   ↓
9. Local data marked as synced
```

### Offline Sync Flow

```
1. App detects offline state
   ↓
2. Data operations stored in local queue (SQLite)
   ↓
3. App detects online state
   ↓
4. Queue processed sequentially
   ↓
5. Each operation sent to backend
   ↓
6. On success, local data updated
   ↓
7. On conflict, conflict resolution applied
```

## Database Schema

### Core Tables

1. **organizations**
   - Organization/company details
   - Subscription information

2. **users**
   - User accounts
   - Authentication data
   - Role assignments

3. **land_measurements**
   - GPS coordinates (JSONB)
   - Calculated area
   - Metadata

4. **jobs**
   - Job details
   - Status tracking
   - Assignments

5. **invoices**
   - Billing information
   - Payment status

6. **expenses**
   - Expense records
   - Categorization

7. **tracking_logs**
   - GPS tracking data
   - Time series data

### Relationships

```
organizations ─┬─ users
               ├─ machines
               ├─ customers
               ├─ jobs
               ├─ invoices
               └─ expenses

users ─┬─ land_measurements
       ├─ tracking_logs (as driver)
       └─ jobs (as driver)

land_measurements ─── jobs

jobs ─┬─ invoices
      └─ tracking_logs

invoices ─── payments
```

## API Design

### RESTful Principles

- Resource-based URLs
- HTTP methods (GET, POST, PATCH, DELETE)
- Stateless requests
- JSON request/response
- Standard HTTP status codes

### Authentication

- JWT Bearer tokens
- Token in Authorization header
- Role-based access control

### Response Format

```json
{
  "status": "success",
  "data": { ... }
}
```

### Error Format

```json
{
  "status": "error",
  "message": "Error description"
}
```

## Security Architecture

### Backend Security

1. **Authentication**
   - JWT tokens with expiry
   - Secure password hashing (bcrypt)
   - Token refresh mechanism

2. **Authorization**
   - Role-based access control
   - Resource ownership validation
   - Organization data isolation

3. **Data Protection**
   - SQL injection prevention (parameterized queries)
   - XSS prevention
   - CORS configuration
   - Rate limiting
   - Helmet security headers

4. **Input Validation**
   - Schema validation (Joi)
   - Type checking (TypeScript)
   - Sanitization

### Mobile Security

1. **Token Storage**
   - Secure AsyncStorage
   - No sensitive data in plain text

2. **API Communication**
   - HTTPS only
   - Certificate pinning (production)
   - Request/response encryption

3. **Local Data**
   - Encrypted SQLite database
   - Secure file storage

## Scalability Considerations

### Horizontal Scaling

- Stateless backend (can run multiple instances)
- Load balancer in front of API servers
- Connection pooling for database

### Database Optimization

- Indexed columns for frequent queries
- Partitioning for large tables (tracking_logs)
- Read replicas for reporting

### Caching Strategy

- Redis for session data (future)
- API response caching
- Mobile app data caching

### Performance

- Pagination for list endpoints
- Lazy loading in mobile app
- Background sync for offline data
- Optimized database queries

## Deployment Architecture

### Development

```
Developer Machine
├── Backend (localhost:3000)
├── PostgreSQL (localhost:5432)
└── Mobile (Expo Dev Server)
```

### Production

```
Cloud Infrastructure (AWS/GCP/Azure)
│
├── Load Balancer
│   └─ API Servers (Auto-scaling)
│       └─ Docker Containers
│
├── Database
│   ├── Primary PostgreSQL
│   └── Read Replicas
│
├── Storage
│   └── Cloud Storage (S3/GCS)
│
└── Monitoring
    ├── Application Logs
    ├── Error Tracking
    └── Performance Metrics
```

## Technology Choices Rationale

### Backend: Node.js + Express

- **Pros**: Fast development, large ecosystem, excellent JSON handling
- **Use Case**: REST API, real-time features (future)

### Database: PostgreSQL

- **Pros**: ACID compliance, spatial data support (PostGIS), JSON support
- **Use Case**: Complex queries, data integrity, geospatial operations

### Mobile: React Native + Expo

- **Pros**: Cross-platform, single codebase, large community
- **Use Case**: iOS + Android support with shared code

### State: Zustand

- **Pros**: Lightweight, simple API, TypeScript support
- **Use Case**: Global state management without boilerplate

## Future Enhancements

1. **Real-time Features**
   - WebSocket for live tracking
   - Push notifications

2. **Analytics**
   - Business intelligence dashboard
   - Usage analytics

3. **Integrations**
   - Payment gateways
   - SMS notifications
   - Email services

4. **Advanced Features**
   - Machine learning for predictions
   - Weather integration
   - Advanced reporting

5. **Multi-tenancy**
   - Multiple organizations per instance
   - White-label support
