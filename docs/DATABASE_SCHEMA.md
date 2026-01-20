# Database Schema & ERD

## GeoOps Platform - Database Design

### Database Engine

- **Recommended**: PostgreSQL 13+ (for PostGIS spatial support)
- **Alternative**: MySQL 8.0+ (with spatial extensions)

---

## Entity Relationship Diagram (Text Format)

```
┌─────────────────┐         ┌──────────────────┐
│  organizations  │◄────────│     users        │
│                 │ 1     * │                  │
│  - id           │         │  - id            │
│  - name         │         │  - organization_id (FK)
│  - package_tier │         │  - role          │
│  - expires_at   │         │  - email         │
└─────────────────┘         │  - password      │
        │                    └──────────────────┘
        │                             │
        │ 1                           │ 1
        │                             │
        │ *                           │ *
┌─────────────────┐         ┌──────────────────┐
│     lands       │         │      jobs        │
│                 │ 1     * │                  │
│  - id           │◄────────│  - id            │
│  - organization_id (FK)   │  - organization_id (FK)
│  - owner_user_id (FK)     │  - land_id (FK)  │
│  - name         │         │  - assigned_driver_id (FK)
│  - coordinates  │         │  - status        │
│  - area_acres   │         │  - scheduled_at  │
│  - area_hectares│         └──────────────────┘
└─────────────────┘                  │
        │ 1                          │ 1
        │                            │
        │ *                          │ *
┌─────────────────┐         ┌──────────────────┐
│  measurements   │         │    expenses      │
│                 │         │                  │
│  - id           │         │  - id            │
│  - organization_id (FK)   │  - organization_id (FK)
│  - land_id (FK) │         │  - job_id (FK)   │
│  - user_id (FK) │         │  - driver_id (FK)│
│  - coordinates  │         │  - category      │
│  - area_acres   │         │  - amount        │
│  - measured_at  │         │  - notes         │
│  - sync_status  │         └──────────────────┘
└─────────────────┘
                            ┌──────────────────┐
        ┌──────────────────►│    invoices      │
        │                   │                  │
        │                   │  - id            │
┌───────┴─────────┐         │  - organization_id (FK)
│      jobs       │         │  - job_id (FK)   │
└─────────────────┘         │  - customer_name │
                            │  - amount        │
                            │  - pdf_path      │
                            │  - status        │
                            └──────────────────┘
                                     │ 1
                                     │
                                     │ *
                            ┌──────────────────┐
                            │     payments     │
                            │                  │
                            │  - id            │
                            │  - organization_id (FK)
                            │  - invoice_id (FK)│
                            │  - amount        │
                            │  - payment_method│
                            │  - paid_at       │
                            └──────────────────┘

┌─────────────────┐
│ tracking_logs   │
│                 │
│  - id           │
│  - organization_id (FK)
│  - driver_id (FK)
│  - job_id (FK)  │
│  - latitude     │
│  - longitude    │
│  - recorded_at  │
└─────────────────┘

┌─────────────────┐
│ subscription_   │
│    packages     │
│  - id           │
│  - name         │
│  - max_measurements
│  - max_drivers  │
│  - max_jobs     │
│  - price_monthly│
│  - features     │
└─────────────────┘
```

---

## Table Definitions

### 1. organizations

Organization/tenant table for multi-tenancy.

```sql
CREATE TABLE organizations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_name VARCHAR(255),
    contact_email VARCHAR(255),
    contact_phone VARCHAR(50),
    address TEXT,
    package_tier ENUM('free', 'basic', 'pro') DEFAULT 'free',
    package_expires_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,

    INDEX idx_package_tier (package_tier),
    INDEX idx_is_active (is_active)
);
```

### 2. users

