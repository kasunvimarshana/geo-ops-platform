# API Documentation

## GPS Field Management Platform REST API

**Base URL:** `https://api.geo-ops.lk/api/v1`  
**Authentication:** JWT Bearer Token  
**Content-Type:** `application/json`

---

## Table of Contents

1. [Authentication](#authentication)
2. [Users & Organizations](#users--organizations)
3. [Land Measurements](#land-measurements)
4. [Jobs Management](#jobs-management)
5. [GPS Tracking](#gps-tracking)
6. [Billing & Invoices](#billing--invoices)
7. [Expenses](#expenses)
8. [Payments](#payments)
9. [Subscriptions](#subscriptions)
10. [Reports](#reports)
11. [Sync Operations](#sync-operations)

---

## Authentication

### POST /auth/register

Register a new organization and admin user.

**Request:**

```json
{
  "organization_name": "Green Fields Farming",
  "name": "John Doe",
  "email": "john@greenfields.lk",
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
      "name": "John Doe",
      "email": "john@greenfields.lk",
      "role": "owner",
      "organization": {
        "id": 1,
        "name": "Green Fields Farming"
      }
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 900
  }
}
```

---

### POST /auth/login

Authenticate user and receive JWT tokens.

**Request:**

```json
{
  "email": "john@greenfields.lk",
  "password": "SecurePass123!",
  "device_id": "uuid-device-123"
}
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@greenfields.lk",
      "role": "owner",
      "organization_id": 1
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 900
  }
}
```

---

### POST /auth/refresh

Refresh expired access token.

**Headers:**

```
Authorization: Bearer {refresh_token}
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 900
  }
}
```

---

### POST /auth/logout

Invalidate current token.

**Headers:**

```
Authorization: Bearer {access_token}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

---

## Users & Organizations

### GET /users

List users in organization.

**Query Parameters:**

- `role` (optional): Filter by role
- `is_active` (optional): Filter by active status
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 20)

**Response (200):**

```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": 2,
        "name": "Driver One",
        "email": "driver1@greenfields.lk",
        "phone": "+94771234568",
        "role": "driver",
        "is_active": true,
        "created_at": "2026-01-15T10:30:00Z"
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 20,
      "total": 5,
      "last_page": 1
    }
  }
}
```

---

### POST /users

Create a new user.

**Request:**

```json
{
  "name": "Driver Two",
  "email": "driver2@greenfields.lk",
  "phone": "+94771234569",
  "password": "SecurePass123!",
  "role": "driver",
  "is_active": true
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "user": {
      "id": 3,
      "name": "Driver Two",
      "email": "driver2@greenfields.lk",
      "role": "driver",
      "is_active": true
    }
  }
}
```

---

### GET /users/{id}

Get user details.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 2,
      "name": "Driver One",
      "email": "driver1@greenfields.lk",
      "phone": "+94771234568",
      "role": "driver",
      "is_active": true,
      "avatar_url": "https://storage.../avatar.jpg",
      "last_login_at": "2026-01-17T08:30:00Z",
      "created_at": "2026-01-15T10:30:00Z"
    }
  }
}
```

---

### PUT /users/{id}

Update user details.

**Request:**

```json
{
  "name": "Driver One Updated",
  "phone": "+94771234599",
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
      "name": "Driver One Updated",
      "phone": "+94771234599"
    }
  }
}
```

---

### GET /organization

Get current organization details.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "organization": {
      "id": 1,
      "name": "Green Fields Farming",
      "email": "info@greenfields.lk",
      "phone": "+94771234567",
      "address": "123 Main Street, Colombo",
      "logo_url": "https://storage.../logo.png",
      "status": "active",
      "subscription": {
        "package": "Pro",
        "status": "active",
        "end_date": "2026-02-17"
      }
    }
  }
}
```

---

## Land Measurements

### GET /land-plots

List measured land plots.

**Query Parameters:**

- `user_id` (optional): Filter by measurer
- `from_date` (optional): Start date
- `to_date` (optional): End date
- `page`, `per_page`

**Response (200):**

```json
{
  "success": true,
  "data": {
    "land_plots": [
      {
        "id": 1,
        "name": "North Field",
        "area_acres": 2.5,
        "area_hectares": 1.012,
        "center_latitude": 6.9271,
        "center_longitude": 79.8612,
        "measurement_method": "walk_around",
        "measured_at": "2026-01-17T09:15:00Z",
        "user": {
          "id": 2,
          "name": "Driver One"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "total": 15
    }
  }
}
```

---

### POST /land-plots

Create a new land measurement.

**Request:**

```json
{
  "name": "South Field",
  "description": "Main cultivation area",
  "coordinates": {
    "type": "Polygon",
    "coordinates": [
      [
        [79.8612, 6.9271],
        [79.8615, 6.9271],
        [79.8615, 6.9268],
        [79.8612, 6.9268],
        [79.8612, 6.9271]
      ]
    ]
  },
  "measurement_method": "walk_around",
  "accuracy_meters": 5.2,
  "measured_at": "2026-01-17T09:15:00Z",
  "notes": "Measured during morning"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Land plot created successfully",
    "data": {
        "land_plot": {
            "id": 2,
            "name": "South Field",
            "area_acres": 3.75,
            "area_hectares": 1.518,
            "area_square_meters": 15180.5,
            "perimeter_meters": 520.3,
            "center_latitude": 6.92695,
            "center_longitude": 79.86135,
            "coordinates": {...},
            "measured_at": "2026-01-17T09:15:00Z"
        }
    }
}
```

---

### GET /land-plots/{id}

Get land plot details.

**Response (200):**

```json
{
    "success": true,
    "data": {
        "land_plot": {
            "id": 1,
            "name": "North Field",
            "description": "Main cultivation area",
            "area_acres": 2.5,
            "area_hectares": 1.012,
            "area_square_meters": 10120,
            "perimeter_meters": 410.5,
            "center_latitude": 6.9271,
            "center_longitude": 79.8612,
            "coordinates": {...},
            "measurement_method": "walk_around",
            "accuracy_meters": 5.2,
            "measured_at": "2026-01-17T09:15:00Z",
            "notes": "Measured during morning",
            "user": {
                "id": 2,
                "name": "Driver One"
            },
            "jobs_count": 3,
            "created_at": "2026-01-17T09:15:00Z"
        }
    }
}
```

---

### PUT /land-plots/{id}

Update land plot.

**Request:**

```json
{
  "name": "North Field - Updated",
  "notes": "Remeasured with better accuracy"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Land plot updated successfully",
  "data": {
    "land_plot": {
      "id": 1,
      "name": "North Field - Updated"
    }
  }
}
```

---

### DELETE /land-plots/{id}

Delete land plot (soft delete).

**Response (200):**

```json
{
  "success": true,
  "message": "Land plot deleted successfully"
}
```

---

## Jobs Management

### GET /jobs

List jobs.

**Query Parameters:**

- `status` (optional): Filter by status
- `driver_id` (optional): Filter by driver
- `from_date`, `to_date`
- `page`, `per_page`

**Response (200):**

```json
{
  "success": true,
  "data": {
    "jobs": [
      {
        "id": 1,
        "customer_name": "Farmer Perera",
        "job_type": "plowing",
        "status": "completed",
        "land_plot": {
          "id": 1,
          "name": "North Field",
          "area_acres": 2.5
        },
        "driver": {
          "id": 2,
          "name": "Driver One"
        },
        "scheduled_date": "2026-01-17",
        "start_time": "2026-01-17T08:00:00Z",
        "end_time": "2026-01-17T11:30:00Z",
        "duration_hours": 3.5,
        "total_amount": 7500.0,
        "created_at": "2026-01-16T10:00:00Z"
      }
    ],
    "meta": {
      "current_page": 1,
      "total": 25
    }
  }
}
```

---

### POST /jobs

Create a new job.

**Request:**

```json
{
  "land_plot_id": 1,
  "driver_id": 2,
  "customer_name": "Farmer Silva",
  "customer_phone": "+94771234570",
  "customer_address": "456 Farm Road, Kurunegala",
  "job_type": "plowing",
  "scheduled_date": "2026-01-18",
  "rate_per_unit": 3000.0,
  "notes": "Prefer morning hours"
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
      "customer_name": "Farmer Silva",
      "job_type": "plowing",
      "status": "pending",
      "scheduled_date": "2026-01-18",
      "rate_per_unit": 3000.0,
      "total_amount": 7500.0
    }
  }
}
```

---

### GET /jobs/{id}

Get job details.

**Response (200):**

```json
{
    "success": true,
    "data": {
        "job": {
            "id": 1,
            "customer_name": "Farmer Perera",
            "customer_phone": "+94771234570",
            "customer_address": "456 Farm Road",
            "job_type": "plowing",
            "status": "completed",
            "land_plot": {
                "id": 1,
                "name": "North Field",
                "area_acres": 2.5,
                "coordinates": {...}
            },
            "driver": {
                "id": 2,
                "name": "Driver One",
                "phone": "+94771234568"
            },
            "scheduled_date": "2026-01-17",
            "start_time": "2026-01-17T08:00:00Z",
            "end_time": "2026-01-17T11:30:00Z",
            "duration_hours": 3.5,
            "rate_per_unit": 3000.00,
            "total_amount": 7500.00,
            "invoice": {
                "id": 1,
                "invoice_number": "INV-2026-0001",
                "status": "paid"
            },
            "expenses": [
                {
                    "id": 1,
                    "category": "fuel",
                    "amount": 1500.00
                }
            ],
            "notes": "Completed successfully",
            "created_at": "2026-01-16T10:00:00Z"
        }
    }
}
```

---

### PUT /jobs/{id}

Update job.

**Request:**

```json
{
  "status": "in_progress",
  "start_time": "2026-01-18T07:30:00Z"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Job updated successfully",
  "data": {
    "job": {
      "id": 2,
      "status": "in_progress",
      "start_time": "2026-01-18T07:30:00Z"
    }
  }
}
```

---

### POST /jobs/{id}/complete

Mark job as completed.

**Request:**

```json
{
  "end_time": "2026-01-18T11:00:00Z",
  "notes": "All work completed as requested"
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
      "end_time": "2026-01-18T11:00:00Z",
      "duration_hours": 3.5
    },
    "invoice": {
      "id": 2,
      "invoice_number": "INV-2026-0002",
      "total_amount": 7500.0,
      "pdf_url": "https://storage.../invoices/INV-2026-0002.pdf"
    }
  }
}
```

---

## GPS Tracking

### POST /gps-tracking

Record GPS location(s).

**Request (Single Point):**

```json
{
  "job_id": 2,
  "latitude": 6.9271,
  "longitude": 79.8612,
  "altitude": 25.5,
  "accuracy": 8.2,
  "speed": 5.5,
  "heading": 180.0,
  "timestamp": "2026-01-18T08:15:00Z",
  "battery_level": 85
}
```

**Request (Batch):**

```json
{
  "locations": [
    {
      "job_id": 2,
      "latitude": 6.9271,
      "longitude": 79.8612,
      "timestamp": "2026-01-18T08:15:00Z"
    },
    {
      "job_id": 2,
      "latitude": 6.9272,
      "longitude": 79.8613,
      "timestamp": "2026-01-18T08:16:00Z"
    }
  ]
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "GPS location(s) recorded successfully",
  "data": {
    "recorded_count": 2
  }
}
```

---

### GET /gps-tracking

Get GPS tracking history.

**Query Parameters:**

- `user_id` (optional): Filter by user
- `job_id` (optional): Filter by job
- `from_time`, `to_time`
- `page`, `per_page`

**Response (200):**

```json
{
  "success": true,
  "data": {
    "locations": [
      {
        "id": 1,
        "latitude": 6.9271,
        "longitude": 79.8612,
        "altitude": 25.5,
        "accuracy": 8.2,
        "speed": 5.5,
        "heading": 180.0,
        "timestamp": "2026-01-18T08:15:00Z",
        "user": {
          "id": 2,
          "name": "Driver One"
        },
        "job": {
          "id": 2,
          "customer_name": "Farmer Silva"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "total": 250
    }
  }
}
```

---

### GET /gps-tracking/active-drivers

Get currently active drivers with their last known location.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "active_drivers": [
      {
        "user_id": 2,
        "name": "Driver One",
        "last_location": {
          "latitude": 6.9271,
          "longitude": 79.8612,
          "timestamp": "2026-01-18T08:15:00Z"
        },
        "current_job": {
          "id": 2,
          "customer_name": "Farmer Silva",
          "status": "in_progress"
        }
      }
    ]
  }
}
```

---

## Billing & Invoices

### GET /invoices

List invoices.

**Query Parameters:**

- `status` (optional)
- `from_date`, `to_date`
- `customer_name` (optional): Search by name
- `page`, `per_page`

**Response (200):**

```json
{
  "success": true,
  "data": {
    "invoices": [
      {
        "id": 1,
        "invoice_number": "INV-2026-0001",
        "customer_name": "Farmer Perera",
        "total_amount": 7500.0,
        "status": "paid",
        "issued_at": "2026-01-17",
        "paid_at": "2026-01-17T15:30:00Z",
        "pdf_url": "https://storage.../invoices/INV-2026-0001.pdf",
        "job": {
          "id": 1,
          "job_type": "plowing"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "total": 50
    }
  }
}
```

---

### GET /invoices/{id}

Get invoice details.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "invoice": {
      "id": 1,
      "invoice_number": "INV-2026-0001",
      "customer_name": "Farmer Perera",
      "customer_email": "perera@email.lk",
      "customer_phone": "+94771234570",
      "subtotal": 7500.0,
      "tax_amount": 0.0,
      "discount_amount": 0.0,
      "total_amount": 7500.0,
      "currency": "LKR",
      "status": "paid",
      "issued_at": "2026-01-17",
      "due_date": "2026-01-24",
      "paid_at": "2026-01-17T15:30:00Z",
      "pdf_url": "https://storage.../invoices/INV-2026-0001.pdf",
      "job": {
        "id": 1,
        "job_type": "plowing",
        "land_plot": {
          "name": "North Field",
          "area_acres": 2.5
        }
      },
      "payments": [
        {
          "id": 1,
          "amount": 7500.0,
          "method": "cash",
          "paid_at": "2026-01-17T15:30:00Z"
        }
      ]
    }
  }
}
```

---

### GET /invoices/{id}/download

Download invoice PDF.

**Response:** Binary PDF file

---

### POST /invoices/{id}/send-email

Send invoice via email.

**Request:**

```json
{
  "email": "customer@email.lk",
  "subject": "Invoice for Field Work",
  "message": "Thank you for your business"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Invoice sent successfully"
}
```

---

## Expenses

### GET /expenses

List expenses.

**Query Parameters:**

- `category` (optional)
- `job_id` (optional)
- `user_id` (optional)
- `from_date`, `to_date`
- `page`, `per_page`

**Response (200):**

```json
{
  "success": true,
  "data": {
    "expenses": [
      {
        "id": 1,
        "category": "fuel",
        "amount": 1500.0,
        "description": "Diesel for tractor",
        "vendor_name": "Fuel Station ABC",
        "expense_date": "2026-01-17",
        "receipt_url": "https://storage.../receipts/R001.jpg",
        "job": {
          "id": 1,
          "customer_name": "Farmer Perera"
        },
        "user": {
          "id": 2,
          "name": "Driver One"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "total": 100
    }
  }
}
```

---

### POST /expenses

Create an expense.

**Request:**

```json
{
  "job_id": 2,
  "category": "fuel",
  "amount": 1800.0,
  "description": "Diesel - 20 liters",
  "vendor_name": "Fuel Station XYZ",
  "expense_date": "2026-01-18",
  "notes": "Morning fill-up"
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
      "category": "fuel",
      "amount": 1800.0,
      "description": "Diesel - 20 liters",
      "expense_date": "2026-01-18"
    }
  }
}
```

---

### POST /expenses/{id}/upload-receipt

Upload expense receipt.

**Request:** Multipart form data

- `receipt`: File (image/pdf)

**Response (200):**

```json
{
  "success": true,
  "message": "Receipt uploaded successfully",
  "data": {
    "receipt_url": "https://storage.../receipts/R002.jpg"
  }
}
```

---

## Payments

### GET /payments

List payments.

**Query Parameters:**

- `invoice_id` (optional)
- `method` (optional)
- `from_date`, `to_date`
- `page`, `per_page`

**Response (200):**

```json
{
  "success": true,
  "data": {
    "payments": [
      {
        "id": 1,
        "amount": 7500.0,
        "method": "cash",
        "reference": "CASH-001",
        "paid_at": "2026-01-17T15:30:00Z",
        "invoice": {
          "id": 1,
          "invoice_number": "INV-2026-0001",
          "customer_name": "Farmer Perera"
        },
        "received_by": {
          "id": 1,
          "name": "John Doe"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "total": 75
    }
  }
}
```

---

### POST /payments

Record a payment.

**Request:**

```json
{
  "invoice_id": 2,
  "amount": 7500.0,
  "method": "bank_transfer",
  "reference": "TRX123456",
  "transaction_id": "BANK-REF-789",
  "paid_at": "2026-01-18T10:00:00Z",
  "notes": "Bank transfer confirmed"
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
      "amount": 7500.0,
      "method": "bank_transfer",
      "paid_at": "2026-01-18T10:00:00Z"
    },
    "invoice": {
      "id": 2,
      "status": "paid",
      "balance": 0.0
    }
  }
}
```

---

## Subscriptions

### GET /subscriptions/packages

List available subscription packages.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "packages": [
      {
        "id": 1,
        "name": "Free",
        "slug": "free",
        "price": 0.0,
        "billing_cycle": "lifetime",
        "features": {
          "measurements": 10,
          "drivers": 1,
          "exports_per_month": 5
        },
        "limits": {
          "max_measurements_per_month": 10,
          "max_drivers": 1
        }
      },
      {
        "id": 2,
        "name": "Basic",
        "slug": "basic",
        "price": 2500.0,
        "billing_cycle": "monthly",
        "features": {
          "measurements": 100,
          "drivers": 5,
          "exports_per_month": 50,
          "pdf_generation": true
        }
      },
      {
        "id": 3,
        "name": "Pro",
        "slug": "pro",
        "price": 5000.0,
        "billing_cycle": "monthly",
        "features": {
          "measurements": -1,
          "drivers": -1,
          "exports_per_month": -1,
          "pdf_generation": true,
          "advanced_reports": true,
          "api_access": true
        }
      }
    ]
  }
}
```

---

### GET /subscriptions/current

Get current subscription status.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "subscription": {
      "id": 1,
      "package": {
        "id": 2,
        "name": "Basic",
        "price": 2500.0
      },
      "status": "active",
      "start_date": "2026-01-01",
      "end_date": "2026-02-01",
      "days_remaining": 14,
      "usage_stats": {
        "measurements_this_month": 45,
        "exports_this_month": 12,
        "active_drivers": 3,
        "storage_used_mb": 512
      },
      "limits": {
        "max_measurements_per_month": 100,
        "max_drivers": 5,
        "max_storage_mb": 1024
      }
    }
  }
}
```

---

### POST /subscriptions/upgrade

Upgrade subscription package.

**Request:**

```json
{
  "package_id": 3,
  "payment_method": "bank_transfer"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Subscription upgraded successfully",
  "data": {
    "subscription": {
      "package": {
        "name": "Pro"
      },
      "status": "active",
      "end_date": "2026-02-18"
    }
  }
}
```

---

## Reports

### GET /reports/dashboard

Get dashboard summary statistics.

**Query Parameters:**

- `from_date`, `to_date`

**Response (200):**

```json
{
  "success": true,
  "data": {
    "summary": {
      "total_jobs": 50,
      "completed_jobs": 45,
      "pending_jobs": 5,
      "total_revenue": 375000.0,
      "total_expenses": 45000.0,
      "net_profit": 330000.0,
      "total_measurements": 75,
      "total_area_measured_acres": 187.5,
      "active_drivers": 5
    },
    "revenue_by_month": [
      {
        "month": "2026-01",
        "revenue": 375000.0,
        "expenses": 45000.0,
        "profit": 330000.0
      }
    ],
    "jobs_by_status": {
      "completed": 45,
      "in_progress": 3,
      "pending": 2
    },
    "top_customers": [
      {
        "name": "Farmer Perera",
        "total_jobs": 10,
        "total_amount": 75000.0
      }
    ]
  }
}
```

---

### GET /reports/financial

Generate financial report.

**Query Parameters:**

- `from_date`, `to_date` (required)
- `format`: `json` | `pdf` | `csv`

**Response (200 - JSON):**

```json
{
  "success": true,
  "data": {
    "period": {
      "from": "2026-01-01",
      "to": "2026-01-31"
    },
    "summary": {
      "total_revenue": 375000.0,
      "total_expenses": 45000.0,
      "net_profit": 330000.0
    },
    "revenue_breakdown": {
      "invoices_paid": 375000.0,
      "invoices_pending": 22500.0
    },
    "expense_breakdown": {
      "fuel": 25000.0,
      "maintenance": 15000.0,
      "parts": 5000.0
    }
  }
}
```

---

## Sync Operations

### POST /sync/pull

Pull updates since last sync.

**Request:**

```json
{
  "device_id": "uuid-device-123",
  "last_sync_timestamp": "2026-01-17T10:00:00Z",
  "entities": ["jobs", "invoices", "expenses"]
}
```

**Response (200):**

```json
{
    "success": true,
    "data": {
        "sync_timestamp": "2026-01-18T12:00:00Z",
        "updates": {
            "jobs": [
                {
                    "id": 2,
                    "action": "update",
                    "data": {...}
                }
            ],
            "invoices": [],
            "expenses": [
                {
                    "id": 5,
                    "action": "create",
                    "data": {...}
                }
            ]
        }
    }
}
```

---

### POST /sync/push

Push local changes to server.

**Request:**

```json
{
    "device_id": "uuid-device-123",
    "changes": [
        {
            "entity_type": "land_plots",
            "action": "create",
            "local_id": "temp-123",
            "data": {
                "name": "New Field",
                "coordinates": {...},
                "measured_at": "2026-01-18T09:00:00Z"
            }
        },
        {
            "entity_type": "expenses",
            "action": "create",
            "local_id": "temp-456",
            "data": {
                "category": "fuel",
                "amount": 1500.00,
                "expense_date": "2026-01-18"
            }
        }
    ]
}
```

**Response (200):**

```json
{
  "success": true,
  "data": {
    "results": [
      {
        "local_id": "temp-123",
        "status": "success",
        "server_id": 15
      },
      {
        "local_id": "temp-456",
        "status": "success",
        "server_id": 42
      }
    ],
    "sync_timestamp": "2026-01-18T12:00:00Z"
  }
}
```

---

## Error Responses

### Standard Error Format

```json
{
  "success": false,
  "message": "Error message here",
  "errors": {
    "field_name": ["Validation error message"]
  },
  "code": "ERROR_CODE"
}
```

### Common Error Codes

- `401` - Unauthorized (invalid/expired token)
- `403` - Forbidden (insufficient permissions)
- `404` - Resource not found
- `422` - Validation error
- `429` - Too many requests (rate limit)
- `500` - Internal server error

### Example Validation Error (422):

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

---

## Rate Limiting

- **Free tier**: 100 requests/hour
- **Basic tier**: 500 requests/hour
- **Pro tier**: Unlimited

**Rate Limit Headers:**

```
X-RateLimit-Limit: 500
X-RateLimit-Remaining: 498
X-RateLimit-Reset: 1642425600
```

---

## Pagination

All list endpoints support pagination with the following parameters:

- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 20, max: 100)

**Response Meta:**

```json
{
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "last_page": 8,
    "from": 1,
    "to": 20
  }
}
```

---

This API documentation provides comprehensive coverage of all endpoints for the GPS Field Management Platform.
