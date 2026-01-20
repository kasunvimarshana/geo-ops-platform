# GeoOps Platform - Complete API Reference

## Overview

Complete REST API for GPS land measurement and agricultural field-service management.

**Base URL**: `http://localhost:8000/api`

---

## Authentication (5 endpoints)

### POST /auth/register

Create new user account with organization

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+94771234567",
  "organization_name": "Green Fields Farm",
  "role": "owner"
}
```

### POST /auth/login

Authenticate user

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Returns**: JWT token, user data

### GET /auth/me

Get authenticated user details

### POST /auth/refresh

Refresh JWT token

### POST /auth/logout

Logout and invalidate token

---

## Land Measurements (5 endpoints)

### GET /measurements

List all measurements with pagination

- Query params: `page`, `per_page`, `status`

### GET /measurements/{id}

Get single measurement with details

### POST /measurements

Create new land measurement

```json
{
  "location_name": "Field A",
  "coordinates": [
    { "latitude": 6.9271, "longitude": 79.8612 },
    { "latitude": 6.9272, "longitude": 79.8613 },
    { "latitude": 6.9273, "longitude": 79.8614 }
  ],
  "measurement_type": "walk_around",
  "notes": "Main cultivation area"
}
```

### PUT /measurements/{id}

Update existing measurement

### DELETE /measurements/{id}

Delete measurement (soft delete)

---

## Jobs (7 endpoints)

### GET /jobs

List all jobs with pagination

- Query params: `status`, `driver_id`, `customer_id`

### GET /jobs/{id}

Get job details with relationships

### POST /jobs

Create new job

```json
{
  "customer_id": 1,
  "land_measurement_id": 5,
  "service_type": "plowing",
  "scheduled_at": "2024-01-20T10:00:00Z",
  "notes": "Heavy soil condition"
}
```

### PUT /jobs/{id}

Update job details

### POST /jobs/{id}/status

Update job status

```json
{
  "status": "in_progress"
}
```

Status values: `pending`, `assigned`, `in_progress`, `completed`, `billed`, `paid`

### POST /jobs/{id}/assign

Assign driver and machine

```json
{
  "driver_id": 2,
  "machine_id": 3
}
```

### DELETE /jobs/{id}

Delete job

---

## GPS Tracking (4 endpoints)

### POST /tracking

Batch upload GPS locations

```json
{
  "driver_id": 2,
  "job_id": 5,
  "locations": [
    {
      "latitude": 6.9271,
      "longitude": 79.8612,
      "accuracy": 5.5,
      "speed": 12.3,
      "heading": 45.0,
      "recorded_at": "2024-01-20T10:15:30Z"
    }
  ]
}
```

### GET /tracking/drivers/{driverId}

Get driver location history

- Query params: `from_date`, `to_date`

### GET /tracking/jobs/{jobId}

Get job-specific tracking data

### GET /tracking/active

Get currently active drivers (tracked in last 2 hours)

---

## Invoices (11 endpoints)

### GET /invoices

List all invoices with pagination

- Query params: `status`, `customer_id`, `from_date`, `to_date`

### GET /invoices/{id}

Get invoice details with balance calculation

### POST /invoices

Create invoice manually

```json
{
  "customer_id": 1,
  "job_id": 5,
  "subtotal": 50000.0,
  "tax": 5000.0,
  "total": 55000.0,
  "issued_at": "2024-01-20",
  "due_at": "2024-02-20"
}
```

### POST /jobs/{jobId}/invoice

Generate invoice from completed job

```json
{
  "rate_per_unit": 5000,
  "tax_percentage": 10,
  "due_at": "2024-02-20"
}
```

### PUT /invoices/{id}

Update invoice (only drafts)

### POST /invoices/{id}/status

Update invoice status

```json
{
  "status": "sent"
}
```

Status values: `draft`, `sent`, `paid`, `overdue`, `cancelled`

### POST /invoices/{id}/paid

Mark invoice as paid

### GET /invoices/{id}/pdf

Download invoice PDF

### POST /invoices/{id}/email

Send invoice via email to customer

### DELETE /invoices/{id}

Delete invoice (not paid invoices only)

### GET /invoices-summary

Get invoice statistics

```json
{
  "total_count": 50,
  "paid_count": 30,
  "outstanding_amount": 500000.0
}
```

---

## Payments (5 endpoints)

### GET /payments

List all payments with pagination

- Query params: `customer_id`, `invoice_id`, `method`, `from_date`, `to_date`

### GET /payments/{id}

Get payment details

### POST /payments

Record new payment

```json
{
  "customer_id": 1,
  "invoice_id": 5,
  "amount": 55000.0,
  "method": "bank",
  "reference": "TXN123456",
  "notes": "Bank transfer",
  "paid_at": "2024-01-20T14:30:00Z"
}
```

Methods: `cash`, `bank`, `mobile`, `credit`

### PUT /payments/{id}

Update payment record

### DELETE /payments/{id}

Delete payment

### GET /payments-summary

Get payment statistics

- Query param: `period` (today, this_week, this_month, this_year, all)

### GET /customers/{customerId}/payments

Get customer payment history

---

## Expenses (8 endpoints)

### GET /expenses

List all expenses with pagination

- Query params: `category`, `status`, `machine_id`, `driver_id`, `from_date`, `to_date`

### GET /expenses/{id}

Get expense details

### POST /expenses

Create new expense

```json
{
  "machine_id": 3,
  "category": "fuel",
  "amount": 15000.0,
  "description": "Diesel refill",
  "expense_date": "2024-01-20"
}
```

Categories: `fuel`, `parts`, `maintenance`, `labor`, `other`

### PUT /expenses/{id}

Update expense (pending only)

### POST /expenses/{id}/receipt

Upload receipt photo

- Form data with `receipt` file

### POST /expenses/{id}/approve

Approve expense

### POST /expenses/{id}/reject

Reject expense

### DELETE /expenses/{id}

Delete expense (not approved)

### GET /expenses-summary

Get expense statistics

- Query param: `period`

### GET /machines/{machineId}/expenses

Get machine-specific expenses

### GET /drivers/{driverId}/expenses

Get driver-specific expenses

---

## Reports (4 endpoints)

### GET /reports/financial

Get financial summary

- Query params: `from_date`, `to_date` (defaults to current month)

```json
{
  "income": {
    "total_invoiced": 1000000.0,
    "total_paid": 800000.0,
    "total_outstanding": 200000.0,
    "payments_by_method": {
      "cash": 300000.0,
      "bank": 400000.0,
      "mobile": 100000.0
    }
  },
  "expenses": {
    "total": 300000.0,
    "by_category": {
      "fuel": 100000.0,
      "maintenance": 150000.0
    }
  },
  "profitability": {
    "revenue": 800000.0,
    "expenses": 300000.0,
    "profit": 500000.0,
    "profit_margin_percentage": 62.5
  }
}
```

### GET /reports/jobs

Get jobs analytics

- Query params: `from_date`, `to_date`

```json
{
  "summary": {
    "total_jobs": 50,
    "completed_jobs": 35,
    "completion_rate_percentage": 70.0,
    "jobs_by_status": {
      "pending": 5,
      "in_progress": 10,
      "completed": 35
    }
  },
  "driver_performance": [...],
  "machine_utilization": [...]
}
```

### GET /reports/expenses

Get expenses breakdown

- Query params: `from_date`, `to_date`

### GET /reports/dashboard

Get dashboard overview (current month summary)

```json
{
  "current_month": {
    "jobs_created": 15,
    "jobs_completed": 12,
    "revenue": 500000.0,
    "expenses": 150000.0,
    "profit": 350000.0
  },
  "outstanding": {
    "invoices_amount": 200000.0
  },
  "current_status": {
    "active_drivers": 3
  }
}
```

---

## Utilities

### GET /health

API health check (public)

```json
{
  "status": "healthy",
  "timestamp": "2024-01-20T10:30:00Z"
}
```

---

## Rate Limiting

- **Default**: 60 requests per minute per user
- **Header**: `X-RateLimit-Remaining` shows remaining requests

---

## Error Responses

### 401 Unauthorized

```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden

```json
{
  "message": "Forbidden. Required role: owner or admin"
}
```

### 422 Validation Error

```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "amount": ["The amount must be greater than 0."]
  }
}
```

---

## Data Models

### Job Status Lifecycle

```
pending → assigned → in_progress → completed → billed → paid
```

### Invoice Status Lifecycle

```
draft → sent → paid
              ↓
           overdue
```

### Expense Status

```
pending → approved
        → rejected
```

---

## Notes

1. All dates are in ISO 8601 format (`YYYY-MM-DDTHH:mm:ssZ`)
2. All monetary amounts are in LKR (Sri Lankan Rupees)
3. Pagination defaults to 15 items per page (max 100)
4. Soft deletes are used - deleted records are not permanently removed
5. Organization-level data isolation is enforced automatically
6. GPS coordinates use decimal degrees format

---

## Total Endpoints: 50+

- Authentication: 5
- Measurements: 5
- Jobs: 7
- Tracking: 4
- Invoices: 11
- Payments: 7
- Expenses: 11
- Reports: 4
- Utilities: 1

---

**Last Updated**: 2024-01-19  
**API Version**: 1.0
