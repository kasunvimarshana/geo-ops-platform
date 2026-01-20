# API Specification - GeoOps Platform

## Base URL

**Development**: `http://localhost:8000/api`  
**Production**: `https://api.geo-ops.lk/api`

## API Version

Current Version: `v1`

## Authentication

All API endpoints (except auth endpoints) require JWT authentication.

**Header Format**:

```
Authorization: Bearer {token}
```

## Response Format

### Success Response

```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Success message",
  "meta": {
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

### Paginated Response

```json
{
    "success": true,
    "data": [...],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100,
        "last_page": 7
    }
}
```

### Error Response

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": {
      "email": ["The email field is required."]
    }
  },
  "meta": {
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

## Error Codes

| Code               | HTTP Status | Description                             |
| ------------------ | ----------- | --------------------------------------- |
| UNAUTHORIZED       | 401         | Invalid or missing authentication token |
| FORBIDDEN          | 403         | User lacks permission for this action   |
| NOT_FOUND          | 404         | Resource not found                      |
| VALIDATION_ERROR   | 422         | Request validation failed               |
| SUBSCRIPTION_LIMIT | 403         | Subscription limit reached              |
| SERVER_ERROR       | 500         | Internal server error                   |

---

## Authentication Endpoints

### Register User

```
POST /auth/register
```

**Request Body**:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+94771234567",
  "organization_name": "Green Fields Farm"
}
```

**Response**: `201 Created`

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+94771234567",
      "role": "owner"
    },
    "organization": {
      "id": 1,
      "name": "Green Fields Farm",
      "subscription_package": "free"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "expires_in": 3600
  }
}
```

### Login

```
POST /auth/login
```

**Request Body**:

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response**: `200 OK`

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "owner",
      "organization_id": 1
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "expires_in": 3600
  }
}
```

### Refresh Token

```
POST /auth/refresh
```

**Response**: `200 OK`

```json
{
  "success": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "expires_in": 3600
  }
}
```

### Get Current User

```
GET /auth/me
```

**Response**: `200 OK`

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+94771234567",
    "role": "owner",
    "organization": {
      "id": 1,
      "name": "Green Fields Farm",
      "subscription_package": "pro",
      "subscription_expires_at": "2025-01-15T00:00:00Z"
    }
  }
}
```

### Logout

```
POST /auth/logout
```

**Response**: `200 OK`

```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

---

## Land Measurements

### List Measurements

```
GET /measurements
```

**Query Parameters**:

- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 15, max: 100)
- `search` (string): Search by name
- `date_from` (date): Filter from date
- `date_to` (date): Filter to date

**Response**: `200 OK`

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "North Field A",
      "area_acres": 2.5,
      "area_hectares": 1.0117,
      "measured_at": "2024-01-10T08:30:00Z",
      "measured_by": {
        "id": 1,
        "name": "John Doe"
      },
      "coordinates": {
        "type": "Polygon",
        "coordinates": [
          [
            [80.1234, 6.9271],
            [80.124, 6.9271],
            [80.124, 6.928],
            [80.1234, 6.928],
            [80.1234, 6.9271]
          ]
        ]
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 45,
    "last_page": 3
  }
}
```

### Create Measurement

```
POST /measurements
```

**Request Body**:

```json
{
  "name": "South Field B",
  "coordinates": [
    { "latitude": 6.9271, "longitude": 80.1234 },
    { "latitude": 6.9271, "longitude": 80.124 },
    { "latitude": 6.928, "longitude": 80.124 },
    { "latitude": 6.928, "longitude": 80.1234 }
  ],
  "measured_at": "2024-01-15T10:30:00Z"
}
```

**Response**: `201 Created`

```json
{
    "success": true,
    "data": {
        "id": 2,
        "name": "South Field B",
        "area_acres": 1.8,
        "area_hectares": 0.7284,
        "measured_at": "2024-01-15T10:30:00Z",
        "coordinates": {
            "type": "Polygon",
            "coordinates": [...]
        }
    }
}
```

### Get Measurement

```
GET /measurements/{id}
```

**Response**: `200 OK`

### Update Measurement

```
PUT /measurements/{id}
```

**Request Body**:

```json
{
  "name": "South Field B - Updated"
}
```

**Response**: `200 OK`

### Delete Measurement

```
DELETE /measurements/{id}
```

**Response**: `204 No Content`

---

## Jobs

### List Jobs

```
GET /jobs
```

**Query Parameters**:

- `page`, `per_page`
- `status` (string): Filter by status
- `driver_id` (integer): Filter by driver
- `customer_id` (integer): Filter by customer
- `date_from`, `date_to`

**Response**: `200 OK`

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "customer": {
                "id": 1,
                "name": "Farmer Sunil"
            },
            "driver": {
                "id": 1,
                "name": "Driver Kamal"
            },
            "machine": {
                "id": 1,
                "name": "Tractor 01",
                "type": "tractor"
            },
            "land_measurement": {
                "id": 1,
                "name": "North Field A",
                "area_acres": 2.5
            },
            "status": "completed",
            "scheduled_at": "2024-01-15T08:00:00Z",
            "started_at": "2024-01-15T08:15:00Z",
            "completed_at": "2024-01-15T12:30:00Z",
            "notes": "Ploughing completed successfully"
        }
    ],
    "meta": {...}
}
```

