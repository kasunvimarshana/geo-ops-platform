# Bluetooth Thermal Printer Integration

This document describes the Bluetooth thermal printer integration for the GPS Field Management mobile application.

## Overview

The mobile app now supports direct printing to Bluetooth thermal printers using ESC/POS commands. This enables on-site printing of invoices, receipts, and job summaries without requiring an internet connection.

## Features

### Core Capabilities
- **Bluetooth Device Discovery**: Scan and detect nearby Bluetooth thermal printers
- **Connection Management**: Connect, disconnect, and manage printer connections
- **ESC/POS Printing**: Direct printing using standard ESC/POS commands
- **Print Queue**: Offline queue with automatic retry logic
- **PDF Fallback**: Automatic PDF generation when Bluetooth printing fails
- **Multi-Document Support**: Print invoices, receipts, and job summaries

### Offline-First Design
- **Print Queue Persistence**: Failed print jobs are stored in SQLite
- **Automatic Retry**: Background retry mechanism for failed jobs
- **Network Independence**: Full functionality without internet connection
- **Conflict Resolution**: Graceful handling of printer connection issues

### User Experience
- **Simple UI**: Easy device discovery and connection
- **Status Indicators**: Real-time connection and printing status
- **Bilingual Support**: Full English and Sinhala translations
- **Error Handling**: Clear error messages and recovery options

## Architecture

### Service Layer
```
src/shared/services/printer/
├── types.ts                      # TypeScript type definitions
├── escPosBuilder.ts             # ESC/POS command generator
├── bluetoothPrinterService.ts   # Bluetooth connection & printing
├── printQueueService.ts         # Print queue management
├── pdfService.ts                # PDF fallback service
└── index.ts                     # Service exports
```

### State Management
```
src/store/
└── printerStore.ts              # Zustand store for printer state
```

### UI Components
```
src/features/printer/
├── screens/
│   ├── PrinterSettingsScreen.tsx  # Device discovery & connection
│   └── PrintQueueScreen.tsx       # Print queue management
└── index.ts
```

## Usage

### 1. Initialize Printer Store

The printer store is automatically initialized when the app starts:

```typescript
import { usePrinterStore } from './store/printerStore';

// In App.tsx or root component
useEffect(() => {
  const initializePrinter = async () => {
    await usePrinterStore.getState().initialize();
  };
  initializePrinter();
}, []);
```

### 2. Connect to Printer

```typescript
import { usePrinterStore } from './store/printerStore';

const PrinterSetup = () => {
  const { scanDevices, connectDevice, availableDevices } = usePrinterStore();

  const handleConnect = async () => {
    // Scan for devices
    await scanDevices();
    
    // Connect to first available device
    if (availableDevices.length > 0) {
      await connectDevice(availableDevices[0]);
    }
  };

  return <Button onPress={handleConnect}>Connect Printer</Button>;
};
```

### 3. Print Invoice

```typescript
import { usePrinterStore } from './store/printerStore';

const InvoiceScreen = ({ invoice }) => {
  const { printInvoice, isConnected } = usePrinterStore();

  const handlePrint = async () => {
    const printData = {
      invoiceNumber: invoice.invoice_number,
      customerName: invoice.customer_name,
      customerPhone: invoice.customer_phone,
      date: new Date().toLocaleDateString(),
      items: [
        {
          description: 'Land plowing service',
          quantity: 1,
          price: invoice.total_amount,
        },
      ],
      subtotal: invoice.subtotal,
      tax: invoice.tax_amount,
      discount: invoice.discount_amount,
      total: invoice.total_amount,
      currency: invoice.currency,
      notes: invoice.notes,
    };

    // Print via Bluetooth or fallback to PDF
    await printInvoice(printData, !isConnected);
  };

  return <Button onPress={handlePrint}>Print Invoice</Button>;
};
```

### 4. Print Receipt

```typescript
const handlePrintReceipt = async () => {
  const receiptData = {
    receiptNumber: 'RCP-001',
    customerName: 'John Doe',
    date: new Date().toLocaleDateString(),
    amount: 5000,
    paymentMethod: 'Cash',
    currency: 'LKR',
    notes: 'Thank you!',
  };

  await printReceipt(receiptData);
};
```

### 5. Print Job Summary

```typescript
const handlePrintJobSummary = async () => {
  const jobData = {
    jobNumber: 'JOB-001',
    customerName: 'Farm Owner',
    driver: 'Driver Name',
    date: new Date().toLocaleDateString(),
    landArea: 2.5,
    areaUnit: 'acres',
    location: 'Colombo, Sri Lanka',
    status: 'Completed',
    notes: 'Job completed successfully',
  };

  await printJobSummary(jobData);
};
```

## Print Queue Management

### View Queue

```typescript
import { usePrinterStore } from './store/printerStore';

const QueueComponent = () => {
  const { printJobs, queueStats, loadPrintQueue } = usePrinterStore();

  useEffect(() => {
    loadPrintQueue();
  }, []);

  return (
    <View>
      <Text>Pending: {queueStats.pending}</Text>
      <Text>Completed: {queueStats.completed}</Text>
      <Text>Failed: {queueStats.failed}</Text>
    </View>
  );
};
```

### Process Queue

```typescript
const { processQueue, isConnected } = usePrinterStore();

const handleProcessQueue = async () => {
  if (isConnected) {
    await processQueue();
  }
};
```

