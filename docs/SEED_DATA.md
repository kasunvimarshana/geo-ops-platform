# Sample Seed Data - GeoOps Platform

## Organizations

### Organization 1: Green Fields Farm

- **Name**: Green Fields Farm
- **Owner**: Sunil Perera
- **Subscription**: Pro
- **Location**: Kandy, Sri Lanka

### Organization 2: Lanka Agri Services

- **Name**: Lanka Agri Services
- **Owner**: Nimal Silva
- **Subscription**: Basic
- **Location**: Colombo, Sri Lanka

## Users

### Admin

- **Name**: Admin User
- **Email**: admin@geo-ops.lk
- **Password**: admin123
- **Role**: admin

### Organization 1 Users

#### Owner

- **Name**: Sunil Perera
- **Email**: sunil@greenfields.lk
- **Password**: password123
- **Role**: owner
- **Phone**: +94771234567

#### Drivers

1. **Kamal Jayawardena**
   - Email: kamal@greenfields.lk
   - Password: password123
   - Role: driver
   - Phone: +94771234568
   - License: B1234567
   - Vehicle: Mahindra 275 DI Tractor

2. **Ranjith Fernando**
   - Email: ranjith@greenfields.lk
   - Password: password123
   - Role: driver
   - Phone: +94771234569
   - License: B7654321
   - Vehicle: John Deere 5045E

#### Broker

- **Name**: Chaminda Wickramasinghe
- **Email**: chaminda@greenfields.lk
- **Password**: password123
- **Role**: broker
- **Phone**: +94771234570

### Organization 2 Users

#### Owner

- **Name**: Nimal Silva
- **Email**: nimal@lankaagri.lk
- **Password**: password123
- **Role**: owner
- **Phone**: +94771234571

#### Driver

- **Name**: Prasad Gunawardena
- **Email**: prasad@lankaagri.lk
- **Password**: password123
- **Role**: driver
- **Phone**: +94771234572
- **License**: B9876543
- **Vehicle**: Massey Ferguson MF 240

## Customers

### Green Fields Farm Customers

1. **Farmer Bandara**
   - Phone: +94712345678
   - Email: bandara@email.lk
   - Address: 123, Temple Road, Peradeniya, Kandy
   - Balance: 0.00

2. **Farmer Wijaya**
   - Phone: +94712345679
   - Email: wijaya@email.lk
   - Address: 456, Main Street, Gampola, Kandy
   - Balance: 5000.00

3. **Farmer Kumara**
   - Phone: +94712345680
   - Email: kumara@email.lk
   - Address: 789, Hill Road, Katugastota, Kandy
   - Balance: 0.00

### Lanka Agri Services Customers

1. **Farmer Rajapaksha**
   - Phone: +94712345681
   - Email: rajapaksha@email.lk
   - Address: 321, Park Avenue, Negombo
   - Balance: 15000.00

2. **Farmer Samaraweera**
   - Phone: +94712345682
   - Email: samaraweera@email.lk
   - Address: 654, Beach Road, Chilaw
   - Balance: 0.00

## Machines

### Green Fields Farm Machines

1. **Tractor 01**
   - Type: Tractor
   - Model: Mahindra 275 DI
   - Registration: WP ABC-1234
   - Status: Active

2. **Tractor 02**
   - Type: Tractor
   - Model: John Deere 5045E
   - Registration: WP DEF-5678
   - Status: Active

3. **Plough 01**
   - Type: Plough
   - Model: 3-Disc Plough
   - Registration: N/A
   - Status: Active

### Lanka Agri Services Machines

1. **Tractor 01**
   - Type: Tractor
   - Model: Massey Ferguson MF 240
   - Registration: WP GHI-9012
   - Status: Active

## Land Measurements

### Green Fields Farm

1. **North Field A**
   - Area: 2.5 acres (1.0117 hectares)
   - Measured by: Kamal Jayawardena
   - Date: 2024-01-10
   - Coordinates (Sample):
     ```json
     [
       { "latitude": 7.2906, "longitude": 80.6337 },
       { "latitude": 7.2906, "longitude": 80.6347 },
       { "latitude": 7.2916, "longitude": 80.6347 },
       { "latitude": 7.2916, "longitude": 80.6337 }
     ]
     ```

2. **South Field B**
   - Area: 1.8 acres (0.7284 hectares)
   - Measured by: Kamal Jayawardena
   - Date: 2024-01-12
   - Coordinates: (Similar polygon)

3. **East Field C**
   - Area: 3.2 acres (1.2950 hectares)
   - Measured by: Ranjith Fernando
   - Date: 2024-01-15

### Lanka Agri Services

1. **Paddy Field 01**
   - Area: 4.0 acres (1.6187 hectares)
   - Measured by: Prasad Gunawardena
   - Date: 2024-01-08

2. **Paddy Field 02**
   - Area: 2.8 acres (1.1331 hectares)
   - Measured by: Prasad Gunawardena
   - Date: 2024-01-11

## Jobs

### Green Fields Farm

#### Job 1: Completed

- Customer: Farmer Bandara
- Land: North Field A (2.5 acres)
- Driver: Kamal Jayawardena
- Machine: Tractor 01 + Plough 01
- Status: Completed
- Scheduled: 2024-01-15 08:00
- Started: 2024-01-15 08:15
- Completed: 2024-01-15 12:30
- Notes: Deep ploughing completed successfully

#### Job 2: In Progress