User accounts with role-based access.

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    role ENUM('admin', 'owner', 'driver', 'broker', 'accountant') NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    email_verified_at TIMESTAMP NULL,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_organization_role (organization_id, role),
    INDEX idx_email (email),
    INDEX idx_is_active (is_active)
);
```

### 3. lands

Agricultural land parcels with GPS boundaries.

```sql
CREATE TABLE lands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    owner_user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,

    -- Spatial data (array of lat/lng points forming polygon)
    coordinates JSON NOT NULL, -- [{lat, lng}, ...]
    -- For PostgreSQL with PostGIS: use GEOGRAPHY(POLYGON, 4326)
    -- polygon GEOGRAPHY(POLYGON, 4326),

    area_acres DECIMAL(12, 4),
    area_hectares DECIMAL(12, 4),
    area_square_meters DECIMAL(12, 2),

    -- Center point for map display
    center_latitude DECIMAL(10, 7),
    center_longitude DECIMAL(10, 7),

    location_address TEXT,
    location_district VARCHAR(100),
    location_province VARCHAR(100),

    status ENUM('active', 'inactive') DEFAULT 'active',
    metadata JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_organization (organization_id),
    INDEX idx_owner (owner_user_id),
    INDEX idx_status (status),
    INDEX idx_location (location_district, location_province)
);
```

### 4. measurements

GPS measurement records (raw tracking data for each measurement session).

```sql
CREATE TABLE measurements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    land_id BIGINT UNSIGNED NULL, -- null if not yet associated
    measured_by_user_id BIGINT UNSIGNED NOT NULL,

    measurement_type ENUM('walk_around', 'point_based') NOT NULL,
    coordinates JSON NOT NULL, -- [{lat, lng, timestamp, accuracy}, ...]

    area_acres DECIMAL(12, 4),
    area_hectares DECIMAL(12, 4),
    area_square_meters DECIMAL(12, 2),

    perimeter_meters DECIMAL(12, 2),

    center_latitude DECIMAL(10, 7),
    center_longitude DECIMAL(10, 7),

    measured_at TIMESTAMP NOT NULL,
    duration_seconds INT UNSIGNED,

    -- Offline sync tracking
    sync_status ENUM('pending', 'synced', 'failed') DEFAULT 'pending',
    synced_at TIMESTAMP NULL,
    device_id VARCHAR(255),

    notes TEXT,
    metadata JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (land_id) REFERENCES lands(id) ON DELETE SET NULL,
    FOREIGN KEY (measured_by_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_organization (organization_id),
    INDEX idx_land (land_id),
    INDEX idx_sync_status (sync_status),
    INDEX idx_measured_at (measured_at)
);
```

### 5. jobs

Field work jobs (plowing, harvesting, etc.).

```sql
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    land_id BIGINT UNSIGNED NOT NULL,
    assigned_driver_id BIGINT UNSIGNED NULL,
    created_by_user_id BIGINT UNSIGNED NOT NULL,

    job_number VARCHAR(50) UNIQUE NOT NULL, -- e.g., JOB-2024-001
    job_type VARCHAR(100) NOT NULL, -- e.g., 'plowing', 'harvesting'

    status ENUM('pending', 'assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',

    scheduled_at TIMESTAMP NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,

    customer_name VARCHAR(255),
    customer_phone VARCHAR(50),
    customer_address TEXT,

    rate_per_acre DECIMAL(10, 2),
    estimated_area_acres DECIMAL(12, 4),
    estimated_cost DECIMAL(10, 2),

    actual_area_acres DECIMAL(12, 4),
    actual_cost DECIMAL(10, 2),

    notes TEXT,
    metadata JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (land_id) REFERENCES lands(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_driver_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_organization (organization_id),
    INDEX idx_land (land_id),
    INDEX idx_driver (assigned_driver_id),
    INDEX idx_status (status),
    INDEX idx_job_number (job_number),
    INDEX idx_scheduled (scheduled_at)
);
```

### 6. tracking_logs

GPS tracking history for drivers during jobs.

```sql
CREATE TABLE tracking_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    driver_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NULL,

    latitude DECIMAL(10, 7) NOT NULL,
    longitude DECIMAL(10, 7) NOT NULL,
    altitude DECIMAL(8, 2),
    accuracy DECIMAL(8, 2), -- in meters
    speed DECIMAL(8, 2), -- in km/h
    heading DECIMAL(5, 2), -- in degrees

    recorded_at TIMESTAMP NOT NULL,

    -- For PostgreSQL with PostGIS:
    -- location GEOGRAPHY(POINT, 4326),

    metadata JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
    INDEX idx_organization (organization_id),
    INDEX idx_driver (driver_id),
    INDEX idx_job (job_id),
    INDEX idx_recorded_at (recorded_at),
    INDEX idx_driver_time (driver_id, recorded_at)
);
```

### 7. invoices

Generated invoices for completed jobs.

```sql
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NOT NULL,

    invoice_number VARCHAR(50) UNIQUE NOT NULL, -- e.g., INV-2024-001

    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50),
    customer_address TEXT,

    line_items JSON NOT NULL, -- [{description, quantity, rate, amount}, ...]

    subtotal DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,

    status ENUM('draft', 'issued', 'paid', 'cancelled') DEFAULT 'draft',

    issued_at TIMESTAMP NULL,
    due_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,

    pdf_path VARCHAR(500),
    pdf_generated_at TIMESTAMP NULL,

    notes TEXT,
    metadata JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    INDEX idx_organization (organization_id),
    INDEX idx_job (job_id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_status (status),
    INDEX idx_issued_at (issued_at)
);
```

### 8. payments

Payment records for invoices.

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    invoice_id BIGINT UNSIGNED NOT NULL,

    payment_number VARCHAR(50) UNIQUE NOT NULL,

    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'cheque', 'digital', 'other') NOT NULL,

    reference_number VARCHAR(255),

    paid_at TIMESTAMP NOT NULL,

    notes TEXT,
    metadata JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_organization (organization_id),
    INDEX idx_invoice (invoice_id),
    INDEX idx_paid_at (paid_at)
);
```

### 9. expenses

Operational expenses (fuel, maintenance, etc.).

