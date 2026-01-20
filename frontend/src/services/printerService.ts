/**
 * Bluetooth Thermal Printer Service
 *
 * Provides ESC/POS compatible thermal printer support with:
 * - Device discovery and pairing
 * - Connection management with retry logic
 * - Invoice, receipt, and job summary printing
 * - Offline print queue with persistent storage
 * - Graceful fallback to PDF generation
 *
 * Architecture: Clean abstraction layer following SOLID principles
 */

import { Platform } from 'react-native';
import * as FileSystem from 'expo-file-system';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Types
export interface BluetoothDevice {
  address: string;
  name: string;
  paired: boolean;
}

export interface PrintJob {
  id: string;
  type: 'invoice' | 'receipt' | 'job_summary';
  data: any;
  timestamp: number;
  status: 'pending' | 'printing' | 'completed' | 'failed';
  retryCount: number;
}

export interface PrinterStatus {
  connected: boolean;
  deviceName: string | null;
  deviceAddress: string | null;
  paperStatus: 'ok' | 'low' | 'out' | 'unknown';
  batteryLevel: number | null;
}

export interface InvoicePrintData {
  invoiceNumber: string;
  customerName: string;
  customerAddress?: string;
  date: string;
  dueDate?: string;
  items: Array<{
    description: string;
    quantity?: number;
    rate?: number;
    amount: number;
  }>;
  subtotal: number;
  tax?: number;
  total: number;
  paymentStatus: string;
  organizationName: string;
  organizationAddress?: string;
  organizationPhone?: string;
  notes?: string;
}

export interface ReceiptPrintData {
  receiptNumber: string;
  customerName: string;
  date: string;
  amount: number;
  paymentMethod: string;
  reference?: string;
  organizationName: string;
}

export interface JobSummaryPrintData {
  jobNumber: string;
  customerName: string;
  driverName: string;
  machineName: string;
  date: string;
  location: string;
  area: number;
  areaUnit: string;
  serviceType: string;
  status: string;
  organizationName: string;
}

// Print Queue Storage Keys
const PRINT_QUEUE_KEY = '@geo-ops_print_queue';
const PRINTER_SETTINGS_KEY = '@geo-ops_printer_settings';

/**
 * Bluetooth Printer Service
 *
 * Note: This is a mock implementation for Expo. In production with bare React Native:
 * - Install: react-native-bluetooth-escpos-printer
 * - Or use: react-native-thermal-receipt-printer
 * - Implement native Bluetooth Classic/BLE integration
 *
 * For Expo managed workflow, consider:
 * - Using custom dev client with config plugin
 * - Or building standalone app with expo-dev-client
 */
class BluetoothPrinterService {
  private connectedDevice: BluetoothDevice | null = null;
  private printQueue: PrintJob[] = [];
  private isProcessingQueue: boolean = false;

  /**
   * Initialize printer service
   * Loads saved settings and pending print queue
   */
  async initialize(): Promise<void> {
    await this.loadPrintQueue();
    await this.loadPrinterSettings();
    this.startQueueProcessor();
  }

