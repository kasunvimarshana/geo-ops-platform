# Subscription Package API Documentation

## Overview

The Subscription Package API provides endpoints for managing subscription packages, viewing current organization subscription status, and checking resource limits. This system enables organizations to track their usage against their subscription tier limits (Free, Basic, Pro).

## Base URL

```
/api/v1/subscriptions
```

## Authentication

All endpoints require JWT authentication and organization isolation middleware.

**Headers:**
```
Authorization: Bearer {jwt_token}
Content-Type: application/json
```

---

## Endpoints

### 1. List Available Subscription Packages

Get all available subscription packages with their limits, pricing, and features.

**Endpoint:** `GET /api/v1/subscriptions/packages`

**Response:** `200 OK`

```json
{
  "success": true,
  "message": "Subscription packages retrieved successfully.",
  "data": [
    {
      "id": 1,
      "name": "free",
      "display_name": "Free Plan",
      "description": "Perfect for getting started with basic features",
      "limits": {
        "measurements": 100,
        "drivers": 2,
        "jobs": 50,
        "lands": 5,
        "storage_mb": 500
      },
      "pricing": {
        "monthly": 0.00,
        "yearly": null,
        "yearly_savings": null,
        "yearly_savings_percentage": null
      },
      "features": [
        "Basic tracking",
        "Limited reporting",
        "Email support"
      ],
      "is_active": true,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "basic",
      "display_name": "Basic Plan",
      "description": "Ideal for small farming operations",
      "limits": {
        "measurements": 500,
        "drivers": 5,
        "jobs": 200,
        "lands": 20,
        "storage_mb": 2048
      },
      "pricing": {
        "monthly": 29.99,
        "yearly": 299.00,
        "yearly_savings": 60.88,
        "yearly_savings_percentage": 16.92
      },
      "features": [
        "Advanced tracking",
        "Comprehensive reporting",
        "Priority support",
        "Mobile app"
      ],
      "is_active": true,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    {
      "id": 3,
      "name": "pro",
      "display_name": "Professional Plan",
      "description": "Complete solution for professional agricultural businesses",
      "limits": {
        "measurements": 2000,
        "drivers": 20,
        "jobs": 1000,
        "lands": 100,
        "storage_mb": 10240
      },
      "pricing": {
        "monthly": 99.99,
        "yearly": 999.00,
        "yearly_savings": 200.88,
        "yearly_savings_percentage": 16.74
      },
      "features": [
        "Unlimited tracking",
        "Custom reports",
        "24/7 support",
        "API access",
        "White label",
        "Advanced analytics"
      ],
      "is_active": true,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

---

### 2. Get Current Organization Subscription

Retrieve the current organization's subscription details with comprehensive usage statistics.

**Endpoint:** `GET /api/v1/subscriptions/current`

**Response:** `200 OK`

```json
{
  "success": true,
  "message": "Current subscription retrieved successfully.",
  "data": {
    "subscription": {
      "package_tier": "basic",
      "package_expires_at": "2025-06-18T19:00:00.000000Z",
      "is_expired": false
    },
    "package_limits": {
      "lands": 20,
      "measurements": 500,
      "jobs": 200,
      "drivers": 5,
      "storage_mb": 2048
    },
    "current_usage": {
      "lands": 8,
      "measurements": 16,
      "jobs": 9,
      "drivers": 3
    },
    "usage_percentages": {
      "lands": 40.00,
      "measurements": 3.20,
      "jobs": 4.50,
      "drivers": 60.00
    },
    "warnings": []
  }
}
```

**Warning Examples:**

When usage approaches or exceeds limits:

```json
{
  "warnings": [
    "You are approaching the limit for drivers (4/5, 80%)",
    "You have reached the limit for lands (20/20)"
  ]
}
```

---

### 3. Check Resource Limit

Check if the organization can perform an action (create land, job, etc.) without exceeding their subscription limits.

**Endpoint:** `POST /api/v1/subscriptions/check-limit`

**Request Body:**

```json
{
  "resource": "lands",
  "count": 2
}
```

**Parameters:**

| Field | Type | Required | Description | Valid Values |
|-------|------|----------|-------------|--------------|
| resource | string | Yes | Type of resource to check | `lands`, `measurements`, `jobs`, `drivers` |
| count | integer | No | Number of items to add (default: 1) | Minimum: 1 |

**Response (Can Perform):** `200 OK`

```json
{
  "success": true,
  "message": "Action is allowed.",
  "data": {
    "can_perform": true,
    "reason": "You can add 2 more lands",
    "current_usage": 8,
    "limit": 20,
    "available": 12,
    "requested": 2
  }
}
```

**Response (Would Exceed Limit):** `200 OK`

```json
{
  "success": true,
  "message": "Action would exceed package limits.",
  "data": {
    "can_perform": false,
    "reason": "Adding 5 lands would exceed your package limit of 20",
    "current_usage": 18,
    "limit": 20,
    "available": 2,
    "requested": 5
  }
}
```

**Response (At Limit):** `200 OK`

```json
{
  "success": true,
  "message": "Action would exceed package limits.",
  "data": {
    "can_perform": false,
    "reason": "Adding 1 lands would exceed your package limit of 20",
    "current_usage": 20,
    "limit": 20,
    "available": 0,
    "requested": 1
  }
}
```

---

## Error Responses

### 400 Bad Request

Invalid input data.

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "resource": [
      "Invalid resource type. Must be one of: lands, measurements, jobs, drivers."
    ]
  }
}
```

