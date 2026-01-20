# Bluetooth Printer Integration Examples

This document shows how to integrate Bluetooth printer functionality into existing screens.

## Example 1: Adding Print Button to Job Detail Screen

```typescript
// In JobDetailScreen.tsx
import { PrintButton } from '../../../shared/components/printer';
import { usePrinterStore } from '../../../store/printerStore';

export const JobDetailScreen: React.FC<JobDetailScreenProps> = ({ route, navigation }) => {
  const { printJobSummary } = usePrinterStore();
  const { currentJob } = useJobsStore();

  const handlePrintJobSummary = async () => {
    if (!currentJob) return;

    const jobData = {
      jobNumber: currentJob.job_number || `JOB-${currentJob.id}`,
      customerName: currentJob.customer_name,
      driver: currentJob.driver_name || 'N/A',
      date: new Date(currentJob.scheduled_date).toLocaleDateString(),
      landArea: currentJob.land_area || 0,
      areaUnit: 'acres',
      location: currentJob.location || 'N/A',
      status: currentJob.status,
      notes: currentJob.description || '',
    };

    try {
      await printJobSummary(jobData);
      Alert.alert('Success', 'Job summary printed successfully');
    } catch (error) {
      Alert.alert('Error', 'Failed to print job summary');
    }
  };

  return (
    <ScrollView>
      {/* Existing job details */}
      
      {/* Add Print Button */}
      <View style={styles.actions}>
        <PrintButton
          label="Print Job Summary"
          onPress={handlePrintJobSummary}
          variant="primary"
        />
      </View>
    </ScrollView>
  );
};
```

## Example 2: Adding Print Button to Invoice Screen

```typescript
// In InvoiceDetailScreen.tsx (when created)
import { PrintButton } from '../../../shared/components/printer';
import { usePrinterStore } from '../../../store/printerStore';

export const InvoiceDetailScreen: React.FC = ({ invoice }) => {
  const { printInvoice, isConnected } = usePrinterStore();

  const handlePrint = async () => {
    const printData = {
      invoiceNumber: invoice.invoice_number,
      customerName: invoice.customer_name,
      customerPhone: invoice.customer_phone,
      date: new Date(invoice.issued_at).toLocaleDateString(),
      items: [
        {
          description: invoice.description || 'Field service',
          quantity: 1,
          price: invoice.subtotal,
        },
      ],
      subtotal: invoice.subtotal,
      tax: invoice.tax_amount,
      discount: invoice.discount_amount,
      total: invoice.total_amount,
      currency: invoice.currency || 'LKR',
      notes: invoice.notes,
    };

    try {
      // Will use Bluetooth if connected, otherwise PDF
      await printInvoice(printData, !isConnected);
      Alert.alert('Success', 'Invoice printed successfully');
    } catch (error) {
      Alert.alert('Error', 'Failed to print invoice');
    }
  };

  return (
    <View>
      {/* Invoice details */}
      
      <PrintButton
        label={isConnected ? "Print to Printer" : "Generate PDF"}
        onPress={handlePrint}
        variant="primary"
      />
    </View>
  );
};
```

## Example 3: Adding Printer Status to Header

```typescript
// In MainNavigator.tsx or any screen
import { PrinterStatus } from '../shared/components/printer';

const ScreenWithPrinterStatus = () => {
  const navigation = useNavigation();

  return (
    <View>
      <PrinterStatus
        compact
        onPress={() => navigation.navigate('PrinterTab')}
      />
      
      {/* Rest of screen content */}
    </View>
  );
};
```

## Example 4: Printing Receipt for Payment

```typescript
// In PaymentScreen.tsx (when created)
import { usePrinterStore } from '../store/printerStore';

export const PaymentScreen: React.FC = () => {
  const { printReceipt } = usePrinterStore();

  const handlePaymentComplete = async (payment: Payment) => {
    const receiptData = {
      receiptNumber: `RCP-${payment.id}`,
      customerName: payment.customer_name,
      date: new Date().toLocaleDateString(),
      amount: payment.amount,
      paymentMethod: payment.payment_method,
      currency: payment.currency || 'LKR',
      notes: payment.notes || 'Thank you for your payment',
    };

    try {
      await printReceipt(receiptData);
      Alert.alert('Success', 'Receipt printed');
    } catch (error) {
      // Silently fail or show error
      console.error('Receipt print failed:', error);
    }
  };

  return (
    <View>
      {/* Payment form */}
    </View>
  );
};
```

## Example 5: Bulk Printing with Queue

