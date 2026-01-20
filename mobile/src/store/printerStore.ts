/**
 * Printer Store
 * Manages Bluetooth printer state and print queue
 */

import { create } from 'zustand';
import {
  bluetoothPrinterService,
  printQueueService,
  pdfService,
} from '../shared/services/printer';
import type {
  BluetoothDevice,
  PrintJob,
  InvoicePrintData,
  ReceiptPrintData,
  JobSummaryPrintData,
} from '../shared/services/printer';

interface PrinterState {
  // Bluetooth state
  connectedDevice: BluetoothDevice | null;
  availableDevices: BluetoothDevice[];
  isScanning: boolean;
  isConnecting: boolean;
  isConnected: boolean;

  // Print queue state
  printJobs: PrintJob[];
  isPrinting: boolean;
  queueStats: {
    total: number;
    pending: number;
    completed: number;
    failed: number;
  };

  // Error state
  error: string | null;

  // Bluetooth actions
  scanDevices: () => Promise<void>;
  connectDevice: (device: BluetoothDevice) => Promise<void>;
  disconnectDevice: () => Promise<void>;
  testPrint: () => Promise<void>;

  // Print actions
  printInvoice: (data: InvoicePrintData, useFallback?: boolean) => Promise<void>;
  printReceipt: (data: ReceiptPrintData, useFallback?: boolean) => Promise<void>;
  printJobSummary: (data: JobSummaryPrintData, useFallback?: boolean) => Promise<void>;

  // Queue actions
  loadPrintQueue: () => Promise<void>;
  processQueue: () => Promise<void>;
  retryJob: (jobId: string) => Promise<void>;
  deleteJob: (jobId: string) => Promise<void>;
  clearCompletedJobs: () => Promise<void>;
  loadQueueStats: () => Promise<void>;

  // Utility actions
  clearError: () => void;
  initialize: () => Promise<void>;
}

