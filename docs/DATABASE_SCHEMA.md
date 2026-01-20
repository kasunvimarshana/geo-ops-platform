# Database Schema - GeoOps Platform

## Entity Relationship Diagram (ERD)

```
┌─────────────────────┐
│   organizations     │
├─────────────────────┤
│ id (PK)             │
│ name                │
│ owner_id (FK)       │
│ subscription_pkg    │
│ subscription_exp_at │
│ settings (JSON)     │
│ created_at          │
│ updated_at          │
└─────────┬───────────┘
          │ 1
          │
          │ N
┌─────────▼───────────┐       ┌──────────────────────┐
│       users         │       │    subscriptions     │
├─────────────────────┤       ├──────────────────────┤
│ id (PK)             │       │ id (PK)              │
│ organization_id (FK)│◄──────┤ organization_id (FK) │
│ name                │       │ package              │
│ email (unique)      │       │ amount               │
│ password            │       │ starts_at            │
│ phone               │       │ expires_at           │
│ role (enum)         │       │ status               │
│ email_verified_at   │       │ created_at           │
│ created_at          │       │ updated_at           │
│ updated_at          │       └──────────────────────┘
│ deleted_at          │
└─────────┬───────────┘
          │ 1
          │
          │ 1
┌─────────▼───────────┐
│      drivers        │
├─────────────────────┤
│ id (PK)             │
│ user_id (FK)        │
│ organization_id (FK)│
│ license_number      │
│ vehicle_info (JSON) │
│ status              │
│ created_at          │
│ updated_at          │
│ deleted_at          │
└─────────┬───────────┘
          │ 1
          │
          │ N
┌─────────▼───────────┐       ┌──────────────────────┐
│   tracking_logs     │       │   land_measurements  │
├─────────────────────┤       ├──────────────────────┤
│ id (PK)             │       │ id (PK)              │
│ driver_id (FK)      │       │ organization_id (FK) │
│ job_id (FK)         │       │ name                 │
│ latitude            │       │ coordinates (POLYGON)│
│ longitude           │       │ area_acres           │
│ accuracy            │       │ area_hectares        │
│ speed               │       │ measured_by (FK)     │
│ heading             │       │ measured_at          │
│ recorded_at         │       │ created_at           │
└─────────────────────┘       │ updated_at           │
                              │ deleted_at           │
                              └──────────┬───────────┘
                                         │ 1
                                         │
                                         │ N
                              ┌──────────▼───────────┐
                              │        jobs          │
                              ├──────────────────────┤
                              │ id (PK)              │
                              │ organization_id (FK) │
                              │ customer_id (FK)     │
                              │ land_measurement_id  │
                              │ driver_id (FK)       │
                              │ machine_id (FK)      │
                              │ status (enum)        │
                              │ scheduled_at         │
                              │ started_at           │
                              │ completed_at         │
                              │ notes (TEXT)         │
                              │ created_by (FK)      │
                              │ created_at           │
                              │ updated_at           │
                              │ deleted_at           │
                              └──────────┬───────────┘
                                         │ 1
                                         │
                                         │ 1
                              ┌──────────▼───────────┐
                              │      invoices        │
                              ├──────────────────────┤
                              │ id (PK)              │
                              │ organization_id (FK) │
                              │ job_id (FK)          │
                              │ customer_id (FK)     │
                              │ invoice_number       │
                              │ amount               │
                              │ tax                  │
                              │ discount             │
                              │ total                │
                              │ status (enum)        │
                              │ pdf_path             │
                              │ issued_at            │
                              │ due_at               │
                              │ paid_at              │
                              │ created_at           │
                              │ updated_at           │
                              └──────────┬───────────┘
                                         │ 1
                                         │
                                         │ N
                              ┌──────────▼───────────┐
                              │      payments        │
                              ├──────────────────────┤
                              │ id (PK)              │
                              │ organization_id (FK) │
                              │ invoice_id (FK)      │
                              │ customer_id (FK)     │
                              │ amount               │
                              │ payment_method       │
                              │ reference_number     │
                              │ payment_date         │
                              │ created_by (FK)      │
                              │ created_at           │
                              │ updated_at           │
                              └──────────────────────┘

┌─────────────────────┐       ┌──────────────────────┐
│      customers      │       │      machines        │
├─────────────────────┤       ├──────────────────────┤
│ id (PK)             │       │ id (PK)              │
│ organization_id (FK)│       │ organization_id (FK) │
│ name                │       │ name                 │
│ phone               │       │ type                 │
│ email               │       │ model                │
│ address             │       │ registration_number  │
│ balance             │       │ status               │
│ created_at          │       │ created_at           │
│ updated_at          │       │ updated_at           │
│ deleted_at          │       │ deleted_at           │
└─────────────────────┘       └──────────────────────┘
          │                              │
          │ 1                            │ 1
          │                              │
          │ N                            │ N
┌─────────▼───────────┐       ┌──────────▼───────────┐
│      expenses       │       │    expense_logs      │
├─────────────────────┤       ├──────────────────────┤
│ id (PK)             │       │ id (PK)              │
│ organization_id (FK)│       │ machine_id (FK)      │
│ job_id (FK)         │       │ expense_type         │
│ driver_id (FK)      │       │ description          │
│ machine_id (FK)     │       │ amount               │
│ category (enum)     │       │ logged_at            │
│ amount              │       │ created_at           │
│ description         │       └──────────────────────┘
│ receipt_path        │
│ expense_date        │
│ created_by (FK)     │
│ created_at          │
│ updated_at          │
│ deleted_at          │
└─────────────────────┘

┌─────────────────────┐
│   password_resets   │
├─────────────────────┤
│ email               │
│ token               │
│ created_at          │
└─────────────────────┘

┌─────────────────────┐
│   audit_logs        │
├─────────────────────┤
│ id (PK)             │
│ user_id (FK)        │
│ organization_id (FK)│
│ action              │
│ entity_type         │
│ entity_id           │
│ old_values (JSON)   │
│ new_values (JSON)   │
│ ip_address          │
│ user_agent          │
│ created_at          │
└─────────────────────┘
```

