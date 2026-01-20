export interface Land {
  id: number;
  organization_id: number;
  name: string;
  description?: string;
  polygon: GPSPoint[];
  area_acres: number;
  area_hectares: number;
  measurement_type: 'walk-around' | 'point-based';
  location_name?: string;
  customer_name?: string;
  customer_phone?: string;
  measured_by: number;
  measured_at: string;
  status: 'draft' | 'confirmed' | 'archived';
  sync_status: 'synced' | 'pending' | 'conflict';
  offline_id?: string;
  created_at: string;
  updated_at: string;
}

export interface GPSPoint {
  latitude: number;
  longitude: number;
  altitude?: number;
  accuracy: number;
  speed?: number;
  heading?: number;
  timestamp?: number;
  recorded_at?: string;
}

export interface Job {
  id: number;
  organization_id: number;
  land_id?: number;
  machine_id: number;
  driver_id: number;
  assigned_by: number;
  title: string;
  description?: string;
  job_date: string;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  start_time?: string;
  end_time?: string;
  duration_minutes?: number;
  customer_name: string;
  customer_phone: string;
  location: {
    latitude: number;
    longitude: number;
  };
  location_name: string;
  notes?: string;
  sync_status: 'synced' | 'pending' | 'conflict';
  offline_id?: string;
  created_at: string;
  updated_at: string;
}

export interface Invoice {
  id: number;
  organization_id: number;
  job_id?: number;
  land_id?: number;
  invoice_number: string;
  customer_name: string;
  customer_phone: string;
  invoice_date: string;
  due_date: string;
  area_acres: number;
  area_hectares: number;
  rate_per_unit: number;
  subtotal: number;
  tax_rate: number;
  tax_amount: number;
  total_amount: number;
  paid_amount: number;
  balance: number;
  status: 'draft' | 'sent' | 'paid' | 'overdue' | 'cancelled';
  notes?: string;
  pdf_path?: string;
  printed_at?: string;
  sync_status: 'synced' | 'pending' | 'conflict';
  offline_id?: string;
  created_at: string;
  updated_at: string;
}

export interface Expense {
  id: number;
  organization_id: number;
  machine_id?: number;
  driver_id?: number;
  job_id?: number;
  expense_type: 'fuel' | 'maintenance' | 'parts' | 'labor' | 'other';
  category: string;
  description: string;
  amount: number;
  expense_date: string;
  receipt_path?: string;
  recorded_by: number;
  sync_status: 'synced' | 'pending' | 'conflict';
  offline_id?: string;
  created_at: string;
  updated_at: string;
}

export interface Payment {
  id: number;
  organization_id: number;
  invoice_id: number;
  payment_method: 'cash' | 'bank' | 'digital' | 'check';
  amount: number;
  payment_date: string;
  reference_number?: string;
  notes?: string;
  received_by: number;
  sync_status: 'synced' | 'pending' | 'conflict';
  offline_id?: string;
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
  language: 'en' | 'si';
  is_active: boolean;
  last_login_at?: string;
  created_at: string;
  updated_at: string;
}

export interface Organization {
  id: number;
  name: string;
  slug: string;
  subscription_package: 'free' | 'basic' | 'pro';
  subscription_expires_at?: string;
  status: 'active' | 'suspended' | 'cancelled';
  settings: Record<string, any>;
  created_at: string;
  updated_at: string;
}

export interface Machine {
  id: number;
  organization_id: number;
  name: string;
  machine_type: string;
  registration_number: string;
  description?: string;
  rate_per_acre: number;
  rate_per_hectare: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface ApiResponse<T = any> {
  success: boolean;
  data?: T;
  message?: string;
  errors?: Record<string, string[]>;
  code?: string;
  meta?: {
    pagination?: {
      total: number;
      per_page: number;
      current_page: number;
      last_page: number;
    };
  };
}

export interface SyncItem<T = any> {
  offline_id: string;
  action: 'create' | 'update' | 'delete';
  data: T;
  updated_at: string;
}

export interface SyncPayload {
  lands?: SyncItem<Land>[];
  jobs?: SyncItem<Job>[];
  invoices?: SyncItem<Invoice>[];
  expenses?: SyncItem<Expense>[];
  payments?: SyncItem<Payment>[];
}

export interface SyncResponse {
  success: boolean;
  data: {
    synced: {
      lands: number;
      jobs: number;
      invoices: number;
      expenses: number;
      payments: number;
    };
    conflicts: ConflictItem[];
    errors: any[];
  };
}

export interface ConflictItem {
  entity_type: string;
  offline_id: string;
  reason: string;
  server_data: any;
  client_data: any;
}