- Customer: Farmer Wijaya
- Land: South Field B (1.8 acres)
- Driver: Ranjith Fernando
- Machine: Tractor 02
- Status: In Progress
- Scheduled: 2024-01-18 09:00
- Started: 2024-01-18 09:10
- Notes: Light ploughing for seeding

#### Job 3: Pending

- Customer: Farmer Kumara
- Land: East Field C (3.2 acres)
- Driver: Kamal Jayawardena
- Machine: Tractor 01
- Status: Pending
- Scheduled: 2024-01-20 08:00
- Notes: Regular ploughing

### Lanka Agri Services

#### Job 1: Completed

- Customer: Farmer Rajapaksha
- Land: Paddy Field 01 (4.0 acres)
- Driver: Prasad Gunawardena
- Machine: Tractor 01
- Status: Completed
- Scheduled: 2024-01-12 07:00
- Started: 2024-01-12 07:20
- Completed: 2024-01-12 13:45

## Invoices

### Green Fields Farm

#### Invoice 1: Paid

- Invoice Number: INV-2024-001
- Job: Job 1 (Farmer Bandara - North Field A)
- Amount: 12,500.00 LKR (2.5 acres × 5,000 LKR/acre)
- Tax: 0.00 LKR
- Discount: 0.00 LKR
- Total: 12,500.00 LKR
- Status: Paid
- Issued: 2024-01-15
- Due: 2024-01-22
- Paid: 2024-01-18

#### Invoice 2: Sent

- Invoice Number: INV-2024-002
- Job: Job 2 (Farmer Wijaya - South Field B)
- Amount: 9,000.00 LKR (1.8 acres × 5,000 LKR/acre)
- Tax: 0.00 LKR
- Discount: 500.00 LKR
- Total: 8,500.00 LKR
- Status: Sent
- Issued: 2024-01-18
- Due: 2024-01-25

### Lanka Agri Services

#### Invoice 1: Overdue

- Invoice Number: INV-2024-001
- Job: Job 1 (Farmer Rajapaksha - Paddy Field 01)
- Amount: 20,000.00 LKR (4.0 acres × 5,000 LKR/acre)
- Tax: 0.00 LKR
- Discount: 0.00 LKR
- Total: 20,000.00 LKR
- Status: Overdue
- Issued: 2024-01-12
- Due: 2024-01-19

## Payments

### Green Fields Farm

1. **Payment for Invoice 1**
   - Amount: 12,500.00 LKR
   - Method: Cash
   - Date: 2024-01-18
   - Reference: N/A

### Lanka Agri Services

1. **Partial Payment for Invoice 1**
   - Amount: 5,000.00 LKR
   - Method: Bank Transfer
   - Date: 2024-01-15
   - Reference: TXN123456789

## Expenses

### Green Fields Farm

1. **Fuel - Diesel**
   - Amount: 5,000.00 LKR
   - Category: Fuel
   - Date: 2024-01-15
   - Driver: Kamal Jayawardena
   - Machine: Tractor 01
   - Job: Job 1
   - Description: 50 liters diesel for ploughing

2. **Spare Parts**
   - Amount: 2,500.00 LKR
   - Category: Spare Parts
   - Date: 2024-01-16
   - Machine: Tractor 02
   - Description: Air filter replacement

3. **Maintenance**
   - Amount: 8,000.00 LKR
   - Category: Maintenance
   - Date: 2024-01-10
   - Machine: Tractor 01
   - Description: Engine oil change and servicing

### Lanka Agri Services

1. **Fuel - Diesel**
   - Amount: 6,500.00 LKR
   - Category: Fuel
   - Date: 2024-01-12
   - Driver: Prasad Gunawardena
   - Machine: Tractor 01
   - Job: Job 1
   - Description: 65 liters diesel

2. **Labor**
   - Amount: 3,000.00 LKR
   - Category: Labor
   - Date: 2024-01-12
   - Description: Helper wage for the day

## Tracking Logs (Sample)

### Job 1 - Kamal Jayawardena - 2024-01-15

```
08:15:00 - Lat: 7.2906, Lon: 80.6337, Speed: 0 km/h
08:20:00 - Lat: 7.2908, Lon: 80.6339, Speed: 8 km/h
08:25:00 - Lat: 7.2910, Lon: 80.6341, Speed: 10 km/h
08:30:00 - Lat: 7.2912, Lon: 80.6343, Speed: 12 km/h
...
12:30:00 - Lat: 7.2915, Lon: 80.6336, Speed: 0 km/h
```

## Subscription History

### Green Fields Farm

- **Current**: Pro
- **Started**: 2024-01-01
- **Expires**: 2025-01-01
- **Status**: Active
- **Amount**: 50,000.00 LKR/year

### Lanka Agri Services

- **Current**: Basic
- **Started**: 2024-01-01
- **Expires**: 2024-07-01
- **Status**: Active
- **Amount**: 15,000.00 LKR/6 months

## Rate Configuration

### Green Fields Farm

- Rate per acre: 5,000.00 LKR
- Rate per hectare: 12,355.00 LKR
- Currency: LKR
- Tax: 0%

### Lanka Agri Services

- Rate per acre: 5,000.00 LKR
- Rate per hectare: 12,355.00 LKR
- Currency: LKR
- Tax: 0%

## Notes

- All passwords in seed data are: `password123`
- Timestamps are in Asia/Colombo timezone
- Currency is Sri Lankan Rupees (LKR)
- Coordinates are approximate for Kandy region, Sri Lanka
- Sample data is for testing and demonstration purposes only
