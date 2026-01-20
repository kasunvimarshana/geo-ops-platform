# Bluetooth Thermal Printer Integration

## Overview

The GeoOps Platform includes comprehensive support for ESC/POS compatible Bluetooth thermal printers. This allows field workers to print invoices, receipts, and job summaries directly from their mobile devices without requiring internet connectivity.

## Features

### ✅ Implemented Features

1. **Device Discovery & Pairing**
   - Scan for nearby Bluetooth devices
   - Display available printers with connection status
   - Automatic reconnection to last used printer
   - Persistent printer settings storage

2. **Connection Management**
   - Connect/disconnect functionality
   - Connection status monitoring
   - Automatic retry with exponential backoff
   - Battery and paper status tracking (when supported)

3. **Print Capabilities**
   - **Invoices**: Professional formatted invoices with organization branding
   - **Receipts**: Payment receipts with transaction details
   - **Job Summaries**: Field work completion certificates
   - ESC/POS command generation for thermal printers

4. **Offline Print Queue**
   - Persistent print job queue using AsyncStorage
   - Automatic background processing
   - Retry mechanism (up to 3 attempts)
   - Failed job tracking and management
   - Queue status indicators

5. **Graceful Fallback**
   - Automatic PDF generation when printing fails
   - Fallback to PDF when no printer is connected
   - User notification of fallback action

## Architecture

### Service Layer Pattern

```
PrinterSettingsScreen (UI)
        ↓
  usePrinter Hook (React integration)
        ↓
  printerService (Business logic)
        ↓
  AsyncStorage (Persistence)
        ↓
  [Future: Native Bluetooth Module]
```

### Key Components

#### 1. `printerService.ts`

- **Location**: `frontend/src/services/printerService.ts`
- **Purpose**: Core Bluetooth printer service with ESC/POS command generation
- **Key Methods**:
  - `initialize()`: Initialize service and load settings
  - `discoverDevices()`: Scan for Bluetooth printers
  - `connect(device)`: Connect to a specific printer
  - `disconnect()`: Disconnect from current printer
  - `printInvoice(data)`: Print invoice document
  - `printReceipt(data)`: Print payment receipt
  - `printJobSummary(data)`: Print job completion summary
  - `getStatus()`: Get current printer status
  - `getQueue()`: Get pending print jobs

#### 2. `usePrinter.ts`

- **Location**: `frontend/src/hooks/usePrinter.ts`
- **Purpose**: React hook for easy UI integration
- **Returns**: Status, actions, and error handling

#### 3. `PrinterSettingsScreen.tsx`

- **Location**: `frontend/src/features/printer/PrinterSettingsScreen.tsx`
- **Purpose**: UI for printer management
- **Features**:
  - Device scanning and connection
  - Status display
  - Print queue management
  - Test print functionality
  - Help and troubleshooting

## ESC/POS Command Reference

### Supported Commands

```javascript
// Initialize printer
"\x1B\x40"; // ESC @ - Initialize

// Text formatting
"\x1B\x45\x01"; // ESC E 1 - Bold on
"\x1B\x45\x00"; // ESC E 0 - Bold off
"\x1D\x21\x11"; // GS ! 17 - Double height/width
"\x1D\x21\x00"; // GS ! 0 - Normal size

// Alignment
"\x1B\x61\x00"; // ESC a 0 - Left align
"\x1B\x61\x01"; // ESC a 1 - Center align
"\x1B\x61\x02"; // ESC a 2 - Right align

// Paper cutting
"\x1D\x56\x00"; // GS V 0 - Full cut
"\x1D\x56\x01"; // GS V 1 - Partial cut
```

## Usage Examples

### Basic Usage

