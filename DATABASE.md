# Database Schema & ERD

## Entity Relationship Diagram

```
┌─────────────────┐         ┌─────────────────┐
│  organizations  │────┬───│      users      │
└─────────────────┘    │    └─────────────────┘
                       │            │
                       │            │
                       │    ┌───────┴──────────┐
                       │    │                  │
                       │    ▼                  ▼
                       │ ┌─────────────┐  ┌────────────┐
                       │ │    roles    │  │permissions │
                       │ └─────────────┘  └────────────┘
                       │
                       ├────────────────────┐
                       │                    │
                       ▼                    ▼
            ┌──────────────────┐   ┌─────────────────┐
            │  subscriptions   │   │   machines      │
            └──────────────────┘   └─────────────────┘
                                            │
                       ┌────────────────────┼──────────┐
                       │                    │          │
                       ▼                    ▼          ▼
            ┌─────────────────┐   ┌─────────────┐ ┌──────────┐
            │  measurements   │◄──│    jobs     │ │ expenses │
            └─────────────────┘   └─────────────┘ └──────────┘
                       │                    │
                       │                    │
                       ▼                    ▼
            ┌─────────────────┐   ┌─────────────────┐
            │ measurement_    │   │ job_assignments │
            │   polygons      │   └─────────────────┘
            └─────────────────┘            │
                                           │
                       ┌───────────────────┤
                       │                   │
                       ▼                   ▼
            ┌──────────────────┐  ┌────────────────┐
            │  gps_tracking    │  │   invoices     │
            └──────────────────┘  └────────────────┘
                                           │
                                           │
                                           ▼
                                  ┌────────────────┐
                                  │   payments     │
                                  └────────────────┘
                                           │
                                           ▼
                                  ┌────────────────┐
                                  │     ledger     │
                                  └────────────────┘
```

## Table Definitions

### 1. organizations
Stores organization/company information for multi-tenancy.

```sql
CREATE TABLE organizations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    owner_id BIGINT UNSIGNED NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Sri Lanka',
    timezone VARCHAR(50) DEFAULT 'Asia/Colombo',
    currency VARCHAR(10) DEFAULT 'LKR',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_owner (owner_id),
    INDEX idx_status (status),
    INDEX idx_slug (slug)
);
```

### 2. users
User accounts with role-based access.

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255),
    language ENUM('en', 'si') DEFAULT 'en',
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    email_verified_at TIMESTAMP NULL,
    phone_verified_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    INDEX idx_org (organization_id),
    INDEX idx_role (role_id),
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_status (status)
);
```

### 3. roles
User roles for RBAC.

```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    is_system BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default roles
INSERT INTO roles (name, display_name, is_system) VALUES
('admin', 'Administrator', TRUE),
('owner', 'Owner/Farmer', TRUE),
('driver', 'Driver/Operator', TRUE),
('broker', 'Broker/Agent', TRUE),
('accountant', 'Accountant', TRUE);
```

### 4. permissions
Granular permissions for RBAC.

```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    display_name VARCHAR(150) NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5. role_permissions
Many-to-many relationship between roles and permissions.

```sql
CREATE TABLE role_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
);
```

### 6. subscriptions
Organization subscription packages.

```sql
CREATE TABLE subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    package_type ENUM('free', 'basic', 'pro', 'enterprise') NOT NULL,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    started_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NULL,
    features JSON, -- Limits: measurements, drivers, exports, etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_org (organization_id),
    INDEX idx_status (status),
    INDEX idx_expires (expires_at)
);
```

### 7. machines
Machinery/equipment used for field work.

```sql
CREATE TABLE machines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100), -- tractor, harvester, plough, etc.
    model VARCHAR(100),
    registration_number VARCHAR(50),
    purchase_date DATE,
    purchase_price DECIMAL(15,2),
    status ENUM('active', 'maintenance', 'retired') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_org (organization_id),
    INDEX idx_status (status)
);
```

### 8. measurements
Land measurements with GPS data.

