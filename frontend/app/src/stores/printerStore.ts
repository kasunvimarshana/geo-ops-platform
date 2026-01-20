/**
 * Printer Store using Zustand
 * 
 * Manages printer connection state and print queue
 */

import { create } from 'zustand';
import { Device } from 'react-native-ble-plx';
import { getPrinterService } from '../services/printer/PrinterService';
import type { PrintJob, PrinterStatus } from '../types';

interface PrinterStore {
  // State
  connectedDevice: Device | null;
  availableDevices: Device[];
  printQueue: PrintJob[];
  isScanning: boolean;
  isPrinting: boolean;
  isConnecting: boolean;
  printerStatus: PrinterStatus | null;
  error: string | null;

  // Actions
  scanDevices: () => Promise<void>;
  connectDevice: (deviceId: string) => Promise<void>;
  disconnectDevice: () => Promise<void>;
  autoReconnect: () => Promise<boolean>;
  printDocument: (printJob: PrintJob) => Promise<void>;
  testPrint: () => Promise<void>;
  retryFailedPrints: () => Promise<void>;
  getStatus: () => Promise<void>;
  clearError: () => void;
  initialize: () => Promise<void>;
}

export const usePrinterStore = create<PrinterStore>((set, get) => ({
  // Initial state
  connectedDevice: null,
  availableDevices: [],
  printQueue: [],
  isScanning: false,
  isPrinting: false,
  isConnecting: false,
  printerStatus: null,
  error: null,

  // Initialize printer service
  initialize: async () => {
    try {
      const service = getPrinterService();
      await service.initialize();
      await service.loadPrintQueue();

      // Try auto-reconnect
      await get().autoReconnect();

      // Update status
      await get().getStatus();
    } catch (error) {
      set({ error: (error as Error).message });
    }
  },

  // Scan for devices
  scanDevices: async () => {
    set({ isScanning: true, error: null });

    try {
      const service = getPrinterService();
      const devices = await service.scanDevices();

      set({ availableDevices: devices, isScanning: false });
    } catch (error) {
      set({
        error: (error as Error).message,
        isScanning: false,
      });
    }
  },

  // Connect to device
  connectDevice: async (deviceId: string) => {
    set({ isConnecting: true, error: null });

    try {
      const service = getPrinterService();
      await service.connectToDevice(deviceId);

      const device = get().availableDevices.find(d => d.id === deviceId);

      set({
        connectedDevice: device || null,
        isConnecting: false,
      });

      // Update status
      await get().getStatus();
    } catch (error) {
      set({
        error: (error as Error).message,
        isConnecting: false,
      });
    }
  },

  // Disconnect device
  disconnectDevice: async () => {
    try {
      const service = getPrinterService();
      await service.disconnectDevice();

      set({ connectedDevice: null });

      // Update status
      await get().getStatus();
    } catch (error) {
      set({ error: (error as Error).message });
    }
  },

  // Auto-reconnect to saved device
  autoReconnect: async () => {
    try {
      const service = getPrinterService();
      const reconnected = await service.autoReconnect();

      if (reconnected) {
        const status = await service.getStatus();
        set({
          connectedDevice: status.deviceName ? ({ name: status.deviceName } as Device) : null,
        });
      }

      return reconnected;
    } catch (error) {
      console.error('Auto-reconnect failed:', error);
      return false;
    }
  },

  // Print document
  printDocument: async (printJob: PrintJob) => {
    set({ isPrinting: true, error: null });

    try {
      const service = getPrinterService();

      // Check if connected
      const isConnected = await service.isPrinterConnected();

      if (!isConnected) {
        // Try to reconnect
        const reconnected = await get().autoReconnect();

        if (!reconnected) {
          // Add to queue if can't connect
          await service.addToQueue(printJob);
          set({
            isPrinting: false,
            error: 'Printer not connected. Job added to queue.',
          });
          await get().getStatus();
          return;
        }
      }

      // Print based on type
      switch (printJob.type) {
        case 'invoice':
          await service.printInvoice(printJob.data);
          break;
        case 'receipt':
          await service.printReceipt(printJob.data);
          break;
        case 'job_summary':
          await service.printJobSummary(printJob.data);
          break;
      }

      set({ isPrinting: false });

      // Update status
      await get().getStatus();
    } catch (error) {
      set({
        error: (error as Error).message,
        isPrinting: false,
      });

      // Add to queue on error
      const service = getPrinterService();
      await service.addToQueue(printJob);
      await get().getStatus();
    }
  },

  // Test print
  testPrint: async () => {
    set({ isPrinting: true, error: null });

    try {
      const service = getPrinterService();

      // Check if connected
      const isConnected = await service.isPrinterConnected();

      if (!isConnected) {
        throw new Error('Printer not connected');
      }

      await service.testPrint();

      set({ isPrinting: false });
    } catch (error) {
      set({
        error: (error as Error).message,
        isPrinting: false,
      });
    }
  },

  // Retry failed prints
  retryFailedPrints: async () => {
    try {
      const service = getPrinterService();
      await service.processQueue();

      // Update status
      await get().getStatus();
    } catch (error) {
      set({ error: (error as Error).message });
    }
  },

  // Get printer status
  getStatus: async () => {
    try {
      const service = getPrinterService();
      const status = await service.getStatus();

      set({
        printerStatus: status,
        printQueue: Array(status.queueLength).fill({}), // Placeholder for queue items
      });
    } catch (error) {
      console.error('Failed to get printer status:', error);
    }
  },

  // Clear error
  clearError: () => {
    set({ error: null });
  },
}));