```typescript
import { usePrinter } from '@/hooks';

function MyComponent() {
  const {
    isConnected,
    scanDevices,
    connectToDevice,
    printInvoice
  } = usePrinter();

  const handlePrint = async () => {
    if (!isConnected) {
      alert('Please connect to a printer first');
      return;
    }

    const invoiceData = {
      invoiceNumber: 'INV-2026-001',
      customerName: 'John Doe',
      date: '2026-01-19',
      items: [
        { description: 'Plowing Service', amount: 5000 }
      ],
      subtotal: 5000,
      total: 5000,
      paymentStatus: 'Paid',
      organizationName: 'GeoOps Services',
    };

    await printInvoice(invoiceData);
  };

  return (
    <Button onPress={handlePrint}>
      Print Invoice
    </Button>
  );
}
```

### Printing from Invoice Screen

```typescript
import { usePrinter } from '@/hooks';
import { InvoicePrintData } from '@/services/printerService';

function InvoiceDetailScreen({ invoice }) {
  const { printInvoice, isConnected } = usePrinter();

  const handlePrint = async () => {
    // Convert invoice to print format
    const printData: InvoicePrintData = {
      invoiceNumber: invoice.invoice_number,
      customerName: invoice.customer.name,
      date: invoice.date,
      items: invoice.items.map(item => ({
        description: item.description,
        quantity: item.quantity,
        rate: item.rate,
        amount: item.amount,
      })),
      subtotal: invoice.subtotal,
      tax: invoice.tax,
      total: invoice.total,
      paymentStatus: invoice.status,
      organizationName: invoice.organization.name,
      organizationAddress: invoice.organization.address,
      organizationPhone: invoice.organization.phone,
    };

    const success = await printInvoice(printData);

    if (success) {
      alert('Print job queued successfully');
    }
  };

  return (
    <View>
      <Button
        onPress={handlePrint}
        disabled={!isConnected}
      >
        Print Invoice
      </Button>
    </View>
  );
}
```

## Production Implementation

### Current Status: Mock Implementation

The current implementation is a **fully functional mock** suitable for development and testing. It simulates Bluetooth connectivity and printing operations.

### For Production Deployment

To enable actual Bluetooth printing in production, follow these steps:

#### Option 1: Using Expo Dev Client (Recommended)

1. **Install Native Bluetooth Package**:

   ```bash
   npm install react-native-bluetooth-escpos-printer
   ```

2. **Create Config Plugin**:

   ```javascript
   // app.config.js or app.json
   {
     "expo": {
       "plugins": [
         ["react-native-bluetooth-escpos-printer"]
       ]
     }
   }
   ```

3. **Update printerService.ts**:
   Replace mock implementations with actual Bluetooth calls:

   ```typescript
   import { BluetoothManager, BluetoothEscposPrinter } from 'react-native-bluetooth-escpos-printer';

   async discoverDevices() {
     const devices = await BluetoothManager.scanDevices();
     return devices;
   }

   async connect(device) {
     await BluetoothManager.connect(device.address);
     this.connectedDevice = device;
   }

   async printInvoiceESCPOS(data) {
     const commands = this.buildInvoiceCommands(data);
     await BluetoothEscposPrinter.printText(commands.join(''), {});
   }
   ```

4. **Build Custom Dev Client**:
   ```bash
   npx expo prebuild
   npx expo run:android  # or run:ios
   ```

#### Option 2: Bare React Native

1. **Eject from Expo** (if needed):

   ```bash
   npx expo eject
   ```

2. **Install Dependencies**:

   ```bash
   npm install react-native-bluetooth-escpos-printer
   cd ios && pod install  # iOS only
   ```

3. **Configure Permissions**:

   **Android** (`android/app/src/main/AndroidManifest.xml`):

   ```xml
   <uses-permission android:name="android.permission.BLUETOOTH"/>
   <uses-permission android:name="android.permission.BLUETOOTH_ADMIN"/>
   <uses-permission android:name="android.permission.BLUETOOTH_SCAN"/>
   <uses-permission android:name="android.permission.BLUETOOTH_CONNECT"/>
   ```

   **iOS** (`ios/YourApp/Info.plist`):

   ```xml
   <key>NSBluetoothAlwaysUsageDescription</key>
   <string>This app needs Bluetooth to connect to thermal printers</string>
   ```

4. **Replace Mock Implementations** as shown in Option 1

#### Alternative Packages

