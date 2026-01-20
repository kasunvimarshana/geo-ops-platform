# Database Schema & ERD

## GPS Field Management Platform Database Design

**Version:** 1.0.0  
**Database:** MySQL 8.0+ / PostgreSQL 15+  
**Spatial Support:** Required (ST\_\* functions)

---

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐
│  organizations  │
├─────────────────┤
│ id              │
│ name            │
│ email           │
│ phone           │
│ address         │
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ 1:N
        │
        ▼
┌─────────────────┐         ┌─────────────────┐
│     users       │         │  subscriptions  │
├─────────────────┤         ├─────────────────┤
│ id              │         │ id              │
│ organization_id │◀────────│ organization_id │
│ name            │         │ package_id      │
│ email           │         │ start_date      │
│ password        │         │ end_date        │
│ phone           │         │ status          │
│ role            │         │ created_at      │
│ created_at      │         └─────────────────┘
│ updated_at      │                 │
└─────────────────┘                 │
        │                           │
        │ 1:N                       │ N:1
        │                           │
        ▼                           ▼
┌─────────────────┐         ┌─────────────────┐
│   land_plots    │         │    packages     │
├─────────────────┤         ├─────────────────┤
│ id              │         │ id              │
│ organization_id │         │ name            │
│ user_id         │         │ price           │
│ name            │         │ features        │
│ area_acres      │         │ limits          │
│ area_hectares   │         │ created_at      │
│ coordinates     │         └─────────────────┘
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ 1:N
        │
        ▼
┌─────────────────┐
│      jobs       │
├─────────────────┤
│ id              │
│ organization_id │
│ land_plot_id    │
│ driver_id       │
│ customer_name   │
│ job_type        │
│ status          │
│ start_date      │
│ end_date        │
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ 1:N
        │
        ├───────────────────┬───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
┌─────────────┐   ┌─────────────┐   ┌─────────────┐
│  invoices   │   │  expenses   │   │gps_tracking │
├─────────────┤   ├─────────────┤   ├─────────────┤
│ id          │   │ id          │   │ id          │
│ job_id      │   │ job_id      │   │ job_id      │
│ amount      │   │ category    │   │ user_id     │
│ pdf_url     │   │ amount      │   │ latitude    │
│ status      │   │ description │   │ longitude   │
│ created_at  │   │ receipt_url │   │ accuracy    │
└─────────────┘   │ created_at  │   │ timestamp   │
        │         └─────────────┘   │ created_at  │
        │                           └─────────────┘
        │ 1:N
        │
        ▼
