# API Documentation

## Base URL
```
Production: https://api.geoops.lk/api/v1
Development: http://localhost:8000/api/v1
```

## Authentication

All API requests (except auth endpoints) require JWT token in header:
```
Authorization: Bearer <access_token>
```

### Token Refresh
- Access tokens expire in 1 hour
- Refresh tokens expire in 30 days
- Use `/auth/refresh` endpoint to get new access token

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

### Pagination Response
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

## API Endpoints

### 1. Authentication

#### POST /auth/register
Register a new user and organization.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+94771234567",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!",
  "organization_name": "John's Farm Services",
  "language": "en"
}
```

**Response:** `201 Created`
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+94771234567",
      "role": "owner",
      "organization_id": 1
    },
    "organization": {
      "id": 1,
      "name": "John's Farm Services",
      "slug": "johns-farm-services"
    },
    "tokens": {
      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "expires_in": 3600
    }
  }
}
```

#### POST /auth/login
Authenticate user and get tokens.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "SecurePass123!"
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "tokens": {
      "access_token": "...",
      "refresh_token": "...",
      "expires_in": 3600
    }
  }
}
```

#### POST /auth/refresh
Refresh access token using refresh token.

**Request Body:**
```json
{
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "access_token": "...",
    "expires_in": 3600
  }
}
```

#### POST /auth/logout
Invalidate current tokens.

**Headers:** `Authorization: Bearer <token>`

**Response:** `200 OK`

---

### 2. User Management

#### GET /users
Get list of users in organization.

**Query Parameters:**
- `role` - Filter by role (admin, owner, driver, broker, accountant)
- `status` - Filter by status (active, inactive, blocked)
- `search` - Search by name, email, or phone
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15)

**Response:** `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+94771234567",
      "role": "owner",
      "status": "active",
      "last_login_at": "2024-01-15T10:30:00Z"
    }
  ],
  "meta": { ... }
}
```

#### POST /users
Create new user in organization.

**Request Body:**
```json
{
  "name": "Jane Smith",
  "email": "jane@example.com",
  "phone": "+94772345678",
  "password": "SecurePass123!",
  "role": "driver",
  "language": "si"
}
```

**Response:** `201 Created`

#### GET /users/{id}
Get user details.

**Response:** `200 OK`

#### PUT /users/{id}
Update user details.

**Request Body:**
```json
{
  "name": "Jane Smith Updated",
  "phone": "+94772345679",
  "status": "active"
}
```

**Response:** `200 OK`

#### DELETE /users/{id}
Soft delete user.

**Response:** `200 OK`

---

### 3. GPS Land Measurement

#### GET /measurements
Get list of land measurements.

**Query Parameters:**
- `customer_phone` - Filter by customer phone
- `measured_by` - Filter by user ID who measured
- `status` - Filter by status (draft, completed, verified)
- `from_date` - Filter from date (YYYY-MM-DD)
- `to_date` - Filter to date (YYYY-MM-DD)
- `page`, `per_page` - Pagination

**Response:** `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "customer_name": "Farmer John",
      "customer_phone": "+94771111111",
      "location_name": "Paddy Field A",
      "area_acres": 2.5,
      "area_hectares": 1.01,
      "perimeter_meters": 450.5,
      "center_latitude": 7.8731,
      "center_longitude": 80.7718,
      "measurement_method": "walk_around",
      "measurement_date": "2024-01-15T08:30:00Z",
      "status": "completed",
      "measured_by": {
        "id": 2,
        "name": "Driver Bob"
      },
      "polygon_points": 25,
      "created_at": "2024-01-15T08:30:00Z"
    }
  ],
  "meta": { ... }
}
```

#### POST /measurements
Create new land measurement.

**Request Body:**
```json
{
  "customer_name": "Farmer John",
  "customer_phone": "+94771111111",
  "location_name": "Paddy Field A",
  "location_address": "123 Main St, Colombo",
  "measurement_method": "walk_around",
  "measurement_date": "2024-01-15T08:30:00Z",
  "polygon_points": [
    {
      "latitude": 7.8731,
      "longitude": 80.7718,
      "altitude": 50.5,
      "accuracy": 3.5,
      "timestamp": "2024-01-15T08:30:00Z"
    },
    {
      "latitude": 7.8735,
      "longitude": 80.7720,
      "altitude": 51.0,
      "accuracy": 3.2,
      "timestamp": "2024-01-15T08:31:00Z"
    }
  ],
  "notes": "Good quality land"
}
```

**Response:** `201 Created`
```json
{
  "success": true,
  "message": "Measurement created successfully",
  "data": {
    "id": 1,
    "customer_name": "Farmer John",
    "area_acres": 2.5,
    "area_hectares": 1.01,
    "perimeter_meters": 450.5,
    "center_latitude": 7.8731,
    "center_longitude": 80.7718,
    "status": "completed"
  }
}
```

#### GET /measurements/{id}
Get measurement details with polygon points.

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "id": 1,
    "customer_name": "Farmer John",
    "area_acres": 2.5,
    "polygon_points": [
      {
        "point_order": 1,
        "latitude": 7.8731,
        "longitude": 80.7718,
        "altitude": 50.5,
        "accuracy": 3.5,
        "timestamp": "2024-01-15T08:30:00Z"
      }
    ]
  }
}
```

