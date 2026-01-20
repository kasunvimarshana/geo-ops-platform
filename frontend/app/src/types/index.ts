/**
 * TypeScript Type Definitions for Geo Ops Platform
 */

// ============================================================================
// Organization & Users
// ============================================================================

export interface Organization {
  id: number;
  name: string;
  slug: string;
  owner_id: number;
  phone?: string;
  email?: string;
  address?: string;
  city?: string;
  country: string;
  timezone: string;
  currency: string;
  status: 'active' | 'inactive' | 'suspended';
  settings?: Record<string, any>;
  created_at: string;
  updated_at: string;
}

export interface User {
  id: number;
  organization_id: number;
  role_id: number;
  name: string;
  email: string;
  phone: string;
  profile_photo?: string;
  language: 'en' | 'si';
  status: 'active' | 'inactive' | 'blocked';
  email_verified_at?: string;
  phone_verified_at?: string;
  last_login_at?: string;
  settings?: Record<string, any>;
  created_at: string;
  updated_at: string;
}

export interface Role {
  id: number;
  name: string;
  display_name: string;
  description?: string;
  is_system: boolean;
}

// ============================================================================
// Measurements & GPS
// ============================================================================

export interface Measurement {
  id: number;
  organization_id: number;
  measured_by: number;
  customer_name?: string;
  customer_phone?: string;
  location_name?: string;
  location_address?: string;
  area_acres?: number;
  area_hectares?: number;
  perimeter_meters?: number;
  center_latitude?: number;
  center_longitude?: number;
  measurement_method: 'walk_around' | 'point_based';
  measurement_date: string;
  notes?: string;
  status: 'draft' | 'completed' | 'verified';
  synced_at?: string;
  created_at: string;
  updated_at: string;
}

export interface MeasurementPolygon {
  id: number;
  measurement_id: number;
  point_order: number;
  latitude: number;
  longitude: number;
  altitude?: number;
  accuracy?: number;
  timestamp: string;
}

export interface GpsLocation {
  latitude: number;
  longitude: number;
  altitude?: number;
  accuracy?: number;
  timestamp?: string;
}

export interface GpsTracking {
  id: number;
  organization_id: number;
  user_id: number;
  job_id?: number;
  latitude: number;
  longitude: number;
  altitude?: number;
  accuracy?: number;
  speed?: number;
  heading?: number;
  timestamp: string;
  battery_level?: number;
  is_moving: boolean;
  created_at: string;
}

// ============================================================================
// Jobs & Assignments
// ============================================================================

export interface Job {
  id: number;
  organization_id: number;
  measurement_id?: number;
  job_number: string;
  customer_name: string;
  customer_phone: string;
  location_name?: string;
  location_address?: string;
  location_latitude?: number;
  location_longitude?: number;
  job_type?: string;
  scheduled_date: string;
  start_time?: string;
  end_time?: string;
  status: 'pending' | 'assigned' | 'in_progress' | 'completed' | 'cancelled';
  priority: 'low' | 'normal' | 'high' | 'urgent';
  notes?: string;
  completed_at?: string;
  created_at: string;
  updated_at: string;
  // Related data
  driver_name?: string;
  machine_name?: string;
  area_measured?: string;
  duration?: string;
}

export interface JobAssignment {
  id: number;
  job_id: number;
  driver_id: number;
  machine_id?: number;
  assigned_at: string;
  started_at?: string;
  completed_at?: string;
  status: 'assigned' | 'accepted' | 'started' | 'completed' | 'declined';
  notes?: string;
}

export interface Machine {
  id: number;
  organization_id: number;
  name: string;
  type?: string;
  model?: string;
  registration_number?: string;
  purchase_date?: string;
  purchase_price?: number;
  status: 'active' | 'maintenance' | 'retired';
  notes?: string;
  created_at: string;
  updated_at: string;
}

// ============================================================================
// Billing & Payments
// ============================================================================

export interface Invoice {
  id: number;
  organization_id: number;
  job_id: number;
  invoice_number: string;
  customer_name: string;
  customer_phone?: string;
  customer_address?: string;
  invoice_date: string;
  due_date?: string;
  subtotal: number;
  tax_amount: number;
  tax_rate?: number;
  discount_amount: number;
  total_amount: number;
  paid_amount: number;
  balance: number;
  status: 'draft' | 'sent' | 'paid' | 'partially_paid' | 'overdue' | 'cancelled';
  payment_terms?: string;
  notes?: string;
  pdf_path?: string;
  created_at: string;
  updated_at: string;
  // Related data
  organization_name?: string;
  currency?: string;
  items?: InvoiceItem[];
}

export interface InvoiceItem {
  id: number;
  invoice_id: number;
  description: string;
  quantity: number;
  unit?: string;
  unit_price: number;
  subtotal: number;
  tax_rate: number;
  tax_amount: number;
  total: number;
}

export interface Payment {
  id: number;
  organization_id: number;
  invoice_id?: number;
  payment_number: string;
  payment_date: string;
  amount: number;
  payment_method: 'cash' | 'bank_transfer' | 'cheque' | 'card' | 'mobile_money';
  reference_number?: string;
  notes?: string;
  received_by?: number;
  status: 'pending' | 'completed' | 'failed' | 'refunded';
  created_at: string;
  updated_at: string;
  // Related data
  customer_name?: string;
  invoice_number?: string;
  received_by_name?: string;
  currency?: string;
}

