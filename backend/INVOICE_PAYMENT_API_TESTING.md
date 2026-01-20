# Invoice and Payment API - Testing Guide

## Overview

Complete implementation of Invoice and Payment API endpoints for the GeoOps Platform following Clean Architecture patterns.

## Implemented Components

### Repositories

- ✅ `InvoiceRepositoryInterface` - Contract for invoice data access
- ✅ `InvoiceRepository` - Implementation with full CRUD and filtering
- ✅ `PaymentRepositoryInterface` - Contract for payment data access
- ✅ `PaymentRepository` - Implementation with full CRUD and filtering

### Services

- ✅ `InvoiceService` - Business logic for invoice management
    - Create invoice (manual or from job)
    - Update invoice (with validation)
    - Delete invoice (with constraints)
    - Generate invoice numbers (INV-YYYYMMDD-XXXX)
    - Calculate amounts (subtotal, tax, discount, total)
    - PDF generation placeholder
- ✅ `PaymentService` - Business logic for payment management
    - Record payment
    - Update invoice balance automatically
    - Delete payment (revert balance)
    - Generate payment numbers (PAY-YYYYMMDD-XXXX)
    - Validate payment amounts

### DTOs

- ✅ `CreateInvoiceDTO` - Data transfer for creating invoices
- ✅ `UpdateInvoiceDTO` - Data transfer for updating invoices
- ✅ `CreatePaymentDTO` - Data transfer for recording payments

### Form Requests

- ✅ `StoreInvoiceRequest` - Validation for creating invoices
- ✅ `UpdateInvoiceRequest` - Validation for updating invoices
- ✅ `StorePaymentRequest` - Validation for recording payments

### Controllers

- ✅ `InvoiceController` - 7 endpoints
- ✅ `PaymentController` - 4 endpoints

### Resources

- ✅ `InvoiceResource` - JSON transformation for invoices
- ✅ `PaymentResource` - JSON transformation for payments

### Seeders

- ✅ `InvoicePaymentSeeder` - Sample data for testing

## API Endpoints

### Invoice Endpoints

#### 1. List Invoices

```
GET /api/v1/invoices
```

**Query Parameters:**

- `status` - Filter by status (draft, pending, paid, overdue, cancelled)
- `customer_id` - Filter by customer
- `job_id` - Filter by job
- `start_date` - Filter by date range (start)
- `end_date` - Filter by date range (end)
- `search` - Search in invoice number, customer name, phone, notes
- `sort_by` - Sort field (default: created_at)
- `sort_direction` - Sort direction (asc/desc, default: desc)
- `per_page` - Items per page (default: 15)

**Response:**

```json
{
  "success": true,
  "message": "Invoices retrieved successfully.",
  "data": {
    "data": [...],
    "links": {...},
    "meta": {...}
  }
}
```

#### 2. Create Invoice (Manual)

```
POST /api/v1/invoices
```

**Request Body:**

```json
{
    "job_id": 1,
    "customer_id": 2,
    "customer_name": "John Doe Farms",
    "customer_phone": "+1234567890",
    "customer_address": "123 Farm Road",
    "invoice_date": "2024-01-15",
    "due_date": "2024-02-14",
    "line_items": [
        {
            "description": "Plowing service - 50 acres",
            "quantity": 50,
            "unit": "acre",
            "rate": 25.0,
            "amount": 1250.0
        }
    ],
    "tax_amount": 100.0,
    "discount_amount": 50.0,
    "currency": "USD",
    "notes": "Thank you for your business",
    "terms": "Payment due within 30 days",
    "status": "draft"
}
```

#### 3. Create Invoice from Job

```
POST /api/v1/invoices/from-job
```

**Request Body:**

```json
{
    "job_id": 1
}
```

**Features:**

- Validates job is completed
- Checks for existing invoice
- Auto-populates customer and line items from job
- Sets default due date (30 days)

#### 4. Get Invoice Details

```
GET /api/v1/invoices/{id}
```

**Response includes:**