  /**
   * Discover nearby Bluetooth devices
   * @returns List of available Bluetooth devices
   */
  async discoverDevices(): Promise<BluetoothDevice[]> {
    console.log('[PrinterService] Discovering Bluetooth devices...');

    // Mock implementation - replace with actual Bluetooth scanning
    // In production: Use BluetoothManager.scanDevices()
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve([
          { address: '00:11:22:33:44:55', name: 'Thermal Printer 1', paired: true },
          { address: '00:11:22:33:44:66', name: 'POS Printer', paired: false },
        ]);
      }, 2000);
    });
  }

  /**
   * Connect to a Bluetooth printer
   * @param device Device to connect to
   */
  async connect(device: BluetoothDevice): Promise<boolean> {
    try {
      console.log(`[PrinterService] Connecting to ${device.name}...`);

      // Mock implementation - replace with actual Bluetooth connection
      // In production: Use BluetoothManager.connect(device.address)
      this.connectedDevice = device;
      await this.savePrinterSettings(device);

      console.log(`[PrinterService] Connected to ${device.name}`);
      return true;
    } catch (error) {
      console.error('[PrinterService] Connection failed:', error);
      return false;
    }
  }

  /**
   * Disconnect from current printer
   */
  async disconnect(): Promise<void> {
    console.log('[PrinterService] Disconnecting...');
    // In production: Use BluetoothManager.disconnect()
    this.connectedDevice = null;
  }

  /**
   * Get current printer status
   */
  async getStatus(): Promise<PrinterStatus> {
    if (!this.connectedDevice) {
      return {
        connected: false,
        deviceName: null,
        deviceAddress: null,
        paperStatus: 'unknown',
        batteryLevel: null,
      };
    }

    // Mock implementation - In production, query actual printer status
    return {
      connected: true,
      deviceName: this.connectedDevice.name,
      deviceAddress: this.connectedDevice.address,
      paperStatus: 'ok',
      batteryLevel: 85,
    };
  }

  /**
   * Print an invoice
   * @param data Invoice data
   */
  async printInvoice(data: InvoicePrintData): Promise<boolean> {
    const job: PrintJob = {
      id: `inv_${Date.now()}`,
      type: 'invoice',
      data,
      timestamp: Date.now(),
      status: 'pending',
      retryCount: 0,
    };

    return this.addToQueue(job);
  }

  /**
   * Print a receipt
   * @param data Receipt data
   */
  async printReceipt(data: ReceiptPrintData): Promise<boolean> {
    const job: PrintJob = {
      id: `rec_${Date.now()}`,
      type: 'receipt',
      data,
      timestamp: Date.now(),
      status: 'pending',
      retryCount: 0,
    };

    return this.addToQueue(job);
  }

  /**
   * Print a job summary
   * @param data Job summary data
   */
  async printJobSummary(data: JobSummaryPrintData): Promise<boolean> {
    const job: PrintJob = {
      id: `job_${Date.now()}`,
      type: 'job_summary',
      data,
      timestamp: Date.now(),
      status: 'pending',
      retryCount: 0,
    };

    return this.addToQueue(job);
  }

  /**
   * Add print job to queue
   */
  private async addToQueue(job: PrintJob): Promise<boolean> {
    this.printQueue.push(job);
    await this.savePrintQueue();

    // Process queue immediately if not already processing
    if (!this.isProcessingQueue) {
      this.processQueue();
    }

    return true;
  }

  /**
   * Process print queue
   */
  private async processQueue(): Promise<void> {
    if (this.isProcessingQueue || this.printQueue.length === 0) {
      return;
    }

    this.isProcessingQueue = true;

    while (this.printQueue.length > 0) {
      const job = this.printQueue[0];

      try {
        job.status = 'printing';
        await this.savePrintQueue();
        await this.executePrintJob(job);

        job.status = 'completed';
        this.printQueue.shift(); // Remove completed job
        await this.savePrintQueue();
      } catch (error) {
        console.error('[PrinterService] Print job failed:', error);

        // Revert status to pending for retry or mark as failed
        job.retryCount++;
        if (job.retryCount >= 3) {
          job.status = 'failed';
          this.printQueue.shift(); // Remove failed job after 3 retries

          // Fallback to PDF generation
          await this.fallbackToPDF(job);
        } else {
          job.status = 'pending'; // Revert to pending for retry
        }

        await this.savePrintQueue();

        // Wait before retry
        await new Promise((resolve) => setTimeout(resolve, 5000));
      }
    }

    this.isProcessingQueue = false;
  }

  /**
   * Execute a print job
   */
  private async executePrintJob(job: PrintJob): Promise<void> {
    if (!this.connectedDevice) {
      throw new Error('No printer connected');
    }

    console.log(`[PrinterService] Printing ${job.type} (${job.id})...`);

    switch (job.type) {
      case 'invoice':
        await this.printInvoiceESCPOS(job.data);
        break;
      case 'receipt':
        await this.printReceiptESCPOS(job.data);
        break;
      case 'job_summary':
        await this.printJobSummaryESCPOS(job.data);
        break;
    }

    console.log(`[PrinterService] Print completed: ${job.id}`);
  }

  /**
   * Print invoice using ESC/POS commands
   */
  private async printInvoiceESCPOS(data: InvoicePrintData): Promise<void> {
    // ESC/POS command builder
    const commands: string[] = [];

    // Initialize printer
    commands.push('\x1B\x40'); // ESC @ - Initialize

    // Organization header (centered, bold, double height)
    commands.push('\x1B\x61\x01'); // ESC a 1 - Center align
    commands.push('\x1B\x45\x01'); // ESC E 1 - Bold on
    commands.push('\x1D\x21\x11'); // GS ! 17 - Double height/width
    commands.push(data.organizationName + '\n');
    commands.push('\x1D\x21\x00'); // GS ! 0 - Normal size
    commands.push('\x1B\x45\x00'); // ESC E 0 - Bold off

    if (data.organizationAddress) {
      commands.push(data.organizationAddress + '\n');
    }
    if (data.organizationPhone) {
      commands.push('Tel: ' + data.organizationPhone + '\n');
    }

    commands.push('\x1B\x61\x00'); // ESC a 0 - Left align
    commands.push('--------------------------------\n');

    // Invoice header
    commands.push('\x1B\x45\x01'); // Bold
    commands.push('INVOICE\n');
    commands.push('\x1B\x45\x00');
    commands.push(`Invoice #: ${data.invoiceNumber}\n`);
    commands.push(`Date: ${data.date}\n`);
    if (data.dueDate) {
      commands.push(`Due Date: ${data.dueDate}\n`);
    }
    commands.push('--------------------------------\n');

    // Customer info
    commands.push(`Customer: ${data.customerName}\n`);
    if (data.customerAddress) {
      commands.push(`Address: ${data.customerAddress}\n`);
    }
    commands.push('--------------------------------\n');

    // Items
    commands.push('Item               Qty  Amount\n');
    commands.push('--------------------------------\n');
    for (const item of data.items) {
      const desc = item.description.substring(0, 18).padEnd(18);
      const qty = item.quantity ? String(item.quantity).padStart(3) : '   ';
      const amt = String(item.amount.toFixed(2)).padStart(7);
      commands.push(`${desc} ${qty} ${amt}\n`);
    }
    commands.push('--------------------------------\n');

    // Totals
    commands.push(`Subtotal:        ${String(data.subtotal.toFixed(2)).padStart(15)}\n`);
    if (data.tax) {
      commands.push(`Tax:             ${String(data.tax.toFixed(2)).padStart(15)}\n`);
    }
    commands.push('\x1B\x45\x01'); // Bold
    commands.push(`TOTAL:           ${String(data.total.toFixed(2)).padStart(15)}\n`);
    commands.push('\x1B\x45\x00');
    commands.push('--------------------------------\n');

    // Payment status
    commands.push(`Status: ${data.paymentStatus}\n`);
    commands.push('--------------------------------\n');

    if (data.notes) {
      commands.push(`Notes: ${data.notes}\n`);
      commands.push('--------------------------------\n');
    }

    // Footer
    commands.push('\x1B\x61\x01'); // Center align
    commands.push('Thank you for your business!\n\n\n');

    // Cut paper
    commands.push('\x1D\x56\x00'); // GS V 0 - Full cut

    // Mock print execution
    // In production: Use BluetoothEscposPrinter.printRawData(commands.join(''))
    await new Promise((resolve) => setTimeout(resolve, 1000));
  }

  /**
   * Print receipt using ESC/POS commands
   */
  private async printReceiptESCPOS(data: ReceiptPrintData): Promise<void> {
    const commands: string[] = [];

    commands.push('\x1B\x40'); // Initialize
    commands.push('\x1B\x61\x01'); // Center align
    commands.push('\x1B\x45\x01'); // Bold
    commands.push(data.organizationName + '\n');
    commands.push('\x1B\x45\x00');
    commands.push('\x1B\x61\x00'); // Left align
    commands.push('--------------------------------\n');
    commands.push('PAYMENT RECEIPT\n');
    commands.push('--------------------------------\n');
    commands.push(`Receipt #: ${data.receiptNumber}\n`);
    commands.push(`Date: ${data.date}\n`);
    commands.push(`Customer: ${data.customerName}\n`);
    commands.push('--------------------------------\n');
    commands.push('\x1B\x45\x01'); // Bold
    commands.push(`Amount Paid: ${data.amount.toFixed(2)}\n`);
    commands.push('\x1B\x45\x00');
    commands.push(`Method: ${data.paymentMethod}\n`);
    if (data.reference) {
      commands.push(`Reference: ${data.reference}\n`);
    }
    commands.push('--------------------------------\n');
    commands.push('\x1B\x61\x01'); // Center
    commands.push('Thank you!\n\n\n');
    commands.push('\x1D\x56\x00'); // Cut

    await new Promise((resolve) => setTimeout(resolve, 800));
  }

  /**
   * Print job summary using ESC/POS commands
   */
  private async printJobSummaryESCPOS(data: JobSummaryPrintData): Promise<void> {
    const commands: string[] = [];

    commands.push('\x1B\x40'); // Initialize
    commands.push('\x1B\x61\x01'); // Center
    commands.push('\x1B\x45\x01'); // Bold
    commands.push(data.organizationName + '\n');
    commands.push('\x1B\x45\x00');
    commands.push('\x1B\x61\x00'); // Left
    commands.push('--------------------------------\n');
    commands.push('JOB SUMMARY\n');
    commands.push('--------------------------------\n');
    commands.push(`Job #: ${data.jobNumber}\n`);
    commands.push(`Date: ${data.date}\n`);
    commands.push(`Customer: ${data.customerName}\n`);
    commands.push(`Driver: ${data.driverName}\n`);
    commands.push(`Machine: ${data.machineName}\n`);
    commands.push('--------------------------------\n');
    commands.push(`Service: ${data.serviceType}\n`);
    commands.push(`Location: ${data.location}\n`);
    commands.push(`Area: ${data.area} ${data.areaUnit}\n`);
    commands.push(`Status: ${data.status}\n`);
    commands.push('--------------------------------\n');
    commands.push('\x1B\x61\x01'); // Center
    commands.push('Job completed successfully\n\n\n');
    commands.push('\x1D\x56\x00'); // Cut

    await new Promise((resolve) => setTimeout(resolve, 800));
  }

  /**
   * Fallback to PDF generation when printing fails
   */
  private async fallbackToPDF(job: PrintJob): Promise<void> {
    console.log(`[PrinterService] Falling back to PDF for ${job.id}`);

    // Generate PDF and save to device
    // This would integrate with the existing PDF generation service
    // For now, just log the action

    // In production:
    // - Call backend API to generate PDF
    // - Download PDF using FileSystem
    // - Open PDF with sharing/viewing options
  }

  /**
   * Start background queue processor
   */
  private queueProcessorInterval: NodeJS.Timeout | null = null;

  private startQueueProcessor(): void {
    // Clear existing interval if any
    if (this.queueProcessorInterval) {
      clearInterval(this.queueProcessorInterval);
    }

    // Process queue every 10 seconds
    this.queueProcessorInterval = setInterval(() => {
      if (!this.isProcessingQueue && this.printQueue.length > 0) {
        this.processQueue();
      }
    }, 10000);
  }

  /**
   * Stop background queue processor
   */
  stopQueueProcessor(): void {
    if (this.queueProcessorInterval) {
      clearInterval(this.queueProcessorInterval);
      this.queueProcessorInterval = null;
    }
  }

  /**
   * Save print queue to persistent storage
   */
  private async savePrintQueue(): Promise<void> {
    try {
      await AsyncStorage.setItem(PRINT_QUEUE_KEY, JSON.stringify(this.printQueue));
    } catch (error) {
      console.error('[PrinterService] Failed to save print queue:', error);
    }
  }

  /**
   * Load print queue from persistent storage
   */
  private async loadPrintQueue(): Promise<void> {
    try {
      const data = await AsyncStorage.getItem(PRINT_QUEUE_KEY);
      if (data) {
        const parsed = JSON.parse(data);
        // Validate that parsed data is an array
        if (Array.isArray(parsed)) {
          this.printQueue = parsed;
          console.log(`[PrinterService] Loaded ${this.printQueue.length} queued print jobs`);
        } else {
          console.warn('[PrinterService] Invalid print queue data, resetting');
          this.printQueue = [];
        }
      }
    } catch (error) {
      console.error('[PrinterService] Failed to load print queue:', error);
      // Reset queue on parse error
      this.printQueue = [];
    }
  }

  /**
   * Save printer settings
   */
  private async savePrinterSettings(device: BluetoothDevice): Promise<void> {
    try {
      await AsyncStorage.setItem(PRINTER_SETTINGS_KEY, JSON.stringify(device));
    } catch (error) {
      console.error('[PrinterService] Failed to save printer settings:', error);
    }
  }

  /**
   * Load saved printer settings
   */
  private async loadPrinterSettings(): Promise<void> {
    try {
      const data = await AsyncStorage.getItem(PRINTER_SETTINGS_KEY);
      if (data) {
        const device = JSON.parse(data);
        // Validate device structure
        if (device && typeof device.address === 'string' && typeof device.name === 'string') {
          console.log(`[PrinterService] Found saved printer: ${device.name}`);
          // Optional: auto-reconnect to last printer
          // await this.connect(device);
        } else {
          console.warn('[PrinterService] Invalid printer settings data');
        }
      }
    } catch (error) {
      console.error('[PrinterService] Failed to load printer settings:', error);
    }
  }

  /**
   * Get pending print jobs count
   */
  getPendingJobsCount(): number {
    return this.printQueue.filter((job) => job.status === 'pending').length;
  }

  /**
   * Clear all failed jobs
   */
  async clearFailedJobs(): Promise<void> {
    this.printQueue = this.printQueue.filter((job) => job.status !== 'failed');
    await this.savePrintQueue();
  }

  /**
   * Get print queue
   */
  getQueue(): PrintJob[] {
    return [...this.printQueue];
  }
}

// Export singleton instance
export const printerService = new BluetoothPrinterService();