export interface Expense {
  id: number;
  organization_id: number;
  machine_id?: number;
  job_id?: number;
  category: string;
  description: string;
  amount: number;
  expense_date: string;
  payment_method: 'cash' | 'bank_transfer' | 'cheque' | 'card';
  reference_number?: string;
  vendor_name?: string;
  receipt_image?: string;
  notes?: string;
  created_at: string;
  updated_at: string;
}

// ============================================================================
// Subscriptions
// ============================================================================

export interface Subscription {
  id: number;
  organization_id: number;
  package_type: 'free' | 'basic' | 'pro' | 'enterprise';
  status: 'active' | 'expired' | 'cancelled';
  started_at: string;
  expires_at?: string;
  features?: SubscriptionFeatures;
  created_at: string;
  updated_at: string;
}

export interface SubscriptionFeatures {
  max_measurements?: number;
  max_drivers?: number;
  max_machines?: number;
  max_exports_per_month?: number;
  gps_tracking?: boolean;
  invoice_generation?: boolean;
  advanced_reports?: boolean;
  api_access?: boolean;
  bluetooth_printing?: boolean;
}

// ============================================================================
// Printer Types
// ============================================================================

export interface PrintJob {
  type: 'invoice' | 'receipt' | 'job_summary';
  data: Invoice | Payment | Job;
  retryCount?: number;
  createdAt?: string;
}

export interface PrinterStatus {
  isConnected: boolean;
  deviceName: string | null;
  queueLength: number;
  isProcessing: boolean;
}

export interface BluetoothDevice {
  id: string;
  name: string;
  rssi?: number;
}

// ============================================================================
// API Response Types
// ============================================================================

export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data?: T;
  errors?: Record<string, string[]>;
}

export interface PaginatedResponse<T = any> {
  success: boolean;
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
}

// ============================================================================
// Authentication Types
// ============================================================================

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  name: string;
  email: string;
  phone: string;
  password: string;
  password_confirmation: string;
  organization_name: string;
  language?: 'en' | 'si';
}

export interface AuthResponse {
  user: User;
  organization: Organization;
  access_token: string;
  refresh_token: string;
  token_type: string;
  expires_in: number;
}

// ============================================================================
// Sync Types
// ============================================================================

export interface SyncQueueItem {
  id?: number;
  organization_id: number;
  user_id: number;
  entity_type: string;
  entity_id?: string;
  action: 'create' | 'update' | 'delete';
  payload: Record<string, any>;
  status: 'pending' | 'processing' | 'completed' | 'failed';
  attempts: number;
  last_error?: string;
  created_at: string;
  processed_at?: string;
}

export interface SyncStatus {
  pendingCount: number;
  lastSyncedAt?: string;
  isSyncing: boolean;
  errors: string[];
}

// ============================================================================
// Form Types
// ============================================================================

export interface MeasurementFormData {
  customer_name?: string;
  customer_phone?: string;
  location_name?: string;
  location_address?: string;
  measurement_method: 'walk_around' | 'point_based';
  notes?: string;
  polygons: GpsLocation[];
}

export interface JobFormData {
  measurement_id?: number;
  customer_name: string;
  customer_phone: string;
  location_name?: string;
  location_address?: string;
  job_type?: string;
  scheduled_date: string;
  start_time?: string;
  priority: 'low' | 'normal' | 'high' | 'urgent';
  notes?: string;
}

export interface ExpenseFormData {
  machine_id?: number;
  job_id?: number;
  category: string;
  description: string;
  amount: number;
  expense_date: string;
  payment_method: 'cash' | 'bank_transfer' | 'cheque' | 'card';
  reference_number?: string;
  vendor_name?: string;
  receipt_image?: string;
  notes?: string;
}

// ============================================================================
// Settings Types
// ============================================================================

export interface AppSettings {
  language: 'en' | 'si';
  theme: 'light' | 'dark' | 'auto';
  gpsAccuracy: 'high' | 'balanced' | 'low';
  gpsUpdateInterval: number;
  offlineSyncEnabled: boolean;
  autoSyncOnWifi: boolean;
  notificationsEnabled: boolean;
  defaultPrinter?: string;
  printQuality: 'draft' | 'normal' | 'high';
}

// ============================================================================
// Navigation Types
// ============================================================================

export type RootStackParamList = {
  Auth: undefined;
  Login: undefined;
  Register: undefined;
  Main: undefined;
  Dashboard: undefined;
  MeasurementList: undefined;
  MeasurementCreate: undefined;
  MeasurementView: { id: number };
  JobList: undefined;
  JobCreate: undefined;
  JobView: { id: number };
  InvoiceList: undefined;
  InvoiceView: { id: number };
  PaymentCreate: { invoiceId: number };
  ExpenseList: undefined;
  ExpenseCreate: undefined;
  Settings: undefined;
  PrinterSettings: undefined;
  Profile: undefined;
};