```sql
CREATE TABLE expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NULL,
    driver_id BIGINT UNSIGNED NULL,

    expense_number VARCHAR(50) UNIQUE NOT NULL,

    category ENUM('fuel', 'maintenance', 'parts', 'salary', 'other') NOT NULL,
    description TEXT NOT NULL,

    amount DECIMAL(10, 2) NOT NULL,

    expense_date DATE NOT NULL,

    receipt_path VARCHAR(500),

    notes TEXT,
    metadata JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_organization (organization_id),
    INDEX idx_job (job_id),
    INDEX idx_driver (driver_id),
    INDEX idx_category (category),
    INDEX idx_expense_date (expense_date)
);
```

### 10. subscription_packages

Subscription tier definitions.

```sql
CREATE TABLE subscription_packages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL, -- 'free', 'basic', 'pro'
    display_name VARCHAR(255) NOT NULL,
    description TEXT,

    max_measurements INT UNSIGNED NOT NULL,
    max_drivers INT UNSIGNED NOT NULL,
    max_jobs INT UNSIGNED NOT NULL,
    max_lands INT UNSIGNED NOT NULL,
    max_storage_mb INT UNSIGNED NOT NULL,

    price_monthly DECIMAL(10, 2) NOT NULL,
    price_yearly DECIMAL(10, 2),

    features JSON, -- ["feature1", "feature2", ...]

    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_name (name),
    INDEX idx_is_active (is_active)
);
```

### 11. audit_logs

Audit trail for critical operations.

```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,

    action VARCHAR(100) NOT NULL, -- 'created', 'updated', 'deleted'
    entity_type VARCHAR(100) NOT NULL, -- 'job', 'invoice', 'payment'
    entity_id BIGINT UNSIGNED,

    old_values JSON,
    new_values JSON,

    ip_address VARCHAR(45),
    user_agent TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_organization (organization_id),
    INDEX idx_user (user_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at)
);
```

---

## Indexes Summary

**Critical indexes for performance:**

1. **organizations**: package_tier, is_active
2. **users**: organization_id + role (composite), email, is_active
3. **lands**: organization_id, owner_user_id, status
4. **measurements**: organization_id, land_id, sync_status, measured_at
5. **jobs**: organization_id, status, scheduled_at, job_number
6. **tracking_logs**: driver_id + recorded_at (composite for historical queries)
7. **invoices**: organization_id, job_id, status, invoice_number
8. **payments**: organization_id, invoice_id, paid_at
9. **expenses**: organization_id, category, expense_date

---

## Spatial Data Handling

### MySQL 8.0+ Approach

Store coordinates as JSON and use ST_GeomFromText for spatial calculations:

```sql
-- Calculate area from polygon coordinates
SET @polygon = ST_GeomFromText('POLYGON((lng1 lat1, lng2 lat2, ...))');
SELECT ST_Area(@polygon) as area_square_meters;
```

### PostgreSQL + PostGIS Approach (Recommended)

Use native GEOGRAPHY type:

```sql
-- Add PostGIS extension
CREATE EXTENSION postgis;

-- Store polygon directly
ALTER TABLE lands ADD COLUMN polygon GEOGRAPHY(POLYGON, 4326);

-- Calculate area
SELECT ST_Area(polygon) as area_square_meters FROM lands WHERE id = 1;
```

---

## Data Retention & Archival

- **Soft Deletes**: All main tables support soft deletes
- **Audit Fields**: created_at, updated_at, created_by, updated_by
- **Archive Strategy**: Move records older than 2 years to archive tables
- **Backup**: Daily automated backups with 30-day retention

---

## Sample Data Relationships

```
Organization: "Green Farm Services"
  └── Users:
      ├── Owner: "Kasun Perera" (owner role)
      ├── Driver: "Nimal Silva" (driver role)
      └── Accountant: "Chamari Fernando" (accountant role)
  └── Lands:
      └── Land: "Field A - Gampaha"
          ├── Area: 2.5 acres
          └── Coordinates: [(lat, lng), ...]
  └── Jobs:
      └── Job: "Plowing - Field A"
          ├── Status: completed
          ├── Driver: Nimal Silva
          └── Cost: LKR 15,000
  └── Invoices:
      └── Invoice: INV-2024-001
          ├── Total: LKR 15,000
          └── Status: paid
  └── Payments:
      └── Payment: PAY-2024-001
          ├── Amount: LKR 15,000
          └── Method: cash
```

---

## Migration Strategy

1. Create migrations in order (respecting foreign keys)
2. Run migrations on staging first
3. Test data integrity
4. Seed with sample data
5. Deploy to production
6. Monitor for issues

---

## Database Performance Optimization

1. **Connection Pooling**: Use persistent connections
2. **Query Optimization**: Use EXPLAIN to analyze slow queries
3. **Partitioning**: Consider partitioning tracking_logs by date
4. **Read Replicas**: Use read replicas for reports
5. **Caching**: Cache frequently accessed data (packages, organizations)