### 401 Unauthorized

Missing or invalid authentication token.

```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 500 Internal Server Error

Server-side error.

```json
{
  "success": false,
  "message": "Failed to retrieve subscription packages."
}
```

---

## Subscription Packages

### Package Tiers

| Tier | Monthly Price | Yearly Price | Lands | Measurements | Jobs | Drivers | Storage |
|------|---------------|--------------|-------|--------------|------|---------|---------|
| **Free** | $0.00 | - | 5 | 100 | 50 | 2 | 500 MB |
| **Basic** | $29.99 | $299.00 | 20 | 500 | 200 | 5 | 2 GB |
| **Pro** | $99.99 | $999.00 | 100 | 2000 | 1000 | 20 | 10 GB |

### Package Features

#### Free Plan
- Basic tracking
- Limited reporting
- Email support

#### Basic Plan
- Advanced tracking
- Comprehensive reporting
- Priority support
- Mobile app
- Save ~17% with yearly billing

#### Professional Plan
- Unlimited tracking
- Custom reports
- 24/7 support
- API access
- White label
- Advanced analytics
- Save ~17% with yearly billing

---

## Usage Examples

### JavaScript/Fetch

```javascript
// Get available packages
const getPackages = async () => {
  const response = await fetch('/api/v1/subscriptions/packages', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  return await response.json();
};

// Get current subscription
const getCurrentSubscription = async () => {
  const response = await fetch('/api/v1/subscriptions/current', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  return await response.json();
};

// Check if can add 3 new lands
const checkLimit = async () => {
  const response = await fetch('/api/v1/subscriptions/check-limit', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      resource: 'lands',
      count: 3
    })
  });
  return await response.json();
};
```

### cURL

```bash
# Get packages
curl -X GET "http://localhost:8000/api/v1/subscriptions/packages" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json"

# Get current subscription
curl -X GET "http://localhost:8000/api/v1/subscriptions/current" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json"

# Check limit
curl -X POST "http://localhost:8000/api/v1/subscriptions/check-limit" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "resource": "lands",
    "count": 2
  }'
```

### PHP/Laravel

```php
use Illuminate\Support\Facades\Http;

// Get packages
$packages = Http::withToken($token)
    ->get(config('app.url') . '/api/v1/subscriptions/packages')
    ->json();

// Get current subscription
$subscription = Http::withToken($token)
    ->get(config('app.url') . '/api/v1/subscriptions/current')
    ->json();

// Check limit
$limitCheck = Http::withToken($token)
    ->post(config('app.url') . '/api/v1/subscriptions/check-limit', [
        'resource' => 'lands',
        'count' => 2
    ])
    ->json();
```

---

## Implementation Notes

### Usage Calculation

Usage is calculated in real-time based on:
- **Lands**: Total non-deleted lands in the organization
- **Measurements**: Total measurements in the organization
- **Jobs**: Total non-deleted field jobs in the organization
- **Drivers**: Total users with 'driver' role in the organization

### Warning Thresholds

- **80% usage**: Warning that limit is being approached
- **100% usage**: Warning that limit has been reached

### Best Practices

1. **Check limits before creation**: Always call `/check-limit` before allowing users to create new resources
2. **Display usage in UI**: Show current usage and limits to help users understand their subscription
3. **Handle limit errors gracefully**: Provide clear upgrade paths when limits are reached
4. **Cache package data**: Available packages rarely change, consider caching for performance

### Frontend Integration

```javascript
// Before creating a new land
const canCreateLand = async () => {
  const result = await checkLimit('lands', 1);
  
  if (!result.data.can_perform) {
    // Show upgrade dialog
    showUpgradeDialog(result.data.reason);
    return false;
  }
  
  return true;
};

// Display usage meter
const displayUsageMeter = async () => {
  const subscription = await getCurrentSubscription();
  const { current_usage, package_limits, usage_percentages } = subscription.data;
  
  // Render usage bars for each resource
  renderUsageBar('lands', current_usage.lands, package_limits.lands, usage_percentages.lands);
  renderUsageBar('jobs', current_usage.jobs, package_limits.jobs, usage_percentages.jobs);
  // ...
};
```

---

## Related APIs

- **Organizations API**: `/api/v1/organizations` - Manage organization details
- **Lands API**: `/api/v1/lands` - Manage lands (affected by subscription limits)
- **Jobs API**: `/api/v1/jobs` - Manage field jobs (affected by subscription limits)
- **Measurements API**: `/api/v1/measurements` - Manage measurements (affected by subscription limits)

---

## Change Log

### Version 1.0.0 (2024-01-18)
- Initial release
- GET `/packages` - List available packages
- GET `/current` - Get current subscription with usage
- POST `/check-limit` - Check resource limits
- Support for Free, Basic, and Pro tiers
- Real-time usage calculation
- Usage percentage warnings
