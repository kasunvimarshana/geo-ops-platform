// User roles
export enum UserRole {
  ADMIN = 'admin',
  OWNER = 'owner',
  DRIVER = 'driver',
  BROKER = 'broker',
  ACCOUNTANT = 'accountant',
}

// Job status
export enum JobStatus {
  PENDING = 'pending',
  IN_PROGRESS = 'in_progress',
  COMPLETED = 'completed',
  CANCELLED = 'cancelled',
}

// Invoice status
export enum InvoiceStatus {
  DRAFT = 'draft',
  SENT = 'sent',
  PAID = 'paid',
  OVERDUE = 'overdue',
  CANCELLED = 'cancelled',
}

// Payment methods
export enum PaymentMethod {
  CASH = 'cash',
  BANK_TRANSFER = 'bank_transfer',
  DIGITAL = 'digital',
  CHEQUE = 'cheque',
}

// Expense categories
export enum ExpenseCategory {
  FUEL = 'fuel',
  SPARE_PARTS = 'spare_parts',
  MAINTENANCE = 'maintenance',
  SALARY = 'salary',
  OTHER = 'other',
}

// Subscription packages
export enum SubscriptionPackage {
  FREE = 'free',
  BASIC = 'basic',
  PRO = 'pro',
  ENTERPRISE = 'enterprise',
}

// Measurement units
export enum MeasurementUnit {
  ACRES = 'acres',
  HECTARES = 'hectares',
  SQUARE_METERS = 'square_meters',
}

export interface User {
  id: string;
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  phone: string;
  role: UserRole;
  organizationId: string;
  subscriptionPackage: SubscriptionPackage;
  subscriptionExpiry: Date | null;
  isActive: boolean;
  createdAt: Date;
  updatedAt: Date;
}

export interface GpsCoordinate {
  latitude: number;
  longitude: number;
  timestamp?: Date;
}

export interface LandMeasurement {
  id: string;
  userId: string;
  name: string;
  description?: string;
  coordinates: GpsCoordinate[];
  area: number;
  unit: MeasurementUnit;
  address?: string;
  metadata?: Record<string, any>;
  createdAt: Date;
  updatedAt: Date;
}

export interface Job {
  id: string;
  organizationId: string;
  landMeasurementId: string;
  driverId: string;
  machineId: string;
  customerId: string;
  status: JobStatus;
  scheduledDate: Date;
  startTime?: Date;
  endTime?: Date;
  notes?: string;
  createdAt: Date;
  updatedAt: Date;
}

export interface Invoice {
  id: string;
  organizationId: string;
  jobId: string;
  customerId: string;
  invoiceNumber: string;
  amount: number;
  currency: string;
  status: InvoiceStatus;
  dueDate: Date;
  paidDate?: Date;
  pdfUrl?: string;
  notes?: string;
  createdAt: Date;
  updatedAt: Date;
}

export interface Expense {
  id: string;
  organizationId: string;
  category: ExpenseCategory;
  amount: number;
  description: string;
  date: Date;
  machineId?: string;
  driverId?: string;
  receiptUrl?: string;
  createdAt: Date;
  updatedAt: Date;
}

export interface Payment {
  id: string;
  organizationId: string;
  invoiceId: string;
  amount: number;
  method: PaymentMethod;
  transactionId?: string;
  notes?: string;
  paidAt: Date;
  createdAt: Date;
}

export interface Machine {
  id: string;
  organizationId: string;
  name: string;
  type: string;
  model: string;
  registrationNumber?: string;
  isActive: boolean;
  createdAt: Date;
  updatedAt: Date;
}

export interface Customer {
  id: string;
  organizationId: string;
  name: string;
  email?: string;
  phone: string;
  address?: string;
  balance: number;
  createdAt: Date;
  updatedAt: Date;
}

export interface Organization {
  id: string;
  name: string;
  email?: string;
  phone?: string;
  address?: string;
  subscriptionPackage: SubscriptionPackage;
  subscriptionExpiry: Date | null;
  isActive: boolean;
  createdAt: Date;
  updatedAt: Date;
}
