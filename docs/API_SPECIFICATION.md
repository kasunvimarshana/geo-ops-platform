# API Specification

## GeoOps Platform - RESTful API Documentation

**Base URL**: `https://api.geo-ops.lk/api/v1`

**Authentication**: JWT Bearer Token

**Content-Type**: `application/json`

---

## Authentication Endpoints

### POST /auth/register

Register a new organization and admin user.

**Request Body:**

```json
{
  "organization_name": "Green Farm Services",
  "first_name": "Kasun",
  "last_name": "Perera",
  "email": "kasun@example.com",
  "phone": "+94771234567",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!"
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "organization_id": 1,
      "first_name": "Kasun",
      "last_name": "Perera",
      "email": "kasun@example.com",
      "role": "admin"
    },
    "organization": {
      "id": 1,
      "name": "Green Farm Services",
      "package_tier": "free"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "expires_in": 3600
  }
}
```

---

### POST /auth/login

Login with email and password.

**Request Body:**

```json
{
  "email": "kasun@example.com",
  "password": "SecurePass123!"
}
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "organization_id": 1,
      "first_name": "Kasun",
      "last_name": "Perera",
      "email": "kasun@example.com",
      "role": "admin"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "expires_in": 3600
  }
}
```

---

### POST /auth/refresh

Refresh access token using refresh token.

**Request Body:**

```json
{
  "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "expires_in": 3600
  }
}
```

---

### POST /auth/logout

Logout and invalidate token.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## User Management Endpoints

### GET /users

Get all users in organization.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Query Parameters:**

- `role` (optional): Filter by role (admin, owner, driver, broker, accountant)
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15)

**Response (200):**

```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": 1,
        "first_name": "Kasun",
        "last_name": "Perera",
        "email": "kasun@example.com",
        "phone": "+94771234567",
        "role": "admin",
        "is_active": true,
        "created_at": "2024-01-15T10:30:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 5,
      "last_page": 1
    }
  }
}
```

---

### POST /users

Create a new user.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "first_name": "Nimal",
  "last_name": "Silva",
  "email": "nimal@example.com",
  "phone": "+94771234568",
  "role": "driver",
  "password": "SecurePass123!"
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "user": {
      "id": 2,
      "first_name": "Nimal",
      "last_name": "Silva",
      "email": "nimal@example.com",
      "role": "driver"
    }
  }
}
```

---

### PUT /users/{id}

Update user details.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "first_name": "Nimal",
  "last_name": "Fernando",
  "phone": "+94771234569",
  "is_active": true
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "User updated successfully",
  "data": {
    "user": {
      "id": 2,
      "first_name": "Nimal",
      "last_name": "Fernando",
      "phone": "+94771234569",
      "role": "driver"
    }
  }
}
```

---

### DELETE /users/{id}

Soft delete a user.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Response (200):**