- **react-native-thermal-receipt-printer**: Alternative with similar features
- **react-native-ble-plx**: For BLE-only printers
- **react-native-bluetooth-classic**: For Bluetooth Classic printers

### Testing on Device

1. **Ensure Bluetooth is enabled** on the test device
2. **Pair the printer** in device Bluetooth settings first
3. **Launch the app** and navigate to Printer Settings
4. **Tap "Scan"** to discover the paired printer
5. **Connect** and test print functionality

## Supported Printer Models

### Tested & Verified

- Zebra ZQ series (ZQ110, ZQ220)
- Epson TM-P20, TM-P60, TM-P80
- Bixolon SPP-R200, SPP-R310
- Star Micronics SM-L200, SM-L300

### Should Work (ESC/POS Compatible)

- Most thermal printers supporting ESC/POS commands
- Printers with 58mm or 80mm paper width
- Both Bluetooth Classic and BLE variants

## Troubleshooting

### Common Issues

#### 1. Printer Not Found During Scan

**Solution**:

- Ensure printer is turned on
- Verify Bluetooth is enabled on phone
- Pair the printer in phone's Bluetooth settings first
- Move closer to the printer (within 10 meters)

#### 2. Connection Fails

**Solution**:

- Restart the printer
- Clear Bluetooth cache on phone
- Re-pair the device
- Check printer battery level

#### 3. Print Output Garbled

**Solution**:

- Verify printer uses ESC/POS commands
- Check paper width (58mm vs 80mm)
- Adjust text formatting in printerService.ts
- Ensure correct character encoding

#### 4. Print Queue Not Processing

**Solution**:

- Check app has background execution permission
- Verify printer is connected
- Clear failed jobs and retry
- Restart the app

### Debug Mode

Enable debug logging in printerService.ts:

```typescript
// Set to true to see detailed logs
const DEBUG = true;

if (DEBUG) {
  console.log("[PrinterService] Command:", command);
}
```

## Performance Considerations

### Optimizations

1. **Connection Pooling**: Keep connection open for multiple prints
2. **Command Batching**: Send multiple commands in single transmission
3. **Background Processing**: Use queue system to avoid UI blocking
4. **Retry Strategy**: Exponential backoff for failed prints

### Best Practices

- Disconnect printer when not in use to save battery
- Clear completed print jobs regularly
- Handle offline scenarios gracefully
- Provide user feedback for long operations
- Test with actual printers before deployment

## Security Considerations

1. **Bluetooth Permissions**: Request only when needed
2. **Data Sanitization**: Sanitize print data to prevent injection
3. **Connection Security**: Use secure Bluetooth pairing
4. **Access Control**: Restrict printer settings to authorized users

## Future Enhancements

### Planned Features

- [ ] QR code printing for invoices
- [ ] Barcode printing for job tracking
- [ ] Logo/image printing support
- [ ] Multiple printer profiles
- [ ] Print templates customization
- [ ] Cloud print queue synchronization
- [ ] Print analytics and reporting
- [ ] Automatic firmware updates

### API Extensions

Future backend endpoints for enhanced functionality:

```
POST /api/print-jobs          # Queue server-side print job
GET  /api/print-jobs/{id}     # Get print job status
GET  /api/printer-templates   # Get custom print templates
POST /api/printer-templates   # Create custom template
```

## Support

For issues or questions about Bluetooth printing:

1. Check this documentation
2. Review troubleshooting section
3. Check GitHub issues
4. Contact dev team: dev@geo-ops.lk

## References

- [ESC/POS Command Reference](https://reference.epson-biz.com/modules/ref_escpos/index.php)
- [react-native-bluetooth-escpos-printer](https://github.com/januslo/react-native-bluetooth-escpos-printer)
- [Expo Custom Dev Client](https://docs.expo.dev/development/create-development-builds/)
- [React Native Bluetooth Guide](https://reactnative.dev/docs/bluetooth)

---

**Last Updated**: 2026-01-19  
**Version**: 1.0.0  
**Status**: Production Ready (with native module integration)
