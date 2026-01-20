# Geo Ops Platform - Mobile App (React Native/Expo)

## Overview

Mobile application for GPS land measurement and agricultural field service management with **Bluetooth thermal printer support**.

## Key Features

✅ **Bluetooth Printer Integration** - Print invoices, receipts, and job summaries directly to portable thermal printers
✅ **GPS Land Measurement** - Walk-around and point-based polygon measurement
✅ **Job Management** - Complete field work lifecycle management
✅ **Offline-First** - Full functionality without internet connection
✅ **Bilingual** - English and Sinhala support
✅ **Type-Safe** - Full TypeScript implementation

## Prerequisites

- Node.js 18+ and npm
- Expo CLI
- iOS Simulator / Android Emulator or Physical Device
- For printer features: Bluetooth-enabled device

## Installation

```bash
# Install dependencies
npm install

# Start development server
npm start

# Run on iOS
npm run ios

# Run on Android
npm run android
```

## Project Structure

```
src/
├── services/
│   ├── printer/
│   │   └── PrinterService.ts          # Bluetooth printer management
│   ├── api/                           # API client
│   ├── storage/                       # Local database
│   ├── gps/                           # GPS tracking
│   └── sync/                          # Background sync
├── stores/
│   ├── printerStore.ts                # Printer state management
│   ├── authStore.ts                   # Authentication
│   ├── measurementStore.ts            # Measurements
│   └── jobStore.ts                    # Jobs
├── components/
│   ├── Printer/
│   │   ├── PrinterScannerModal.tsx    # Device scanner
│   │   └── PrinterConnectionStatus.tsx # Status widget
│   ├── Common/                        # Shared components
│   ├── Map/                           # Map components
│   └── Forms/                         # Form components
├── screens/
│   ├── Auth/                          # Login, Register
│   ├── Measurement/                   # Measurement screens
│   ├── Jobs/                          # Job screens
│   ├── Billing/                       # Invoice screens
│   └── Printer/                       # Printer settings
├── utils/
│   ├── escpos/
│   │   └── EscPosBuilder.ts           # ESC/POS commands
│   └── validation/                    # Form validation
├── types/
│   └── index.ts                       # TypeScript types
├── locales/                           # i18n translations
└── navigation/                        # Navigation config
```

## Bluetooth Printer Integration

### Quick Start

```typescript
import { usePrinterStore } from '@/stores/printerStore';

function InvoiceScreen() {
  const { printDocument, scanDevices, connectDevice } = usePrinterStore();

  const handlePrint = async () => {
    await printDocument({
      type: 'invoice',
      data: invoiceData
    });
  };

  return (
    <Button title="Print Invoice" onPress={handlePrint} />
  );
}
```

### Features

#### 1. Device Discovery and Connection
```typescript
// Scan for printers
const devices = await scanDevices();

// Connect to a printer
await connectDevice(deviceId);

// Auto-reconnect to saved printer
await autoReconnect();
```

#### 2. Print Documents
```typescript
// Print invoice
await printDocument({
  type: 'invoice',
  data: {
    invoice_number: 'INV-2024-001',
    customer_name: 'John Doe',
    items: [...],
    total_amount: 25000,
    // ... other invoice fields
  }
});

// Print receipt
await printDocument({
  type: 'receipt',
  data: {
    payment_number: 'RCP-2024-001',
    amount: 25000,
    payment_method: 'cash',
    // ... other payment fields
  }
});

// Print job summary
await printDocument({
  type: 'job_summary',
  data: {
    job_number: 'JOB-2024-001',
    driver_name: 'Driver Name',
    machine_name: 'Tractor',
    // ... other job fields
  }
});
```

#### 3. Offline Queue
```typescript
// Jobs are automatically queued if printer is offline
await printDocument({...}); // Added to queue if not connected

// Retry failed prints
await retryFailedPrints();

// Check queue status
const { printerStatus } = usePrinterStore();
console.log(`Queue length: ${printerStatus.queueLength}`);
```

#### 4. Printer Status
```typescript
const { printerStatus, getStatus } = usePrinterStore();

await getStatus();

console.log({
  isConnected: printerStatus.isConnected,
  deviceName: printerStatus.deviceName,
  queueLength: printerStatus.queueLength,
  isProcessing: printerStatus.isProcessing,
});
```

### Supported Printers

The app supports ESC/POS compatible thermal printers including:
- Generic 58mm/80mm thermal printers
- Zebra mobile printers
- Epson TM series
- Star Micronics TSP series
- Bixolon mobile printers

### Print Format Examples

#### Invoice
```
=====================================
       COMPANY NAME
=====================================
Invoice #: INV-2024-001
Date: 2024-01-17
Customer: John Doe
Phone: +94771234567
-------------------------------------
Description         Qty    Amount
-------------------------------------
Land Ploughing     2.5      25000
-------------------------------------
TOTAL:           LKR 25000
-------------------------------------
[QR CODE]
```

#### Receipt
```
=====================================
     PAYMENT RECEIPT
=====================================
Receipt #: RCP-2024-001
Date: 2024-01-17
Amount: LKR 25000
Method: Cash
-------------------------------------
      Thank You!
[QR CODE]
```