```sql
CREATE TABLE measurements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    measured_by BIGINT UNSIGNED NOT NULL,
    customer_name VARCHAR(255),
    customer_phone VARCHAR(20),
    location_name VARCHAR(255),
    location_address TEXT,
    area_acres DECIMAL(10,4),
    area_hectares DECIMAL(10,4),
    perimeter_meters DECIMAL(10,2),
    center_latitude DECIMAL(10,8),
    center_longitude DECIMAL(11,8),
    measurement_method ENUM('walk_around', 'point_based') NOT NULL,
    measurement_date TIMESTAMP NOT NULL,
    notes TEXT,
    status ENUM('draft', 'completed', 'verified') DEFAULT 'draft',
    synced_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (measured_by) REFERENCES users(id),
    INDEX idx_org (organization_id),
    INDEX idx_measured_by (measured_by),
    INDEX idx_customer_phone (customer_phone),
    INDEX idx_date (measurement_date),
    INDEX idx_status (status)
);
```

### 9. measurement_polygons
GPS polygon coordinates for each measurement.

```sql
CREATE TABLE measurement_polygons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    measurement_id BIGINT UNSIGNED NOT NULL,
    point_order INT NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    altitude DECIMAL(10,2),
    accuracy DECIMAL(10,2),
    timestamp TIMESTAMP NOT NULL,
    
    FOREIGN KEY (measurement_id) REFERENCES measurements(id) ON DELETE CASCADE,
    INDEX idx_measurement (measurement_id),
    INDEX idx_order (measurement_id, point_order)
);

-- For PostgreSQL with PostGIS
-- ALTER TABLE measurements ADD COLUMN polygon GEOMETRY(POLYGON, 4326);
-- CREATE INDEX idx_polygon ON measurements USING GIST(polygon);
```

### 10. jobs
Field work jobs/assignments.

```sql
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    measurement_id BIGINT UNSIGNED,
    job_number VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    location_name VARCHAR(255),
    location_address TEXT,
    location_latitude DECIMAL(10,8),
    location_longitude DECIMAL(11,8),
    job_type VARCHAR(100), -- ploughing, harvesting, etc.
    scheduled_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    status ENUM('pending', 'assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    notes TEXT,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (measurement_id) REFERENCES measurements(id) ON DELETE SET NULL,
    INDEX idx_org (organization_id),
    INDEX idx_measurement (measurement_id),
    INDEX idx_customer_phone (customer_phone),
    INDEX idx_status (status),
    INDEX idx_date (scheduled_date)
);
```

### 11. job_assignments
Assignment of drivers and machines to jobs.

```sql
CREATE TABLE job_assignments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id BIGINT UNSIGNED NOT NULL,
    driver_id BIGINT UNSIGNED NOT NULL,
    machine_id BIGINT UNSIGNED,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    status ENUM('assigned', 'accepted', 'started', 'completed', 'declined') DEFAULT 'assigned',
    notes TEXT,
    
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id),
    FOREIGN KEY (machine_id) REFERENCES machines(id) ON DELETE SET NULL,
    INDEX idx_job (job_id),
    INDEX idx_driver (driver_id),
    INDEX idx_machine (machine_id),
    INDEX idx_status (status)
);
```

### 12. gps_tracking
Real-time and historical GPS tracking.

```sql
CREATE TABLE gps_tracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    altitude DECIMAL(10,2),
    accuracy DECIMAL(10,2),
    speed DECIMAL(10,2), -- km/h
    heading DECIMAL(5,2), -- degrees
    timestamp TIMESTAMP NOT NULL,
    battery_level INT,
    is_moving BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
    INDEX idx_user_time (user_id, timestamp),
    INDEX idx_job (job_id),
    INDEX idx_timestamp (timestamp)
);

-- Partitioning by month for large-scale tracking data
-- ALTER TABLE gps_tracking PARTITION BY RANGE (YEAR(timestamp) * 100 + MONTH(timestamp));
```

### 13. invoices
Generated invoices for jobs.

```sql
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    job_id BIGINT UNSIGNED NOT NULL,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    customer_address TEXT,
    invoice_date DATE NOT NULL,
    due_date DATE,
    subtotal DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    balance DECIMAL(15,2) NOT NULL,
    status ENUM('draft', 'sent', 'paid', 'partially_paid', 'overdue', 'cancelled') DEFAULT 'draft',
    payment_terms TEXT,
    notes TEXT,
    pdf_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    INDEX idx_org (organization_id),
    INDEX idx_job (job_id),
    INDEX idx_customer_phone (customer_phone),
    INDEX idx_status (status),
    INDEX idx_date (invoice_date)
);
```