┌─────────────┐
│  payments   │
├─────────────┤
│ id          │
│ invoice_id  │
│ amount      │
│ method      │
│ reference   │
│ paid_at     │
│ created_at  │
└─────────────┘
```

---

## Table Definitions

### 1. organizations

Stores organization/company information for multi-tenancy.

```sql
CREATE TABLE organizations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    logo_url VARCHAR(500),
    settings JSON,
    status ENUM('active', 'suspended', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

**Columns:**

- `id`: Primary key
- `name`: Organization name
- `email`: Contact email
- `phone`: Contact phone
- `address`: Physical address
- `logo_url`: Organization logo
- `settings`: JSON field for custom settings
- `status`: Organization status
- `created_at`: Creation timestamp
- `updated_at`: Last update timestamp
- `deleted_at`: Soft delete timestamp

---

### 2. users

User accounts with role-based access.

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'owner', 'driver', 'broker', 'accountant') NOT NULL,
    avatar_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_organization (organization_id),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
);
```

**Roles:**

- `admin`: System administrator
- `owner`: Land/machine owner
- `driver`: Equipment operator
- `broker`: Field agent/broker
- `accountant`: Financial management

---

### 3. packages

Subscription package definitions.

```sql
CREATE TABLE packages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    billing_cycle ENUM('monthly', 'yearly', 'lifetime') NOT NULL,
    features JSON NOT NULL,
    limits JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
);
```

**Features JSON Example:**

```json
{
  "measurements": 100,
  "drivers": 5,
  "exports_per_month": 50,
  "pdf_generation": true,
  "advanced_reports": false,
  "api_access": false
}
```

**Limits JSON Example:**

```json
{
  "max_measurements_per_month": 100,
  "max_drivers": 5,
  "max_storage_mb": 1024,
  "max_api_calls_per_day": 1000
}
```

---

### 4. subscriptions

Active subscriptions for organizations.

```sql
CREATE TABLE subscriptions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    package_id BIGINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    auto_renew BOOLEAN DEFAULT FALSE,
    payment_method VARCHAR(50),
    usage_stats JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id),
    INDEX idx_organization (organization_id),
    INDEX idx_package (package_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date)
);
```

**Usage Stats JSON Example:**

```json
{
  "measurements_this_month": 45,
  "exports_this_month": 12,
  "active_drivers": 3,
  "storage_used_mb": 512
}
```

---

### 5. land_plots

Measured land parcels with GPS coordinates.

```sql
CREATE TABLE land_plots (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    area_acres DECIMAL(12, 4) NOT NULL,
    area_hectares DECIMAL(12, 4) NOT NULL,
    area_square_meters DECIMAL(12, 2) NOT NULL,
    perimeter_meters DECIMAL(12, 2),
    coordinates JSON NOT NULL,
    center_latitude DECIMAL(10, 8) NOT NULL,
    center_longitude DECIMAL(11, 8) NOT NULL,
    location GEOMETRY NOT NULL, -- Spatial data
    measurement_method ENUM('walk_around', 'manual_points') NOT NULL,
    accuracy_meters DECIMAL(6, 2),
    measured_at TIMESTAMP NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_organization (organization_id),
    INDEX idx_user (user_id),
    SPATIAL INDEX idx_location (location),
    INDEX idx_measured_at (measured_at)
);
```

**Coordinates JSON Example:**

```json
{
  "type": "Polygon",
  "coordinates": [
    [
      [79.8612, 6.9271],
      [79.8615, 6.9271],
      [79.8615, 6.9268],
      [79.8612, 6.9268],
      [79.8612, 6.9271]
    ]
  ]
}
```

---

### 6. jobs

Field work jobs with assignments.

```sql
CREATE TABLE jobs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    land_plot_id BIGINT UNSIGNED,
    driver_id BIGINT UNSIGNED,
    created_by BIGINT UNSIGNED NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    customer_address TEXT,
    job_type ENUM('plowing', 'harvesting', 'spraying', 'seeding', 'other') NOT NULL,
    status ENUM('pending', 'assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    scheduled_date DATE,
    start_time TIMESTAMP NULL,
    end_time TIMESTAMP NULL,
    duration_hours DECIMAL(6, 2),
    rate_per_unit DECIMAL(10, 2),
    total_amount DECIMAL(10, 2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (land_plot_id) REFERENCES land_plots(id) ON DELETE SET NULL,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_organization (organization_id),
    INDEX idx_land_plot (land_plot_id),
    INDEX idx_driver (driver_id),
    INDEX idx_status (status),
    INDEX idx_scheduled_date (scheduled_date)
);
```

---

### 7. gps_tracking

GPS tracking logs for drivers and jobs.

```sql
CREATE TABLE gps_tracking (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    altitude DECIMAL(8, 2),
    accuracy DECIMAL(6, 2),
    speed DECIMAL(6, 2),
    heading DECIMAL(5, 2),
    location POINT NOT NULL, -- Spatial data
    timestamp TIMESTAMP NOT NULL,
    battery_level INT,
    is_manual BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
    INDEX idx_organization (organization_id),
    INDEX idx_user (user_id),
    INDEX idx_job (job_id),
    INDEX idx_timestamp (timestamp),
    SPATIAL INDEX idx_location (location)
);
```

---

### 8. invoices

Billing invoices for completed jobs.

```sql
CREATE TABLE invoices (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NOT NULL,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255),
    customer_phone VARCHAR(20),
    subtotal DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0.00,
    discount_amount DECIMAL(10, 2) DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    issued_at DATE NOT NULL,
    due_date DATE,
    paid_at TIMESTAMP NULL,
    pdf_url VARCHAR(500),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    INDEX idx_organization (organization_id),
    INDEX idx_job (job_id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_status (status),
    INDEX idx_issued_at (issued_at)
);
```

---

### 9. payments

Payment records for invoices.

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    invoice_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    method ENUM('cash', 'bank_transfer', 'cheque', 'mobile_money', 'card') NOT NULL,
    reference VARCHAR(100),
    transaction_id VARCHAR(100),
    notes TEXT,
    paid_at TIMESTAMP NOT NULL,
    received_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id),
    FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_organization (organization_id),
    INDEX idx_invoice (invoice_id),
    INDEX idx_paid_at (paid_at)
);
```

---

### 10. expenses

Operational expenses tracking.

```sql
CREATE TABLE expenses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NOT NULL,
    category ENUM('fuel', 'maintenance', 'parts', 'labor', 'transport', 'other') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    description TEXT NOT NULL,
    vendor_name VARCHAR(255),
    receipt_url VARCHAR(500),
    expense_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_organization (organization_id),
    INDEX idx_job (job_id),
    INDEX idx_user (user_id),
    INDEX idx_category (category),
    INDEX idx_expense_date (expense_date)
);
```

---

### 11. sync_logs

Offline sync tracking for mobile devices.

```sql
CREATE TABLE sync_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    device_id VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id BIGINT UNSIGNED,
    action ENUM('create', 'update', 'delete') NOT NULL,
    payload JSON,
    status ENUM('pending', 'success', 'failed', 'conflict') DEFAULT 'pending',
    error_message TEXT,
    synced_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_organization (organization_id),
    INDEX idx_user (user_id),
    INDEX idx_device (device_id),
    INDEX idx_status (status)
);
```

---

### 12. audit_logs

Audit trail for sensitive operations.

```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    organization_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id BIGINT UNSIGNED,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_organization (organization_id),
    INDEX idx_user (user_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at)
);
```

---

## Indexes Strategy

### Primary Indexes

- All tables have primary key on `id` with AUTO_INCREMENT
- Unique indexes on email fields, invoice numbers, package slugs

### Foreign Key Indexes

- All foreign key columns are indexed for join performance
- Cascade deletes configured for organization-level cleanup

### Spatial Indexes

- `land_plots.location` - for geographic queries
- `gps_tracking.location` - for position queries

### Composite Indexes (Future Optimization)

```sql
-- For common queries
CREATE INDEX idx_jobs_org_status ON jobs(organization_id, status);
CREATE INDEX idx_invoices_org_status ON invoices(organization_id, status);
CREATE INDEX idx_user_org_role ON users(organization_id, role, is_active);
```

---

## Data Relationships Summary

1. **One-to-Many:**
   - Organization → Users
   - Organization → Land Plots
   - Organization → Jobs
   - Job → Invoices
   - Invoice → Payments
   - Job → Expenses
   - User → GPS Tracking

2. **Many-to-One:**
   - User → Organization
   - Subscription → Package
   - Job → Land Plot
   - Job → Driver (User)

3. **Optional Relationships:**
   - Job → Land Plot (can be null for general jobs)
   - Job → Driver (can be unassigned)
   - Expense → Job (can be general expense)

---

## Seed Data Requirements

### Initial Packages

```sql
INSERT INTO packages (name, slug, price, billing_cycle, features, limits) VALUES
('Free', 'free', 0.00, 'lifetime',
 '{"measurements": 10, "drivers": 1, "exports_per_month": 5}',
 '{"max_measurements_per_month": 10, "max_drivers": 1, "max_storage_mb": 100}'),
('Basic', 'basic', 2500.00, 'monthly',
 '{"measurements": 100, "drivers": 5, "exports_per_month": 50, "pdf_generation": true}',
 '{"max_measurements_per_month": 100, "max_drivers": 5, "max_storage_mb": 1024}'),
('Pro', 'pro', 5000.00, 'monthly',
 '{"measurements": -1, "drivers": -1, "exports_per_month": -1, "pdf_generation": true, "advanced_reports": true, "api_access": true}',
 '{"max_measurements_per_month": -1, "max_drivers": -1, "max_storage_mb": 10240}');
```

### Admin User

```sql
INSERT INTO organizations (name, email, phone, status) VALUES
('System Admin', 'admin@geo-ops.lk', '+94770000000', 'active');

INSERT INTO users (organization_id, name, email, password, role, is_active, email_verified_at) VALUES
(1, 'System Administrator', 'admin@geo-ops.lk', '$2y$10$...', 'admin', TRUE, NOW());
```

---

## Migration Order

1. `organizations`
2. `packages`
3. `users`
4. `subscriptions`
5. `land_plots`
6. `jobs`
7. `gps_tracking`
8. `invoices`
9. `payments`
10. `expenses`
11. `sync_logs`
12. `audit_logs`

---

## Performance Considerations

1. **Partitioning**: Consider partitioning `gps_tracking` and `audit_logs` by date
2. **Archiving**: Move old records (>1 year) to archive tables
3. **Indexing**: Monitor slow queries and add indexes as needed
4. **Spatial Queries**: Use spatial functions for geographic searches
5. **JSON Indexing**: MySQL 8.0+ supports functional indexes on JSON fields
6. **Read Replicas**: Use for reporting queries to reduce master load

---

## Backup Strategy

1. **Daily Automated Backups**: Full database backup at midnight
2. **Point-in-Time Recovery**: Binary log enabled
3. **Retention**: 30 days for daily, 12 months for monthly
4. **Testing**: Monthly restore test on staging environment
5. **Off-site Storage**: Encrypted backups stored in cloud

---

This schema provides a solid foundation for the GPS Field Management Platform with proper normalization, relationships, and scalability considerations.
