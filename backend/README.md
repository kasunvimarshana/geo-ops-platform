# GeoOps Platform Backend

## Overview

The GeoOps Platform backend is built using Laravel, providing a robust REST API for managing agricultural field services. This application supports functionalities such as user authentication, job management, land measurement, invoicing, and payment processing.

## Features

- **User Authentication**: Secure registration and login using JWT.
- **Job Management**: Create, update, and track jobs with real-time GPS tracking.
- **Land Measurement**: Measure land areas with GPS and store measurements.
- **Invoicing**: Generate and manage invoices automatically.
- **Payment Processing**: Handle various payment methods and track payments.
- **Expense Management**: Track expenses related to jobs and operations.
- **Reporting**: Generate reports for jobs, invoices, and financials.

## Technology Stack

- **Backend Framework**: Laravel 11.x
- **Database**: MySQL / PostgreSQL
- **Caching**: Redis
- **Authentication**: JWT
- **Testing**: PHPUnit

## Project Structure

The backend project is organized into several key directories:

- **app/**: Contains the core application logic, including models, controllers, services, and repositories.
- **database/**: Contains migrations, seeders, and factories for database management.
- **routes/**: Defines the API routes for the application.
- **tests/**: Contains feature and unit tests for the application.

## Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- MySQL / PostgreSQL
- Redis

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/kasunvimarshana/geo-ops-platform.git
   cd geo-ops-platform/backend
   ```

2. Install dependencies:

   ```bash
   composer install
   ```

3. Configure the environment:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Set up the database:
   - Create a database and update the `.env` file with your database credentials.
   - Run migrations:
     ```bash
     php artisan migrate --seed
     ```

5. Start the server:
   ```bash
   php artisan serve
   ```

## Testing

To run tests, use the following command:

```bash
php artisan test
```

## Contributing

Contributions are welcome! Please refer to the [CONTRIBUTING.md](CONTRIBUTING.md) file for guidelines.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Support

For support, please contact support@geo-ops.lk or open an issue on GitHub.