## Table Definitions

### organizations

Stores organization/company information.

```sql
CREATE TABLE organizations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    owner_id BIGINT UNSIGNED NOT NULL,
    subscription_package ENUM('free', 'basic', 'pro') DEFAULT 'free',
    subscription_expires_at TIMESTAMP NULL,
    settings JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_owner_id (owner_id),
    INDEX idx_subscription_package (subscription_package)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**settings JSON structure:**

```json
{
  "currency": "LKR",
  "timezone": "Asia/Colombo",
  "default_rate_per_acre": 5000,
  "tax_percentage": 0,
  "invoice_prefix": "INV",
  "locale": "si"
}
```

### users

Stores all system users (admin, owners, drivers, brokers, accountants).

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    role ENUM('admin', 'owner', 'driver', 'broker', 'accountant') NOT NULL,
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_organization_id (organization_id),
    INDEX idx_role (role),
    INDEX idx_email (email),
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### drivers

Extended information for users with driver role.

```sql
CREATE TABLE drivers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    organization_id BIGINT UNSIGNED NOT NULL,
    license_number VARCHAR(50) NULL,
    vehicle_info JSON NULL,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    UNIQUE KEY unique_user_driver (user_id),
    INDEX idx_organization_id (organization_id),
    INDEX idx_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**vehicle_info JSON structure:**

```json
{
  "type": "tractor",
  "make": "Mahindra",
  "model": "275 DI",
  "year": 2020,
  "registration": "ABC-1234"
}
```

### land_measurements

Stores GPS-measured land polygons.

