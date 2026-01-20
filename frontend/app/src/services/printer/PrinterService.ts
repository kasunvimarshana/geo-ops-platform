/**
 * Bluetooth Thermal Printer Service
 * 
 * Manages Bluetooth connection to ESC/POS thermal printers
 * and handles printing of invoices, receipts, and job summaries.
 */

import { BleManager, Device, Characteristic } from 'react-native-ble-plx';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { EscPosBuilder } from '../../utils/escpos/EscPosBuilder';
import type { Invoice, Payment, Job, PrintJob, PrinterStatus, InvoiceItem } from '../../types';

const PRINTER_SERVICE_UUID = '000018f0-0000-1000-8000-00805f9b34fb';
const PRINTER_CHARACTERISTIC_UUID = '00002af1-0000-1000-8000-00805f9b34fb';
const SAVED_PRINTER_KEY = 'saved_printer_device';

export class PrinterService {
  private bleManager: BleManager;
  private connectedDevice: Device | null = null;
  private printQueue: PrintJob[] = [];
  private isProcessingQueue = false;

  constructor() {
    this.bleManager = new BleManager();
  }

  /**
   * Initialize the printer service
   */
  async initialize(): Promise<void> {
    const state = await this.bleManager.state();
    if (state !== 'PoweredOn') {
      throw new Error('Bluetooth is not powered on');
    }
  }

  /**
   * Scan for nearby Bluetooth devices
   */
  async scanDevices(timeout: number = 10000): Promise<Device[]> {
    const devices: Device[] = [];
    const deviceMap = new Map<string, Device>();

    return new Promise((resolve, reject) => {
      const timeoutId = setTimeout(() => {
        this.bleManager.stopDeviceScan();
        resolve(Array.from(deviceMap.values()));
      }, timeout);

      this.bleManager.startDeviceScan(null, null, (error, device) => {
        if (error) {
          clearTimeout(timeoutId);
          this.bleManager.stopDeviceScan();
          reject(error);
          return;
        }

        if (device && device.name && !deviceMap.has(device.id)) {
          // Filter for likely printer devices
          const deviceName = device.name.toLowerCase();
          if (
            deviceName.includes('printer') ||
            deviceName.includes('pos') ||
            deviceName.includes('rpp') ||
            deviceName.includes('mpt') ||
            deviceName.includes('thermal')
          ) {
            deviceMap.set(device.id, device);
          }
        }
      });
    });
  }

  /**
   * Connect to a Bluetooth device
   */
  async connectToDevice(deviceId: string): Promise<void> {
    try {
      // Disconnect existing device
      if (this.connectedDevice) {
        await this.disconnectDevice();
      }

      // Connect to new device
      const device = await this.bleManager.connectToDevice(deviceId);
      await device.discoverAllServicesAndCharacteristics();

      this.connectedDevice = device;

      // Save device for auto-reconnect
      await AsyncStorage.setItem(SAVED_PRINTER_KEY, deviceId);

      console.log(`Connected to printer: ${device.name}`);
    } catch (error) {
      console.error('Failed to connect to device:', error);
      throw error;
    }
  }

  /**
   * Disconnect from current device
   */
  async disconnectDevice(): Promise<void> {
    if (this.connectedDevice) {
      await this.bleManager.cancelDeviceConnection(this.connectedDevice.id);
      this.connectedDevice = null;
    }
  }

  /**
   * Check if printer is connected
   */
  async isPrinterConnected(): Promise<boolean> {
    if (!this.connectedDevice) {
      return false;
    }

    try {
      const isConnected = await this.connectedDevice.isConnected();
      return isConnected;
    } catch {
      return false;
    }
  }

  /**
   * Get saved printer device ID
   */
  async getSavedPrinterDevice(): Promise<string | null> {
    return await AsyncStorage.getItem(SAVED_PRINTER_KEY);
  }

  /**
   * Auto-reconnect to saved printer
   */
  async autoReconnect(): Promise<boolean> {
    try {
      const savedDeviceId = await this.getSavedPrinterDevice();
      if (savedDeviceId) {
        await this.connectToDevice(savedDeviceId);
        return true;
      }
      return false;
    } catch {
      return false;
    }
  }

