# Implementation Examples

This directory contains production-ready code examples for the Geo Ops Platform, demonstrating Clean Architecture principles, SOLID design, and best practices.

## Backend Examples (Laravel)

### Controllers
- **AuthController.php** - Thin authentication controller following Clean Architecture
- **MeasurementController.php** - GPS land measurement endpoints

**Key Features:**
- Dependency injection
- Request validation via Form Requests
- Response transformation via API Resources
- Consistent response format
- Proper error handling

### Services
- **AuthService.php** - Authentication business logic
- **MeasurementService.php** - Land measurement workflows
- **AreaCalculationService.php** - GPS polygon area calculations

**Key Features:**
- Single responsibility
- Database transactions
- Complex business logic encapsulation
- No direct database access (uses repositories)
- Comprehensive error handling

### Repositories
- **MeasurementRepository.php** - Data access abstraction for measurements

**Key Features:**
- Interface-based design
- Query optimization
- Organization-level filtering
- Reusable query methods
- Pagination support

### Models
- **User.php** - User model with JWT authentication
- **Measurement.php** - Land measurement model with relationships

**Key Features:**
- Eloquent relationships
- Global scopes for multi-tenancy
- Custom accessors/mutators
- Query scopes
- Soft deletes and audit fields

### Middleware
- **AuthenticateJWT.php** - JWT token authentication
- **RoleMiddleware.php** - Role-based authorization
- **SubscriptionMiddleware.php** - Package limit enforcement

**Key Features:**
- Token validation
- Permission checking
- Subscription feature gating
- Proper error responses

## Frontend Examples (React Native/Expo)

### API Layer
- **client.ts** - Centralized HTTP client with interceptors
- **measurement.api.ts** - Measurement API methods

**Key Features:**
- Axios interceptors
- Automatic token refresh
- Error handling
- Offline detection
- Type-safe with TypeScript

### State Management
- **authStore.ts** - Authentication state with Zustand
- **measurementStore.ts** - Measurement state with offline support

**Key Features:**
- Zustand for predictable state
- Persistent storage
- Optimistic updates
- Offline queue management
- Type-safe actions

### Screens
- **MeasurementListScreen.tsx** - List view with pull-to-refresh

**Key Features:**
- FlatList optimization
- Pull-to-refresh
- Empty states
- Error handling
- Floating action button
- Responsive design

### Custom Hooks
- **useGPSTracking.ts** - GPS location tracking hook
- **useAreaCalculation** - Area calculation from GPS points

**Key Features:**
- Permission handling
- Battery-optimized tracking
- Real-time location updates
- Haversine distance calculations
- Memory cleanup

## Code Quality Standards

### TypeScript
- Strict type checking enabled
- Interfaces for all data structures
- No implicit any
- Proper error typing

### React Native
- Functional components with hooks
- Proper cleanup in useEffect
- Optimized re-renders
- Platform-specific code where needed

### Laravel
- PSR-12 coding standards
- Type hints on all methods
- Return type declarations
- Comprehensive PHPDoc comments

### Security
- Input validation
- SQL injection prevention (Eloquent)
- XSS protection
- CSRF tokens
- JWT token expiry
- Secure storage for sensitive data

### Performance
- Database query optimization
- Proper indexing
- Lazy loading
- Code splitting
- Caching strategies
- Background job processing

## Testing Examples

### Backend Tests
```php
// Feature test example
public function test_user_can_create_measurement()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->postJson('/api/v1/measurements', [
        'customer_name' => 'John Doe',
        'customer_phone' => '+94771234567',
        'polygon_points' => [
            ['latitude' => 7.8731, 'longitude' => 80.7718],
            ['latitude' => 7.8735, 'longitude' => 80.7720],
            ['latitude' => 7.8733, 'longitude' => 80.7722],
        ],
    ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('measurements', [
        'customer_name' => 'John Doe',
    ]);
}
```

### Frontend Tests
```typescript
// Component test example
import { render, fireEvent, waitFor } from '@testing-library/react-native';
import { MeasurementListScreen } from './MeasurementListScreen';

test('displays measurements list', async () => {
  const { getByText } = render(<MeasurementListScreen />);
  
  await waitFor(() => {
    expect(getByText('John Doe')).toBeTruthy();
  });
});
```

## Architecture Patterns

### Backend Patterns
1. **Repository Pattern** - Data access abstraction
2. **Service Layer** - Business logic encapsulation
3. **DTO Pattern** - Request/response transformation
4. **Observer Pattern** - Events and listeners
5. **Strategy Pattern** - Payment methods, storage drivers

### Frontend Patterns
1. **Container/Presenter** - Logic/UI separation
2. **Custom Hooks** - Reusable logic
3. **State Management** - Centralized with Zustand
4. **API Layer** - Centralized HTTP client
5. **Offline-First** - Local-first with sync

## Best Practices Demonstrated

### SOLID Principles
- **Single Responsibility** - Each class/component has one job
- **Open/Closed** - Extension without modification
- **Liskov Substitution** - Interface implementations
- **Interface Segregation** - Focused interfaces
- **Dependency Inversion** - Depend on abstractions

### DRY (Don't Repeat Yourself)
- Reusable services
- Custom hooks
- Shared components
- Utility functions
- Trait/mixin usage

### KISS (Keep It Simple, Stupid)
- Clear, readable code
- Minimal complexity
- No over-engineering
- Straightforward logic
- Obvious naming

## File Organization

```
examples/
├── backend/
│   ├── controllers/      # HTTP request handlers
│   ├── services/         # Business logic
│   ├── repositories/     # Data access
│   ├── models/          # Eloquent models
│   ├── middleware/      # Request filters
│   └── requests/        # Validation rules
└── frontend/
    ├── api/             # API clients
    ├── stores/          # State management
    ├── screens/         # Full-page views
    ├── components/      # Reusable UI
    ├── hooks/           # Custom hooks
    └── services/        # Business logic
```

## Usage Guidelines

1. **Copy patterns, not code** - Adapt examples to your needs
2. **Maintain consistency** - Follow established patterns
3. **Test thoroughly** - Write tests for new code
4. **Document changes** - Update comments and docs
5. **Review carefully** - Code review before merging

## Additional Resources

- [Backend STRUCTURE.md](../STRUCTURE.md) - Complete backend architecture
- [Frontend STRUCTURE.md](../../frontend/STRUCTURE.md) - Complete frontend architecture
- [API.md](../../API.md) - API documentation
- [DATABASE.md](../../DATABASE.md) - Database schema

---

These examples are production-ready and demonstrate enterprise-grade code quality suitable for large-scale applications serving thousands of users.
