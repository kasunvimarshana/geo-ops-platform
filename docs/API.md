# API Documentation

## Base URL

```
Development: http://localhost:8000/api/v1
Production: https://api.geo-ops.com/api/v1
```

## Authentication

All protected endpoints require a JWT token in the Authorization header:

```
Authorization: Bearer {token}
```

## Error Responses

All endpoints may return these error responses:

- `400 Bad Request` - Invalid request data
- `401 Unauthorized` - Missing or invalid authentication token
- `403 Forbidden` - User doesn't have permission
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation error
- `500 Internal Server Error` - Server error

Error response format:

```json
{
  "error": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

## Authentication Endpoints

### Register User

```http
POST /auth/register
```

**Request Body:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+94771234567",
  "organization_id": 1
}
```

**Response:** `201 Created`

```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+94771234567",
    "organization_id": 1,
    "role": "field_worker",
    "is_active": true,
    "created_at": "2024-01-19T10:00:00.000000Z",
    "updated_at": "2024-01-19T10:00:00.000000Z"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### Login

```http
POST /auth/login
```

**Request Body:**

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:** `200 OK`

```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    ...
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### Get Current User

```http
GET /auth/me
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:** `200 OK`

```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+94771234567",
  "organization_id": 1,
  "role": "field_worker",
  "is_active": true,
  "created_at": "2024-01-19T10:00:00.000000Z",
  "updated_at": "2024-01-19T10:00:00.000000Z"
}
```

### Logout

```http
POST /auth/logout
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:** `200 OK`

```json
{
  "message": "Successfully logged out"
}
```

### Refresh Token

```http
POST /auth/refresh
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:** `200 OK`

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

## Field Endpoints

### List Fields

```http
GET /fields
```

**Query Parameters:**

- `page` (integer) - Page number (default: 1)
- `per_page` (integer) - Results per page (default: 15)
- `crop_type` (string) - Filter by crop type
- `sort_by` (string) - Sort field (default: created_at)
- `sort_order` (string) - Sort order: asc, desc (default: desc)

**Headers:**

```
Authorization: Bearer {token}
```

**Response:** `200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "name": "North Field",
      "organization_id": 1,
      "user_id": 1,
      "boundary": [
        { "latitude": 7.8731, "longitude": 80.7718 },
        { "latitude": 7.8735, "longitude": 80.772 },
        { "latitude": 7.873, "longitude": 80.7725 },
        { "latitude": 7.8728, "longitude": 80.772 }
      ],
      "area": 5000.5,
      "perimeter": 300.25,
      "crop_type": "Rice",
      "notes": "Main rice field",
      "measurement_type": "walk_around",
      "measured_at": "2024-01-19T10:00:00.000000Z",
      "created_at": "2024-01-19T10:00:00.000000Z",
      "updated_at": "2024-01-19T10:00:00.000000Z",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "organization": {
        "id": 1,
        "name": "Green Farm"
      }
    }
  ],
  "total": 10,
  "current_page": 1,
  "per_page": 15
}
```

### Get Single Field

```http
GET /fields/{id}
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:** `200 OK`

```json
{
  "id": 1,
  "name": "North Field",
  "organization_id": 1,
  "user_id": 1,
  "boundary": [...],
  "area": 5000.50,
  "perimeter": 300.25,
  "crop_type": "Rice",
  "notes": "Main rice field",
  "measurement_type": "walk_around",
  "measured_at": "2024-01-19T10:00:00.000000Z",
  "created_at": "2024-01-19T10:00:00.000000Z",
  "updated_at": "2024-01-19T10:00:00.000000Z",
  "user": {...},
  "organization": {...},
  "jobs": [...]
}
```

### Create Field

```http
POST /fields
```

**Headers:**

```
Authorization: Bearer {token}
```

**Request Body:**

```json
{
  "name": "South Field",
  "boundary": [
    { "latitude": 7.8731, "longitude": 80.7718 },
    { "latitude": 7.8735, "longitude": 80.772 },
    { "latitude": 7.873, "longitude": 80.7725 }
  ],
  "area": 3500.75,
  "perimeter": 250.5,
  "crop_type": "Vegetables",
  "notes": "New vegetable field",
  "measurement_type": "polygon"
}
```

**Response:** `201 Created`

```json
{
  "id": 2,
  "name": "South Field",
  "organization_id": 1,
  "user_id": 1,
  "boundary": [...],
  "area": 3500.75,
  "perimeter": 250.50,
  "crop_type": "Vegetables",
  "notes": "New vegetable field",
  "measurement_type": "polygon",
  "measured_at": "2024-01-19T11:00:00.000000Z",
  "created_at": "2024-01-19T11:00:00.000000Z",
  "updated_at": "2024-01-19T11:00:00.000000Z",
  "user": {...},
  "organization": {...}
}
```

### Update Field

```http
PUT /fields/{id}
```

**Headers:**

```
Authorization: Bearer {token}
```

**Request Body:**

```json
{
  "name": "Updated Field Name",
  "crop_type": "Corn",
  "notes": "Changed to corn cultivation"
}
```

**Response:** `200 OK`

```json
{
  "id": 1,
  "name": "Updated Field Name",
  "crop_type": "Corn",
  "notes": "Changed to corn cultivation",
  ...
}
```

### Delete Field