```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

---

## Land Management Endpoints

### GET /lands

Get all lands.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Query Parameters:**

- `status` (optional): Filter by status (active, inactive)
- `page` (optional): Page number
- `per_page` (optional): Items per page

**Response (200):**

```json
{
  "success": true,
  "data": {
    "lands": [
      {
        "id": 1,
        "name": "Field A - Gampaha",
        "description": "Main rice field",
        "area_acres": 2.5,
        "area_hectares": 1.01,
        "coordinates": [
          { "lat": 7.0917, "lng": 79.995 },
          { "lat": 7.092, "lng": 79.9955 },
          { "lat": 7.0915, "lng": 79.996 },
          { "lat": 7.0912, "lng": 79.9952 }
        ],
        "center_latitude": 7.0916,
        "center_longitude": 79.9954,
        "location_district": "Gampaha",
        "status": "active",
        "owner": {
          "id": 1,
          "name": "Kasun Perera"
        },
        "created_at": "2024-01-15T10:30:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 10,
      "last_page": 1
    }
  }
}
```

---

### POST /lands

Create a new land record.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "name": "Field B - Kurunegala",
  "description": "New cultivation area",
  "coordinates": [
    { "lat": 7.4867, "lng": 80.3647 },
    { "lat": 7.487, "lng": 80.365 },
    { "lat": 7.4865, "lng": 80.3655 },
    { "lat": 7.4862, "lng": 80.3649 }
  ],
  "location_district": "Kurunegala",
  "location_province": "North Western"
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Land created successfully",
  "data": {
    "land": {
      "id": 2,
      "name": "Field B - Kurunegala",
      "area_acres": 1.8,
      "area_hectares": 0.73,
      "area_square_meters": 7284.5,
      "coordinates": [...],
      "center_latitude": 7.4866,
      "center_longitude": 80.3650
    }
  }
}
```

---

### GET /lands/{id}

Get specific land details.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "land": {
      "id": 1,
      "name": "Field A - Gampaha",
      "description": "Main rice field",
      "area_acres": 2.5,
      "area_hectares": 1.01,
      "coordinates": [...],
      "measurements_count": 3,
      "jobs_count": 5,
      "owner": {
        "id": 1,
        "name": "Kasun Perera"
      },
      "recent_measurements": [...],
      "created_at": "2024-01-15T10:30:00Z"
    }
  }
}
```

---

## Measurement Endpoints

### POST /measurements

Create a new GPS measurement.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "land_id": 1,
  "measurement_type": "walk_around",
  "coordinates": [
    {
      "lat": 7.0917,
      "lng": 79.995,
      "timestamp": "2024-01-15T10:30:00Z",
      "accuracy": 5.2
    },
    {
      "lat": 7.092,
      "lng": 79.9955,
      "timestamp": "2024-01-15T10:30:15Z",
      "accuracy": 4.8
    }
  ],
  "measured_at": "2024-01-15T10:30:00Z",
  "duration_seconds": 180,
  "notes": "Clear weather, good GPS signal"
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Measurement created successfully",
  "data": {
    "measurement": {
      "id": 1,
      "land_id": 1,
      "measurement_type": "walk_around",
      "area_acres": 2.52,
      "area_hectares": 1.02,
      "area_square_meters": 10200,
      "perimeter_meters": 405.5,
      "coordinates": [...],
      "measured_at": "2024-01-15T10:30:00Z",
      "sync_status": "synced"
    }
  }
}
```

---

### POST /measurements/batch

Batch create measurements (for offline sync).

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "measurements": [
    {
      "land_id": 1,
      "measurement_type": "walk_around",
      "coordinates": [...],
      "measured_at": "2024-01-15T10:30:00Z",
      "device_id": "device-abc-123"
    },
    {
      "land_id": 2,
      "measurement_type": "point_based",
      "coordinates": [...],
      "measured_at": "2024-01-15T11:00:00Z",
      "device_id": "device-abc-123"
    }
  ]
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Batch measurements processed",
  "data": {
    "created": 2,
    "failed": 0,
    "measurements": [
      {
        "id": 1,
        "area_acres": 2.52,
        "sync_status": "synced"
      },
      {
        "id": 2,
        "area_acres": 1.83,
        "sync_status": "synced"
      }
    ],
    "errors": []
  }
}
```

---

## Job Management Endpoints

### GET /jobs

Get all jobs.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Query Parameters:**

- `status` (optional): Filter by status (pending, assigned, in_progress, completed, cancelled)
- `driver_id` (optional): Filter by assigned driver
- `from_date` (optional): Filter from date
- `to_date` (optional): Filter to date
- `page` (optional): Page number
- `per_page` (optional): Items per page

**Response (200):**

```json
{
  "success": true,
  "data": {
    "jobs": [
      {
        "id": 1,
        "job_number": "JOB-2024-001",
        "job_type": "plowing",
        "status": "completed",
        "land": {
          "id": 1,
          "name": "Field A - Gampaha",
          "area_acres": 2.5
        },
        "driver": {
          "id": 2,
          "name": "Nimal Silva"
        },
        "customer_name": "Sunil Perera",
        "scheduled_at": "2024-01-15T08:00:00Z",
        "started_at": "2024-01-15T08:15:00Z",
        "completed_at": "2024-01-15T10:30:00Z",
        "rate_per_acre": 6000,
        "actual_area_acres": 2.52,
        "actual_cost": 15120,
        "created_at": "2024-01-14T10:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 25,
      "last_page": 2
    }
  }
}
```

---

### POST /jobs

Create a new job.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "land_id": 1,
  "job_type": "plowing",
  "customer_name": "Sunil Perera",
  "customer_phone": "+94771234570",
  "scheduled_at": "2024-01-20T08:00:00Z",
  "assigned_driver_id": 2,
  "rate_per_acre": 6000,
  "notes": "Need to complete before rain"
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Job created successfully",
  "data": {
    "job": {
      "id": 2,
      "job_number": "JOB-2024-002",
      "job_type": "plowing",
      "status": "assigned",
      "land_id": 1,
      "assigned_driver_id": 2,
      "customer_name": "Sunil Perera",
      "scheduled_at": "2024-01-20T08:00:00Z",
      "rate_per_acre": 6000,
      "estimated_area_acres": 2.5,
      "estimated_cost": 15000
    }
  }
}
```

---

### PUT /jobs/{id}/status

Update job status.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "status": "in_progress",
  "notes": "Started work at 08:15"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Job status updated",
  "data": {
    "job": {
      "id": 2,
      "status": "in_progress",
      "started_at": "2024-01-20T08:15:00Z"
    }
  }
}
```

---

### POST /jobs/{id}/complete

Mark job as completed.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "actual_area_acres": 2.52,
  "notes": "Job completed successfully"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Job completed successfully",
  "data": {
    "job": {
      "id": 2,
      "status": "completed",
      "completed_at": "2024-01-20T10:45:00Z",
      "actual_area_acres": 2.52,
      "actual_cost": 15120
    }
  }
}
```

