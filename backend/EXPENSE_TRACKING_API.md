# Expense Management and GPS Tracking API Documentation

This document provides comprehensive information about the Expense Management and GPS Tracking API endpoints for the GeoOps Platform.

## Table of Contents

1. [Expense Management API](#expense-management-api)
2. [GPS Tracking API](#gps-tracking-api)
3. [Testing Guide](#testing-guide)

---

## Expense Management API

### Overview

The Expense Management API allows organizations to track operational costs including fuel, parts, maintenance, salary, and other expenses. All expenses are organization-scoped and support offline sync capabilities.

### Supported Expense Categories

- `fuel` - Fuel and diesel expenses
- `maintenance` - Vehicle/equipment maintenance
- `parts` - Replacement parts and supplies
- `salary` - Staff salary and wages
- `transport` - Transportation costs
- `food` - Meals and refreshments
- `other` - Miscellaneous expenses

### Base URL

```
/api/v1/expenses
```

---

### 1. List Expenses

Get a paginated list of expenses with filters.

**Endpoint:** `GET /api/v1/expenses`

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Query Parameters:**

- `category` (string, optional) - Filter by expense category
- `driver_id` (integer, optional) - Filter by driver
- `job_id` (integer, optional) - Filter by job
- `start_date` (date, optional) - Filter by start date (YYYY-MM-DD)
- `end_date` (date, optional) - Filter by end date (YYYY-MM-DD)
- `search` (string, optional) - Search in expense number, description, vendor name
- `sort_by` (string, optional) - Sort field (default: expense_date)
- `sort_direction` (string, optional) - Sort direction: asc/desc (default: desc)
- `per_page` (integer, optional) - Items per page (default: 15)

**Example Request:**

```bash
curl -X GET "https://api.geo-ops.com/api/v1/expenses?category=fuel&start_date=2024-01-01&end_date=2024-01-31&per_page=20" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**

```json
{
    "success": true,
    "message": "Expenses retrieved successfully.",
    "data": {
        "data": [
            {
                "id": 1,
                "organization_id": 1,
                "job_id": 5,
                "driver_id": 3,
                "expense_number": "EXP-20240118-0001",
                "category": "fuel",
                "amount": "125.50",
                "currency": "USD",
                "expense_date": "2024-01-18T00:00:00.000000Z",
                "vendor_name": "Shell Station",
                "description": "Diesel refill for tractor",
                "receipt_path": "/receipts/receipt_123.jpg",
                "attachments": ["/receipts/attachment_456.pdf"],
                "is_synced": true,
                "job": {
                    "id": 5,
                    "job_number": "JOB-20240115-0003",
                    "service_type": "plowing",
                    "status": "in_progress"
                },
                "driver": {
                    "id": 3,
                    "name": "John Driver",
                    "email": "john@geo-ops.test",
                    "phone": "+1234567890"
                },
                "created_at": "2024-01-18T10:30:00.000000Z",
                "updated_at": "2024-01-18T10:30:00.000000Z",
                "created_by": 1,
                "updated_by": null
            }
        ],
        "meta": {
            "total": 50,
            "count": 20,
            "per_page": 20,
            "current_page": 1,
            "total_pages": 3
        }
    }
}
```

---

### 2. Create Expense

Create a new expense record.

**Endpoint:** `POST /api/v1/expenses`

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**

```json
{
    "job_id": 5,
    "driver_id": 3,
    "category": "fuel",
    "amount": 125.5,
    "currency": "USD",
    "expense_date": "2024-01-18",
    "vendor_name": "Shell Station",
    "description": "Diesel refill for tractor",
    "receipt_path": "/receipts/receipt_123.jpg",
    "attachments": ["/receipts/attachment_456.pdf"]
}
```

**Validation Rules:**

- `job_id` - Optional, must exist in field_jobs table
- `driver_id` - Optional, must exist in users table
- `category` - Required, one of: fuel, maintenance, parts, salary, transport, food, other
- `amount` - Required, numeric, min: 0, max: 9999999.99
- `currency` - Optional, one of: USD, EUR, GBP, KES (default: USD)
- `expense_date` - Required, valid date
- `vendor_name` - Optional, string, max 255 characters
- `description` - Required, string, max 1000 characters
- `receipt_path` - Optional, string, max 500 characters
- `attachments` - Optional, array of strings

**Example Response:**

```json
{
    "success": true,
    "message": "Expense created successfully.",
    "data": {
        "id": 51,
        "expense_number": "EXP-20240118-0010",
        "category": "fuel",
        "amount": "125.50",
        "currency": "USD",
        "expense_date": "2024-01-18T00:00:00.000000Z",
        "vendor_name": "Shell Station",
        "description": "Diesel refill for tractor",
        "is_synced": false,
        "created_at": "2024-01-18T14:30:00.000000Z"
    }
}
```

**Note:** Expense number is auto-generated in format: `EXP-YYYYMMDD-XXXX`

---

### 3. Get Single Expense

Retrieve details of a specific expense.

**Endpoint:** `GET /api/v1/expenses/{id}`

**Headers:**

```
Authorization: Bearer {token}
```

**Example Response:**

```json
{
    "success": true,
    "message": "Expense retrieved successfully.",
    "data": {
        "id": 1,
        "expense_number": "EXP-20240118-0001",
        "category": "fuel",
        "amount": "125.50",
        "currency": "USD",
        "expense_date": "2024-01-18T00:00:00.000000Z",
        "vendor_name": "Shell Station",
        "description": "Diesel refill for tractor",
        "receipt_path": "/receipts/receipt_123.jpg",
        "job": {
            "id": 5,
            "job_number": "JOB-20240115-0003",
            "service_type": "plowing",
            "status": "completed"
        },
        "driver": {
            "id": 3,
            "name": "John Driver",
            "email": "john@geo-ops.test"
        }
    }
}
```

---

### 4. Update Expense

Update an existing expense.

**Endpoint:** `PUT /api/v1/expenses/{id}`

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:** (all fields optional)

```json
{
    "category": "maintenance",
    "amount": 150.0,
    "description": "Updated description",
    "vendor_name": "New Vendor"
}
```

**Example Response:**

```json
{
    "success": true,
    "message": "Expense updated successfully.",
    "data": {
        "id": 1,
        "expense_number": "EXP-20240118-0001",
        "category": "maintenance",
        "amount": "150.00",
        "updated_at": "2024-01-18T15:00:00.000000Z"
    }
}
```

---

### 5. Delete Expense

Soft delete an expense.

**Endpoint:** `DELETE /api/v1/expenses/{id}`

**Headers:**

```
Authorization: Bearer {token}
```

**Example Response:**

```json
{
    "success": true,
    "message": "Expense deleted successfully.",
    "data": null
}
```

---

### 6. Get Expense Totals by Category

Get aggregated expense totals grouped by category.

**Endpoint:** `GET /api/v1/expenses/totals`

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

- `start_date` (date, optional) - Filter by start date
- `end_date` (date, optional) - Filter by end date

**Example Response:**

```json
{
    "success": true,
    "message": "Expense totals retrieved successfully.",
    "data": {
        "fuel": {
            "total": 1250.5,
            "count": 15
        },
        "maintenance": {
            "total": 2500.0,
            "count": 8
        },
        "parts": {
            "total": 800.75,
            "count": 12
        },
        "salary": {
            "total": 15000.0,
            "count": 5
        }
    }
}
```

---

## GPS Tracking API

### Overview

The GPS Tracking API enables real-time location tracking of drivers and equipment during field operations. It supports both single location submissions and batch uploads for offline sync.

### Base URL

```
/api/v1/tracking
```

---

### 1. Submit GPS Location

Submit single GPS location or batch for offline sync.

**Endpoint:** `POST /api/v1/tracking`

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Single Location Submission

**Request Body:**

```json
{
    "user_id": 3,
    "job_id": 5,
    "latitude": -1.2921,
    "longitude": 36.8219,
    "accuracy_meters": 10.5,
    "altitude_meters": 1650.0,
    "speed_mps": 5.5,
    "heading_degrees": 45.0,
    "recorded_at": "2024-01-18T10:30:00Z",
    "device_id": "device_123",
    "platform": "android",
    "metadata": {
        "battery": 85,
        "signal_strength": 4
    }
}
```

**Validation Rules:**

- `user_id` - Required, must exist in users table
- `job_id` - Optional, must exist in field_jobs table
- `latitude` - Required, between -90 and 90
- `longitude` - Required, between -180 and 180
- `accuracy_meters` - Optional, min: 0, max: 10000
- `altitude_meters` - Optional, between -500 and 10000
- `speed_mps` - Optional, min: 0, max: 200
- `heading_degrees` - Optional, between 0 and 360
- `recorded_at` - Optional, valid date (defaults to now)
- `device_id` - Optional, string, max 255 characters
- `platform` - Optional, one of: android, ios, web
- `metadata` - Optional, JSON object

**Example Response:**

```json
{
    "success": true,
    "message": "Tracking log created successfully.",
    "data": {
        "id": 1001,
        "user_id": 3,
        "job_id": 5,
        "location": {
            "latitude": "-1.2921000",
            "longitude": "36.8219000",
            "accuracy_meters": "10.50",
            "altitude_meters": "1650.00"
        },
        "movement": {
            "speed_mps": "5.50",
            "speed_kmh": 19.8,
            "heading_degrees": "45.00"
        },
        "recorded_at": "2024-01-18T10:30:00.000000Z",
        "platform": "android",
        "is_synced": false
    }
}
```

#### Batch Location Submission (Offline Sync)

**Request Body:**

```json
{
    "tracking_logs": [
        {
            "user_id": 3,
            "job_id": 5,
            "latitude": -1.2921,
            "longitude": 36.8219,
            "accuracy_meters": 10.5,
            "speed_mps": 5.5,
            "recorded_at": "2024-01-18T10:30:00Z",
            "platform": "android"
        },
        {
            "user_id": 3,
            "job_id": 5,
            "latitude": -1.2925,
            "longitude": 36.8223,
            "accuracy_meters": 12.0,
            "speed_mps": 6.0,
            "recorded_at": "2024-01-18T10:35:00Z",
            "platform": "android"
        }
    ]
}
```

**Validation Rules:**

- `tracking_logs` - Required, array, min: 1, max: 100 items
- Each item follows the same validation as single submission

**Example Response:**

```json
{
    "success": true,
    "message": "Tracking logs created successfully.",
    "data": {
        "success": true,
        "count": 2,
        "message": "Successfully created 2 tracking logs"
    }
}
```

---

### 2. Get User Tracking History

Retrieve tracking history for a specific user.

**Endpoint:** `GET /api/v1/tracking/user/{userId}`

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

- `job_id` (integer, optional) - Filter by job
- `start_date` (datetime, optional) - Filter by start date
- `end_date` (datetime, optional) - Filter by end date
- `platform` (string, optional) - Filter by platform: android/ios/web
- `sort_by` (string, optional) - Sort field (default: recorded_at)
- `sort_direction` (string, optional) - Sort direction: asc/desc (default: desc)
- `per_page` (integer, optional) - Items per page (default: 50)

**Example Request:**

```bash
curl -X GET "https://api.geo-ops.com/api/v1/tracking/user/3?job_id=5&per_page=100" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**

```json
{
    "success": true,
    "message": "Tracking logs retrieved successfully.",
    "data": {
        "data": [
            {
                "id": 1001,
                "user_id": 3,
                "job_id": 5,
                "location": {
                    "latitude": "-1.2921000",
                    "longitude": "36.8219000",
                    "accuracy_meters": "10.50",
                    "altitude_meters": "1650.00"
                },
                "movement": {
                    "speed_mps": "5.50",
                    "speed_kmh": 19.8,
                    "heading_degrees": "45.00"
                },
                "recorded_at": "2024-01-18T10:30:00.000000Z",
                "platform": "android",
                "user": {
                    "id": 3,
                    "name": "John Driver",
                    "email": "john@geo-ops.test"
                },
                "job": {
                    "id": 5,
                    "job_number": "JOB-20240115-0003",
                    "service_type": "plowing"
                }
            }
        ],
        "meta": {
            "total": 150,
            "count": 100,
            "per_page": 100,
            "current_page": 1,
            "total_pages": 2
        }
    }
}
```

---

### 3. Get Job Tracking Logs

Retrieve all tracking logs for a specific job (all users).

**Endpoint:** `GET /api/v1/tracking/job/{jobId}`

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

- `start_date` (datetime, optional) - Filter by start date
- `end_date` (datetime, optional) - Filter by end date
- `platform` (string, optional) - Filter by platform
- `sort_by` (string, optional) - Sort field (default: recorded_at)
- `sort_direction` (string, optional) - Sort direction: asc/desc (default: asc)
- `per_page` (integer, optional) - Items per page (default: 50)

**Example Response:**

```json
{
    "success": true,
    "message": "Job tracking logs retrieved successfully.",
    "data": {
        "data": [
            {
                "id": 1001,
                "user_id": 3,
                "job_id": 5,
                "location": {
                    "latitude": "-1.2921000",
                    "longitude": "36.8219000",
                    "accuracy_meters": "10.50"
                },
                "movement": {
                    "speed_mps": "5.50",
                    "speed_kmh": 19.8,
                    "heading_degrees": "45.00"
                },
                "recorded_at": "2024-01-18T10:30:00.000000Z",
                "user": {
                    "id": 3,
                    "name": "John Driver",
                    "email": "john@geo-ops.test"
                }
            }
        ]
    }
}
```

---

### 4. Get Live Locations

Get current live locations of all active users (last update within 5 minutes).

**Endpoint:** `GET /api/v1/tracking/live`

**Headers:**

```
Authorization: Bearer {token}
```

**Example Response:**

```json
{
    "success": true,
    "message": "Live locations retrieved successfully.",
    "data": [
        {
            "user_id": 3,
            "user": {
                "id": 3,
                "name": "John Driver",
                "email": "john@geo-ops.test",
                "phone": "+1234567890"
            },
            "job_id": 5,
            "job": {
                "id": 5,
                "job_number": "JOB-20240115-0003",
                "service_type": "plowing",
                "status": "in_progress"
            },
            "location": {
                "latitude": "-1.2921000",
                "longitude": "36.8219000",
                "accuracy_meters": "10.50"
            },
            "movement": {
                "speed_mps": "5.50",
                "speed_kmh": 19.8,
                "heading_degrees": "45.00"
            },
            "last_update": "2024-01-18T10:32:00.000000Z",
            "platform": "android",
            "is_active": true
        }
    ]
}
```

---

### 5. Get User Tracking Statistics

Get tracking statistics for a user including total logs and distance traveled.

**Endpoint:** `GET /api/v1/tracking/user/{userId}/stats`

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

- `start_date` (datetime, optional) - Filter by start date
- `end_date` (datetime, optional) - Filter by end date

**Example Response:**

```json
{
    "success": true,
    "message": "User tracking statistics retrieved successfully.",
    "data": {
        "user_id": 3,
        "total_logs": 245,
        "distance_meters": 45230.5,
        "distance_km": 45.23,
        "start_date": "2024-01-01T00:00:00Z",
        "end_date": "2024-01-18T23:59:59Z",
        "is_active": true
    }
}
```

---

## Testing Guide

### Prerequisites

1. Install dependencies: `composer install`
2. Configure database connection in `.env`
3. Run migrations: `php artisan migrate`
4. Seed test data: `php artisan db:seed --class=TestAuthSeeder`
5. Seed jobs: `php artisan db:seed --class=FieldJobSeeder`
6. Seed expenses & tracking: `php artisan db:seed --class=ExpenseTrackingSeeder`

### Test Data Overview

After running seeders, you'll have:

- **Expenses:** 10 sample expenses across different categories
- **Tracking Logs:** 50+ GPS tracking points for multiple users and jobs
- Mix of recent (last 7 days) and historical data
- Various expense categories: fuel, maintenance, parts, salary, etc.

### Sample Test Credentials

```
Owner Account:
- Email: owner@geo-ops.test
- Password: password123

Driver Account:
- Email: driver@geo-ops.test
- Password: password123
```

### Testing Expenses

#### 1. List All Expenses

```bash
curl -X GET "http://localhost:8000/api/v1/expenses" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 2. Filter Expenses by Category

```bash
curl -X GET "http://localhost:8000/api/v1/expenses?category=fuel" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 3. Create New Expense

```bash
curl -X POST "http://localhost:8000/api/v1/expenses" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "category": "fuel",
    "amount": 75.50,
    "currency": "USD",
    "expense_date": "2024-01-18",
    "description": "Fuel for tractor",
    "vendor_name": "Shell Station"
  }'
```

#### 4. Get Expense Totals

```bash
curl -X GET "http://localhost:8000/api/v1/expenses/totals?start_date=2024-01-01&end_date=2024-01-31" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Testing GPS Tracking

#### 1. Submit Single Location

```bash
curl -X POST "http://localhost:8000/api/v1/tracking" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 3,
    "job_id": 5,
    "latitude": -1.2921,
    "longitude": 36.8219,
    "accuracy_meters": 10.5,
    "speed_mps": 5.5,
    "platform": "android"
  }'
```

#### 2. Submit Batch Locations (Offline Sync)

```bash
curl -X POST "http://localhost:8000/api/v1/tracking" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tracking_logs": [
      {
        "user_id": 3,
        "latitude": -1.2921,
        "longitude": 36.8219,
        "recorded_at": "2024-01-18T10:30:00Z"
      },
      {
        "user_id": 3,
        "latitude": -1.2925,
        "longitude": 36.8223,
        "recorded_at": "2024-01-18T10:35:00Z"
      }
    ]
  }'