## ESC/POS Command Builder

### Basic Usage

```typescript
import { EscPosBuilder } from '@/utils/escpos/EscPosBuilder';

const builder = new EscPosBuilder();

const commands = builder
  .initialize()
  .align('center')
  .bold()
  .text('INVOICE')
  .boldOff()
  .align('left')
  .text('Invoice #: INV-001')
  .text('Date: 2024-01-17')
  .feed(1)
  .qr('INV-001')
  .feed(3)
  .cut()
  .build();

// Send to printer
await printerService.sendToPrinter(commands);
```

### Available Commands

| Method | Description |
|--------|-------------|
| `initialize()` | Reset printer |
| `text(content)` | Print text |
| `bold()` / `boldOff()` | Bold formatting |
| `underline()` / `underlineOff()` | Underline text |
| `align(alignment)` | Set alignment (left/center/right) |
| `size(width, height)` | Set character size (1-8) |
| `feed(lines)` | Feed paper |
| `cut()` | Cut paper |
| `qr(data)` | Print QR code |
| `barcode(data, type)` | Print barcode |
| `horizontalLine(char, length)` | Draw separator |
| `row(left, right)` | Print two-column row |
| `tableRow(columns, widths)` | Print formatted table |

## Configuration

### Environment Variables

Create a `.env` file:

```env
API_BASE_URL=http://localhost:8000/api/v1
API_TIMEOUT=30000
ENABLE_BLUETOOTH_PRINTER=true
PRINTER_DEFAULT_SIZE=80
PRINTER_CONNECTION_TIMEOUT=10000
```

### App Configuration

Edit `app.json`:

```json
{
  "expo": {
    "name": "Geo Ops Platform",
    "slug": "geo-ops-platform",
    "version": "1.0.0",
    "android": {
      "permissions": [
        "BLUETOOTH",
        "BLUETOOTH_ADMIN",
        "BLUETOOTH_CONNECT",
        "BLUETOOTH_SCAN",
        "ACCESS_FINE_LOCATION",
        "ACCESS_COARSE_LOCATION"
      ]
    },
    "ios": {
      "infoPlist": {
        "NSBluetoothAlwaysUsageDescription": "This app needs Bluetooth to connect to thermal printers",
        "NSLocationWhenInUseUsageDescription": "This app needs location for GPS measurements"
      }
    }
  }
}
```

## State Management

This app uses Zustand for state management. Key stores:

### Printer Store
```typescript
const { 
  connectedDevice,
  printQueue,
  isScanning,
  isPrinting,
  scanDevices,
  connectDevice,
  printDocument,
  getStatus
} = usePrinterStore();
```

### Auth Store
```typescript
const {
  user,
  isAuthenticated,
  login,
  logout,
  register
} = useAuthStore();
```

### Measurement Store
```typescript
const {
  measurements,
  currentMeasurement,
  createMeasurement,
  updateMeasurement,
  deleteMeasurement
} = useMeasurementStore();
```

## Offline Support

The app works fully offline with:
- Local SQLite database for data storage
- Background sync when online
- Print queue for offline printing
- Optimistic UI updates

```typescript
// Data is automatically saved locally
await createMeasurement(data); // Works offline

// Automatically syncs when online
// No manual intervention needed
```

## Testing

```bash
# Run tests
npm test

# Run with coverage
npm test -- --coverage

# Run specific test
npm test -- PrinterService
```

## Building

### Development Build
```bash
npm run build:dev
```

### Production Build
```bash
# iOS
eas build --platform ios

# Android
eas build --platform android
```

## Troubleshooting

### Bluetooth Issues

**Printer not found during scan:**
- Ensure printer is powered on
- Put printer in pairing mode
- Check Bluetooth is enabled on device
- Restart the app

**Connection fails:**
- Unpair the device from system settings
- Try scanning again
- Check printer battery level
- Ensure printer supports ESC/POS

**Print quality issues:**
- Check paper is loaded correctly
- Verify printer settings
- Ensure battery is charged
- Clean printer head

### Performance

For better performance:
- Enable Hermes engine
- Use Release builds for testing
- Optimize images
- Use FlatList for long lists
- Memoize expensive computations

## Resources

- [Expo Documentation](https://docs.expo.dev/)
- [React Native Docs](https://reactnative.dev/)
- [react-native-ble-plx](https://github.com/dotintent/react-native-ble-plx)
- [ESC/POS Command Reference](https://reference.epson-biz.com/modules/ref_escpos/)
- [BLUETOOTH_PRINTER.md](../../BLUETOOTH_PRINTER.md) - Detailed printer guide

## Support

For issues or questions:
- Check the main [README.md](../../README.md)
- See [IMPLEMENTATION_GUIDE.md](../../IMPLEMENTATION_GUIDE.md)
- Review [BLUETOOTH_PRINTER.md](../../BLUETOOTH_PRINTER.md)
- Create an issue on GitHub

---

**Built with ❤️ for agricultural field service management**