### Create Job

```
POST /jobs
```

**Request Body**:

```json
{
  "customer_id": 1,
  "land_measurement_id": 1,
  "driver_id": 1,
  "machine_id": 1,
  "scheduled_at": "2024-01-20T08:00:00Z",
  "notes": "Deep ploughing required"
}
```

**Response**: `201 Created`

### Get Job

```
GET /jobs/{id}
```

**Response**: `200 OK`

### Update Job

```
PUT /jobs/{id}
```

**Response**: `200 OK`

### Update Job Status

```
PUT /jobs/{id}/status
```

**Request Body**:

```json
{
  "status": "in_progress",
  "notes": "Started work on site"
}
```

**Response**: `200 OK`

### Delete Job

```
DELETE /jobs/{id}
```

**Response**: `204 No Content`

---

## Drivers

### List Drivers

```
GET /drivers
```

**Query Parameters**:

- `page`, `per_page`
- `status` (string): active, inactive, on_leave

**Response**: `200 OK`

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user": {
                "id": 2,
                "name": "Kamal Perera",
                "phone": "+94771234568"
            },
            "license_number": "B1234567",
            "vehicle_info": {
                "type": "tractor",
                "make": "Mahindra",
                "model": "275 DI"
            },
            "status": "active"
        }
    ],
    "meta": {...}
}
```

### Create Driver

```
POST /drivers
```

**Request Body**:

```json
{
  "name": "Nimal Silva",
  "email": "nimal@example.com",
  "phone": "+94771234569",
  "password": "password123",
  "license_number": "B7654321",
  "vehicle_info": {
    "type": "tractor",
    "make": "John Deere",
    "model": "5045E"
  }
}
```

**Response**: `201 Created`

### Get Driver

```
GET /drivers/{id}
```

**Response**: `200 OK`

### Update Driver

```
PUT /drivers/{id}
```

**Response**: `200 OK`

### Delete Driver

```
DELETE /drivers/{id}
```

**Response**: `204 No Content`

---

## Tracking

### Submit Location Updates (Batch)

```
POST /tracking/locations
```

**Request Body**:

```json
{
  "driver_id": 1,
  "job_id": 1,
  "locations": [
    {
      "latitude": 6.9271,
      "longitude": 80.1234,
      "accuracy": 5.2,
      "speed": 12.5,
      "heading": 45.0,
      "recorded_at": "2024-01-15T08:15:00Z"
    },
    {
      "latitude": 6.9275,
      "longitude": 80.1238,
      "accuracy": 4.8,
      "speed": 15.0,
      "heading": 48.0,
      "recorded_at": "2024-01-15T08:16:00Z"
    }
  ]
}
```

**Response**: `201 Created`

```json
{
  "success": true,
  "message": "2 location updates recorded"
}
```

### Get Driver Location History

```
GET /tracking/drivers/{id}/history
```

**Query Parameters**:

- `date_from`, `date_to`
- `job_id` (integer): Filter by job

**Response**: `200 OK`

```json
{
  "success": true,
  "data": [
    {
      "latitude": 6.9271,
      "longitude": 80.1234,
      "accuracy": 5.2,
      "speed": 12.5,
      "recorded_at": "2024-01-15T08:15:00Z"
    }
  ]
}
```

### Get Job Route

```
GET /tracking/jobs/{id}/route
```

**Response**: `200 OK`

```json
{
  "success": true,
  "data": {
    "job_id": 1,
    "driver": {
      "id": 1,
      "name": "Kamal Perera"
    },
    "route": [
      {
        "latitude": 6.9271,
        "longitude": 80.1234,
        "recorded_at": "2024-01-15T08:15:00Z"
      }
    ],
    "total_distance_km": 12.5,
    "duration_minutes": 240
  }
}
```

---

## Invoices

### List Invoices

```
GET /invoices
```

**Query Parameters**:

- `page`, `per_page`
- `status` (string): draft, sent, paid, overdue, cancelled
- `customer_id` (integer)
- `date_from`, `date_to`

**Response**: `200 OK`

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "invoice_number": "INV-2024-001",
            "customer": {
                "id": 1,
                "name": "Farmer Sunil"
            },
            "job": {
                "id": 1,
                "land_measurement": {
                    "name": "North Field A",
                    "area_acres": 2.5
                }
            },
            "amount": 12500.00,
            "tax": 0.00,
            "discount": 0.00,
            "total": 12500.00,
            "status": "paid",
            "issued_at": "2024-01-15T14:00:00Z",
            "due_at": "2024-01-22T23:59:59Z",
            "paid_at": "2024-01-18T10:30:00Z"
        }
    ],
    "meta": {...}
}
```