```

#### 3. Get Live Locations

```bash
curl -X GET "http://localhost:8000/api/v1/tracking/live" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 4. Get User Tracking History

```bash
curl -X GET "http://localhost:8000/api/v1/tracking/user/3" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 5. Get Job Tracking

```bash
curl -X GET "http://localhost:8000/api/v1/tracking/job/5" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Error Responses

### Common Error Codes

- `400` - Bad Request (invalid input)
- `401` - Unauthorized (missing or invalid token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found (resource doesn't exist)
- `422` - Unprocessable Entity (validation failed)
- `500` - Internal Server Error

### Error Response Format

```json
{
    "success": false,
    "message": "Error message here",
    "errors": {
        "field_name": ["Error detail"]
    }
}
```

---

## Key Features

### Expense Management

- ✅ Auto-generated unique expense numbers (EXP-YYYYMMDD-XXXX)
- ✅ Support for 7 expense categories
- ✅ Link expenses to jobs and drivers
- ✅ Receipt file upload support
- ✅ Organization-scoped queries
- ✅ Calculate totals by category/period
- ✅ Advanced filtering and searching
- ✅ Offline sync support

### GPS Tracking

- ✅ Single and batch location submission
- ✅ Offline sync with batch upload (up to 100 logs)
- ✅ Record GPS coordinates with accuracy, speed, heading
- ✅ Link tracking to jobs
- ✅ Live location detection (within 5 minutes)
- ✅ Distance calculation using Haversine formula
- ✅ Route history with time range filtering
- ✅ Platform tracking (Android/iOS/Web)

---

## Architecture

### Clean Architecture Pattern

```
Controllers → Services → Repositories → Models
     ↓           ↓            ↓
   DTOs     Business      Database
           Logic         Operations
```

### Components

- **Controllers:** Handle HTTP requests/responses
- **Services:** Business logic and orchestration
- **Repositories:** Data access abstraction
- **DTOs:** Data transfer objects for type safety
- **Form Requests:** Input validation
- **Resources:** API response transformation
- **Models:** Eloquent ORM models

---

## Notes

### Organization Isolation

All endpoints automatically filter data by the authenticated user's organization. Cross-organization access is prevented at the middleware level.

### Offline Sync

Both Expense and Tracking APIs support offline sync:

- Expenses: Create/update with `is_synced: false`
- Tracking: Batch submit up to 100 logs at once

### Performance

- Tracking logs use efficient batch inserts
- Live locations use optimized queries with subqueries
- Distance calculations use the Haversine formula
- Proper indexing on organization_id, user_id, job_id, recorded_at

### Security

- JWT authentication required for all endpoints
- Organization isolation middleware
- Input validation on all endpoints
- Prepared statements prevent SQL injection
- XSS protection via Laravel's built-in escaping
