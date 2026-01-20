# API Reference

> Complete REST API documentation for the GeoOps Platform

## Table of Contents
- [Base URL](#base-url)
- [Authentication](#authentication)
- [Response Format](#response-format)
- [API Endpoints](#api-endpoints)
  - [Authentication](#authentication-endpoints)
  - [Land Measurement](#land-measurement-endpoints)
  - [Job Management](#job-management-endpoints)
  - [Invoicing](#invoice-endpoints)
  - [Expenses](#expense-endpoints)
  - [Payments](#payment-endpoints)
  - [Reports](#financial-reports-endpoints)
  - [Sync](#sync-endpoints)
  - [Maps](#map--location-endpoints)
  - [Users](#user-management-endpoints)
  - [Machines](#machine-management-endpoints)
  - [Subscription](#subscription-endpoints)
- [Error Codes](#error-codes)
- [Rate Limiting](#rate-limiting)
- [Webhooks](#webhook-events)

## Base URL

```
Production: https://api.geo-ops.lk/api/v1
Development: http://localhost:8000/api/v1
```

## Authentication

All API endpoints (except auth endpoints) require JWT Bearer token authentication.

### Headers

```http
Authorization: Bearer {jwt_token}
Content-Type: application/json
Accept: application/json
X-Organization-ID: {organization_id}
```

## Response Format

### Success Response

```json
{
  "success": true,
  "data": { ... },
  "message": "Success message",
  "meta": {
    "pagination": {
      "total": 100,
      "per_page": 15,
      "current_page": 1,
      "last_page": 7
    }
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Validation error message"]
  },
  "code": "ERROR_CODE"
}
```

## API Endpoints

### Authentication Endpoints

#### Register

```http
POST /auth/register
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+94771234567",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123",
  "organization_name": "ABC Agro Services",
  "language": "si"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "owner"
    },
    "organization": {
      "id": 1,
      "name": "ABC Agro Services",
      "subscription_package": "free"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

#### Login

```http
POST /auth/login
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "SecurePass123"
}
```

#### Refresh Token

```http
POST /auth/refresh
```

#### Logout

```http
POST /auth/logout
```

#### Get Current User

```http
GET /auth/me
```

### Land Measurement Endpoints

#### Create Land Measurement

```http
POST /lands
```

**Request Body:**
```json
{
  "name": "Paddy Field North",
  "description": "Main paddy cultivation area",
  "measurement_type": "walk-around",
  "polygon": [
    {"latitude": 7.8731, "longitude": 80.7718, "accuracy": 5.2},
    {"latitude": 7.8735, "longitude": 80.7720, "accuracy": 4.8},
    {"latitude": 7.8738, "longitude": 80.7715, "accuracy": 5.1},
    {"latitude": 7.8732, "longitude": 80.7713, "accuracy": 5.5}
  ],
  "customer_name": "Silva Farmer",
  "customer_phone": "+94771234567",
  "location_name": "Anuradhapura",
  "measured_at": "2026-01-19T10:30:00Z",
  "offline_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "Paddy Field North",
    "area_acres": 2.5,
    "area_hectares": 1.0117,
    "polygon": [...],
    "measurement_type": "walk-around",
    "status": "confirmed",
    "sync_status": "synced",
    "created_at": "2026-01-19T10:30:00Z"
  }
}
```

#### List Lands

```http
GET /lands?page=1&per_page=15&status=confirmed&search=paddy
```

#### Get Land Details

```http
GET /lands/{id}
```

#### Update Land

```http
PUT /lands/{id}
```

#### Delete Land

```http
DELETE /lands/{id}
```

### Job Management Endpoints

#### Create Job

```http
POST /jobs
```

**Request Body:**
```json
{
  "title": "Ploughing Service",
  "description": "Deep ploughing for paddy cultivation",
  "land_id": 123,
  "machine_id": 5,
  "driver_id": 12,
  "job_date": "2026-01-20",
  "customer_name": "Silva Farmer",
  "customer_phone": "+94771234567",
  "location": {
    "latitude": 7.8731,
    "longitude": 80.7718
  },
  "location_name": "Anuradhapura District",
  "offline_id": "660e8400-e29b-41d4-a716-446655440001"
}
```

#### List Jobs

```http
GET /jobs?status=in_progress&driver_id=12&from=2026-01-01&to=2026-01-31
```

#### Get Job Details

```http
GET /jobs/{id}
```

#### Update Job Status

```http
PATCH /jobs/{id}/status
```

**Request Body:**
```json
{
  "status": "in_progress",
  "start_time": "2026-01-20T08:00:00Z"
}
```

#### Track Job GPS

```http
POST /jobs/{id}/tracking
```

**Request Body:**
```json
{
  "points": [
    {
      "latitude": 7.8731,
      "longitude": 80.7718,
      "accuracy": 5.2,
      "speed": 12.5,
      "heading": 45.0,
      "recorded_at": "2026-01-20T08:15:00Z"
    }
  ]
}
```

#### Get Job Tracking History

```http
GET /jobs/{id}/tracking?from=2026-01-20T08:00:00Z&to=2026-01-20T17:00:00Z
```

### Invoice Endpoints

#### Create Invoice

```http
POST /invoices
```

**Request Body:**
```json
{
  "job_id": 456,
  "land_id": 123,
  "customer_name": "Silva Farmer",
  "customer_phone": "+94771234567",
  "invoice_date": "2026-01-20",
  "due_date": "2026-02-20",
  "area_acres": 2.5,
  "area_hectares": 1.0117,
  "rate_per_unit": 15000.00,
  "unit": "acre",
  "tax_rate": 0,
  "notes": "Ploughing service completed",
  "offline_id": "770e8400-e29b-41d4-a716-446655440002"
}
```

#### List Invoices

```http
GET /invoices?status=paid&from=2026-01-01&to=2026-01-31&customer=Silva
```

#### Get Invoice Details

```http
GET /invoices/{id}
```

#### Generate Invoice PDF

```http
GET /invoices/{id}/pdf
```

#### Mark Invoice as Printed

```http
POST /invoices/{id}/printed
```

### Expense Endpoints

#### Create Expense

```http
POST /expenses
```

**Request Body:**
```json
{
  "expense_type": "fuel",
  "category": "Diesel",
  "description": "Fuel for tractor",
  "amount": 5000.00,
  "expense_date": "2026-01-20",
  "machine_id": 5,
  "driver_id": 12,
  "job_id": 456,
  "offline_id": "880e8400-e29b-41d4-a716-446655440003"
}
```

#### List Expenses

```http
GET /expenses?expense_type=fuel&machine_id=5&from=2026-01-01&to=2026-01-31
```

#### Get Expense Summary

```http
GET /expenses/summary?from=2026-01-01&to=2026-01-31&group_by=expense_type
```

### Payment Endpoints

#### Record Payment

```http
POST /payments
```

**Request Body:**
```json
{
  "invoice_id": 789,
  "payment_method": "cash",
  "amount": 20000.00,
  "payment_date": "2026-01-21",
  "reference_number": "PMT-001",
  "notes": "Partial payment received",
  "offline_id": "990e8400-e29b-41d4-a716-446655440004"
}
```

#### List Payments

```http
GET /payments?invoice_id=789&from=2026-01-01&to=2026-01-31
```

### Financial Reports Endpoints

#### Income vs Expense Report

```http
GET /reports/financial?from=2026-01-01&to=2026-01-31&group_by=month
```

**Response:**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_income": 500000.00,
      "total_expenses": 150000.00,
      "net_profit": 350000.00
    },
    "monthly_breakdown": [
      {
        "month": "2026-01",
        "income": 500000.00,
        "expenses": 150000.00,
        "profit": 350000.00
      }
    ]
  }
}
```

#### Customer Ledger

```http
GET /reports/ledger?customer_name=Silva&from=2026-01-01&to=2026-01-31
```

#### Machine Performance Report

```http
GET /reports/machines/{machine_id}?from=2026-01-01&to=2026-01-31
```

### Subscription Endpoints

#### Get Current Subscription

```http
GET /subscription
```

**Response:**
```json
{
  "success": true,
  "data": {
    "package": "basic",
    "expires_at": "2026-12-31T23:59:59Z",
    "limits": {
      "measurements": {
        "used": 45,
        "limit": 100
      },
      "drivers": {
        "used": 3,
        "limit": 5
      },
      "exports": {
        "used": 20,
        "limit": 50
      }
    },
    "features": [
      "gps_measurement",
      "job_management",
      "basic_billing",
      "offline_sync"
    ]
  }
}
```

#### Check Feature Access

```http
GET /subscription/features/{feature_name}
```

### Sync Endpoints

#### Bulk Sync

```http
POST /sync/bulk
```

**Request Body:**
```json
{
  "lands": [
    {
      "offline_id": "550e8400-e29b-41d4-a716-446655440000",
      "action": "create",
      "data": {...},
      "updated_at": "2026-01-19T10:30:00Z"
    }
  ],
  "jobs": [...],
  "invoices": [...],
  "expenses": [...],
  "payments": [...]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "synced": {
      "lands": 5,
      "jobs": 3,
      "invoices": 2
    },
    "conflicts": [
      {
        "entity_type": "land",
        "offline_id": "550e8400-e29b-41d4-a716-446655440000",
        "reason": "modified_on_server",
        "server_data": {...},
        "client_data": {...}
      }
    ],
    "errors": []
  }
}
```

#### Get Sync Status

```http
GET /sync/status?since=2026-01-19T00:00:00Z
```

#### Resolve Conflict

```http
POST /sync/conflicts/{conflict_id}/resolve
```

**Request Body:**
```json
{
  "resolution": "use_client" | "use_server" | "merge",
  "merged_data": {...}
}
```

### Map & Location Endpoints

#### Get Nearby Lands

```http
GET /map/lands/nearby?latitude=7.8731&longitude=80.7718&radius=5000
```

#### Get Active Drivers

```http
GET /map/drivers/active
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "driver_id": 12,
      "name": "Driver Name",
      "current_location": {
        "latitude": 7.8731,
        "longitude": 80.7718
      },
      "current_job": {
        "id": 456,
        "title": "Ploughing Service"
      },
      "last_updated": "2026-01-20T10:15:00Z"
    }
  ]
}
```

### User Management Endpoints

#### List Users

```http
GET /users?role=driver&is_active=true
```

#### Create User

```http
POST /users
```

**Request Body:**
```json
{
  "name": "New Driver",
  "email": "driver@example.com",
  "phone": "+94771234567",
  "password": "SecurePass123",
  "role_id": 3,
  "language": "si"
}
```

#### Update User

```http
PUT /users/{id}
```

#### Deactivate User

```http
PATCH /users/{id}/deactivate
```

### Machine Management Endpoints

#### List Machines

```http
GET /machines?is_active=true
```

#### Create Machine

```http
POST /machines
```

**Request Body:**
```json
{
  "name": "Tractor John Deere",
  "machine_type": "tractor",
  "registration_number": "AB-1234",
  "description": "70HP tractor",
  "rate_per_acre": 15000.00,
  "rate_per_hectare": 37050.00
}
```

## Rate Limiting

- 60 requests per minute for authenticated users
- 10 requests per minute for unauthenticated users
- 1000 requests per hour per organization

## Error Codes

| Code | Description |
|------|-------------|
| UNAUTHORIZED | Invalid or expired token |
| FORBIDDEN | Insufficient permissions |
| NOT_FOUND | Resource not found |
| VALIDATION_ERROR | Request validation failed |
| SUBSCRIPTION_LIMIT_REACHED | Subscription limit exceeded |
| SYNC_CONFLICT | Data synchronization conflict |
| SERVER_ERROR | Internal server error |

## Webhook Events

Organizations can register webhook URLs to receive real-time notifications:

- `job.created`
- `job.started`
- `job.completed`
- `invoice.created`
- `invoice.paid`
- `payment.received`
- `subscription.expiring`

## API Versioning

- Current version: v1
- Version specified in URL: `/api/v1/`
- Backward compatibility maintained for 12 months
- Deprecation warnings in response headers

---

**Next**: See [Database Schema](database-schema.md) for data structure details.