### 14. invoice_items
Line items for invoices.

```sql
CREATE TABLE invoice_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit VARCHAR(50), -- acres, hours, etc.
    unit_price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    total DECIMAL(15,2) NOT NULL,
    
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice (invoice_id)
);
```

### 15. payments
Payment records.

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    invoice_id BIGINT UNSIGNED,
    payment_number VARCHAR(50) UNIQUE NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'cheque', 'card', 'mobile_money') NOT NULL,
    reference_number VARCHAR(100),
    notes TEXT,
    received_by BIGINT UNSIGNED,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (received_by) REFERENCES users(id),
    INDEX idx_org (organization_id),
    INDEX idx_invoice (invoice_id),
    INDEX idx_date (payment_date),
    INDEX idx_status (status)
);
```

### 16. expenses
Expense tracking for machines, fuel, maintenance, etc.

```sql
CREATE TABLE expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    machine_id BIGINT UNSIGNED,
    job_id BIGINT UNSIGNED,
    category VARCHAR(100) NOT NULL, -- fuel, maintenance, spare_parts, salary, etc.
    description TEXT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    expense_date DATE NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'cheque', 'card') NOT NULL,
    reference_number VARCHAR(100),
    vendor_name VARCHAR(255),
    receipt_image VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (machine_id) REFERENCES machines(id) ON DELETE SET NULL,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
    INDEX idx_org (organization_id),
    INDEX idx_machine (machine_id),
    INDEX idx_job (job_id),
    INDEX idx_category (category),
    INDEX idx_date (expense_date)
);
```

### 17. ledger
Financial ledger for income and expense tracking.

```sql
CREATE TABLE ledger (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    transaction_type ENUM('income', 'expense') NOT NULL,
    reference_type VARCHAR(50), -- invoice, payment, expense
    reference_id BIGINT UNSIGNED,
    description TEXT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    balance DECIMAL(15,2) NOT NULL,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_org_date (organization_id, transaction_date),
    INDEX idx_type (transaction_type),
    INDEX idx_reference (reference_type, reference_id)
);
```

### 18. sync_queue
Offline sync queue for mobile apps.

```sql
CREATE TABLE sync_queue (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    entity_type VARCHAR(50) NOT NULL, -- measurement, job, expense, etc.
    entity_id VARCHAR(100),
    action ENUM('create', 'update', 'delete') NOT NULL,
    payload JSON NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    attempts INT DEFAULT 0,
    last_error TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_org_user (organization_id, user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);
```

### 19. rate_cards
Configurable pricing rates.

```sql
CREATE TABLE rate_cards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    unit VARCHAR(50) NOT NULL, -- acre, hectare, hour
    rate DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    effective_from DATE NOT NULL,
    effective_to DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED,
    
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_org_active (organization_id, is_active)
);
```

## Indexes Summary

Critical indexes for performance:

1. **Organizations**: slug, owner_id, status
2. **Users**: organization_id, email, phone, role_id
3. **Measurements**: organization_id, measured_by, customer_phone, measurement_date
4. **Jobs**: organization_id, status, scheduled_date, customer_phone
5. **GPS Tracking**: user_id + timestamp (composite), job_id
6. **Invoices**: organization_id, job_id, status, invoice_date
7. **Payments**: organization_id, invoice_id, payment_date
8. **Expenses**: organization_id, machine_id, expense_date
9. **Ledger**: organization_id + transaction_date (composite)

## Data Retention & Archival

- GPS Tracking: Partition by month, archive after 12 months
- Sync Queue: Delete after 30 days if completed
- Ledger: Permanent retention for legal compliance
- Invoices: Permanent retention
- Measurements: Permanent retention

## Security Considerations

1. All sensitive data encrypted at rest
2. Organization-level data isolation enforced at query level
3. Audit fields (created_by, updated_by) for all major tables
4. Soft deletes for recoverability
5. Row-level security for multi-tenancy

---

This schema supports scalability to millions of records with proper indexing and partitioning strategies.