```http
DELETE /fields/{id}
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:** `200 OK`

```json
{
  "message": "Field deleted successfully"
}
```

### Get Field Report

```http
GET /fields/{id}/report?format={format}
```

**Query Parameters:**

- `format` (string, optional) - Report format: `json` (default) or `html`

**Headers:**

```
Authorization: Bearer {token}
```

**Response (JSON format):** `200 OK`

```json
{
  "title": "Field Measurement Report",
  "generated_at": "2024-01-19 15:30:00",
  "field": {
    "id": 1,
    "name": "North Field",
    "location": "Maharagama, Sri Lanka",
    "area_ha": 0.50,
    "area_sqm": 5000,
    "perimeter_km": 0.30,
    "perimeter_m": 300,
    "crop_type": "Rice",
    "measurement_type": "walk_around",
    "notes": "Main rice field"
  },
  "organization": {
    "name": "Test Farm",
    "type": "farm",
    "email": "test@farm.com"
  },
  "measured_by": {
    "name": "John Doe",
    "email": "john@example.com"
  },
  "coordinates": [...],
  "jobs_count": 2,
  "jobs": [...]
}
```

**Response (HTML format):** `200 OK`

- Returns formatted HTML report suitable for printing or PDF conversion

## Job Endpoints

### List Jobs

```http
GET /jobs
```

**Query Parameters:**

- `page` (integer) - Page number
- `per_page` (integer) - Results per page
- `status` (string) - Filter by status: pending, in_progress, completed, cancelled
- `assigned_to` (integer) - Filter by assigned user ID

**Headers:**

```
Authorization: Bearer {token}
```

**Response:** `200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "title": "Harvest North Field",
      "description": "Complete harvesting of rice",
      "organization_id": 1,
      "field_id": 1,
      "created_by": 1,
      "assigned_to": 2,
      "status": "in_progress",
      "priority": "high",
      "due_date": "2024-01-25T00:00:00.000000Z",
      "started_at": "2024-01-20T08:00:00.000000Z",
      "completed_at": null,
      "location": {"latitude": 7.8731, "longitude": 80.7718},
      "created_at": "2024-01-19T10:00:00.000000Z",
      "updated_at": "2024-01-20T08:00:00.000000Z",
      "field": {...},
      "creator": {...},
      "assignee": {...}
    }
  ],
  "total": 5,
  "current_page": 1,
  "per_page": 15
}
```

### Create Job

```http
POST /jobs
```

**Headers:**

```
Authorization: Bearer {token}
```

**Request Body:**

```json
{
  "title": "Fertilize South Field",
  "description": "Apply organic fertilizer",
  "field_id": 2,
  "assigned_to": 3,
  "priority": "medium",
  "due_date": "2024-01-30T00:00:00.000Z",
  "location": { "latitude": 7.873, "longitude": 80.772 }
}
```

**Response:** `201 Created`

```json
{
  "id": 2,
  "title": "Fertilize South Field",
  "description": "Apply organic fertilizer",
  "status": "pending",
  "priority": "medium",
  ...
}
```

### Get Job Report

```http
GET /jobs/{id}/report?format={format}
```

**Query Parameters:**

- `format` (string, optional) - Report format: `json` (default) or `html`

**Headers:**

```
Authorization: Bearer {token}
```

**Response (JSON format):** `200 OK`

```json
{
  "title": "Job Report",
  "generated_at": "2024-01-19 15:30:00",
  "job": {
    "id": 1,
    "title": "Harvest North Field",
    "description": "Complete harvesting of rice",
    "status": "in_progress",
    "priority": "high",
    "due_date": "2024-01-25",
    "started_at": "2024-01-20 08:00:00",
    "completed_at": null
  },
  "organization": {
    "name": "Test Farm",
    "type": "farm"
  },
  "field": {
    "name": "North Field",
    "location": "Maharagama",
    "area_ha": 0.50
  },
  "creator": {
    "name": "John Doe",
    "email": "john@example.com"
  },
  "assignee": {
    "name": "Jane Smith",
    "email": "jane@example.com"
  },
  "invoices": [...]
}
```

**Response (HTML format):** `200 OK`

- Returns formatted HTML report suitable for printing or PDF conversion

## Data Models

### User Roles

- `admin` - Full system access
- `manager` - Manage organization resources
- `driver` - Field worker with tracking
- `field_worker` - Basic field operations

### Job Status

- `pending` - Not yet started
- `in_progress` - Currently being worked on
- `completed` - Finished
- `cancelled` - Cancelled

### Job Priority

- `low` - Low priority
- `medium` - Normal priority
- `high` - High priority
- `urgent` - Requires immediate attention

### Measurement Types

- `walk_around` - GPS tracking while walking perimeter
- `polygon` - Manual point placement
- `manual` - Direct coordinate entry

## Rate Limiting

API endpoints are rate limited to:

- 60 requests per minute for authenticated users
- 10 requests per minute for unauthenticated users

Rate limit headers:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1674125400
```

## Pagination

List endpoints support pagination with these parameters:

- `page` - Page number (default: 1)
- `per_page` - Results per page (default: 15, max: 100)

Pagination metadata is included in responses:

```json
{
  "data": [...],
  "total": 100,
  "current_page": 1,
  "per_page": 15,
  "last_page": 7
}
```

## Versioning

API version is specified in the URL:

```
/api/v1/...
```

Future versions will be available at `/api/v2/`, etc.
