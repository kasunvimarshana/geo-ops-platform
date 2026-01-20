# GPS Field Manager - Mobile App API Documentation

## Overview
This document describes the API endpoints expected by the mobile application and their integration with the Laravel backend.

## Base Configuration
- Base URL: Configurable via `EXPO_PUBLIC_API_URL` environment variable
- Default: `http://localhost:8000/api`
- Timeout: 30 seconds
- Retry: 3 attempts with 1 second delay

## Authentication

### Login
**Endpoint:** `POST /auth/login`

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Expected Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "username": "user",
    "first_name": "John",
    "last_name": "Doe",
    "role": "driver"
  }
}
```

**Notes:**
- Mobile app will fetch user data from `/auth/me` if not included in response
- Token is stored securely in MMKV storage
- Token is used for all subsequent authenticated requests

### Register
**Endpoint:** `POST /auth/register`

**Request:**
```json
{
  "email": "newuser@example.com",
  "username": "newuser",
  "password": "password123",
  "first_name": "Jane",
  "last_name": "Doe"
}
```

**Expected Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 2,
    "email": "newuser@example.com",
    "username": "newuser",
    "first_name": "Jane",
    "last_name": "Doe",
    "role": "customer"
  }
}
```

### Get Current User
**Endpoint:** `GET /auth/me`

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "id": 1,
  "email": "user@example.com",
  "username": "user",
  "first_name": "John",
  "last_name": "Doe",
  "role": "driver"
}
```

### Logout
**Endpoint:** `POST /auth/logout`

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

### Token Refresh
**Endpoint:** `POST /auth/refresh`

**Request:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

**Response:**
```json
{
  "access": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

## Field Jobs

### List Jobs
**Endpoint:** `GET /field-jobs`

**Query Parameters:**
- `status` (optional): Filter by status (pending, in_progress, completed, cancelled)
- `page` (optional): Page number for pagination

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "count": 25,
  "next": "http://api.example.com/field-jobs?page=2",
  "previous": null,
  "results": [
    {
      "id": 1,
      "title": "Field Measurement - Silva Farm",
      "customer_name": "Mr. Silva",
      "location": "Anuradhapura",
      "description": "Measure paddy field",
      "status": "pending",
      "estimated_price": 5000.00,
      "actual_price": null,
      "scheduled_date": "2024-01-15T10:00:00Z",
      "completed_date": null,
      "created_at": "2024-01-10T08:00:00Z",
      "updated_at": "2024-01-10T08:00:00Z"
    }
  ]
}
```

### Get Job
**Endpoint:** `GET /field-jobs/{id}`

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "id": 1,
  "title": "Field Measurement - Silva Farm",
  "customer_name": "Mr. Silva",
  "location": "Anuradhapura",
  "description": "Measure paddy field",
  "status": "pending",
  "estimated_price": 5000.00,
  "actual_price": null,
  "scheduled_date": "2024-01-15T10:00:00Z",
  "completed_date": null,
  "land_plots": [
    {
      "id": 1,
      "coordinates": [
        {"latitude": 8.3114, "longitude": 80.4037},
        {"latitude": 8.3124, "longitude": 80.4047},
        {"latitude": 8.3134, "longitude": 80.4037}
      ],
      "area_sqm": 1250.50,
      "area_acres": 0.309,
      "perimeter_m": 145.25
    }
  ],
  "created_at": "2024-01-10T08:00:00Z",
  "updated_at": "2024-01-10T08:00:00Z"
}
```

### Create Job
**Endpoint:** `POST /field-jobs`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request:**
```json
{
  "title": "New Field Measurement",
  "customer_name": "Mr. Perera",
  "location": "Kurunegala",
  "description": "Tea plantation measurement",
  "status": "pending",
  "estimated_price": 7500.00,
  "scheduled_date": "2024-01-20T09:00:00Z"
}
```

**Response:**
```json
{
  "id": 2,
  "title": "New Field Measurement",
  "customer_name": "Mr. Perera",
  "location": "Kurunegala",
  "description": "Tea plantation measurement",
  "status": "pending",
  "estimated_price": 7500.00,
  "actual_price": null,
  "scheduled_date": "2024-01-20T09:00:00Z",
  "completed_date": null,
  "created_at": "2024-01-12T10:00:00Z",
  "updated_at": "2024-01-12T10:00:00Z"
}
```

### Update Job
**Endpoint:** `PATCH /field-jobs/{id}`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request:**
```json
{
  "status": "in_progress",
  "actual_price": 7000.00
}
```

**Response:**
```json
{
  "id": 2,
  "title": "New Field Measurement",
  "status": "in_progress",
  "actual_price": 7000.00,
  ...
}
```

