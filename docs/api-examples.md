# API Examples for GeoOps Platform

This document provides examples of how to interact with the GeoOps Platform's REST API. Each section includes a brief description of the endpoint, the HTTP method used, and example requests and responses.

## Authentication

### Register a New User

- **Endpoint:** `/api/v1/auth/register`
- **Method:** `POST`
- **Request Body:**

```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "password": "securepassword",
  "password_confirmation": "securepassword"
}
```

- **Response:**

```json
{
  "message": "User registered successfully.",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com"
  }
}
```

### User Login

- **Endpoint:** `/api/v1/auth/login`
- **Method:** `POST`
- **Request Body:**

```json
{
  "email": "john.doe@example.com",
  "password": "securepassword"
}
```

- **Response:**

```json
{
  "token": "your_jwt_token",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com"
  }
}
```

## Land Management

### Create a New Land Measurement

- **Endpoint:** `/api/v1/lands`
- **Method:** `POST`
- **Request Body:**

```json
{
  "name": "Farm Land",
  "coordinates": [[
    [longitude1, latitude1],
    [longitude2, latitude2],
    [longitude3, latitude3],
    [longitude4, latitude4]
  ]]
}
```

- **Response:**

```json
{
  "message": "Land measurement created successfully.",
  "land": {
    "id": 1,
    "name": "Farm Land",
    "area": 2.5
  }
}
```

### Retrieve Land Measurements

- **Endpoint:** `/api/v1/lands`
- **Method:** `GET`
- **Response:**

```json
[
  {
    "id": 1,
    "name": "Farm Land",
    "area": 2.5
  },
  {
    "id": 2,
    "name": "Garden",
    "area": 1.2
  }
]
```

## Job Management

### Create a New Job

- **Endpoint:** `/api/v1/jobs`
- **Method:** `POST`
- **Request Body:**

```json
{
  "land_id": 1,
  "service_type": "Plowing",
  "scheduled_date": "2024-01-15"
}
```

- **Response:**

```json
{
  "message": "Job created successfully.",
  "job": {
    "id": 1,
    "service_type": "Plowing",
    "status": "Pending"
  }
}
```

### Retrieve Job Details

- **Endpoint:** `/api/v1/jobs/{id}`
- **Method:** `GET`
- **Response:**

```json
{
  "id": 1,
  "land_id": 1,
  "service_type": "Plowing",
  "status": "Pending",
  "scheduled_date": "2024-01-15"
}
```

## Invoicing

### Create an Invoice

- **Endpoint:** `/api/v1/invoices`
- **Method:** `POST`
- **Request Body:**

```json
{
  "job_id": 1,
  "amount": 100.0
}
```

- **Response:**

```json
{
  "message": "Invoice created successfully.",
  "invoice": {
    "id": 1,
    "amount": 100.0,
    "status": "Draft"
  }
}
```

### Retrieve Invoices

- **Endpoint:** `/api/v1/invoices`
- **Method:** `GET`
- **Response:**

```json
[
  {
    "id": 1,
    "amount": 100.0,
    "status": "Draft"
  },
  {
    "id": 2,
    "amount": 150.0,
    "status": "Paid"
  }
]
```

## Conclusion

This document provides a basic overview of the API endpoints available in the GeoOps Platform. For more detailed information, please refer to the [API Specification](API_SPECIFICATION.md).
