/**
 * usePrinter Hook
 * 
 * React hook for Bluetooth printer operations
 * Provides easy access to printer functionality in components
 */

import { useState, useEffect, useCallback } from 'react';
import {
  printerService,
  BluetoothDevice,
  PrinterStatus,
  InvoicePrintData,
  ReceiptPrintData,
  JobSummaryPrintData,
  PrintJob,
} from '../services/printerService';

export interface UsePrinterReturn {
  // Status
  status: PrinterStatus;
  isConnected: boolean;
  isScanning: boolean;
  devices: BluetoothDevice[];
  pendingJobsCount: number;
  printQueue: PrintJob[];
  
  // Actions
  scanDevices: () => Promise<void>;
  connectToDevice: (device: BluetoothDevice) => Promise<boolean>;
  disconnect: () => Promise<void>;
  printInvoice: (data: InvoicePrintData) => Promise<boolean>;
  printReceipt: (data: ReceiptPrintData) => Promise<boolean>;
  printJobSummary: (data: JobSummaryPrintData) => Promise<boolean>;
  refreshStatus: () => Promise<void>;
  clearFailedJobs: () => Promise<void>;
  
  // Errors
  error: string | null;
  clearError: () => void;
}

export function usePrinter(): UsePrinterReturn {
  const [status, setStatus] = useState<PrinterStatus>({
    connected: false,
    deviceName: null,
    deviceAddress: null,
    paperStatus: 'unknown',
    batteryLevel: null,
  });
  const [isScanning, setIsScanning] = useState(false);
  const [devices, setDevices] = useState<BluetoothDevice[]>([]);
  const [pendingJobsCount, setPendingJobsCount] = useState(0);
  const [printQueue, setPrintQueue] = useState<PrintJob[]>([]);
  const [error, setError] = useState<string | null>(null);

  // Initialize printer service
  useEffect(() => {
    printerService.initialize().catch(err => {
      console.error('Failed to initialize printer service:', err);
      setError('Failed to initialize printer service');
    });
    
    // Refresh status periodically
    const interval = setInterval(() => {
      refreshStatus();
    }, 5000);
    
    return () => {
      clearInterval(interval);
    };
  }, [refreshStatus]);

  // Refresh printer status
  const refreshStatus = useCallback(async () => {
    try {
      const newStatus = await printerService.getStatus();
      setStatus(newStatus);
      setPendingJobsCount(printerService.getPendingJobsCount());
      setPrintQueue(printerService.getQueue());
    } catch (err) {
      console.error('Failed to get printer status:', err);
    }
  }, []);

  // Scan for Bluetooth devices
  const scanDevices = useCallback(async () => {
    try {
      setIsScanning(true);
      setError(null);
      const foundDevices = await printerService.discoverDevices();
      setDevices(foundDevices);
    } catch (err: any) {
      console.error('Failed to scan devices:', err);
      setError(err.message || 'Failed to scan for devices');
    } finally {
      setIsScanning(false);
    }
  }, []);

  // Connect to a device
  const connectToDevice = useCallback(async (device: BluetoothDevice): Promise<boolean> => {
    try {
      setError(null);
      const success = await printerService.connect(device);
      if (success) {
        await refreshStatus();
      } else {
        setError('Failed to connect to printer');
      }
      return success;
    } catch (err: any) {
      console.error('Connection error:', err);
      setError(err.message || 'Failed to connect to printer');
      return false;
    }
  }, [refreshStatus]);

  // Disconnect from printer
  const disconnect = useCallback(async () => {
    try {
      setError(null);
      await printerService.disconnect();
      await refreshStatus();
    } catch (err: any) {
      console.error('Disconnect error:', err);
      setError(err.message || 'Failed to disconnect');
    }
  }, [refreshStatus]);

  // Print invoice
  const printInvoice = useCallback(async (data: InvoicePrintData): Promise<boolean> => {
    try {
      setError(null);
      const success = await printerService.printInvoice(data);
      await refreshStatus();
      return success;
    } catch (err: any) {
      console.error('Print invoice error:', err);
      setError(err.message || 'Failed to print invoice');
      return false;
    }
  }, [refreshStatus]);

  // Print receipt
  const printReceipt = useCallback(async (data: ReceiptPrintData): Promise<boolean> => {
    try {
      setError(null);
      const success = await printerService.printReceipt(data);
      await refreshStatus();
      return success;
    } catch (err: any) {
      console.error('Print receipt error:', err);
      setError(err.message || 'Failed to print receipt');
      return false;
    }
  }, [refreshStatus]);

  // Print job summary
  const printJobSummary = useCallback(async (data: JobSummaryPrintData): Promise<boolean> => {
    try {
      setError(null);
      const success = await printerService.printJobSummary(data);
      await refreshStatus();
      return success;
    } catch (err: any) {
      console.error('Print job summary error:', err);
      setError(err.message || 'Failed to print job summary');
      return false;
    }
  }, [refreshStatus]);

  // Clear failed jobs
  const clearFailedJobs = useCallback(async () => {
    try {
      await printerService.clearFailedJobs();
      await refreshStatus();
    } catch (err: any) {
      console.error('Clear failed jobs error:', err);
      setError(err.message || 'Failed to clear failed jobs');
    }
  }, [refreshStatus]);

  // Clear error
  const clearError = useCallback(() => {
    setError(null);
  }, []);

  return {
    status,
    isConnected: status.connected,
    isScanning,
    devices,
    pendingJobsCount,
    printQueue,
    scanDevices,
    connectToDevice,
    disconnect,
    printInvoice,
    printReceipt,
    printJobSummary,
    refreshStatus,
    clearFailedJobs,
    error,
    clearError,
  };
}