  /**
   * Send raw data to printer
   */
  private async sendToPrinter(data: Buffer): Promise<void> {
    if (!this.connectedDevice) {
      throw new Error('No printer connected');
    }

    try {
      // Convert buffer to base64
      const base64Data = data.toString('base64');

      // Write to characteristic in chunks (max 20 bytes per write)
      const chunkSize = 20;
      const chunks = [];

      for (let i = 0; i < data.length; i += chunkSize) {
        chunks.push(data.slice(i, i + chunkSize));
      }

      for (const chunk of chunks) {
        await this.connectedDevice.writeCharacteristicWithoutResponseForService(
          PRINTER_SERVICE_UUID,
          PRINTER_CHARACTERISTIC_UUID,
          chunk.toString('base64')
        );
        // Small delay between chunks
        await new Promise(resolve => setTimeout(resolve, 50));
      }
    } catch (error) {
      console.error('Failed to send data to printer:', error);
      throw error;
    }
  }

  /**
   * Print invoice
   */
  async printInvoice(invoice: Invoice): Promise<void> {
    const builder = new EscPosBuilder();

    builder
      .initialize()
      .align('center')
      .size(2, 2)
      .bold()
      .text(invoice.organization_name || 'GEO OPS PLATFORM')
      .boldOff()
      .size(1, 1)
      .feed(1)
      .text('=====================================')
      .feed(1)
      .align('left')
      .text(`Invoice #: ${invoice.invoice_number}`)
      .text(`Date: ${new Date(invoice.invoice_date).toLocaleDateString()}`)
      .text(`Customer: ${invoice.customer_name}`)
      .text(`Phone: ${invoice.customer_phone}`)
      .feed(1)
      .text('-------------------------------------')
      .text('Description         Qty    Amount')
      .text('-------------------------------------');

    // Add line items
    invoice.items?.forEach((item: InvoiceItem) => {
      builder.text(
        `${item.description.substring(0, 20).padEnd(20)}${item.quantity.toString().padStart(3)}${item.total
          .toFixed(2)
          .padStart(10)}`
      );
    });

    builder
      .text('-------------------------------------')
      .text(`Subtotal:                ${invoice.subtotal.toFixed(2).padStart(10)}`)
      .text(`Tax (${invoice.tax_rate || 0}%):${invoice.tax_amount.toFixed(2).padStart(20)}`)
      .text('-------------------------------------')
      .bold()
      .text(`TOTAL:          ${invoice.currency || 'LKR'} ${invoice.total_amount.toFixed(2)}`)
      .boldOff()
      .text('-------------------------------------')
      .text(`Payment Status: ${invoice.status.toUpperCase()}`)
      .text(`Balance: ${invoice.currency || 'LKR'} ${invoice.balance.toFixed(2)}`)
      .feed(1)
      .align('center')
      .text('Thank You!')
      .feed(1)
      .qr(invoice.invoice_number)
      .feed(3)
      .cut();

    const data = builder.build();
    await this.sendToPrinter(data);
  }

  /**
   * Print payment receipt
   */
  async printReceipt(payment: Payment): Promise<void> {
    const builder = new EscPosBuilder();

    builder
      .initialize()
      .align('center')
      .size(2, 2)
      .bold()
      .text('PAYMENT RECEIPT')
      .boldOff()
      .size(1, 1)
      .feed(1)
      .text('=====================================')
      .feed(1)
      .align('left')
      .text(`Receipt #: ${payment.payment_number}`)
      .text(`Date: ${new Date(payment.payment_date).toLocaleDateString()}`)
      .text(`Invoice #: ${payment.invoice_number || 'N/A'}`)
      .text(`Customer: ${payment.customer_name}`)
      .feed(1)
      .text('-------------------------------------')
      .bold()
      .text(`Amount Paid: LKR ${payment.amount.toFixed(2)}`)
      .boldOff()
      .text(`Payment Method: ${payment.payment_method.replace('_', ' ').toUpperCase()}`)
      .text(`Received By: ${payment.received_by}`)
      .feed(1)
      .align('center')
      .text('Thank You!')
      .feed(1)
      .qr(payment.payment_number)
      .feed(3)
      .cut();

    const data = builder.build();
    await this.sendToPrinter(data);
  }

