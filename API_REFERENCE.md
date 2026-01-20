# API Reference

Complete API documentation for the Geo Ops Platform backend.

## Base URL

```
http://localhost:3000/api/v1
```

## Authentication

All protected endpoints require a JWT token in the Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

## Response Format

### Success Response

```json
{
  "status": "success",
  "data": {
    // Response data
  }
}
```

### Error Response

```json
{
  "status": "error",
  "message": "Error description"
}
```

## HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request succeeded |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 500 | Internal Server Error |

---

## Authentication Endpoints

### Register User

Create a new user account.

**Endpoint:** `POST /auth/register`

**Request Body:**

```json
{
  "email": "john@example.com",
  "password": "securePassword123",
  "firstName": "John",
  "lastName": "Doe",
  "phone": "+94771234567",
  "organizationName": "My Farm" // Optional
}
```

**Response:** `201 Created`

```json
{
  "status": "success",
  "data": {
    "user": {
      "id": "uuid",
      "email": "john@example.com",
      "firstName": "John",
      "lastName": "Doe",
      "phone": "+94771234567",
      "role": "owner",
      "organizationId": "uuid",
      "subscriptionPackage": "free"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

**Validation Rules:**
- `email`: Valid email format, required
- `password`: Minimum 8 characters, required
- `firstName`: Required
- `lastName`: Required
- `phone`: Required

---

### Login

Authenticate and receive JWT token.

**Endpoint:** `POST /auth/login`

**Request Body:**

```json
{
  "email": "john@example.com",
  "password": "securePassword123"
}
```

**Response:** `200 OK`

```json
{
  "status": "success",
  "data": {
    "user": {
      "id": "uuid",
      "email": "john@example.com",
      "firstName": "John",
      "lastName": "Doe",
      "phone": "+94771234567",
      "role": "owner",
      "organizationId": "uuid",
      "subscriptionPackage": "free"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

---

### Get Profile

Get current user's profile information.

**Endpoint:** `GET /auth/profile`

**Headers:** `Authorization: Bearer <token>`

**Response:** `200 OK`

```json
{
  "status": "success",
  "data": {
    "id": "uuid",
    "email": "john@example.com",
    "firstName": "John",
    "lastName": "Doe",
    "phone": "+94771234567",
    "role": "owner",
    "organizationId": "uuid",
    "subscriptionPackage": "free",
    "subscriptionExpiry": null,
    "isActive": true,
    "createdAt": "2024-01-01T00:00:00.000Z"
  }
}
```

---

## Land Measurement Endpoints

All land measurement endpoints require authentication.

### Create Measurement

Create a new land measurement from GPS coordinates.

**Endpoint:** `POST /land-measurements`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**

```json
{
  "name": "North Field",
  "description": "Main agricultural field",
  "coordinates": [
    {
      "latitude": 6.9271,
      "longitude": 79.8612,
      "timestamp": "2024-01-01T10:00:00Z"
    },
    {
      "latitude": 6.9272,
      "longitude": 79.8613,
      "timestamp": "2024-01-01T10:01:00Z"
    },
    {
      "latitude": 6.9273,
      "longitude": 79.8614,
      "timestamp": "2024-01-01T10:02:00Z"
    }
  ],
  "unit": "acres",
  "address": "Colombo, Sri Lanka",
  "metadata": {
    "soilType": "loamy",
    "crop": "rice"
  }
}
```

**Response:** `201 Created`

```json
{
  "status": "success",
  "data": {
    "id": "uuid",
    "userId": "uuid",
    "name": "North Field",
    "description": "Main agricultural field",
    "coordinates": [...],
    "area": 2.5,
    "unit": "acres",
    "address": "Colombo, Sri Lanka",
    "metadata": {
      "soilType": "loamy",
      "crop": "rice"
    },
    "createdAt": "2024-01-01T10:00:00.000Z",
    "updatedAt": "2024-01-01T10:00:00.000Z"
  }
}
```

**Validation Rules:**
- `name`: Required
- `coordinates`: Array of at least 3 points, required
- `coordinates[].latitude`: Number, required
- `coordinates[].longitude`: Number, required
- `unit`: One of `acres`, `hectares`, `square_meters`, required

---

### Get All Measurements

List all measurements for the authenticated user.

**Endpoint:** `GET /land-measurements`

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `limit` (optional): Number of results per page (default: 30)
- `offset` (optional): Pagination offset (default: 0)
- `search` (optional): Search in name or address

**Example:**
```
GET /land-measurements?limit=10&offset=0&search=field
```

**Response:** `200 OK`

```json
{
  "status": "success",
  "data": [
    {
      "id": "uuid",
      "userId": "uuid",
      "name": "North Field",
      "description": "Main agricultural field",
      "coordinates": [...],
      "area": 2.5,
      "unit": "acres",
      "address": "Colombo, Sri Lanka",
      "metadata": {...},
      "createdAt": "2024-01-01T10:00:00.000Z",
      "updatedAt": "2024-01-01T10:00:00.000Z"
    },
    // More measurements...
  ]
}
```

---

### Get Measurement by ID

Get details of a specific measurement.

**Endpoint:** `GET /land-measurements/:id`

**Headers:** `Authorization: Bearer <token>`

**Response:** `200 OK`

```json
{
  "status": "success",
  "data": {
    "id": "uuid",
    "userId": "uuid",
    "name": "North Field",
    "description": "Main agricultural field",
    "coordinates": [
      {
        "latitude": 6.9271,
        "longitude": 79.8612,
        "timestamp": "2024-01-01T10:00:00Z"
      },
      // More coordinates...
    ],
    "area": 2.5,
    "unit": "acres",
    "address": "Colombo, Sri Lanka",
    "metadata": {
      "soilType": "loamy",
      "crop": "rice"
    },
    "createdAt": "2024-01-01T10:00:00.000Z",
    "updatedAt": "2024-01-01T10:00:00.000Z"
  }
}
```

---

### Update Measurement

Update measurement metadata (coordinates cannot be changed).

**Endpoint:** `PATCH /land-measurements/:id`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**

```json
{
  "name": "Updated North Field",
  "description": "Updated description",
  "address": "New address",
  "metadata": {
    "crop": "wheat"
  }
}
```

**Response:** `200 OK`

```json
{
  "status": "success",
  "data": {
    // Updated measurement object
  }
}
```

---

### Delete Measurement

Delete a measurement.

**Endpoint:** `DELETE /land-measurements/:id`

**Headers:** `Authorization: Bearer <token>`

**Response:** `200 OK`

```json
{
  "status": "success",
  "data": {
    "message": "Land measurement deleted successfully"
  }
}
```

---

## Error Examples

### Validation Error

```json
{
  "status": "error",
  "message": "\"email\" must be a valid email, \"password\" length must be at least 8 characters long"
}
```

### Authentication Error

```json
{
  "status": "error",
  "message": "Authentication required"
}
```

### Not Found Error

```json
{
  "status": "error",
  "message": "Land measurement not found"
}
```

### Server Error

```json
{
  "status": "error",
  "message": "Internal server error"
}
```

---

## Rate Limiting

API requests are rate limited to prevent abuse:

- **Window**: 15 minutes
- **Max Requests**: 100 per window

When rate limit is exceeded, you'll receive a `429 Too Many Requests` response.

---

## CORS

The API supports CORS for the following origins:
- `http://localhost:19006` (Expo development)
- `http://localhost:19000` (Expo development)

Configure additional origins in the `.env` file:
```
CORS_ORIGIN=http://localhost:19006,http://localhost:19000,https://yourdomain.com
```

---

## Webhook Events (Future)

Coming soon: Webhook support for real-time notifications.

---

## SDK & Client Libraries (Future)

Official client libraries will be available for:
- JavaScript/TypeScript
- Python
- PHP
- Java

---

## Support

For API issues or questions:
- Check this documentation
- Review example requests
- Check server logs for errors
- Verify authentication tokens

## Changelog

### v1.0.0 (Current)
- Authentication (register, login, profile)
- Land measurement CRUD operations
- Area calculation
- User management
- Role-based access control