- Invoice details
- Line items
- Customer information
- Associated job (if any)
- All payments

#### 5. Update Invoice

```
PUT /api/v1/invoices/{id}
```

**Constraints:**

- Can only modify draft invoices fully
- Non-draft invoices can only change status
- Cannot modify paid invoices
- Recalculates totals if line items change

#### 6. Delete Invoice

```
DELETE /api/v1/invoices/{id}
```

**Constraints:**

- Cannot delete paid invoices
- Cannot delete invoices with payments
- Soft delete only

#### 7. Generate PDF

```
GET /api/v1/invoices/{id}/pdf
```

**Note:** Currently returns placeholder response. TODO: Implement with dompdf or snappy.

### Payment Endpoints

#### 1. List Payments

```
GET /api/v1/payments
```

**Query Parameters:**

- `invoice_id` - Filter by invoice
- `customer_id` - Filter by customer
- `payment_method` - Filter by payment method
- `start_date` - Filter by date range (start)
- `end_date` - Filter by date range (end)
- `search` - Search in payment number, reference number, notes
- `sort_by` - Sort field (default: created_at)
- `sort_direction` - Sort direction (asc/desc, default: desc)
- `per_page` - Items per page (default: 15)

#### 2. Record Payment

```
POST /api/v1/payments
```

**Request Body:**

```json
{
    "invoice_id": 1,
    "amount": 500.0,
    "payment_method": "bank_transfer",
    "payment_date": "2024-01-18",
    "reference_number": "TRF-20240118-001",
    "notes": "Partial payment received",
    "currency": "USD"
}
```

**Features:**

- Auto-generates payment number
- Updates invoice paid_amount and balance_amount
- Changes invoice status to 'paid' when fully paid
- Validates amount doesn't exceed balance

**Payment Methods:**

- cash
- check
- bank_transfer
- mobile_money
- credit_card
- other

#### 3. Get Payment Details

```
GET /api/v1/payments/{id}
```

#### 4. Delete Payment

```
DELETE /api/v1/payments/{id}
```

**Features:**

- Reverts invoice balance
- Updates invoice status back to pending if needed
- Soft delete only

## Testing Instructions

### 1. Setup Test Data

```bash
cd backend
php artisan db:seed --class=InvoicePaymentSeeder
```

### 2. Test Invoice Endpoints

**List Invoices:**

```bash
curl -X GET "http://localhost:8000/api/v1/invoices" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Filter by Status:**

```bash
curl -X GET "http://localhost:8000/api/v1/invoices?status=pending" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Create Invoice:**

```bash
curl -X POST "http://localhost:8000/api/v1/invoices" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Test Customer",
    "line_items": [
      {
        "description": "Test Service",
        "quantity": 10,
        "unit": "acre",
        "rate": 50.00,
        "amount": 500.00
      }
    ]
  }'
```

**Create from Job:**

```bash
curl -X POST "http://localhost:8000/api/v1/invoices/from-job" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"job_id": 1}'
```

**Get Invoice:**

```bash
curl -X GET "http://localhost:8000/api/v1/invoices/1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Update Invoice:**

```bash
curl -X PUT "http://localhost:8000/api/v1/invoices/1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status": "pending"}'
```

**Delete Invoice:**

```bash
curl -X DELETE "http://localhost:8000/api/v1/invoices/1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Generate PDF:**

```bash
curl -X GET "http://localhost:8000/api/v1/invoices/1/pdf" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### 3. Test Payment Endpoints

**List Payments:**

```bash
curl -X GET "http://localhost:8000/api/v1/payments" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Record Payment:**

```bash
curl -X POST "http://localhost:8000/api/v1/payments" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "invoice_id": 1,
    "amount": 500.00,
    "payment_method": "cash"
  }'
```

**Get Payment:**

```bash
curl -X GET "http://localhost:8000/api/v1/payments/1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Delete Payment:**

```bash
curl -X DELETE "http://localhost:8000/api/v1/payments/1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Key Features Implemented

