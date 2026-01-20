# Bluetooth Portable Thermal Printer Integration

## Overview

This document details the Bluetooth thermal printer integration for the Geo Ops Platform, enabling direct printing of invoices, receipts, and job summaries from the React Native mobile application.

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                 Mobile Application Layer                     │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐  │
│  │  Print UI    │  │ Print Store  │  │ Print Queue UI   │  │
│  │  Components  │  │  (Zustand)   │  │                  │  │
│  └──────────────┘  └──────────────┘  └──────────────────┘  │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────┴─────────────────────────────────┐
│              Printer Service Abstraction Layer              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │         PrinterService (services/printer/)             │ │
│  │  - Device Discovery                                    │ │
│  │  - Connection Management                               │ │
│  │  - Print Job Queue                                     │ │
│  │  - Status Monitoring                                   │ │
│  └────────────────────────────────────────────────────────┘ │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────┴─────────────────────────────────┐
│                  ESC/POS Command Layer                       │
│  ┌────────────────────────────────────────────────────────┐ │
│  │         EscPosBuilder (utils/escpos/)                  │ │
│  │  - Text Formatting                                     │ │
│  │  - Alignment & Styles                                  │ │
│  │  - Barcode/QR Generation                               │ │
│  │  - Image Processing                                    │ │
│  │  - Paper Cutting                                       │ │
│  └────────────────────────────────────────────────────────┘ │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────┴─────────────────────────────────┐
│               Bluetooth Low Energy Layer                     │
│  ┌────────────────────────────────────────────────────────┐ │
│  │      react-native-ble-plx / expo-bluetooth             │ │
│  │  - Device Scanning                                     │ │
│  │  - Pairing & Connection                                │ │
│  │  - Data Transmission                                   │ │
│  │  - Error Handling                                      │ │
│  └────────────────────────────────────────────────────────┘ │
└───────────────────────────┬─────────────────────────────────┘
                            │
                  ┌─────────┴────────┐
                  │  Thermal Printer  │
                  │   (ESC/POS)      │
                  └──────────────────┘
```

## Features

### 1. Device Management
- **Discovery**: Scan for nearby Bluetooth thermal printers
- **Pairing**: Pair and save printer devices
- **Connection**: Automatic reconnection to saved printers
- **Status**: Real-time printer status monitoring

### 2. Print Document Types
- **Invoices**: Detailed customer invoices with line items
- **Receipts**: Payment receipts with transaction details
- **Job Summaries**: Field work job completion reports
- **Reports**: Daily/weekly summaries

### 3. Print Features
- **Text Formatting**: Bold, underline, different sizes
- **Alignment**: Left, center, right alignment
- **Barcodes**: Generate and print barcodes
- **QR Codes**: Print QR codes for invoice tracking
- **Images**: Print logos and signatures
- **Line Breaks**: Proper spacing and separators

### 4. Offline Support
- **Print Queue**: Queue prints when printer unavailable
- **Retry Logic**: Automatic retry with exponential backoff
- **Persistence**: Save failed prints to local storage
- **Background Sync**: Retry when connection restored

### 5. Fallback Mechanisms
- **PDF Generation**: Generate PDF when printer unavailable
- **Share Option**: Share generated PDF via email/WhatsApp
- **Cloud Backup**: Upload documents to backend

## Technology Stack

### React Native Packages
```json
{
  "react-native-ble-plx": "^3.1.2",
  "buffer": "^6.0.3",
  "expo-print": "^12.4.4",
  "expo-sharing": "^11.5.0"
}
```

### ESC/POS Command Support
- **Text Commands**: Initialize, font selection, styles
- **Formatting**: Character spacing, line spacing
- **Alignment**: Justification commands
- **Cutting**: Full/partial cut commands
- **Graphics**: Image/logo printing
- **Barcode**: Multiple barcode formats

## Implementation

### Printer Service Structure

```typescript
// services/printer/PrinterService.ts
class PrinterService {
  // Device Management
  async scanDevices(): Promise<BluetoothDevice[]>
  async connectToDevice(deviceId: string): Promise<void>
  async disconnectDevice(): Promise<void>
  
  // Printing
  async printInvoice(invoice: Invoice): Promise<void>
  async printReceipt(payment: Payment): Promise<void>
  async printJobSummary(job: Job): Promise<void>
  
  // Queue Management
  async addToQueue(printJob: PrintJob): Promise<void>
  async processQueue(): Promise<void>
  
  // Status
  async getStatus(): Promise<PrinterStatus>
  async isPrinterConnected(): Promise<boolean>
}
```

### ESC/POS Builder

```typescript
// utils/escpos/EscPosBuilder.ts
class EscPosBuilder {
  initialize(): this
  text(content: string): this
  bold(): this
  boldOff(): this
  underline(): this
  underlineOff(): this
  align(alignment: 'left' | 'center' | 'right'): this
  size(width: number, height: number): this
  feed(lines: number): this
  cut(): this
  qr(data: string): this
  barcode(data: string, type: string): this
  build(): Buffer
}
```

### Print Store (Zustand)

```typescript
// stores/printerStore.ts
interface PrinterStore {
  // State
  connectedDevice: BluetoothDevice | null
  printQueue: PrintJob[]
  isScanning: boolean
  isPrinting: boolean
  