  /**
   * Print job summary
   */
  async printJobSummary(job: Job): Promise<void> {
    const builder = new EscPosBuilder();

    builder
      .initialize()
      .align('center')
      .size(2, 2)
      .bold()
      .text('JOB COMPLETION SUMMARY')
      .boldOff()
      .size(1, 1)
      .feed(1)
      .text('=====================================')
      .feed(1)
      .align('left')
      .text(`Job #: ${job.job_number}`)
      .text(`Date: ${new Date(job.scheduled_date).toLocaleDateString()}`)
      .text(`Driver: ${job.driver_name}`)
      .text(`Machine: ${job.machine_name}`)
      .feed(1)
      .text('-------------------------------------')
      .text(`Customer: ${job.customer_name}`)
      .text(`Location: ${job.location_name}`)
      .text(`Job Type: ${job.job_type}`)
      .text(`Area: ${job.area_measured || 'N/A'}`)
      .feed(1)
      .text('-------------------------------------')
      .text(`Start Time: ${job.start_time || 'N/A'}`)
      .text(`End Time: ${job.end_time || 'N/A'}`)
      .text(`Duration: ${job.duration || 'N/A'}`)
      .feed(1)
      .text('-------------------------------------')
      .bold()
      .text(`Status: ${job.status.toUpperCase()}`)
      .boldOff()
      .feed(1)
      .text('-------------------------------------')
      .text('Driver Signature: ______________')
      .feed(2)
      .text('Customer Signature: ______________')
      .feed(1)
      .align('center')
      .feed(3)
      .cut();

    const data = builder.build();
    await this.sendToPrinter(data);
  }

  /**
   * Add job to print queue
   */
  async addToQueue(printJob: PrintJob): Promise<void> {
    this.printQueue.push(printJob);
    await this.savePrintQueue();

    // Auto-process if not already processing
    if (!this.isProcessingQueue) {
      await this.processQueue();
    }
  }

  /**
   * Process print queue
   */
  async processQueue(): Promise<void> {
    if (this.isProcessingQueue || this.printQueue.length === 0) {
      return;
    }

    this.isProcessingQueue = true;

    while (this.printQueue.length > 0) {
      const job = this.printQueue[0];

      try {
        // Ensure connected
        if (!(await this.isPrinterConnected())) {
          await this.autoReconnect();
        }

        // Print based on type
        switch (job.type) {
          case 'invoice':
            await this.printInvoice(job.data);
            break;
          case 'receipt':
            await this.printReceipt(job.data);
            break;
          case 'job_summary':
            await this.printJobSummary(job.data);
            break;
        }

        // Remove from queue on success
        this.printQueue.shift();
        await this.savePrintQueue();
      } catch (error) {
        console.error('Failed to print job:', error);

        // Increment retry count
        job.retryCount = (job.retryCount || 0) + 1;

        // Remove if max retries exceeded
        if (job.retryCount >= 3) {
          console.error('Max retries exceeded for print job, removing from queue');
          this.printQueue.shift();
          await this.savePrintQueue();
        } else {
          // Wait before retry
          await new Promise(resolve => setTimeout(resolve, 5000));
        }
      }
    }

    this.isProcessingQueue = false;
  }

  /**
   * Save print queue to storage
   */
  private async savePrintQueue(): Promise<void> {
    try {
      await AsyncStorage.setItem('print_queue', JSON.stringify(this.printQueue));
    } catch (error) {
      console.error('Failed to save print queue:', error);
    }
  }

  /**
   * Load print queue from storage
   */
  async loadPrintQueue(): Promise<void> {
    try {
      const queueData = await AsyncStorage.getItem('print_queue');
      if (queueData) {
        this.printQueue = JSON.parse(queueData);
      }
    } catch (error) {
      console.error('Failed to load print queue:', error);
    }
  }

  /**
   * Get current printer status
   */
  async getStatus(): Promise<PrinterStatus> {
    const isConnected = await this.isPrinterConnected();

    return {
      isConnected,
      deviceName: this.connectedDevice?.name || null,
      queueLength: this.printQueue.length,
      isProcessing: this.isProcessingQueue,
    };
  }

  /**
   * Test print - prints a test page
   */
  async testPrint(): Promise<void> {
    const builder = new EscPosBuilder();

    builder
      .initialize()
      .align('center')
      .size(2, 2)
      .bold()
      .text('TEST PRINT')
      .boldOff()
      .size(1, 1)
      .feed(1)
      .text('=====================================')
      .feed(1)
      .text('If you can read this, your printer')
      .text('is working correctly!')
      .feed(1)
      .text('Date: ' + new Date().toLocaleString())
      .feed(1)
      .text('=====================================')
      .feed(3)
      .cut();

    const data = builder.build();
    await this.sendToPrinter(data);
  }

  /**
   * Cleanup and destroy
   */
  async destroy(): Promise<void> {
    if (this.connectedDevice) {
      await this.disconnectDevice();
    }
    this.bleManager.destroy();
  }
}

// Singleton instance
let printerServiceInstance: PrinterService | null = null;

export const getPrinterService = (): PrinterService => {
  if (!printerServiceInstance) {
    printerServiceInstance = new PrinterService();
  }
  return printerServiceInstance;
};