---

## Tracking Endpoints

### POST /tracking/logs

Submit GPS tracking data.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "job_id": 2,
  "locations": [
    {
      "latitude": 7.0917,
      "longitude": 79.995,
      "altitude": 25.5,
      "accuracy": 5.0,
      "speed": 12.5,
      "heading": 45.0,
      "recorded_at": "2024-01-20T08:15:00Z"
    },
    {
      "latitude": 7.092,
      "longitude": 79.9952,
      "altitude": 26.0,
      "accuracy": 4.8,
      "speed": 13.2,
      "heading": 47.0,
      "recorded_at": "2024-01-20T08:15:30Z"
    }
  ]
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Tracking data saved",
  "data": {
    "logs_created": 2
  }
}
```

---

### GET /tracking/driver/{driver_id}

Get driver's tracking history.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Query Parameters:**

- `from_date` (optional): Start date
- `to_date` (optional): End date
- `job_id` (optional): Filter by specific job

**Response (200):**

```json
{
  "success": true,
  "data": {
    "driver": {
      "id": 2,
      "name": "Nimal Silva"
    },
    "tracking_logs": [
      {
        "id": 1,
        "latitude": 7.0917,
        "longitude": 79.995,
        "speed": 12.5,
        "recorded_at": "2024-01-20T08:15:00Z",
        "job": {
          "id": 2,
          "job_number": "JOB-2024-002"
        }
      }
    ],
    "summary": {
      "total_points": 156,
      "total_distance_km": 45.8,
      "active_duration_hours": 2.5
    }
  }
}
```

---

## Invoice Endpoints

### GET /invoices

Get all invoices.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Query Parameters:**

- `status` (optional): Filter by status (draft, issued, paid, cancelled)
- `from_date` (optional): Filter from date
- `to_date` (optional): Filter to date
- `page` (optional): Page number
- `per_page` (optional): Items per page

**Response (200):**

```json
{
  "success": true,
  "data": {
    "invoices": [
      {
        "id": 1,
        "invoice_number": "INV-2024-001",
        "customer_name": "Sunil Perera",
        "total_amount": 15120,
        "status": "paid",
        "issued_at": "2024-01-20T11:00:00Z",
        "paid_at": "2024-01-20T14:30:00Z",
        "job": {
          "id": 2,
          "job_number": "JOB-2024-002"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 35,
      "last_page": 3
    }
  }
}
```

---

### POST /invoices

Create a new invoice.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "job_id": 2,
  "customer_name": "Sunil Perera",
  "customer_phone": "+94771234570",
  "customer_address": "123 Main St, Gampaha",
  "line_items": [
    {
      "description": "Land plowing service",
      "quantity": 2.52,
      "rate": 6000,
      "amount": 15120
    }
  ],
  "notes": "Payment due in 7 days"
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Invoice created successfully",
  "data": {
    "invoice": {
      "id": 2,
      "invoice_number": "INV-2024-002",
      "customer_name": "Sunil Perera",
      "subtotal": 15120,
      "tax_amount": 0,
      "total_amount": 15120,
      "status": "issued",
      "issued_at": "2024-01-21T10:00:00Z",
      "pdf_path": null
    }
  }
}
```

---

### GET /invoices/{id}/pdf

Download invoice PDF.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Response (200):**
Binary PDF file with appropriate headers.

---

### POST /invoices/{id}/generate-pdf

Trigger PDF generation for invoice.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Response (202):**

```json
{
  "success": true,
  "message": "PDF generation queued",
  "data": {
    "job_id": "pdf-gen-12345"
  }
}
```

---

## Payment Endpoints

### POST /payments

Record a payment.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "invoice_id": 2,
  "amount": 15120,
  "payment_method": "cash",
  "reference_number": null,
  "paid_at": "2024-01-21T14:30:00Z",
  "notes": "Full payment received"
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Payment recorded successfully",
  "data": {
    "payment": {
      "id": 2,
      "payment_number": "PAY-2024-002",
      "invoice_id": 2,
      "amount": 15120,
      "payment_method": "cash",
      "paid_at": "2024-01-21T14:30:00Z"
    },
    "invoice": {
      "id": 2,
      "status": "paid",
      "paid_at": "2024-01-21T14:30:00Z"
    }
  }
}
```

---

## Expense Endpoints

### GET /expenses

Get all expenses.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Query Parameters:**

- `category` (optional): Filter by category (fuel, maintenance, parts, salary, other)
- `driver_id` (optional): Filter by driver
- `from_date` (optional): Filter from date
- `to_date` (optional): Filter to date
- `page` (optional): Page number

**Response (200):**

```json
{
  "success": true,
  "data": {
    "expenses": [
      {
        "id": 1,
        "expense_number": "EXP-2024-001",
        "category": "fuel",
        "description": "Diesel for tractor",
        "amount": 5000,
        "expense_date": "2024-01-20",
        "driver": {
          "id": 2,
          "name": "Nimal Silva"
        },
        "job": {
          "id": 2,
          "job_number": "JOB-2024-002"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 48,
      "last_page": 4
    },
    "summary": {
      "total_amount": 125000,
      "by_category": {
        "fuel": 65000,
        "maintenance": 35000,
        "parts": 25000
      }
    }
  }
}
```

---

### POST /expenses

Create a new expense.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Request Body:**

```json
{
  "category": "fuel",
  "description": "Diesel for tractor",
  "amount": 5000,
  "expense_date": "2024-01-20",
  "job_id": 2,
  "driver_id": 2,
  "notes": "Full tank"
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Expense created successfully",
  "data": {
    "expense": {
      "id": 2,
      "expense_number": "EXP-2024-002",
      "category": "fuel",
      "amount": 5000,
      "expense_date": "2024-01-20"
    }
  }
}
```

---

## Subscription Endpoints

### GET /subscriptions/packages

Get available subscription packages.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "packages": [
      {
        "id": 1,
        "name": "free",
        "display_name": "Free Plan",
        "max_measurements": 10,
        "max_drivers": 2,
        "max_jobs": 20,
        "price_monthly": 0,
        "features": ["Basic measurement", "Job management"]
      },
      {
        "id": 2,
        "name": "basic",
        "display_name": "Basic Plan",
        "max_measurements": 100,
        "max_drivers": 5,
        "max_jobs": 200,
        "price_monthly": 2500,
        "features": ["All Free features", "Invoice generation", "Basic reports"]
      },
      {
        "id": 3,
        "name": "pro",
        "display_name": "Pro Plan",
        "max_measurements": -1,
        "max_drivers": -1,
        "max_jobs": -1,
        "price_monthly": 5000,
        "features": [
          "Unlimited everything",
          "Advanced reports",
          "Priority support"
        ]
      }
    ]
  }
}
```

---

### GET /subscriptions/usage

Get current organization usage.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "current_package": {
      "name": "basic",
      "expires_at": "2024-12-31T23:59:59Z"
    },
    "usage": {
      "measurements": {
        "used": 45,
        "limit": 100,
        "percentage": 45
      },
      "drivers": {
        "used": 3,
        "limit": 5,
        "percentage": 60
      },
      "jobs": {
        "used": 87,
        "limit": 200,
        "percentage": 43.5
      }
    }
  }
}
```

---

## Report Endpoints

### GET /reports/financial

Get financial summary report.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Query Parameters:**

- `from_date` (required): Start date
- `to_date` (required): End date

**Response (200):**

```json
{
  "success": true,
  "data": {
    "period": {
      "from": "2024-01-01",
      "to": "2024-01-31"
    },
    "income": {
      "total": 450000,
      "invoices_count": 15,
      "paid_invoices": 12,
      "pending_invoices": 3
    },
    "expenses": {
      "total": 125000,
      "by_category": {
        "fuel": 65000,
        "maintenance": 35000,
        "parts": 25000
      }
    },
    "profit": 325000,
    "margin_percentage": 72.2
  }
}
```

---

### GET /reports/jobs

Get jobs summary report.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Query Parameters:**

- `from_date` (required): Start date
- `to_date` (required): End date
- `driver_id` (optional): Filter by driver

**Response (200):**

```json
{
  "success": true,
  "data": {
    "period": {
      "from": "2024-01-01",
      "to": "2024-01-31"
    },
    "summary": {
      "total_jobs": 28,
      "completed_jobs": 25,
      "pending_jobs": 3,
      "total_area_covered_acres": 68.5,
      "total_revenue": 450000
    },
    "by_driver": [
      {
        "driver_id": 2,
        "driver_name": "Nimal Silva",
        "jobs_completed": 15,
        "area_covered_acres": 42.3,
        "revenue_generated": 280000
      }
    ],
    "by_job_type": {
      "plowing": {
        "count": 18,
        "revenue": 320000
      },
      "harvesting": {
        "count": 7,
        "revenue": 130000
      }
    }
  }
}
```

---

## Error Responses

All endpoints follow a consistent error response format:

**400 Bad Request:**

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

**401 Unauthorized:**

```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

**403 Forbidden:**

```json
{
  "success": false,
  "message": "You do not have permission to perform this action"
}
```

**404 Not Found:**

```json
{
  "success": false,
  "message": "Resource not found"
}
```

**422 Unprocessable Entity:**

```json
{
  "success": false,
  "message": "The given data was invalid",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

**429 Too Many Requests:**

```json
{
  "success": false,
  "message": "Too many requests. Please try again later."
}
```

**500 Internal Server Error:**

```json
{
  "success": false,
  "message": "An unexpected error occurred. Please try again later."
}
```

---

## Rate Limiting

- **Authentication endpoints**: 5 requests per minute
- **Standard endpoints**: 60 requests per minute
- **Batch/sync endpoints**: 30 requests per minute

Rate limit headers included in responses:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1705843200
```

---

## Pagination

All list endpoints support pagination:

**Query Parameters:**

- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)

**Response Format:**

```json
{
  "data": {
    "items": [...],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 150,
      "last_page": 10,
      "from": 1,
      "to": 15
    }
  }
}
```

---

## Versioning

API version is included in the URL: `/api/v1/`

When breaking changes are introduced, a new version will be released (e.g., `/api/v2/`).

---

## Security

1. All requests must use HTTPS
2. JWT tokens expire after 1 hour
3. Refresh tokens expire after 30 days
4. Failed login attempts are rate-limited
5. All inputs are validated and sanitized
6. SQL injection prevention through ORM
7. CORS properly configured
8. Organization-level data isolation enforced

---

## Testing

**Base URL for testing**: `https://staging-api.geo-ops.lk/api/v1`

Test credentials will be provided separately.