#### PUT /measurements/{id}
Update measurement details.

**Request Body:**
```json
{
  "customer_name": "Farmer John Updated",
  "location_name": "Paddy Field A - Updated",
  "notes": "Updated notes"
}
```

**Response:** `200 OK`

#### DELETE /measurements/{id}
Soft delete measurement.

**Response:** `200 OK`

---

### 4. Jobs Management

#### GET /jobs
Get list of jobs.

**Query Parameters:**
- `status` - Filter by status
- `customer_phone` - Filter by customer
- `driver_id` - Filter by assigned driver
- `scheduled_date` - Filter by date
- `priority` - Filter by priority
- `page`, `per_page` - Pagination

**Response:** `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "job_number": "JOB-2024-0001",
      "customer_name": "Farmer John",
      "customer_phone": "+94771111111",
      "location_name": "Paddy Field A",
      "job_type": "ploughing",
      "scheduled_date": "2024-01-20",
      "status": "assigned",
      "priority": "normal",
      "assignment": {
        "driver": {
          "id": 2,
          "name": "Driver Bob"
        },
        "machine": {
          "id": 1,
          "name": "Tractor #1"
        }
      },
      "measurement": {
        "id": 1,
        "area_acres": 2.5
      }
    }
  ],
  "meta": { ... }
}
```

#### POST /jobs
Create new job.

**Request Body:**
```json
{
  "measurement_id": 1,
  "customer_name": "Farmer John",
  "customer_phone": "+94771111111",
  "location_name": "Paddy Field A",
  "location_address": "123 Main St",
  "location_latitude": 7.8731,
  "location_longitude": 80.7718,
  "job_type": "ploughing",
  "scheduled_date": "2024-01-20",
  "start_time": "08:00:00",
  "priority": "normal",
  "notes": "Prepare field for planting"
}
```

**Response:** `201 Created`

#### GET /jobs/{id}
Get job details.

**Response:** `200 OK`

#### PUT /jobs/{id}
Update job details.

**Response:** `200 OK`

#### PUT /jobs/{id}/status
Update job status.

**Request Body:**
```json
{
  "status": "in_progress",
  "notes": "Started work at 8:00 AM"
}
```

**Response:** `200 OK`

#### POST /jobs/{id}/assign
Assign driver and machine to job.

**Request Body:**
```json
{
  "driver_id": 2,
  "machine_id": 1,
  "notes": "Handle with care"
}
```

**Response:** `200 OK`

#### DELETE /jobs/{id}
Cancel/delete job.

**Response:** `200 OK`

---

### 5. GPS Tracking

#### POST /tracking/location
Record GPS location point.

**Request Body:**
```json
{
  "job_id": 1,
  "latitude": 7.8731,
  "longitude": 80.7718,
  "altitude": 50.5,
  "accuracy": 3.5,
  "speed": 5.2,
  "heading": 180.5,
  "timestamp": "2024-01-15T10:30:00Z",
  "battery_level": 85,
  "is_moving": true
}
```

**Response:** `201 Created`

#### POST /tracking/locations/batch
Record multiple GPS points in batch.

**Request Body:**
```json
{
  "locations": [
    {
      "job_id": 1,
      "latitude": 7.8731,
      "longitude": 80.7718,
      "timestamp": "2024-01-15T10:30:00Z"
    }
  ]
}
```

**Response:** `201 Created`

#### GET /tracking/users/{userId}
Get user's GPS tracking history.

**Query Parameters:**
- `job_id` - Filter by job
- `from_date` - Start date/time
- `to_date` - End date/time
- `page`, `per_page` - Pagination