### Invoice Features

✅ Auto-generate invoice numbers (INV-YYYYMMDD-XXXX)
✅ Create invoice from completed job
✅ Multiple line items support
✅ Automatic amount calculations (subtotal, tax, discount, total)
✅ Track balance and paid amounts
✅ Invoice status tracking (draft, pending, paid, overdue, cancelled)
✅ Organization-scoped queries
✅ Soft delete with constraints
✅ Filtering, searching, pagination
✅ Offline sync support (is_synced flag)
✅ PDF generation placeholder

### Payment Features

✅ Auto-generate payment numbers (PAY-YYYYMMDD-XXXX)
✅ Automatic invoice balance updates
✅ Multiple payment methods
✅ Payment validation (can't exceed balance)
✅ Reverse payment on delete
✅ Organization-scoped queries
✅ Filtering, searching, pagination
✅ Offline sync support (is_synced flag)

### Validation Rules

✅ Can't modify paid invoices
✅ Can only modify draft invoices fully
✅ Can't delete paid invoices
✅ Can't delete invoices with payments
✅ Payment amount must be positive
✅ Payment can't exceed invoice balance
✅ Can't pay cancelled invoices
✅ Due date must be after invoice date
✅ Line items validation

## Architecture Compliance

✅ **Clean Architecture** - Clear separation of concerns
✅ **Repository Pattern** - Interfaces and implementations
✅ **Service Layer** - Business logic isolation
✅ **DTOs** - Data transfer objects for type safety
✅ **Form Requests** - Validation separation
✅ **API Resources** - Response transformation
✅ **Dependency Injection** - Constructor injection
✅ **Transaction Management** - DB transactions for consistency
✅ **Organization Isolation** - Multi-tenancy support
✅ **Soft Deletes** - Data preservation
✅ **Audit Trail** - created_by, updated_by tracking

## File Structure

```
backend/
├── app/
│   ├── DTOs/
│   │   ├── Invoice/
│   │   │   ├── CreateInvoiceDTO.php
│   │   │   └── UpdateInvoiceDTO.php
│   │   └── Payment/
│   │       └── CreatePaymentDTO.php
│   ├── Http/
│   │   ├── Controllers/Api/V1/
│   │   │   ├── InvoiceController.php
│   │   │   └── PaymentController.php
│   │   ├── Requests/
│   │   │   ├── Invoice/
│   │   │   │   ├── StoreInvoiceRequest.php
│   │   │   │   └── UpdateInvoiceRequest.php
│   │   │   └── Payment/
│   │   │       └── StorePaymentRequest.php
│   │   └── Resources/
│   │       ├── InvoiceResource.php
│   │       └── PaymentResource.php
│   ├── Models/
│   │   ├── Invoice.php
│   │   └── Payment.php
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── InvoiceRepositoryInterface.php
│   │   │   └── PaymentRepositoryInterface.php
│   │   ├── InvoiceRepository.php
│   │   └── PaymentRepository.php
│   ├── Services/
│   │   ├── InvoiceService.php
│   │   └── PaymentService.php
│   └── Providers/
│       └── AppServiceProvider.php (updated)
├── database/
│   └── seeders/
│       └── InvoicePaymentSeeder.php
└── routes/
    └── api.php (updated)
```

## Future Enhancements

### PDF Generation

- Implement actual PDF generation using dompdf or snappy
- Store PDF files in storage
- Add PDF download endpoint
- Email PDF to customers

### Additional Features

- Recurring invoices
- Invoice templates
- Payment reminders
- Late payment penalties
- Bulk invoice actions
- Invoice export (Excel, CSV)
- Payment receipts generation
- Invoice versioning
- Credit notes/refunds
- Multi-currency support expansion

## Notes

- All endpoints require JWT authentication
- All queries are scoped to user's organization
- All timestamps are in ISO 8601 format
- Currency defaults to USD
- Amounts use decimal(2) precision
- Soft deletes preserve data integrity
- Transaction management ensures data consistency
