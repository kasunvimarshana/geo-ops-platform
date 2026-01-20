# Contributing to GeoOps Platform

Thank you for your interest in contributing to GeoOps Platform! This document provides guidelines and instructions for contributing.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Pull Request Process](#pull-request-process)
- [Issue Reporting](#issue-reporting)

## Code of Conduct

### Our Pledge

We pledge to make participation in our project a harassment-free experience for everyone, regardless of age, body size, disability, ethnicity, gender identity and expression, level of experience, nationality, personal appearance, race, religion, or sexual identity and orientation.

### Our Standards

- Using welcoming and inclusive language
- Being respectful of differing viewpoints
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards others

## Getting Started

### Prerequisites

- Git
- PHP 8.3+
- Composer
- Node.js 18+
- npm or yarn
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional but recommended)

### Fork and Clone

```bash
# Fork the repository on GitHub, then clone your fork
git clone https://github.com/YOUR_USERNAME/geo-ops-platform.git
cd geo-ops-platform

# Add upstream remote
git remote add upstream https://github.com/kasunvimarshana/geo-ops-platform.git
```

### Setup Development Environment

#### Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

#### Mobile Setup

```bash
cd mobile
npm install
cp .env.example .env
# Update EXPO_PUBLIC_API_URL in .env
npm start
```

## Development Workflow

### 1. Create a Branch

```bash
git checkout -b feature/your-feature-name
# or
git checkout -b fix/your-bug-fix
```

### Branch Naming Convention

- `feature/` - New features
- `fix/` - Bug fixes
- `docs/` - Documentation changes
- `refactor/` - Code refactoring
- `test/` - Adding tests
- `chore/` - Maintenance tasks

### 2. Make Changes

- Write clean, readable code
- Follow coding standards
- Add tests for new features
- Update documentation as needed
- Commit regularly with clear messages

### 3. Commit Messages

Follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation only
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

**Examples:**

```bash
git commit -m "feat(auth): add password reset functionality"
git commit -m "fix(gps): correct area calculation in shoelace formula"
git commit -m "docs(api): update field endpoints documentation"
```

### 4. Keep Your Branch Updated

```bash
git fetch upstream
git rebase upstream/main
```

### 5. Push Changes

```bash
git push origin feature/your-feature-name
```

## Coding Standards

### PHP (Backend)

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Use type hints for parameters and return types
- Write PHPDoc comments for classes and methods
- Use dependency injection
- Follow Laravel best practices

**Example:**

```php
<?php

namespace App\Services;

use App\Models\Field;
use Illuminate\Support\Collection;

class FieldService
{
    /**
     * Calculate area and perimeter for a field boundary.
     *
     * @param array $coordinates Array of [longitude, latitude] points
     * @return array{area: float, perimeter: float}
     */
    public function calculateMetrics(array $coordinates): array
    {
        // Implementation
    }
}
```

### TypeScript/JavaScript (Mobile)

- Follow the [Airbnb JavaScript Style Guide](https://github.com/airbnb/javascript)
- Use TypeScript for type safety
- Use functional components with hooks
- Write JSDoc comments for complex functions
- Follow Clean Architecture principles

**Example:**

```typescript
/**
 * Validates email address format
 * @param email - Email address to validate
 * @returns True if email is valid, false otherwise
 */
export const isValidEmail = (email: string): boolean => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};
```

### Code Formatting

- **Backend**: Run `vendor/bin/phpcs` and `vendor/bin/phpcbf`
- **Mobile**: Run `npm run lint` and `npm run format`

## Testing

### Backend Tests

```bash
cd backend

# Run all tests
php artisan test

# Run specific test file
php artisan test --filter FieldControllerTest

# Run with coverage
php artisan test --coverage
```

### Mobile Tests

```bash
cd mobile

# Run tests (when implemented)
npm test

# Run with coverage
npm test -- --coverage
```

### Writing Tests

#### Backend (PHPUnit)

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class FieldControllerTest extends TestCase
{
    public function test_user_can_create_field(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/v1/fields', [
                'name' => 'Test Field',
                'location' => 'Test Location',
                // ...
            ]);

        $response->assertStatus(201)
            ->assertJson(['name' => 'Test Field']);
    }
}
```

#### Mobile (Jest - when implemented)

```typescript
import { render, fireEvent } from '@testing-library/react-native';
import LoginScreen from './LoginScreen';

describe('LoginScreen', () => {
  it('should display error for invalid email', () => {
    const { getByTestId, getByText } = render(<LoginScreen />);

    fireEvent.changeText(getByTestId('email-input'), 'invalid-email');
    fireEvent.press(getByTestId('login-button'));

    expect(getByText('Invalid email address')).toBeTruthy();
  });
});
```

## Pull Request Process

### Before Submitting

1. ✅ Code follows style guidelines
2. ✅ Tests pass locally
3. ✅ New tests added for new features
4. ✅ Documentation updated
5. ✅ Commits are clean and well-formatted
6. ✅ Branch is up to date with main

### Submitting a Pull Request

1. Push your changes to your fork
2. Go to the original repository on GitHub
3. Click "New Pull Request"
4. Select your branch
5. Fill in the PR template

### PR Template

```markdown
## Description

Brief description of changes

## Type of Change

- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing

- [ ] Tests pass locally
- [ ] New tests added
- [ ] Manual testing completed

## Screenshots (if applicable)

Add screenshots for UI changes

## Checklist

- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings generated
```

### Review Process

- At least one maintainer must approve
- All CI checks must pass
- All comments must be resolved
- Code coverage should not decrease

### After Approval

- Maintainer will merge the PR
- Your contribution will be credited
- Branch will be deleted

## Issue Reporting

### Before Creating an Issue

- Search existing issues to avoid duplicates
- Check if it's already fixed in the latest version
- Collect relevant information

### Issue Template

**Bug Report:**

```markdown
## Description

Clear description of the bug

## Steps to Reproduce

1. Go to '...'
2. Click on '...'
3. See error

## Expected Behavior

What should happen

## Actual Behavior

What actually happens

## Environment

- OS: [e.g., Ubuntu 22.04]
- PHP Version: [e.g., 8.3]
- Node Version: [e.g., 18.0]
- Browser: [e.g., Chrome 120]

## Screenshots

If applicable

## Additional Context

Any other relevant information
```

**Feature Request:**

```markdown
## Problem Statement

Describe the problem this feature would solve

## Proposed Solution

Describe your proposed solution

## Alternatives Considered

Other solutions you've considered

## Additional Context

Any other context or screenshots
```

## Questions or Help?

- 💬 **Discussions**: Use GitHub Discussions for questions
- 📧 **Email**: kasunvmail@gmail.com
- 📚 **Documentation**: Check the `/docs` folder

## Recognition

Contributors will be:

- Listed in the README
- Credited in release notes
- Eligible for contributor badge

Thank you for contributing! 🎉
