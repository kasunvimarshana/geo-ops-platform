# Database Schema for GeoOps Platform

## Overview

This document outlines the database schema for the GeoOps Platform, detailing the core tables and their relationships. The schema is designed to support the application's functionalities, including user management, job tracking, land measurement, billing, and payments.

## Core Tables

### 1. Organizations

- **Table Name**: `organizations`
- **Description**: Stores information about different organizations using the platform.
- **Columns**:
  - `id`: Primary key
  - `name`: Organization name
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

### 2. Users

- **Table Name**: `users`
- **Description**: Stores user information, including roles and authentication details.
- **Columns**:
  - `id`: Primary key
  - `organization_id`: Foreign key referencing `organizations.id`
  - `name`: User's full name
  - `email`: User's email address (unique)
  - `password`: Hashed password
  - `role`: User role (Admin, Owner, Driver, Broker, Accountant)
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

### 3. Customers

- **Table Name**: `customers`
- **Description**: Stores information about customers (farmers) requesting services.
- **Columns**:
  - `id`: Primary key
  - `organization_id`: Foreign key referencing `organizations.id`
  - `name`: Customer's name
  - `contact_number`: Customer's contact number
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

### 4. Lands

- **Table Name**: `lands`
- **Description**: Stores land measurement data.
- **Columns**:
  - `id`: Primary key
  - `customer_id`: Foreign key referencing `customers.id`
  - `area`: Measured area (in acres/hectares)
  - `coordinates`: Geospatial data for land boundaries
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

### 5. Jobs

- **Table Name**: `jobs`
- **Description**: Stores information about jobs assigned to drivers.
- **Columns**:
  - `id`: Primary key
  - `land_id`: Foreign key referencing `lands.id`
  - `driver_id`: Foreign key referencing `drivers.id`
  - `status`: Job status (Pending, In Progress, Completed)
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

### 6. Job Tracking

- **Table Name**: `job_tracking`
- **Description**: Stores GPS tracking data for jobs.
- **Columns**:
  - `id`: Primary key
  - `job_id`: Foreign key referencing `jobs.id`
  - `latitude`: Latitude of the job location
  - `longitude`: Longitude of the job location
  - `timestamp`: Timestamp of the tracking data
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

### 7. Invoices

- **Table Name**: `invoices`
- **Description**: Stores billing information for completed jobs.
- **Columns**:
  - `id`: Primary key
  - `job_id`: Foreign key referencing `jobs.id`
  - `amount`: Total amount billed
  - `status`: Invoice status (Draft, Sent, Paid, Overdue)
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

### 8. Payments

- **Table Name**: `payments`
- **Description**: Stores payment records against invoices.
- **Columns**:
  - `id`: Primary key
  - `invoice_id`: Foreign key referencing `invoices.id`
  - `amount`: Amount paid
  - `payment_method`: Method of payment (Cash, Bank Transfer, Mobile Money)
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

### 9. Expenses

- **Table Name**: `expenses`
- **Description**: Stores expense records related to jobs and operations.
- **Columns**:
  - `id`: Primary key
  - `job_id`: Foreign key referencing `jobs.id`
  - `amount`: Expense amount
  - `description`: Description of the expense
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

### 10. Subscriptions

- **Table Name**: `subscriptions`
- **Description**: Stores subscription details for organizations.
- **Columns**:
  - `id`: Primary key
  - `organization_id`: Foreign key referencing `organizations.id`
  - `tier`: Subscription tier (Free, Basic, Pro)
  - `expiry_date`: Subscription expiry date
  - `created_at`: Timestamp
  - `updated_at`: Timestamp

## Relationships

- Each **Organization** can have multiple **Users** and **Customers**.
- Each **Customer** can have multiple **Lands**.
- Each **Land** can be associated with multiple **Jobs**.
- Each **Job** can have multiple **Job Tracking** records.
- Each **Job** can generate one **Invoice**.
- Each **Invoice** can have multiple **Payments**.
- Each **Job** can have multiple **Expenses**.
- Each **Organization** can have one **Subscription**.

## Conclusion

This schema is designed to efficiently manage the data required for the GeoOps Platform, ensuring scalability and reliability as the user base grows.