export const usePrinterStore = create<PrinterState>((set, get) => ({
  // Initial state
  connectedDevice: null,
  availableDevices: [],
  isScanning: false,
  isConnecting: false,
  isConnected: false,
  printJobs: [],
  isPrinting: false,
  queueStats: {
    total: 0,
    pending: 0,
    completed: 0,
    failed: 0,
  },
  error: null,

  // Initialize printer services
  initialize: async () => {
    try {
      await printQueueService.initialize();
      await get().loadPrintQueue();
      await get().loadQueueStats();

      // Check if there's a previously connected device
      const connectedDevice = bluetoothPrinterService.getConnectedDevice();
      if (connectedDevice) {
        set({
          connectedDevice,
          isConnected: true,
        });

        // Process queue if there are pending jobs
        const stats = await printQueueService.getStats();
        if (stats.pending > 0) {
          get().processQueue();
        }
      }
    } catch (error) {
      console.error('Error initializing printer store:', error);
      set({
        error: error instanceof Error ? error.message : 'Initialization failed',
      });
    }
  },

  // Scan for Bluetooth devices
  scanDevices: async () => {
    set({ isScanning: true, error: null });
    try {
      const devices = await bluetoothPrinterService.discoverDevices();
      set({
        availableDevices: devices,
        isScanning: false,
      });
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Failed to scan devices',
        isScanning: false,
      });
      throw error;
    }
  },

  // Connect to a device
  connectDevice: async (device: BluetoothDevice) => {
    set({ isConnecting: true, error: null });
    try {
      await bluetoothPrinterService.connect(device);
      set({
        connectedDevice: device,
        isConnected: true,
        isConnecting: false,
      });

      // Process pending print jobs
      await get().processQueue();
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Connection failed',
        isConnecting: false,
      });
      throw error;
    }
  },

  // Disconnect from device
  disconnectDevice: async () => {
    try {
      await bluetoothPrinterService.disconnect();
      set({
        connectedDevice: null,
        isConnected: false,
        error: null,
      });
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Disconnection failed',
      });
      throw error;
    }
  },

  // Test print
  testPrint: async () => {
    set({ error: null });
    try {
      if (!get().isConnected) {
        throw new Error('No printer connected');
      }
      await bluetoothPrinterService.testPrint();
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Test print failed',
      });
      throw error;
    }
  },

  /**
   * Helper: Handle Bluetooth print with fallback
   */
  _printWithFallback: async <T extends PrintData>(
    printFn: () => Promise<void>,
    pdfFn: () => Promise<string>,
    fileName: string,
    jobType: 'invoice' | 'receipt' | 'job_summary',
    data: T,
    useFallback: boolean = false
  ) => {
    const state = get();
    
    if (state.isConnected && !useFallback) {
      // Try Bluetooth printing
      try {
        await printFn();
      } catch (bluetoothError) {
        console.warn('Bluetooth printing failed, adding to queue:', bluetoothError);
        // Add to queue for retry
        await printQueueService.addJob({
          type: jobType,
          data,
        });
        await state.loadPrintQueue();
        await state.loadQueueStats();
        // Use PDF fallback if requested
        if (useFallback) {
          const pdfUri = await pdfFn();
          await pdfService.sharePDF(pdfUri, fileName);
        }
      }
    } else {
      // No printer connected or explicit fallback - use PDF
      const pdfUri = await pdfFn();
      await pdfService.sharePDF(pdfUri, fileName);
    }
  },

  // Print invoice
  printInvoice: async (data: InvoicePrintData, useFallback: boolean = false) => {
    set({ isPrinting: true, error: null });
    try {
      await get()._printWithFallback(
        () => bluetoothPrinterService.printInvoice(data),
        () => pdfService.generateInvoicePDF(data),
        `invoice_${data.invoiceNumber}.pdf`,
        'invoice',
        data,
        useFallback
      );
      set({ isPrinting: false });
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Print failed',
        isPrinting: false,
      });
      throw error;
    }
  },

  // Print receipt
  printReceipt: async (data: ReceiptPrintData, useFallback: boolean = false) => {
    set({ isPrinting: true, error: null });
    try {
      await get()._printWithFallback(
        () => bluetoothPrinterService.printReceipt(data),
        () => pdfService.generateReceiptPDF(data),
        `receipt_${data.receiptNumber}.pdf`,
        'receipt',
        data,
        useFallback
      );
      set({ isPrinting: false });
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Print failed',
        isPrinting: false,
      });
      throw error;
    }
  },

  // Print job summary
  printJobSummary: async (data: JobSummaryPrintData, useFallback: boolean = false) => {
    set({ isPrinting: true, error: null });
    try {
      await get()._printWithFallback(
        () => bluetoothPrinterService.printJobSummary(data),
        () => pdfService.generateJobSummaryPDF(data),
        `job_${data.jobNumber}.pdf`,
        'job_summary',
        data,
        useFallback
      );
      set({ isPrinting: false });
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Print failed',
        isPrinting: false,
      });
      throw error;
    }
  },

  // Load print queue
  loadPrintQueue: async () => {
    try {
      const jobs = await printQueueService.getAllJobs();
      set({ printJobs: jobs });
    } catch (error) {
      console.error('Error loading print queue:', error);
    }
  },

  // Process queue
  processQueue: async () => {
    if (!get().isConnected) {
      console.log('Cannot process queue: No printer connected');
      return;
    }

    set({ isPrinting: true, error: null });
    try {
      await printQueueService.processQueue();
      await get().loadPrintQueue();
      await get().loadQueueStats();
      set({ isPrinting: false });
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Queue processing failed',
        isPrinting: false,
      });
    }
  },

  // Retry job
  retryJob: async (jobId: string) => {
    try {
      await printQueueService.retryJob(jobId);
      await get().loadPrintQueue();
      await get().loadQueueStats();
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Retry failed',
      });
      throw error;
    }
  },

  // Delete job
  deleteJob: async (jobId: string) => {
    try {
      await printQueueService.deleteJob(jobId);
      await get().loadPrintQueue();
      await get().loadQueueStats();
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Delete failed',
      });
      throw error;
    }
  },

  // Clear completed jobs
  clearCompletedJobs: async () => {
    try {
      await printQueueService.clearCompleted();
      await get().loadPrintQueue();
      await get().loadQueueStats();
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Clear failed',
      });
      throw error;
    }
  },

  // Load queue stats
  loadQueueStats: async () => {
    try {
      const stats = await printQueueService.getStats();
      set({ queueStats: stats });
    } catch (error) {
      console.error('Error loading queue stats:', error);
    }
  },

  // Clear error
  clearError: () => {
    set({ error: null });
  },
}));