### Create Invoice

```
POST /invoices
```

**Request Body**:

```json
{
  "job_id": 1,
  "customer_id": 1,
  "amount": 12500.0,
  "tax": 0.0,
  "discount": 500.0,
  "due_days": 7
}
```

**Response**: `201 Created`

```json
{
  "success": true,
  "data": {
    "id": 1,
    "invoice_number": "INV-2024-001",
    "total": 12000.0,
    "status": "draft",
    "pdf_url": "https://api.geo-ops.lk/storage/invoices/INV-2024-001.pdf"
  }
}
```

### Get Invoice

```
GET /invoices/{id}
```

**Response**: `200 OK`

### Download Invoice PDF

```
GET /invoices/{id}/pdf
```

**Response**: `200 OK` (PDF file)

### Update Invoice

```
PUT /invoices/{id}
```

**Response**: `200 OK`

### Delete Invoice

```
DELETE /invoices/{id}
```

**Response**: `204 No Content`

---

## Expenses

### List Expenses

```
GET /expenses
```

**Query Parameters**:

- `page`, `per_page`
- `category` (string): fuel, spare_parts, maintenance, labor, other
- `driver_id`, `machine_id`, `job_id`
- `date_from`, `date_to`

**Response**: `200 OK`

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "category": "fuel",
            "amount": 5000.00,
            "description": "Diesel refill - 50 liters",
            "expense_date": "2024-01-15",
            "driver": {
                "id": 1,
                "name": "Kamal Perera"
            },
            "machine": {
                "id": 1,
                "name": "Tractor 01"
            },
            "receipt_url": "https://api.geo-ops.lk/storage/receipts/receipt-001.jpg"
        }
    ],
    "meta": {...}
}
```

### Create Expense

```
POST /expenses
```

**Request Body** (multipart/form-data):

```
category: fuel
amount: 5000.00
description: Diesel refill - 50 liters
expense_date: 2024-01-15
driver_id: 1
machine_id: 1
job_id: 1
receipt: [file upload]
```

**Response**: `201 Created`

### Get Expense

```
GET /expenses/{id}
```

**Response**: `200 OK`

### Update Expense

```
PUT /expenses/{id}
```

**Response**: `200 OK`

### Delete Expense

```
DELETE /expenses/{id}
```

**Response**: `204 No Content`

---

## Payments

### List Payments

```
GET /payments
```

**Query Parameters**:

- `page`, `per_page`
- `customer_id` (integer)
- `invoice_id` (integer)
- `payment_method` (string)
- `date_from`, `date_to`

**Response**: `200 OK`

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "invoice": {
                "id": 1,
                "invoice_number": "INV-2024-001"
            },
            "customer": {
                "id": 1,
                "name": "Farmer Sunil"
            },
            "amount": 12000.00,
            "payment_method": "cash",
            "payment_date": "2024-01-18T10:30:00Z"
        }
    ],
    "meta": {...}
}
```

### Record Payment

```
POST /payments
```

**Request Body**:

```json
{
  "invoice_id": 1,
  "customer_id": 1,
  "amount": 12000.0,
  "payment_method": "bank_transfer",
  "reference_number": "TXN123456",
  "payment_date": "2024-01-18T10:30:00Z"
}
```

**Response**: `201 Created`

### Get Payment

```
GET /payments/{id}
```

**Response**: `200 OK`

---

## Customers

### List Customers

```
GET /customers
```

**Query Parameters**:

- `page`, `per_page`
- `search` (string): Search by name or phone

