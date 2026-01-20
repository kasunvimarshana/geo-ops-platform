# JWT Authentication System

Complete JWT authentication system for GeoOps Platform using `tymon/jwt-auth`.

## Features

- User registration with validation
- User login with JWT token generation
- Token refresh
- Logout with token invalidation
- Get authenticated user profile
- Role-based authorization middleware
- Organization isolation middleware
- Consistent JSON API responses
- Comprehensive error handling

## Architecture

The system follows Laravel Clean Architecture:

- **Controller Layer**: Handles HTTP requests and responses
- **Service Layer**: Contains business logic
- **DTO Layer**: Data Transfer Objects for type-safe data passing
- **Middleware Layer**: Authentication and authorization
- **Resource Layer**: API response transformation

## Configuration

### JWT Configuration

JWT is configured in `config/jwt.php`. Key settings:

- Token TTL: 60 minutes (configurable via `JWT_TTL`)
- Refresh TTL: 20160 minutes (2 weeks, configurable via `JWT_REFRESH_TTL`)
- Algorithm: HS256
- Secret: Set via `JWT_SECRET` in `.env`

### Authentication Guard

The API guard is configured in `config/auth.php`:

```php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
        'hash' => false,
    ],
],
```

## API Endpoints

### Public Endpoints

#### Register User

```http
POST /api/v1/auth/register
Content-Type: application/json

{
  "organization_id": 1,
  "role": "owner",
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!"
}
```

**Response:**

```json
{
    "success": true,
    "message": "User registered successfully.",
    "data": {
        "id": 1,
        "organization_id": 1,
        "role": "owner",
        "first_name": "John",
        "last_name": "Doe",
        "full_name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "is_active": true,
        "last_login_at": null,
        "email_verified_at": null,
        "settings": null,
        "created_at": "2024-01-18T10:00:00.000000Z",
        "updated_at": "2024-01-18T10:00:00.000000Z"
    }
}
```

#### Login

```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "SecurePass123!"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Login successful.",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "bearer",
        "expires_in": 3600,
        "user": {
            "id": 1,
            "organization_id": 1,
            "role": "owner",
            "first_name": "John",
            "last_name": "Doe",
            "full_name": "John Doe",
            "email": "john@example.com",
            "phone": "+1234567890",
            "is_active": true,
            "last_login_at": "2024-01-18T10:00:00.000000Z",
            "email_verified_at": null,
            "settings": null,
            "created_at": "2024-01-18T10:00:00.000000Z",
            "updated_at": "2024-01-18T10:00:00.000000Z"
        }
    }
}
```

### Protected Endpoints

All protected endpoints require the `Authorization: Bearer <token>` header.

#### Get Current User

```http
GET /api/v1/auth/me
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

#### Refresh Token

```http
POST /api/v1/auth/refresh
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

#### Logout

```http
POST /api/v1/auth/logout
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

## Middleware

### JWT Authentication Middleware (`jwt.auth`)

Validates JWT tokens and authenticates users.

Usage:

```php
Route::middleware(['jwt.auth'])->group(function () {
    // Protected routes
});
```

### Role Middleware (`role`)

Restricts access based on user roles.

Usage:

```php
// Single role
Route::middleware(['role:admin'])->group(function () {
    // Admin only routes
});

// Multiple roles
Route::middleware(['role:admin,owner'])->group(function () {
    // Admin and Owner routes
});
```

### Organization Isolation Middleware (`organization.isolation`)

Ensures users can only access resources within their organization.

Usage:

```php
Route::middleware(['organization.isolation'])->group(function () {
    // Organization-specific routes
});
```

## Validation Rules

### Registration

- `organization_id`: Required, must exist in organizations table
- `role`: Optional, must be one of: admin, owner, driver, broker, accountant (defaults to 'owner')
- `first_name`: Required, max 255 characters
- `last_name`: Required, max 255 characters
- `email`: Required, valid email, max 255 characters, unique
- `phone`: Optional, max 20 characters
- `password`: Required, confirmed, minimum 8 characters with mixed case, numbers, and symbols

### Login

- `email`: Required, valid email
- `password`: Required

## Error Handling

The system returns consistent JSON error responses:

```json
{
    "success": false,
    "message": "Error message"
}
```

Common error codes:

- `400`: Bad Request (validation errors)
- `401`: Unauthorized (invalid credentials, expired token)
- `403`: Forbidden (insufficient permissions)
- `500`: Internal Server Error

## Security Features

1. **Password Hashing**: Bcrypt with configurable rounds
2. **Token Blacklisting**: Invalidated tokens cannot be reused
3. **Active User Check**: Only active users can authenticate
4. **Role-Based Access Control**: Fine-grained permission system
5. **Organization Isolation**: Multi-tenant data separation
6. **HTTPS Ready**: Designed for secure transmission
7. **JWT Custom Claims**: Organization and role embedded in token

## Testing

Test credentials (created by TestAuthSeeder):

- Admin: `admin@test.com` / `Password123!`
- Owner: `owner@test.com` / `Password123!`
- Driver: `driver@test.com` / `Password123!`

Run the seeder:

```bash
php artisan db:seed --class=TestAuthSeeder
```

## Usage Examples

### Using cURL

```bash
# Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@test.com","password":"Password123!"}' \
  | jq -r '.data.access_token')

# Get current user
curl -X GET http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

# Refresh token
curl -X POST http://localhost:8000/api/v1/auth/refresh \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

# Logout
curl -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### Using JavaScript (Axios)

```javascript
// Login
const loginResponse = await axios.post("/api/v1/auth/login", {
    email: "admin@test.com",
    password: "Password123!",
});

const token = loginResponse.data.data.access_token;

// Set default authorization header
axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

// Get current user
const userResponse = await axios.get("/api/v1/auth/me");
console.log(userResponse.data.data);

// Refresh token
const refreshResponse = await axios.post("/api/v1/auth/refresh");
const newToken = refreshResponse.data.data.access_token;

// Logout
await axios.post("/api/v1/auth/logout");
```

## Project Structure

```
app/
├── DTOs/
│   └── Auth/
│       ├── RegisterDTO.php
│       └── LoginDTO.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   │           └── AuthController.php
│   ├── Middleware/
│   │   ├── JwtAuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── OrganizationIsolationMiddleware.php
│   ├── Requests/
│   │   └── Auth/
│   │       ├── RegisterRequest.php
│   │       └── LoginRequest.php
│   └── Resources/
│       ├── UserResource.php
│       └── AuthResource.php
├── Models/
│   └── User.php
└── Services/
    └── AuthService.php
```

## Maintenance

### Rotating JWT Secret

To rotate the JWT secret:

1. Generate a new secret: `php artisan jwt:secret --force`
2. All existing tokens will be invalidated
3. Users will need to log in again

### Extending Token Lifetime

In `.env`:

```
JWT_TTL=120  # 2 hours
JWT_REFRESH_TTL=40320  # 4 weeks
```

## Troubleshooting

### Token not provided

Ensure the Authorization header is set: `Authorization: Bearer <token>`

### Token has expired

Refresh the token using the `/api/v1/auth/refresh` endpoint

### Token has been blacklisted

The user has logged out. A new login is required.

### User account is inactive

Contact an administrator to activate the account.