  // Actions
  scanDevices: () => Promise<void>
  connectDevice: (deviceId: string) => Promise<void>
  disconnectDevice: () => Promise<void>
  printDocument: (document: PrintDocument) => Promise<void>
  retryFailedPrints: () => Promise<void>
}
```

## Print Document Formats

### Invoice Format
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
Land Ploughing     2.5 acres  25000
  @ 10000/acre
-------------------------------------
Subtotal:                    25000
Tax (0%):                        0
-------------------------------------
TOTAL:                LKR 25000
-------------------------------------
Payment Status: PAID
Payment Method: Cash
-------------------------------------
       Thank You!
=====================================
[QR CODE: INV-2024-001]
```

### Receipt Format
```
=====================================
       PAYMENT RECEIPT
=====================================
Receipt #: RCP-2024-001
Date: 2024-01-17 10:30 AM
Invoice #: INV-2024-001
Customer: John Doe
-------------------------------------
Amount Paid:          LKR 25000
Payment Method: Cash
Received By: Admin User
-------------------------------------
       Thank You!
=====================================
```

### Job Summary Format
```
=====================================
       JOB COMPLETION SUMMARY
=====================================
Job #: JOB-2024-001
Date: 2024-01-17
Driver: Driver Name
Machine: Tractor - ABC-1234
-------------------------------------
Customer: John Doe
Location: Farm Location
Job Type: Land Ploughing
Area: 2.5 acres
-------------------------------------
Start Time: 08:00 AM
End Time: 12:00 PM
Duration: 4 hours
-------------------------------------
Status: COMPLETED
-------------------------------------
Driver Signature: ______________
-------------------------------------
Customer Signature: ______________
=====================================
```

## ESC/POS Commands Reference

### Initialization
```
0x1B 0x40  // Initialize printer
```

### Text Formatting
```
0x1B 0x45 0x01  // Bold on
0x1B 0x45 0x00  // Bold off
0x1B 0x21 0x30  // Double size
0x1B 0x21 0x00  // Normal size
```

### Alignment
```
0x1B 0x61 0x00  // Left
0x1B 0x61 0x01  // Center
0x1B 0x61 0x02  // Right
```

### Feeding & Cutting
```
0x1B 0x64 0x03  // Feed 3 lines
0x1D 0x56 0x00  // Full cut
0x1D 0x56 0x01  // Partial cut
```

## Error Handling

### Connection Errors
- **Timeout**: Retry with exponential backoff
- **Device Not Found**: Show pairing instructions
- **Permission Denied**: Request Bluetooth permissions
- **Low Battery**: Warn user about device battery

### Print Errors
- **Paper Out**: Notify user to refill paper
- **Printer Busy**: Add to queue and retry
- **Data Transmission Failed**: Retry up to 3 times
- **Invalid Command**: Log error and fallback to PDF

## Fallback Strategy

```typescript
async function printDocument(document: Document) {
  try {
    // Try Bluetooth printing first
    await printerService.print(document);
  } catch (error) {
    console.warn('Bluetooth printing failed:', error);
    
    try {
      // Fallback to PDF generation
      const pdf = await generatePDF(document);
      await savePDF(pdf);
      await sharePDF(pdf);
    } catch (pdfError) {
      console.error('PDF generation failed:', pdfError);
      
      // Last resort: Save to sync queue
      await saveToSyncQueue({
        type: 'print',
        document,
        status: 'failed'
      });
    }
  }
}
```

## Security Considerations

1. **Pairing**: Require user confirmation for new devices
2. **Data**: Don't cache sensitive information in print queue
3. **Permissions**: Request Bluetooth permissions gracefully
4. **Encryption**: Use encrypted channels when possible

## Testing Strategy

1. **Unit Tests**: Test ESC/POS command generation
2. **Integration Tests**: Test printer connectivity
3. **Device Tests**: Test with actual thermal printers
4. **Error Tests**: Simulate error conditions
5. **Performance Tests**: Test queue processing

## Supported Printer Models

- Generic ESC/POS thermal printers (58mm/80mm)
- Zebra mobile printers
- Epson TM series
- Star Micronics TSP series
- Bixolon mobile printers

## Usage Examples

### Print Invoice
```typescript
import { usePrinterStore } from '@/stores/printerStore';

const { printDocument } = usePrinterStore();

await printDocument({
  type: 'invoice',
  data: invoiceData
});
```

### Scan and Connect
```typescript
const { scanDevices, connectDevice } = usePrinterStore();

const devices = await scanDevices();
await connectDevice(devices[0].id);
```

### Check Status
```typescript
const { isPrinterConnected } = usePrinterStore();

if (await isPrinterConnected()) {
  // Print document
} else {
  // Show connection dialog
}
```

## Performance Optimization

1. **Connection Pooling**: Keep connections alive
2. **Command Batching**: Batch ESC/POS commands
3. **Async Operations**: Use async/await for all operations
4. **Queue Processing**: Process queue in background
5. **Memory Management**: Clean up buffers after use

## Future Enhancements

1. **Cloud Printing**: Print via cloud service
2. **Remote Printing**: Print to office printers
3. **Template System**: Custom print templates
4. **Multi-Printer**: Support multiple printers
5. **Advanced Graphics**: Support for more complex layouts

---

**Status**: Ready for Implementation  
**Priority**: High (Key differentiator)  
**Estimated Effort**: 2-3 weeks  
**Dependencies**: Bluetooth permissions, ESC/POS library