```typescript
// Printing multiple documents at once
import { printQueueService } from '../shared/services/printer';
import { usePrinterStore } from '../store/printerStore';

export const BulkPrintScreen: React.FC = () => {
  const { processQueue, isConnected } = usePrinterStore();

  const handleBulkPrint = async (jobs: Job[]) => {
    // Add all jobs to queue
    for (const job of jobs) {
      const jobData = {
        jobNumber: `JOB-${job.id}`,
        customerName: job.customer_name,
        // ... other fields
      };

      await printQueueService.addJob({
        type: 'job_summary',
        data: jobData,
      });
    }

    // Process queue if connected
    if (isConnected) {
      await processQueue();
      Alert.alert('Success', `Printing ${jobs.length} job summaries`);
    } else {
      Alert.alert(
        'Printer Not Connected',
        'Jobs added to queue. Connect printer to print.'
      );
    }
  };

  return (
    <View>
      <Button title="Print All Jobs" onPress={() => handleBulkPrint(selectedJobs)} />
    </View>
  );
};
```

## Example 6: Custom ESC/POS Commands

```typescript
// For advanced users who want custom print formatting
import { EscPosBuilder } from '../shared/services/printer';
import { bluetoothPrinterService } from '../shared/services/printer';

const printCustomReceipt = async () => {
  const builder = new EscPosBuilder();
  
  const commands = builder
    .align('center')
    .bold(true)
    .size('double')
    .textLn('MY COMPANY')
    .size('normal')
    .bold(false)
    .textLn('123 Farm Road, Colombo')
    .textLn('Tel: +94 123 456 789')
    .feed(1)
    .separator()
    .align('left')
    .textLn('Date: ' + new Date().toLocaleDateString())
    .textLn('Customer: John Doe')
    .separator()
    .textLn('Services:')
    .textLn('  Land Plowing - 2.5 acres')
    .align('right')
    .textLn('LKR 25,000.00')
    .align('left')
    .separator()
    .bold(true)
    .textLn('TOTAL: LKR 25,000.00')
    .bold(false)
    .feed(2)
    .align('center')
    .textLn('Thank you for your business!')
    .textLn('Visit us at www.mycompany.lk')
    .feed(3)
    .cut()
    .build();

  await bluetoothPrinterService.printRaw(commands);
};
```

## Example 7: Monitoring Print Queue

```typescript
// Display print queue status in a component
import { usePrinterStore } from '../store/printerStore';

export const PrintQueueStatus: React.FC = () => {
  const { queueStats, loadQueueStats } = usePrinterStore();

  useEffect(() => {
    loadQueueStats();
    const interval = setInterval(loadQueueStats, 30000); // Refresh every 30s
    return () => clearInterval(interval);
  }, []);

  if (queueStats.total === 0) {
    return null;
  }

  return (
    <View style={styles.queueStatus}>
      <Text>Print Queue:</Text>
      {queueStats.pending > 0 && (
        <Text style={styles.pending}>
          {queueStats.pending} pending
        </Text>
      )}
      {queueStats.failed > 0 && (
        <Text style={styles.failed}>
          {queueStats.failed} failed
        </Text>
      )}
    </View>
  );
};
```

## Best Practices

### 1. Always Handle Errors
```typescript
try {
  await printInvoice(data);
} catch (error) {
  // Log error
  console.error('Print failed:', error);
  
  // Offer PDF as alternative
  Alert.alert(
    'Print Failed',
    'Would you like to generate a PDF instead?',
    [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Generate PDF', onPress: () => printInvoice(data, true) }
    ]
  );
}
```

### 2. Check Connection Before Printing
```typescript
const { isConnected, printInvoice } = usePrinterStore();

const handlePrint = async () => {
  if (!isConnected) {
    // Automatically use PDF
    await printInvoice(data, true);
    return;
  }
  
  // Use Bluetooth
  await printInvoice(data);
};
```

### 3. Show Loading States
```typescript
const [isPrinting, setIsPrinting] = useState(false);

const handlePrint = async () => {
  setIsPrinting(true);
  try {
    await printInvoice(data);
  } finally {
    setIsPrinting(false);
  }
};

return (
  <PrintButton
    label="Print"
    onPress={handlePrint}
    loading={isPrinting}
  />
);
```

### 4. Use Queue for Unreliable Connections
```typescript
// Add to queue instead of direct printing
import { printQueueService } from '../shared/services/printer';

const handlePrint = async () => {
  // Add to queue - will be processed when printer connects
  await printQueueService.addJob({
    type: 'invoice',
    data: invoiceData,
  });
  
  // Try to process immediately if connected
  if (isConnected) {
    await processQueue();
  } else {
    Alert.alert(
      'Added to Queue',
      'Document will print when printer connects'
    );
  }
};
```

## Testing Checklist

- [ ] Test Bluetooth device discovery
- [ ] Test connection to printer
- [ ] Test printing invoice
- [ ] Test printing receipt
- [ ] Test printing job summary
- [ ] Test print queue with offline mode
- [ ] Test PDF fallback when printer unavailable
- [ ] Test retry mechanism for failed prints
- [ ] Test disconnection handling
- [ ] Test multiple print jobs in sequence
- [ ] Test with different printer models
- [ ] Test on both Android and iOS devices