**Response**: `200 OK`

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Farmer Sunil",
            "phone": "+94771234567",
            "email": "sunil@example.com",
            "address": "123, Main Street, Kandy",
            "balance": 5000.00
        }
    ],
    "meta": {...}
}
```

### Create Customer

```
POST /customers
```

**Request Body**:

```json
{
  "name": "Farmer Sunil",
  "phone": "+94771234567",
  "email": "sunil@example.com",
  "address": "123, Main Street, Kandy"
}
```

**Response**: `201 Created`

### Get Customer

```
GET /customers/{id}
```

**Response**: `200 OK`

### Update Customer

```
PUT /customers/{id}
```

**Response**: `200 OK`

### Delete Customer

```
DELETE /customers/{id}
```

**Response**: `204 No Content`

---

## Machines

### List Machines

```
GET /machines
```

**Query Parameters**:

- `page`, `per_page`
- `status` (string): active, maintenance, inactive
- `type` (string): tractor, harvester, plough, etc.

**Response**: `200 OK`

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Tractor 01",
            "type": "tractor",
            "model": "Mahindra 275 DI",
            "registration_number": "ABC-1234",
            "status": "active"
        }
    ],
    "meta": {...}
}
```

### Create Machine

```
POST /machines
```

**Request Body**:

```json
{
  "name": "Tractor 01",
  "type": "tractor",
  "model": "Mahindra 275 DI",
  "registration_number": "ABC-1234"
}
```

**Response**: `201 Created`

### Get Machine

```
GET /machines/{id}
```

**Response**: `200 OK`

### Update Machine

```
PUT /machines/{id}
```

**Response**: `200 OK`

### Delete Machine

```
DELETE /machines/{id}
```

**Response**: `204 No Content`

---

## Reports

### Financial Summary

```
GET /reports/financial
```

**Query Parameters**:

- `date_from` (required)
- `date_to` (required)
- `group_by` (string): day, week, month

**Response**: `200 OK`

```json
{
  "success": true,
  "data": {
    "period": {
      "from": "2024-01-01",
      "to": "2024-01-31"
    },
    "summary": {
      "total_income": 250000.0,
      "total_expenses": 85000.0,
      "net_profit": 165000.0,
      "outstanding_invoices": 45000.0
    },
    "income_breakdown": {
      "completed_jobs": 25,
      "paid_invoices": 20,
      "pending_invoices": 5
    },
    "expense_breakdown": {
      "fuel": 45000.0,
      "spare_parts": 25000.0,
      "maintenance": 10000.0,
      "labor": 5000.0
    }
  }
}
```

### Job Report

```
GET /reports/jobs
```

**Query Parameters**:

- `date_from`, `date_to`
- `driver_id`, `machine_id`, `customer_id`
- `status`

**Response**: `200 OK`

### Expense Report

```
GET /reports/expenses
```

**Query Parameters**:

- `date_from`, `date_to`
- `category`, `driver_id`, `machine_id`

**Response**: `200 OK`

---

## Sync

### Push Offline Data

```
POST /sync/push
```

**Request Body**:

```json
{
    "measurements": [...],
    "jobs": [...],
    "expenses": [...],
    "tracking_logs": [...]
}
```

**Response**: `200 OK`

```json
{
  "success": true,
  "data": {
    "measurements": {
      "created": 5,
      "updated": 2,
      "conflicts": 0
    },
    "jobs": {
      "created": 3,
      "updated": 1,
      "conflicts": 0
    }
  }
}
```

### Pull Server Updates

```
GET /sync/pull
```

**Query Parameters**:

- `last_sync_at` (timestamp): Get updates since this time

**Response**: `200 OK`

```json
{
    "success": true,
    "data": {
        "measurements": [...],
        "jobs": [...],
        "invoices": [...],
        "customers": [...]
    },
    "meta": {
        "sync_timestamp": "2024-01-15T12:00:00Z"
    }
}
```

---

## Subscriptions

### Get Subscription Info

```
GET /subscriptions/current
```

**Response**: `200 OK`

```json
{
  "success": true,
  "data": {
    "package": "pro",
    "status": "active",
    "expires_at": "2025-01-15T00:00:00Z",
    "limits": {
      "measurements_per_month": -1,
      "drivers": -1,
      "pdf_exports_per_month": -1
    },
    "usage": {
      "measurements_this_month": 145,
      "drivers": 5,
      "pdf_exports_this_month": 67
    }
  }
}
```

### Get Usage Statistics

```
GET /subscriptions/usage
```

**Response**: `200 OK`

---

## Rate Limiting

- **Standard endpoints**: 60 requests/minute per user
- **Tracking endpoints**: 120 requests/minute per user
- **Auth endpoints**: 10 requests/minute per IP

## Webhooks (Future)

- Invoice paid
- Job completed
- Subscription expiring
- Payment received

## Versioning

API version is included in the base URL path: `/api/v1/...`
