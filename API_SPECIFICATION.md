# API Specification for GeoOps Platform Platform

## Overview

This document outlines the API endpoints for the GeoOps Platform Platform, detailing the available resources, request methods, and response formats. The API is designed to facilitate interactions between the frontend mobile application and the backend Laravel REST API.

## Base URL

The base URL for the API is:

```
https://api.geo-ops.lk/api/v1
```

## Authentication

All endpoints require authentication via JWT. Include the token in the `Authorization` header as follows:

```
Authorization: Bearer <token>
```

## Endpoints

### Authentication

- **POST /auth/register**
  - Description: Register a new user.
  - Request Body: `{ "name": "string", "email": "string", "password": "string" }`
  - Response: `{ "token": "string", "user": { ... } }`

- **POST /auth/login**
  - Description: Authenticate a user and return a token.
  - Request Body: `{ "email": "string", "password": "string" }`
  - Response: `{ "token": "string", "user": { ... } }`

- **POST /auth/logout**
  - Description: Log out the authenticated user.
  - Response: `{ "message": "Successfully logged out." }`

- **GET /auth/me**
  - Description: Retrieve the authenticated user's profile.
  - Response: `{ "user": { ... } }`

### Land Management

- **GET /lands**
  - Description: Retrieve a list of lands.
  - Response: `[ { "id": "number", "name": "string", "area": "number" }, ... ]`

- **POST /lands**
  - Description: Create a new land entry.
  - Request Body: `{ "name": "string", "area": "number", "coordinates": "array" }`
  - Response: `{ "land": { ... } }`

- **GET /lands/{id}**
  - Description: Retrieve details of a specific land.
  - Response: `{ "land": { ... } }`

- **PUT /lands/{id}**
  - Description: Update a specific land entry.
  - Request Body: `{ "name": "string", "area": "number", "coordinates": "array" }`
  - Response: `{ "land": { ... } }`

- **DELETE /lands/{id}**
  - Description: Delete a specific land entry.
  - Response: `{ "message": "Land deleted successfully." }`

### Job Management

- **GET /jobs**
  - Description: Retrieve a list of jobs.
  - Response: `[ { "id": "number", "title": "string", "status": "string" }, ... ]`

- **POST /jobs**
  - Description: Create a new job.
  - Request Body: `{ "title": "string", "description": "string", "land_id": "number" }`
  - Response: `{ "job": { ... } }`

- **POST /jobs/{id}/start**
  - Description: Start a specific job.
  - Response: `{ "message": "Job started successfully." }`

- **POST /jobs/{id}/complete**
  - Description: Complete a specific job.
  - Response: `{ "message": "Job completed successfully." }`

- **POST /jobs/{id}/tracking**
  - Description: Update GPS tracking for a job.
  - Request Body: `{ "location": { "latitude": "number", "longitude": "number" } }`
  - Response: `{ "message": "Tracking updated successfully." }`

### Invoice Management

- **GET /invoices**
  - Description: Retrieve a list of invoices.
  - Response: `[ { "id": "number", "amount": "number", "status": "string" }, ... ]`

- **POST /invoices**
  - Description: Create a new invoice.
  - Request Body: `{ "customer_id": "number", "amount": "number", "due_date": "string" }`
  - Response: `{ "invoice": { ... } }`

- **GET /invoices/{id}/pdf**
  - Description: Generate a PDF for a specific invoice.
  - Response: PDF file download.

### Payment Management

- **GET /payments**
  - Description: Retrieve a list of payments.
  - Response: `[ { "id": "number", "amount": "number", "status": "string" }, ... ]`

- **POST /payments**
  - Description: Create a new payment.
  - Request Body: `{ "invoice_id": "number", "amount": "number", "method": "string" }`
  - Response: `{ "payment": { ... } }`

## Error Handling

All error responses will follow this format:

- **Response Format:**
  - Status Code: `4xx` or `5xx`
  - Response Body: `{ "error": { "message": "Error description" } }`

## Conclusion

This API specification provides a comprehensive overview of the endpoints available for the GeoOps Platform. For further details on request and response formats, please refer to the individual endpoint documentation.
