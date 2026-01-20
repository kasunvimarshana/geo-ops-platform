# Sample Seed Data for GeoOps Platform Application

This document provides sample data to seed the database for testing and development purposes. The data includes organizations, users, customers, lands, jobs, invoices, payments, and expenses.

## Sample Organizations

```json
[
  {
    "name": "Agro Services Ltd",
    "address": "123 Agro Lane, Colombo, Sri Lanka",
    "contact_number": "+94 123 456 789",
    "email": "info@agroservices.lk"
  },
  {
    "name": "Farm Solutions Inc",
    "address": "456 Farm Road, Kandy, Sri Lanka",
    "contact_number": "+94 987 654 321",
    "email": "contact@farmsolutions.lk"
  }
]
```

## Sample Users

```json
[
  {
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "password123",
    "role": "Admin",
    "organization_id": 1
  },
  {
    "name": "Jane Smith",
    "email": "jane.smith@example.com",
    "password": "password123",
    "role": "Owner",
    "organization_id": 1
  },
  {
    "name": "Mike Johnson",
    "email": "mike.johnson@example.com",
    "password": "password123",
    "role": "Driver",
    "organization_id": 2
  }
]
```

## Sample Customers

```json
[
  {
    "name": "Farmer A",
    "contact_number": "+94 111 222 333",
    "email": "farmera@example.com",
    "organization_id": 1
  },
  {
    "name": "Farmer B",
    "contact_number": "+94 444 555 666",
    "email": "farmerb@example.com",
    "organization_id": 2
  }
]
```

## Sample Lands

```json
[
  {
    "name": "Field 1",
    "area": 5.0,
    "coordinates": "[(6.9271, 79.9614), (6.9272, 79.9615), (6.9273, 79.9616)]",
    "customer_id": 1
  },
  {
    "name": "Field 2",
    "area": 10.0,
    "coordinates": "[(6.9274, 79.9617), (6.9275, 79.9618), (6.9276, 79.9619)]",
    "customer_id": 2
  }
]
```

## Sample Jobs

```json
[
  {
    "title": "Plowing Field 1",
    "land_id": 1,
    "driver_id": 1,
    "status": "Completed",
    "scheduled_date": "2024-01-15"
  },
  {
    "title": "Harvesting Field 2",
    "land_id": 2,
    "driver_id": 3,
    "status": "In Progress",
    "scheduled_date": "2024-01-20"
  }
]
```

## Sample Invoices

```json
[
  {
    "customer_id": 1,
    "amount": 15000,
    "status": "Paid",
    "issued_date": "2024-01-16"
  },
  {
    "customer_id": 2,
    "amount": 20000,
    "status": "Pending",
    "issued_date": "2024-01-21"
  }
]
```

## Sample Payments

```json
[
  {
    "invoice_id": 1,
    "amount": 15000,
    "payment_date": "2024-01-17",
    "method": "Bank Transfer"
  },
  {
    "invoice_id": 2,
    "amount": 20000,
    "payment_date": null,
    "method": "Cash"
  }
]
```

## Sample Expenses

```json
[
  {
    "description": "Fuel for plowing",
    "amount": 5000,
    "date": "2024-01-15",
    "driver_id": 1
  },
  {
    "description": "Maintenance for tractor",
    "amount": 3000,
    "date": "2024-01-18",
    "driver_id": 3
  }
]
```

This seed data can be used in the `DatabaseSeeder.php` file to populate the database with initial values for testing and development.