**Response:** `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "latitude": 7.8731,
      "longitude": 80.7718,
      "altitude": 50.5,
      "accuracy": 3.5,
      "speed": 5.2,
      "heading": 180.5,
      "timestamp": "2024-01-15T10:30:00Z",
      "job": {
        "id": 1,
        "job_number": "JOB-2024-0001"
      }
    }
  ],
  "meta": { ... }
}
```

#### GET /tracking/jobs/{jobId}
Get all GPS tracking for a specific job.

**Response:** `200 OK`

---

### 6. Billing & Invoices

#### GET /invoices
Get list of invoices.

**Query Parameters:**
- `status` - Filter by status
- `customer_phone` - Filter by customer
- `from_date`, `to_date` - Date range
- `page`, `per_page` - Pagination

**Response:** `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "invoice_number": "INV-2024-0001",
      "customer_name": "Farmer John",
      "customer_phone": "+94771111111",
      "invoice_date": "2024-01-20",
      "due_date": "2024-02-20",
      "subtotal": 12500.00,
      "tax_amount": 0.00,
      "discount_amount": 0.00,
      "total_amount": 12500.00,
      "paid_amount": 5000.00,
      "balance": 7500.00,
      "status": "partially_paid",
      "job": {
        "id": 1,
        "job_number": "JOB-2024-0001"
      },
      "pdf_url": "https://..."
    }
  ],
  "meta": { ... }
}
```

#### POST /invoices
Create new invoice.

**Request Body:**
```json
{
  "job_id": 1,
  "customer_name": "Farmer John",
  "customer_phone": "+94771111111",
  "customer_address": "123 Main St, Colombo",
  "invoice_date": "2024-01-20",
  "due_date": "2024-02-20",
  "items": [
    {
      "description": "Ploughing service - 2.5 acres",
      "quantity": 2.5,
      "unit": "acre",
      "unit_price": 5000.00,
      "tax_rate": 0
    }
  ],
  "discount_amount": 0,
  "payment_terms": "Net 30 days",
  "notes": "Thank you for your business"
}
```

**Response:** `201 Created`

#### GET /invoices/{id}
Get invoice details with line items.

**Response:** `200 OK`

#### GET /invoices/{id}/pdf
Download invoice PDF.

**Response:** `200 OK` (PDF file)

#### POST /invoices/{id}/send
Send invoice via email/SMS.

**Request Body:**
```json
{
  "method": "email",
  "recipient": "customer@example.com"
}
```

**Response:** `200 OK`

#### PUT /invoices/{id}/status
Update invoice status.

**Request Body:**
```json
{
  "status": "paid"
}
```

**Response:** `200 OK`

---

### 7. Payments

#### GET /payments
Get list of payments.

**Query Parameters:**
- `invoice_id` - Filter by invoice
- `status` - Filter by status
- `from_date`, `to_date` - Date range
- `page`, `per_page` - Pagination

**Response:** `200 OK`

#### POST /payments
Record new payment.

**Request Body:**
```json
{
  "invoice_id": 1,
  "payment_date": "2024-01-25",
  "amount": 5000.00,
  "payment_method": "cash",
  "reference_number": "CASH-001",
  "notes": "Partial payment received"
}
```

**Response:** `201 Created`

#### GET /payments/{id}
Get payment details.

**Response:** `200 OK`

---

### 8. Expenses

#### GET /expenses
Get list of expenses.

**Query Parameters:**
- `category` - Filter by category
- `machine_id` - Filter by machine
- `job_id` - Filter by job
- `from_date`, `to_date` - Date range
- `page`, `per_page` - Pagination

**Response:** `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "category": "fuel",
      "description": "Diesel for Tractor #1",
      "amount": 5000.00,
      "expense_date": "2024-01-15",
      "payment_method": "cash",
      "vendor_name": "Shell Fuel Station",
      "machine": {
        "id": 1,
        "name": "Tractor #1"
      },
      "job": {
        "id": 1,
        "job_number": "JOB-2024-0001"
      }
    }
  ],
  "meta": { ... }
}
```

#### POST /expenses
Create new expense.

**Request Body:**
```json
{
  "machine_id": 1,
  "job_id": 1,
  "category": "fuel",
  "description": "Diesel for Tractor #1",
  "amount": 5000.00,
  "expense_date": "2024-01-15",
  "payment_method": "cash",
  "vendor_name": "Shell Fuel Station",
  "reference_number": "FUEL-001",
  "notes": "Full tank"
}
```