```sql
CREATE TABLE land_measurements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    coordinates POLYGON NOT NULL,
    area_acres DECIMAL(10, 4) NOT NULL,
    area_hectares DECIMAL(10, 4) NOT NULL,
    measured_by BIGINT UNSIGNED NOT NULL,
    measured_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_organization_id (organization_id),
    INDEX idx_measured_by (measured_by),
    INDEX idx_measured_at (measured_at),
    SPATIAL INDEX idx_coordinates (coordinates),
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (measured_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### customers

Stores customer/client information.

```sql
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    address TEXT NULL,
    balance DECIMAL(12, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_organization_id (organization_id),
    INDEX idx_phone (phone),
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### machines

Stores agricultural machinery information.

```sql
CREATE TABLE machines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('tractor', 'harvester', 'plough', 'seeder', 'sprayer', 'other') NOT NULL,
    model VARCHAR(255) NULL,
    registration_number VARCHAR(50) NULL,
    status ENUM('active', 'maintenance', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_organization_id (organization_id),
    INDEX idx_status (status),
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### jobs

Stores field work jobs.

```sql
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    land_measurement_id BIGINT UNSIGNED NULL,
    driver_id BIGINT UNSIGNED NULL,
    machine_id BIGINT UNSIGNED NULL,
    status ENUM('pending', 'assigned', 'in_progress', 'completed', 'billed', 'paid') DEFAULT 'pending',
    scheduled_at TIMESTAMP NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_organization_id (organization_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_driver_id (driver_id),
    INDEX idx_machine_id (machine_id),
    INDEX idx_status (status),
    INDEX idx_scheduled_at (scheduled_at),
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (land_measurement_id) REFERENCES land_measurements(id),
    FOREIGN KEY (driver_id) REFERENCES drivers(id),
    FOREIGN KEY (machine_id) REFERENCES machines(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### tracking_logs

Stores GPS tracking data for drivers.

```sql
CREATE TABLE tracking_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    driver_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    accuracy DECIMAL(6, 2) NULL,
    speed DECIMAL(6, 2) NULL,
    heading DECIMAL(5, 2) NULL,
    recorded_at TIMESTAMP NOT NULL,

    INDEX idx_driver_id (driver_id),
    INDEX idx_job_id (job_id),
    INDEX idx_recorded_at (recorded_at),
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### invoices

Stores billing invoices.

```sql
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    amount DECIMAL(12, 2) NOT NULL,
    tax DECIMAL(12, 2) DEFAULT 0.00,
    discount DECIMAL(12, 2) DEFAULT 0.00,
    total DECIMAL(12, 2) NOT NULL,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    pdf_path VARCHAR(500) NULL,
    issued_at TIMESTAMP NULL,
    due_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_organization_id (organization_id),
    INDEX idx_job_id (job_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_status (status),
    INDEX idx_issued_at (issued_at),
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### payments

Stores payment transactions.

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    invoice_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'mobile_payment', 'credit') NOT NULL,
    reference_number VARCHAR(100) NULL,
    payment_date TIMESTAMP NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_organization_id (organization_id),
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_payment_date (payment_date),
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### expenses

Stores expense records.

```sql
CREATE TABLE expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NULL,
    driver_id BIGINT UNSIGNED NULL,
    machine_id BIGINT UNSIGNED NULL,
    category ENUM('fuel', 'spare_parts', 'maintenance', 'labor', 'other') NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    description TEXT NULL,
    receipt_path VARCHAR(500) NULL,
    expense_date DATE NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_organization_id (organization_id),
    INDEX idx_job_id (job_id),
    INDEX idx_driver_id (driver_id),
    INDEX idx_machine_id (machine_id),
    INDEX idx_category (category),
    INDEX idx_expense_date (expense_date),
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    FOREIGN KEY (driver_id) REFERENCES drivers(id),
    FOREIGN KEY (machine_id) REFERENCES machines(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### subscriptions

Stores subscription history.

```sql
CREATE TABLE subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    package ENUM('free', 'basic', 'pro') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    starts_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_organization_id (organization_id),
    INDEX idx_package (package),
    INDEX idx_status (status),
    INDEX idx_expires_at (expires_at),
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### audit_logs

Stores audit trail for critical operations.

```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    organization_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_id (user_id),
    INDEX idx_organization_id (organization_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### password_resets

Stores password reset tokens.

```sql
CREATE TABLE password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Relationships Summary

- **organizations** ↔ **users**: One-to-Many
- **organizations** ↔ **subscriptions**: One-to-Many
- **users** ↔ **drivers**: One-to-One
- **drivers** ↔ **tracking_logs**: One-to-Many
- **land_measurements** ↔ **jobs**: One-to-Many
- **jobs** ↔ **invoices**: One-to-One
- **invoices** ↔ **payments**: One-to-Many
- **jobs** ↔ **expenses**: One-to-Many
- **customers** ↔ **jobs**: One-to-Many
- **machines** ↔ **jobs**: One-to-Many

## Indexes Strategy

1. **Foreign Keys**: All foreign keys are indexed for join performance
2. **Search Fields**: Email, phone, invoice_number indexed for lookups
3. **Status Fields**: Status enum fields indexed for filtering
4. **Date Fields**: Timestamp fields indexed for range queries
5. **Spatial Data**: SPATIAL index on coordinates for geographic queries

## Data Retention

- **Soft Deletes**: Most tables support soft deletes for data recovery
- **Audit Logs**: Retained for 2 years
- **Tracking Logs**: Retained for 6 months (configurable)
- **Deleted Records**: Permanently deleted after 90 days

## Storage Estimates

For 1000 active organizations:

- **users**: ~10,000 rows, ~2 MB
- **land_measurements**: ~100,000 rows, ~50 MB
- **jobs**: ~500,000 rows, ~100 MB
- **tracking_logs**: ~50,000,000 rows, ~5 GB (archived monthly)
- **invoices**: ~500,000 rows, ~80 MB
- **Total DB**: ~10 GB/year (excluding tracking logs)
