# GeoOps Platform - Comprehensive Testing Guide

## Table of Contents

1. [Overview](#overview)
2. [Backend Testing (Laravel)](#backend-testing-laravel)
   - [Setup](#backend-setup)
   - [Test Structure](#backend-test-structure)
   - [Unit Tests](#unit-tests)
   - [Feature Tests](#feature-tests)
   - [Running Tests](#running-backend-tests)
3. [Mobile App Testing (React Native)](#mobile-app-testing-react-native)
   - [Setup](#mobile-setup)
   - [Test Structure](#mobile-test-structure)
   - [Service Tests](#service-tests)
   - [Component Tests](#component-tests)
   - [Running Tests](#running-mobile-tests)
4. [End-to-End Testing (Detox)](#end-to-end-testing-detox)
   - [Setup](#e2e-setup)
   - [E2E Test Examples](#e2e-test-examples)
5. [Test Coverage Goals](#test-coverage-goals)
6. [Continuous Integration](#continuous-integration)
7. [Best Practices](#best-practices)

---

## Overview

The GeoOps Platform implements a comprehensive testing strategy across multiple layers of the application stack. This guide covers unit tests, integration tests, and end-to-end tests that ensure the reliability and quality of the platform.

The testing strategy includes:
- **Backend Testing**: Laravel-based unit and feature tests for API endpoints and business logic
- **Mobile Testing**: React Native tests for services, stores, and components
- **E2E Testing**: Detox-based integration tests for complete user workflows
- **CI/CD Integration**: Automated testing via GitHub Actions

---

## Backend Testing (Laravel)

### Backend Setup

To set up the Laravel backend testing environment:

```bash
cd backend
composer install
php artisan test
```

### Backend Test Structure

The backend test suite is organized in a hierarchical structure that mirrors the application architecture:

```
backend/tests/
├── Unit/                          # Unit tests for isolated components
│   ├── Services/
│   │   ├── LandMeasurementServiceTest.php
│   │   ├── BillingServiceTest.php
│   │   └── SyncServiceTest.php
│   └── Utils/
│       └── AreaCalculationTest.php
├── Feature/                       # Integration tests for API endpoints
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── RegisterTest.php
│   ├── Land/
│   │   ├── CreateLandTest.php
│   │   ├── UpdateLandTest.php
│   │   └── DeleteLandTest.php
│   └── Job/
│       └── JobManagementTest.php
└── TestCase.php                   # Base test case class
```

#### Directory Organization Best Practices

- **Unit Tests**: Test individual services, models, and utilities in isolation
- **Feature Tests**: Test complete API flows including authentication and database interactions
- **TestCase.php**: Base class that provides common test setup and utilities

### Unit Tests

Unit tests focus on testing individual components in isolation. They should use mocks and stubs to isolate the code being tested.

#### Example: Land Measurement Service Test

This example demonstrates testing a core business service with multiple test cases covering happy paths and edge cases:

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\LandMeasurementService;
use App\DTOs\LandMeasurementDTO;

class LandMeasurementServiceTest extends TestCase
{
    private LandMeasurementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LandMeasurementService::class);
    }

    /**
     * Test that area calculation produces correct results
     */
    public function test_calculate_area_correctly()
    {
        $polygon = [
            ['latitude' => 7.8731, 'longitude' => 80.7718, 'accuracy' => 5],
            ['latitude' => 7.8735, 'longitude' => 80.7720, 'accuracy' => 5],
            ['latitude' => 7.8738, 'longitude' => 80.7715, 'accuracy' => 5],
            ['latitude' => 7.8732, 'longitude' => 80.7713, 'accuracy' => 5],
        ];

        $dto = new LandMeasurementDTO(
            name: 'Test Land',
            description: 'Test Description',
            measurementType: 'walk-around',
            polygon: $polygon,
            locationName: 'Test Location',
            customerName: 'Test Customer',
            customerPhone: '+94771234567',
            measuredAt: now()->toIso8601String(),
            offlineId: null
        );

        $result = $this->service->createMeasurement($dto, 1, 1);

        $this->assertArrayHasKey('area_acres', $result);
        $this->assertGreaterThan(0, $result['area_acres']);
    }

    /**
     * Test validation of polygon with insufficient points
     */
    public function test_rejects_polygon_with_less_than_three_points()
    {
        $this->expectException(\InvalidArgumentException::class);

        $dto = new LandMeasurementDTO(
            name: 'Test Land',
            description: null,
            measurementType: 'walk-around',
            polygon: [
                ['latitude' => 7.8731, 'longitude' => 80.7718, 'accuracy' => 5],
                ['latitude' => 7.8735, 'longitude' => 80.7720, 'accuracy' => 5],
            ],
            locationName: null,
            customerName: null,
            customerPhone: null,
            measuredAt: null,
            offlineId: null
        );
    }
}
```

**Key Testing Patterns:**
- Use `setUp()` method to initialize service dependencies
- Test both successful operations and error cases
- Use Data Transfer Objects (DTOs) for complex inputs
- Assert on specific properties of results
- Use `expectException()` for error scenarios

### Feature Tests

Feature tests verify the complete API endpoint workflow, including authentication, database operations, and response validation. These tests use the `RefreshDatabase` trait to ensure data isolation.

#### Example: Create Land Measurement API Test

This example demonstrates comprehensive testing of an API endpoint:

```php
<?php

namespace Tests\Feature\Land;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateLandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful land measurement creation
     */
    public function test_can_create_land_measurement()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        
        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Organization-ID' => $organization->id,
        ])->postJson('/api/v1/lands', [
            'name' => 'Test Field',
            'description' => 'Test paddy field',
            'measurement_type' => 'walk-around',
            'polygon' => [
                ['latitude' => 7.8731, 'longitude' => 80.7718, 'accuracy' => 5],
                ['latitude' => 7.8735, 'longitude' => 80.7720, 'accuracy' => 5],
                ['latitude' => 7.8738, 'longitude' => 80.7715, 'accuracy' => 5],
            ],
            'customer_name' => 'John Farmer',
            'customer_phone' => '+94771234567',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'area_acres',
                    'area_hectares',
                    'status',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('lands', [
            'name' => 'Test Field',
            'organization_id' => $organization->id,
        ]);
    }

    /**
     * Test that unauthenticated requests are rejected
     */
    public function test_requires_authentication()
    {
        $response = $this->postJson('/api/v1/lands', [
            'name' => 'Test Field',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test validation of required fields
     */
    public function test_validates_required_fields()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/lands', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'polygon']);
    }
}
```

**Key Testing Patterns:**
- Use `RefreshDatabase` trait to isolate test data
- Use factories to create test data cleanly
- Set authentication headers with bearer tokens
- Validate response structure using `assertJsonStructure()`
- Verify database changes with `assertDatabaseHas()`
- Test both positive and negative scenarios

### Running Backend Tests

The Laravel test suite provides multiple commands for different testing scenarios:

```bash
# Run all tests with output
php artisan test

# Run specific test file
php artisan test tests/Unit/Services/LandMeasurementServiceTest.php

# Run specific test method
php artisan test --filter=test_calculate_area_correctly

# Run tests with code coverage report
php artisan test --coverage

# Run tests with detailed output
php artisan test --verbose

# Run tests in parallel for faster execution
php artisan test --parallel

# Run tests with custom number of parallel processes
php artisan test --parallel --processes=4
```

---

## Mobile App Testing (React Native)

### Mobile Setup

To set up the React Native mobile testing environment:

```bash
cd mobile
npm install
npm test
```

### Mobile Test Structure

The mobile test suite is organized by functionality and component type:

```
mobile/__tests__/
├── services/                      # Service layer unit tests
│   ├── gpsService.test.ts
│   ├── apiClient.test.ts
│   └── syncService.test.ts
├── stores/                        # State management tests
│   ├── authStore.test.ts
│   └── measurementStore.test.ts
├── utils/                         # Utility function tests
│   └── helpers.test.ts
└── components/                    # UI component tests
    ├── MeasurementButton.test.tsx
    └── MapView.test.tsx
```

### Service Tests

Service tests verify the business logic of the mobile application, including GPS calculations, API communication, and data synchronization.

#### Example: GPS Service Test

This example demonstrates testing GPS-related calculations with multiple test cases:

```typescript
import { gpsService } from '@/services/gps/gpsService';
import { GPSPoint } from '@/types';

describe('GPSService', () => {
  describe('calculatePolygonArea', () => {
    /**
     * Test area calculation for valid polygon with 4 points
     */
    it('should calculate area correctly for valid polygon', () => {
      const points: GPSPoint[] = [
        { latitude: 7.8731, longitude: 80.7718, accuracy: 5, timestamp: Date.now() },
        { latitude: 7.8735, longitude: 80.7720, accuracy: 5, timestamp: Date.now() },
        { latitude: 7.8738, longitude: 80.7715, accuracy: 5, timestamp: Date.now() },
        { latitude: 7.8732, longitude: 80.7713, accuracy: 5, timestamp: Date.now() },
      ];

      const area = gpsService.calculatePolygonArea(points);

      expect(area.acres).toBeGreaterThan(0);
      expect(area.hectares).toBeGreaterThan(0);
      expect(area.squareMeters).toBeGreaterThan(0);
    });

    /**
     * Test edge case: polygon with less than 3 points
     */
    it('should return zero for polygon with less than 3 points', () => {
      const points: GPSPoint[] = [
        { latitude: 7.8731, longitude: 80.7718, accuracy: 5, timestamp: Date.now() },
        { latitude: 7.8735, longitude: 80.7720, accuracy: 5, timestamp: Date.now() },
      ];

      const area = gpsService.calculatePolygonArea(points);

      expect(area.acres).toBe(0);
      expect(area.hectares).toBe(0);
      expect(area.squareMeters).toBe(0);
    });
  });

  describe('calculateDistance', () => {
    /**
     * Test distance calculation between two GPS points
     */
    it('should calculate distance between two points', () => {
      const point1: GPSPoint = {
        latitude: 7.8731,
        longitude: 80.7718,
        accuracy: 5,
        timestamp: Date.now(),
      };
      const point2: GPSPoint = {
        latitude: 7.8735,
        longitude: 80.7720,
        accuracy: 5,
        timestamp: Date.now(),
      };

      const distance = gpsService.calculateDistance(point1, point2);

      expect(distance).toBeGreaterThan(0);
      expect(distance).toBeLessThan(1000); // Should be less than 1km
    });
  });
});
```

**Key Testing Patterns:**
- Organize tests using nested `describe()` blocks
- Use descriptive test names with `it()` or `test()`
- Test both positive and edge cases
- Use specific assertions to verify expected behavior
- Group related tests together

### Component Tests

Component tests verify that UI components render correctly and respond to user interactions.

#### Example: Measurement Button Component Test

This example demonstrates component rendering and interaction testing:

```typescript
import React from 'react';
import { render, fireEvent } from '@testing-library/react-native';
import { MeasurementButton } from '@/components/MeasurementButton';

describe('MeasurementButton', () => {
  /**
   * Test component renders with correct text
   */
  it('should render correctly', () => {
    const { getByText } = render(<MeasurementButton onPress={() => {}} />);
    expect(getByText('Start Measurement')).toBeTruthy();
  });

  /**
   * Test button press callback is triggered
   */
  it('should call onPress when pressed', () => {
    const onPressMock = jest.fn();
    const { getByText } = render(<MeasurementButton onPress={onPressMock} />);

    fireEvent.press(getByText('Start Measurement'));

    expect(onPressMock).toHaveBeenCalledTimes(1);
  });

  /**
   * Test loading state disables button and shows loading text
   */
  it('should be disabled when isLoading is true', () => {
    const { getByText } = render(
      <MeasurementButton onPress={() => {}} isLoading={true} />
    );

    const button = getByText('Loading...');
    expect(button).toBeTruthy();
  });
});
```

**Key Testing Patterns:**
- Use `render()` to mount components in test environment
- Use `fireEvent` to simulate user interactions
- Mock callback functions with `jest.fn()`
- Verify text content and element visibility
- Test different component states (loading, disabled, etc.)

### Running Mobile Tests

The mobile test suite provides multiple npm commands for different testing scenarios:

```bash
# Run all tests
npm test

# Run tests with coverage report
npm test -- --coverage

# Run tests in watch mode (re-runs on file changes)
npm test -- --watch

# Run specific test file
npm test -- gpsService.test.ts

# Run tests matching a pattern
npm test -- --testNamePattern="GPS"

# Run tests with verbose output
npm test -- --verbose

# Update snapshots
npm test -- -u
```

---

## End-to-End Testing (Detox)

End-to-end (E2E) testing simulates complete user workflows from start to finish, providing the highest level of confidence in critical user journeys.

### E2E Setup

To set up the Detox E2E testing environment:

```bash
cd mobile
npm install -g detox-cli
detox build -c ios.sim.debug
detox test -c ios.sim.debug
```

**Configuration Options:**
- `ios.sim.debug`: Debug build for iOS simulator
- `ios.sim.release`: Release build for iOS simulator
- `android.emu.debug`: Debug build for Android emulator
- `android.emu.release`: Release build for Android emulator

### E2E Test Examples

#### Example: Complete Land Measurement Flow

This example demonstrates a full user journey from login through saving a measurement:

```typescript
describe('Land Measurement Flow', () => {
  /**
   * Launch the app before all tests
   */
  beforeAll(async () => {
    await device.launchApp();
  });

  /**
   * Reload React Native before each test
   */
  beforeEach(async () => {
    await device.reloadReactNative();
  });

  /**
   * Test complete measurement workflow: login -> measure -> save
   */
  it('should allow user to measure land', async () => {
    // Step 1: User Login
    // Enter credentials
    await element(by.id('email-input')).typeText('test@example.com');
    await element(by.id('password-input')).typeText('password123');
    
    // Submit login form
    await element(by.id('login-button')).tap();

    // Step 2: Navigate to Measurement Section
    // Wait for login to complete and navigation to occur
    await waitFor(element(by.id('measurement-tab')))
      .toBeVisible()
      .withTimeout(5000);
    
    // Switch to measurement tab
    await element(by.id('measurement-tab')).tap();
    
    // Start measurement
    await element(by.id('start-measurement-button')).tap();

    // Step 3: Simulate GPS Points Collection
    // Add multiple GPS points to create a polygon
    await element(by.id('add-point-button')).tap();
    await element(by.id('add-point-button')).tap();
    await element(by.id('add-point-button')).tap();

    // Step 4: Finalize Measurement
    // Stop GPS collection
    await element(by.id('stop-measurement-button')).tap();
    
    // Enter measurement details
    await element(by.id('name-input')).typeText('Test Field');
    
    // Save measurement
    await element(by.id('save-button')).tap();

    // Step 5: Verify Saved Measurement
    // Confirm the measurement appears in the list
    await expect(element(by.text('Test Field'))).toBeVisible();
  });
});
```

**E2E Testing Best Practices:**
- Use descriptive test names that describe the user journey
- Break complex flows into logical steps with comments
- Use `waitFor()` to handle asynchronous operations
- Verify UI updates after user interactions
- Test critical user workflows end-to-end
- Keep E2E tests stable and deterministic

**Common Detox Matchers:**
- `by.id('elementId')`: Find element by test ID
- `by.text('text')`: Find element by visible text
- `by.type('RCTScrollView')`: Find element by component type
- `by.label('accessibility label')`: Find by accessibility label

---

## Test Coverage Goals

Maintaining high test coverage ensures code quality and reduces the risk of bugs in production. The following coverage targets have been established:

### Backend Coverage Targets

- **Overall**: Minimum 80% code coverage
- **Services**: 90%+ coverage (core business logic)
- **Controllers**: 80%+ coverage (API endpoints)
- **Models**: 70%+ coverage (data models and relationships)

### Mobile Coverage Targets

- **Overall**: Minimum 75% code coverage
- **Services**: 85%+ coverage (business logic)
- **Stores**: 80%+ coverage (state management)
- **Utils**: 90%+ coverage (utility functions)
- **Components**: 70%+ coverage (UI components)

### Coverage Measurement Commands

```bash
# Backend: Generate coverage report
php artisan test --coverage

# Mobile: Generate coverage report
npm test -- --coverage

# Mobile: View coverage in HTML format
npm test -- --coverage --collectCoverageFrom='src/**/*.{ts,tsx}'
```

### Coverage Goals Rationale

- **High coverage on services (85-90%)**: Services contain critical business logic that must be thoroughly tested
- **Moderate coverage on components (70%)**: Component testing focuses on behavior rather than 100% line coverage
- **Utility/Utils coverage (90%)**: Pure functions should have near-complete coverage
- **Controller coverage (80%)**: API endpoints need solid coverage but integration tests may cover some paths

---

## Continuous Integration

Automated testing via CI/CD ensures that all code changes are tested before merging.

### GitHub Actions Workflow

The following GitHub Actions workflow automates backend and mobile testing:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  backend:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: geoops_test
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
        ports:
          - 3306:3306
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: mbstring, pdo, pdo_mysql
      
      - name: Install Composer dependencies
        run: |
          cd backend
          composer install --no-interaction --prefer-dist
      
      - name: Setup Laravel environment
        run: |
          cd backend
          cp .env.testing .env
          php artisan key:generate
      
      - name: Run database migrations
        run: |
          cd backend
          php artisan migrate --env=testing
      
      - name: Run tests
        run: |
          cd backend
          php artisan test --coverage
  
  mobile:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '20'
          cache: 'npm'
          cache-dependency-path: mobile/package-lock.json
      
      - name: Install dependencies
        run: |
          cd mobile
          npm install
      
      - name: Run tests
        run: |
          cd mobile
          npm test -- --coverage
      
      - name: Upload coverage reports
        uses: codecov/codecov-action@v3
        with:
          files: ./mobile/coverage/lcov.info
```

### CI/CD Best Practices

1. **Run tests on every push and pull request** to catch issues early
2. **Use service containers** for databases and external dependencies
3. **Cache dependencies** to speed up workflow execution
4. **Parallelize test runs** when possible
5. **Upload coverage reports** for tracking and visibility
6. **Fail builds on test failures** to maintain code quality
7. **Set reasonable timeouts** for test execution

---

## Best Practices

### General Testing Principles

1. **Write Tests First (TDD)**
   - Write tests before implementing features
   - Ensures code is testable and specifications are clear
   - Provides documentation of expected behavior

2. **Mock External Dependencies**
   - Mock API calls to avoid external service dependencies
   - Mock GPS and Bluetooth interfaces
   - Isolate the code under test
   - Use `jest.mock()` in JavaScript or mock objects in PHP

3. **Test Edge Cases and Error Scenarios**
   - Test boundary conditions (empty arrays, null values, max values)
   - Test error handling and exception cases
   - Test with invalid data inputs
   - Verify proper error messages

4. **Keep Tests Isolated and Independent**
   - Each test should stand alone
   - Use `setUp()` and `tearDown()` for test initialization
   - Use `RefreshDatabase` trait in Laravel for data isolation
   - Avoid test interdependencies

5. **Use Descriptive Test Names**
   - Test names should describe what is being tested
   - Use format: `test_<scenario>_<expected_result>`
   - Example: `test_calculate_area_correctly_for_valid_polygon`
   - Avoid generic names like `test_function()` or `test_1()`

6. **Maintain High Coverage, Focus on Critical Paths**
   - Prioritize testing critical business logic
   - Focus on paths that directly impact users
   - Don't aim for 100% coverage on trivial code
   - Use coverage metrics as a guide, not a hard requirement

7. **Run Tests Before Committing**
   - Run full test suite locally before pushing changes
   - Use pre-commit hooks to enforce this
   - Fix test failures before requesting review

8. **Review Test Failures in CI/CD Pipeline**
   - Monitor CI/CD build status
   - Debug and fix failures promptly
   - Investigate flaky tests and make them stable
   - Document known issues and workarounds

9. **Update Tests When Refactoring**
   - Refactor tests along with production code
   - Ensure test coverage remains comprehensive
   - Update test expectations for behavior changes
   - Keep tests and code in sync

10. **Document Complex Test Scenarios**
    - Add comments explaining complex test setup
    - Document assumptions and prerequisites
    - Explain why certain edge cases are tested
    - Provide context for maintainers

### Testing Checklist

Before submitting a pull request, verify:

- [ ] All new features have corresponding tests
- [ ] Tests pass locally (`npm test`, `php artisan test`)
- [ ] Code coverage for new code is adequate
- [ ] Edge cases and error scenarios are tested
- [ ] Test names are descriptive
- [ ] No test warnings or deprecations
- [ ] Tests are isolated and can run in any order
- [ ] CI/CD pipeline passes all checks
- [ ] Test changes are documented if substantial

### Performance Considerations

- **Test Speed**: Aim for unit tests to complete in milliseconds
- **Database Tests**: Use transactions to speed up database tests
- **Parallel Execution**: Run tests in parallel when possible
- **Avoid Sleep/Delays**: Use proper wait mechanisms instead of fixed delays
- **Clean Up**: Ensure proper teardown to avoid memory leaks

### Test Maintenance

- Review and update tests during code reviews
- Refactor tests as production code evolves
- Remove redundant or obsolete tests
- Monitor and fix flaky tests
- Keep testing libraries and frameworks up to date

---

## Additional Resources

- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [Jest Testing Framework](https://jestjs.io/)
- [React Native Testing Library](https://callstack.github.io/react-native-testing-library/)
- [Detox E2E Testing](https://wix.github.io/Detox/)
- [Test-Driven Development](https://en.wikipedia.org/wiki/Test-driven_development)

---

**Last Updated**: January 2026  
**Version**: 1.0.0