**Response:** `201 Created`

#### GET /expenses/{id}
Get expense details.

**Response:** `200 OK`

#### PUT /expenses/{id}
Update expense.

**Response:** `200 OK`

#### DELETE /expenses/{id}
Delete expense.

**Response:** `200 OK`

---

### 9. Machines

#### GET /machines
Get list of machines.

**Query Parameters:**
- `status` - Filter by status
- `type` - Filter by machine type
- `page`, `per_page` - Pagination

**Response:** `200 OK`

#### POST /machines
Create new machine.

**Request Body:**
```json
{
  "name": "Tractor #1",
  "type": "tractor",
  "model": "Massey Ferguson 240",
  "registration_number": "ABC-1234",
  "purchase_date": "2023-01-15",
  "purchase_price": 2500000.00,
  "status": "active",
  "notes": "Primary field tractor"
}
```

**Response:** `201 Created`

#### GET /machines/{id}
Get machine details.

**Response:** `200 OK`

#### PUT /machines/{id}
Update machine.

**Response:** `200 OK`

#### DELETE /machines/{id}
Delete machine.

**Response:** `200 OK`

---

### 10. Reports

#### GET /reports/financial
Get financial summary report.

**Query Parameters:**
- `from_date`, `to_date` - Date range
- `group_by` - Group by (day, week, month)

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "total_income": 125000.00,
    "total_expenses": 45000.00,
    "net_profit": 80000.00,
    "invoices": {
      "total": 15,
      "paid": 8,
      "pending": 7,
      "total_amount": 125000.00
    },
    "payments": {
      "total": 20,
      "total_amount": 100000.00
    },
    "expenses_by_category": {
      "fuel": 20000.00,
      "maintenance": 15000.00,
      "spare_parts": 10000.00
    }
  }
}
```

#### GET /reports/jobs
Get jobs summary report.

**Response:** `200 OK`

#### GET /reports/drivers
Get drivers performance report.

**Response:** `200 OK`

---

### 11. Subscriptions

#### GET /subscriptions/current
Get current organization subscription.

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "package_type": "basic",
    "status": "active",
    "started_at": "2024-01-01T00:00:00Z",
    "expires_at": "2024-12-31T23:59:59Z",
    "features": {
      "max_measurements": 100,
      "max_drivers": 5,
      "max_exports": 50,
      "has_gps_tracking": true,
      "has_offline_mode": true
    },
    "usage": {
      "measurements": 45,
      "drivers": 3,
      "exports": 12
    }
  }
}
```

#### POST /subscriptions/upgrade
Upgrade subscription package.

**Request Body:**
```json
{
  "package_type": "pro",
  "payment_method": "card",
  "payment_token": "..."
}
```

**Response:** `200 OK`

---

### 12. Offline Sync

#### POST /sync/push
Push offline changes to server.

**Request Body:**
```json
{
  "measurements": [
    {
      "client_id": "offline-meas-001",
      "customer_name": "Farmer John",
      "polygon_points": [...],
      "created_at": "2024-01-15T08:30:00Z"
    }
  ],
  "jobs": [...],
  "expenses": [...],
  "tracking_points": [...]
}
```

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "synced": {
      "measurements": 5,
      "jobs": 2,
      "expenses": 3,
      "tracking_points": 150
    },
    "conflicts": [],
    "errors": []
  }
}
```

#### GET /sync/pull
Pull latest data from server.

**Query Parameters:**
- `last_sync_at` - Last sync timestamp

**Response:** `200 OK`
```json
{
  "success": true,
  "data": {
    "measurements": [...],
    "jobs": [...],
    "invoices": [...],
    "updated_at": "2024-01-15T12:00:00Z"
  }
}
```

---

## Rate Limiting

- 100 requests per minute per user
- 1000 requests per hour per organization
- Rate limit info in response headers:
  - `X-RateLimit-Limit`
  - `X-RateLimit-Remaining`
  - `X-RateLimit-Reset`

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Invalid/expired token |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation error |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error |

## Webhooks (Optional)

Configure webhooks to receive real-time notifications:

### Events
- `job.created`
- `job.assigned`
- `job.completed`
- `invoice.created`
- `payment.received`
- `measurement.completed`

**Webhook Payload:**
```json
{
  "event": "job.completed",
  "timestamp": "2024-01-15T12:00:00Z",
  "data": { ... }
}
```

---

This API follows RESTful conventions and supports JSON format for all requests and responses.