### Delete Job
**Endpoint:** `DELETE /field-jobs/{id}`

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "message": "Job deleted successfully"
}
```

## Land Plots

### List Plots
**Endpoint:** `GET /land-plots`

**Query Parameters:**
- `job` (optional): Filter by job ID

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
[
  {
    "id": 1,
    "coordinates": [
      {"latitude": 8.3114, "longitude": 80.4037},
      {"latitude": 8.3124, "longitude": 80.4047},
      {"latitude": 8.3134, "longitude": 80.4037}
    ],
    "area_sqm": 1250.50,
    "area_acres": 0.309,
    "perimeter_m": 145.25,
    "job": 1,
    "created_at": "2024-01-10T09:00:00Z"
  }
]
```

### Create Plot
**Endpoint:** `POST /land-plots`

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request:**
```json
{
  "coordinates": [
    {"latitude": 8.3114, "longitude": 80.4037},
    {"latitude": 8.3124, "longitude": 80.4047},
    {"latitude": 8.3134, "longitude": 80.4037},
    {"latitude": 8.3104, "longitude": 80.4027}
  ],
  "area_sqm": 1500.75,
  "area_acres": 0.371,
  "perimeter_m": 160.50,
  "job": 1
}
```

**Response:**
```json
{
  "id": 2,
  "coordinates": [...],
  "area_sqm": 1500.75,
  "area_acres": 0.371,
  "perimeter_m": 160.50,
  "job": 1,
  "created_at": "2024-01-12T11:00:00Z"
}
```

## Invoices

### List Invoices
**Endpoint:** `GET /invoices`

**Query Parameters:**
- `page` (optional): Page number for pagination

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "count": 15,
  "next": "http://api.example.com/invoices?page=2",
  "previous": null,
  "results": [
    {
      "id": 1,
      "job": 1,
      "invoice_number": "INV-2024-001",
      "customer_name": "Mr. Silva",
      "issued_date": "2024-01-15",
      "due_date": "2024-01-30",
      "total_amount": 5000.00,
      "status": "paid",
      "pdf_url": "http://api.example.com/storage/invoices/INV-2024-001.pdf"
    }
  ]
}
```

### Get Invoice
**Endpoint:** `GET /invoices/{id}`

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "id": 1,
  "job": 1,
  "invoice_number": "INV-2024-001",
  "customer_name": "Mr. Silva",
  "issued_date": "2024-01-15",
  "due_date": "2024-01-30",
  "total_amount": 5000.00,
  "status": "paid",
  "pdf_url": "http://api.example.com/storage/invoices/INV-2024-001.pdf"
}
```

### Download Invoice PDF
**Endpoint:** `GET /invoices/{id}/download`

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "pdf_url": "http://api.example.com/storage/invoices/INV-2024-001.pdf"
}
```

## Error Responses

All endpoints may return the following error responses:

### 400 Bad Request
```json
{
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized"
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

### 422 Unprocessable Entity
```json
{
  "message": "The given data was invalid",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 500 Internal Server Error
```json
{
  "message": "Server error occurred"
}
```

## Offline Sync

The mobile app implements an offline-first approach:

1. **Create/Update/Delete Operations**
   - Operations are stored locally in SQLite
   - Added to sync queue with status "pending"
   - Synced automatically when online

2. **Sync Queue Processing**
   - Batch size: 10 items per sync
   - Interval: Every 5 minutes
   - Max retry attempts: 5
   - On network reconnection, immediate sync is triggered

3. **Conflict Resolution**
   - Strategy: Server-wins (last successful sync)
   - Local changes marked as synced on success
   - Failed syncs retry with exponential backoff

## Authentication Flow

1. **Initial Login**
   - User enters email and password
   - POST to `/auth/login`
   - Token stored in MMKV
   - User data stored in MMKV
   - Navigate to main app

2. **Auto-Login**
   - On app start, check for stored token
   - If exists, validate with `/auth/me`
   - If valid, navigate to main app
   - If invalid, navigate to login

3. **Token Refresh**
   - On 401 response, attempt token refresh
   - POST to `/auth/refresh` with current token
   - Update stored token
   - Retry original request
   - If refresh fails, logout and navigate to login

4. **Logout**
   - POST to `/auth/logout`
   - Clear stored token and user data
   - Navigate to login screen

## Network Configuration

- **Timeout**: 30 seconds
- **Retry Logic**: 3 attempts with 1 second delay
- **Connection Check**: Before each sync operation
- **Offline Mode**: All operations continue in offline mode
- **Sync Trigger**: Network reconnection, app foreground, manual trigger

## GPS Configuration

- **Accuracy Threshold**: 10 meters
- **Update Interval**: 5 seconds (battery optimized)
- **Distance Filter**: 5 meters
- **Location Permission**: Requested on first use
- **Background Location**: Not used (foreground only)
