# Database Schema

> Complete database schema and entity relationship diagram for the GeoOps Platform

## Table of Contents
- [Overview](#overview)
- [Core Entities](#core-entities)
- [Relationships](#relationships)
- [Spatial Data](#spatial-data-types)
- [Indexes](#indexes-strategy)
- [Data Integrity](#data-integrity)
- [Offline Support](#offline-support)

## Overview

The GeoOps Platform uses a relational database with spatial extensions for GPS coordinate storage. The schema supports multi-tenancy, role-based access control, and offline synchronization.

### Database Options

- **MySQL 8.0+** with Spatial Extensions
- **PostgreSQL 14+** with PostGIS

## Core Entities

### organizations

Primary entity for multi-tenancy

```sql
CREATE TABLE organizations (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(100) UNIQUE NOT NULL,
  subscription_package ENUM('free', 'basic', 'pro') DEFAULT 'free',
  subscription_expires_at TIMESTAMP NULL,
  status ENUM('active', 'suspended', 'cancelled') DEFAULT 'active',
  settings JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL
);
```

### users

System users with role-based access

```sql
CREATE TABLE users (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  role_id BIGINT NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  phone VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  language ENUM('en', 'si') DEFAULT 'si',
  is_active BOOLEAN DEFAULT TRUE,
  last_login_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
  FOREIGN KEY (role_id) REFERENCES roles(id),
  INDEX idx_organization (organization_id),
  INDEX idx_email (email)
);
```

### roles

Role definitions for RBAC

```sql
CREATE TABLE roles (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) UNIQUE NOT NULL,
  slug VARCHAR(50) UNIQUE NOT NULL,
  description TEXT,
  permissions JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Default roles:** admin, owner, driver, broker, accountant

### lands

Measured land parcels

```sql
CREATE TABLE lands (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  polygon POLYGON NOT NULL,
  area_acres DECIMAL(10, 4) NOT NULL,
  area_hectares DECIMAL(10, 4) NOT NULL,
  measurement_type ENUM('walk-around', 'point-based') NOT NULL,
  location_name VARCHAR(255),
  customer_name VARCHAR(255),
  customer_phone VARCHAR(20),
  measured_by BIGINT NOT NULL,
  measured_at TIMESTAMP NOT NULL,
  status ENUM('draft', 'confirmed', 'archived') DEFAULT 'confirmed',
  sync_status ENUM('synced', 'pending', 'conflict') DEFAULT 'synced',
  offline_id UUID NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
  FOREIGN KEY (measured_by) REFERENCES users(id),
  INDEX idx_organization (organization_id),
  INDEX idx_measured_by (measured_by),
  SPATIAL INDEX idx_polygon (polygon),
  INDEX idx_sync_status (sync_status),
  INDEX idx_offline_id (offline_id)
);
```

### measurement_points

Individual GPS points for land measurements

```sql
CREATE TABLE measurement_points (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  land_id BIGINT NOT NULL,
  latitude DECIMAL(10, 8) NOT NULL,
  longitude DECIMAL(11, 8) NOT NULL,
  altitude DECIMAL(8, 2) NULL,
  accuracy DECIMAL(5, 2) NOT NULL,
  sequence INT NOT NULL,
  recorded_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (land_id) REFERENCES lands(id) ON DELETE CASCADE,
  INDEX idx_land_sequence (land_id, sequence)
);
```

### machines

Agricultural machinery/equipment

```sql
CREATE TABLE machines (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  name VARCHAR(255) NOT NULL,
  machine_type VARCHAR(100) NOT NULL,
  registration_number VARCHAR(50),
  description TEXT,
  rate_per_acre DECIMAL(10, 2),
  rate_per_hectare DECIMAL(10, 2),
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
  INDEX idx_organization (organization_id)
);
```

### jobs

Field work jobs

```sql
CREATE TABLE jobs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  land_id BIGINT NULL,
  machine_id BIGINT NOT NULL,
  driver_id BIGINT NOT NULL,
  assigned_by BIGINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  job_date DATE NOT NULL,
  status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
  start_time TIMESTAMP NULL,
  end_time TIMESTAMP NULL,
  duration_minutes INT NULL,
  customer_name VARCHAR(255),
  customer_phone VARCHAR(20),
  location POINT NULL,
  location_name VARCHAR(255),
  notes TEXT,
  sync_status ENUM('synced', 'pending', 'conflict') DEFAULT 'synced',
  offline_id UUID NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
  FOREIGN KEY (land_id) REFERENCES lands(id) ON DELETE SET NULL,
  FOREIGN KEY (machine_id) REFERENCES machines(id),
  FOREIGN KEY (driver_id) REFERENCES users(id),
  FOREIGN KEY (assigned_by) REFERENCES users(id),
  INDEX idx_organization (organization_id),
  INDEX idx_driver (driver_id),
  INDEX idx_status (status),
  INDEX idx_job_date (job_date),
  SPATIAL INDEX idx_location (location),
  INDEX idx_sync_status (sync_status)
);
```

### job_tracking

GPS tracking data for jobs

```sql
CREATE TABLE job_tracking (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  job_id BIGINT NOT NULL,
  latitude DECIMAL(10, 8) NOT NULL,
  longitude DECIMAL(11, 8) NOT NULL,
  accuracy DECIMAL(5, 2) NOT NULL,
  speed DECIMAL(5, 2) NULL,
  heading DECIMAL(5, 2) NULL,
  recorded_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  INDEX idx_job_recorded (job_id, recorded_at)
);
```

### invoices

Billing invoices

```sql
CREATE TABLE invoices (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  job_id BIGINT NULL,
  land_id BIGINT NULL,
  invoice_number VARCHAR(50) UNIQUE NOT NULL,
  customer_name VARCHAR(255) NOT NULL,
  customer_phone VARCHAR(20),
  invoice_date DATE NOT NULL,
  due_date DATE NOT NULL,
  area_acres DECIMAL(10, 4),
  area_hectares DECIMAL(10, 4),
  rate_per_unit DECIMAL(10, 2) NOT NULL,
  subtotal DECIMAL(10, 2) NOT NULL,
  tax_rate DECIMAL(5, 2) DEFAULT 0,
  tax_amount DECIMAL(10, 2) DEFAULT 0,
  total_amount DECIMAL(10, 2) NOT NULL,
  paid_amount DECIMAL(10, 2) DEFAULT 0,
  balance DECIMAL(10, 2) NOT NULL,
  status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
  notes TEXT,
  pdf_path VARCHAR(500) NULL,
  printed_at TIMESTAMP NULL,
  sync_status ENUM('synced', 'pending', 'conflict') DEFAULT 'synced',
  offline_id UUID NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
  FOREIGN KEY (land_id) REFERENCES lands(id) ON DELETE SET NULL,
  INDEX idx_organization (organization_id),
  INDEX idx_invoice_number (invoice_number),
  INDEX idx_status (status),
  INDEX idx_invoice_date (invoice_date),
  INDEX idx_sync_status (sync_status)
);
```

### expenses

Operating expenses

```sql
CREATE TABLE expenses (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  machine_id BIGINT NULL,
  driver_id BIGINT NULL,
  job_id BIGINT NULL,
  expense_type ENUM('fuel', 'maintenance', 'parts', 'labor', 'other') NOT NULL,
  category VARCHAR(100) NOT NULL,
  description TEXT,
  amount DECIMAL(10, 2) NOT NULL,
  expense_date DATE NOT NULL,
  receipt_path VARCHAR(500) NULL,
  recorded_by BIGINT NOT NULL,
  sync_status ENUM('synced', 'pending', 'conflict') DEFAULT 'synced',
  offline_id UUID NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
  FOREIGN KEY (machine_id) REFERENCES machines(id) ON DELETE SET NULL,
  FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
  FOREIGN KEY (recorded_by) REFERENCES users(id),
  INDEX idx_organization (organization_id),
  INDEX idx_machine (machine_id),
  INDEX idx_driver (driver_id),
  INDEX idx_expense_date (expense_date),
  INDEX idx_expense_type (expense_type),
  INDEX idx_sync_status (sync_status)
);
```

### payments

Payment records

```sql
CREATE TABLE payments (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  invoice_id BIGINT NOT NULL,
  payment_method ENUM('cash', 'bank', 'digital', 'check') NOT NULL,
  amount DECIMAL(10, 2) NOT NULL,
  payment_date DATE NOT NULL,
  reference_number VARCHAR(100) NULL,
  notes TEXT,
  received_by BIGINT NOT NULL,
  sync_status ENUM('synced', 'pending', 'conflict') DEFAULT 'synced',
  offline_id UUID NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
  FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
  FOREIGN KEY (received_by) REFERENCES users(id),
  INDEX idx_organization (organization_id),
  INDEX idx_invoice (invoice_id),
  INDEX idx_payment_date (payment_date),
  INDEX idx_sync_status (sync_status)
);
```

### subscription_limits

Usage tracking for subscription packages

```sql
CREATE TABLE subscription_limits (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT UNIQUE NOT NULL,
  measurements_count INT DEFAULT 0,
  measurements_limit INT NOT NULL,
  drivers_count INT DEFAULT 0,
  drivers_limit INT NOT NULL,
  exports_count INT DEFAULT 0,
  exports_limit INT NOT NULL,
  reset_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
);
```

### sync_logs

Synchronization audit trail

```sql
CREATE TABLE sync_logs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  entity_type VARCHAR(50) NOT NULL,
  entity_id BIGINT NOT NULL,
  offline_id UUID NULL,
  action ENUM('create', 'update', 'delete') NOT NULL,
  sync_status ENUM('success', 'conflict', 'failed') NOT NULL,
  conflict_data JSON NULL,
  error_message TEXT NULL,
  synced_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_organization_entity (organization_id, entity_type, entity_id),
  INDEX idx_offline_id (offline_id)
);
```

## Relationships

### One-to-Many

- organizations → users
- organizations → lands
- organizations → machines
- organizations → jobs
- organizations → invoices
- organizations → expenses
- organizations → payments
- roles → users
- lands → measurement_points
- jobs → job_tracking
- invoices → payments
- users → lands (measured_by)
- users → jobs (driver_id)
- machines → jobs

### One-to-One

- organizations → subscription_limits

### Many-to-One with Optional

- lands → jobs (one land can have multiple jobs)
- jobs → invoices (one job can have one invoice)

## Spatial Data Types

### MySQL

```sql
ALTER TABLE lands ADD COLUMN polygon POLYGON NOT NULL;
CREATE SPATIAL INDEX idx_polygon ON lands(polygon);

ALTER TABLE jobs ADD COLUMN location POINT;
CREATE SPATIAL INDEX idx_location ON jobs(location);
```

### PostgreSQL

```sql
CREATE EXTENSION IF NOT EXISTS postgis;

ALTER TABLE lands ADD COLUMN polygon GEOMETRY(Polygon, 4326);
CREATE INDEX idx_polygon ON lands USING GIST(polygon);

ALTER TABLE jobs ADD COLUMN location GEOMETRY(Point, 4326);
CREATE INDEX idx_location ON jobs USING GIST(location);
```

## Indexes Strategy

### Performance Indexes

- Foreign key columns for join optimization
- Date columns for range queries
- Status/enum columns for filtering
- Spatial indexes for GPS queries
- Composite indexes for common query patterns

### Sync Indexes

- `offline_id` for matching offline records
- `sync_status` for pending sync queries
- `organization_id` for multi-tenant isolation

### Example Composite Indexes

```sql
CREATE INDEX idx_jobs_org_driver_status ON jobs(organization_id, driver_id, status);
CREATE INDEX idx_invoices_org_status_date ON invoices(organization_id, status, invoice_date);
CREATE INDEX idx_lands_org_sync ON lands(organization_id, sync_status);
```

## Data Integrity

### Constraints

- All foreign keys with `CASCADE ON DELETE` for child records
- Unique constraints on business keys (invoice_number, email, slug)
- Check constraints on amounts (>= 0)
- NOT NULL on critical fields

### Soft Deletes

- All main entities support soft deletes via `deleted_at`
- Maintains data integrity and audit trail
- Cascade soft deletes to related records

### Audit Fields

- `created_at` and `updated_at` on all tables
- `created_by` and `updated_by` where applicable
- `deleted_at` for soft deletes

## Offline Support

### Offline ID Strategy

- UUID v4 for offline records
- Maps to server-side BIGINT primary key after sync
- Prevents conflicts during concurrent offline operations
- Unique constraint ensures no duplicates

### Sync Status

- **synced**: Successfully synchronized with server
- **pending**: Waiting to be synced
- **conflict**: Needs manual resolution

### Conflict Resolution

- Last-write-wins based on `updated_at` timestamp
- Conflict data stored in `sync_logs` for review
- User notification for critical conflicts

---

**Next**: See [API Reference](api-reference.md) for endpoint documentation.
