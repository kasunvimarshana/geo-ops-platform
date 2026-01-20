/**
 * Bluetooth Printer Service Types
 * Defines all types used by the printer service
 */

export interface BluetoothDevice {
  id: string;
  name: string;
  address: string;
  paired?: boolean;
  connected?: boolean;
}

export interface PrinterStatus {
  isConnected: boolean;
  connectedDevice: BluetoothDevice | null;
  isDiscovering: boolean;
  error: string | null;
}

export interface PrintJob {
  id: string;
  type: 'invoice' | 'receipt' | 'job_summary';
  data: any;
  status: 'pending' | 'printing' | 'completed' | 'failed';
  createdAt: Date;
  attempts: number;
  error?: string;
}

export interface InvoicePrintData {
  invoiceNumber: string;
  customerName: string;
  customerPhone?: string;
  date: string;
  items: Array<{
    description: string;
    quantity?: number;
    price: number;
  }>;
  subtotal: number;
  tax?: number;
  discount?: number;
  total: number;
  currency: string;
  notes?: string;
}

export interface ReceiptPrintData {
  receiptNumber: string;
  customerName: string;
  date: string;
  amount: number;
  paymentMethod: string;
  currency: string;
  notes?: string;
}

export interface JobSummaryPrintData {
  jobNumber: string;
  customerName: string;
  driver: string;
  date: string;
  landArea: number;
  areaUnit: string;
  location: string;
  status: string;
  notes?: string;
}

export type PrintData = InvoicePrintData | ReceiptPrintData | JobSummaryPrintData;

export interface PrintOptions {
  enableFallbackPDF?: boolean;
  retryOnFailure?: boolean;
  maxRetries?: number;
}