### Retry Failed Job

```typescript
const { retryJob } = usePrinterStore();

const handleRetry = async (jobId: string) => {
  await retryJob(jobId);
};
```

## ESC/POS Command Reference

The `EscPosBuilder` class provides a fluent API for building print commands:

```typescript
import { EscPosBuilder } from './shared/services/printer';

const builder = new EscPosBuilder();
const commands = builder
  .align('center')
  .bold(true)
  .size('double')
  .textLn('HEADER')
  .size('normal')
  .bold(false)
  .align('left')
  .textLn('Line 1')
  .textLn('Line 2')
  .separator()
  .feed(2)
  .cut()
  .build();
```

### Available Methods

- `text(text: string)` - Add text without line feed
- `textLn(text: string)` - Add text with line feed
- `align(alignment: 'left' | 'center' | 'right')` - Set text alignment
- `bold(enabled: boolean)` - Enable/disable bold text
- `size(size: 'normal' | 'double' | 'large')` - Set font size
- `separator()` - Add horizontal line separator
- `feed(lines: number)` - Add line feeds
- `cut()` - Cut paper
- `build()` - Build final command string

## PDF Fallback

When Bluetooth printing fails or no printer is connected, the system automatically generates a PDF:

```typescript
import { pdfService } from './shared/services/printer';

// Generate PDF
const uri = await pdfService.generateInvoicePDF(invoiceData);

// Share PDF
await pdfService.sharePDF(uri, 'invoice.pdf');

// Print PDF (system print dialog)
await pdfService.printPDF(uri);
```

## Permissions

### Android

The app requires Bluetooth permissions:

```xml
<!-- AndroidManifest.xml -->
<uses-permission android:name="android.permission.BLUETOOTH" />
<uses-permission android:name="android.permission.BLUETOOTH_ADMIN" />
<uses-permission android:name="android.permission.BLUETOOTH_SCAN" />
<uses-permission android:name="android.permission.BLUETOOTH_CONNECT" />
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
```

Permissions are requested automatically by the `bluetoothPrinterService`.

### iOS

Add Bluetooth usage description to `Info.plist`:

```xml
<key>NSBluetoothAlwaysUsageDescription</key>
<string>This app needs Bluetooth to connect to thermal printers</string>
<key>NSBluetoothPeripheralUsageDescription</key>
<string>This app needs Bluetooth to connect to thermal printers</string>
```

## Supported Printers

The integration is compatible with most ESC/POS thermal printers, including:

- **58mm thermal printers** (most common for mobile use)
- **80mm thermal printers** (wider receipts)
- Common brands: Xprinter, Rongta, Goojprt, MUNBYN, etc.
- Any printer supporting ESC/POS commands via Bluetooth

## Testing

### Test Print

Use the built-in test print function to verify connectivity:

```typescript
const { testPrint } = usePrinterStore();

await testPrint();
```

This prints a simple test receipt with timestamp.

## Troubleshooting

### Printer Not Detected

1. Ensure Bluetooth is enabled on the device
2. Put printer in pairing mode (usually holding power button)
3. Check if printer battery is charged
4. Try scanning multiple times

### Connection Fails

1. Unpair and re-pair the printer in device Bluetooth settings
2. Restart the printer
3. Restart the mobile app
4. Check Bluetooth permissions

### Print Quality Issues

1. Ensure printer paper is loaded correctly
2. Check printer battery level
3. Clean printer head if prints are faded
4. Adjust ESC/POS commands for your specific printer model

### Failed Print Jobs

1. Check print queue in the app
2. Verify printer connection
3. Use retry function for failed jobs
4. Use PDF fallback as alternative

## Configuration

### Print Queue Settings

Modify in `printQueueService.ts`:

```typescript
private maxRetries: number = 3;        // Max retry attempts
private retryDelay: number = 5000;     // Delay between retries (ms)
```

### Paper Width

Most commands auto-adjust to printer width. For custom formatting:

```typescript
// 58mm printer (32 characters)
const LINE_WIDTH = 32;

// 80mm printer (48 characters)
const LINE_WIDTH = 48;
```

## Dependencies

```json
{
  "react-native-bluetooth-escpos-printer": "^1.0.0-beta.11",
  "expo-print": "~14.0.2",
  "expo-sharing": "~14.0.0"
}
```

## Future Enhancements

- [ ] Support for printing images/logos
- [ ] QR code generation and printing
- [ ] Barcode printing
- [ ] Custom receipt templates
- [ ] Print preview screen
- [ ] Multiple printer profiles
- [ ] Network printer support (Wi-Fi)
- [ ] Print statistics and analytics

## Best Practices

1. **Always check connection before printing**
   ```typescript
   if (isConnected) {
     await printInvoice(data);
   } else {
     await printInvoice(data, true); // Use PDF fallback
   }
   ```

2. **Handle errors gracefully**
   ```typescript
   try {
     await printReceipt(data);
   } catch (error) {
     Alert.alert('Print Failed', 'Would you like to generate a PDF instead?');
   }
   ```

3. **Process queue after connecting**
   ```typescript
   await connectDevice(device);
   await processQueue(); // Print any pending jobs
   ```

4. **Clean up on unmount**
   ```typescript
   useEffect(() => {
     return () => {
       disconnectDevice();
     };
   }, []);
   ```

## License

Part of the GPS Field Management Platform - MIT License
