export interface User {
  id: number;
  username: string;
  email: string;
  first_name: string;
  last_name: string;
  role: 'admin' | 'driver' | 'customer';
}

export interface AuthTokens {
  access: string;
  refresh: string;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  username: string;
  email: string;
  password: string;
  first_name: string;
  last_name: string;
}

export interface Coordinates {
  latitude: number;
  longitude: number;
}

export interface LandPlot {
  id?: number;
  coordinates: Coordinates[];
  area_sqm: number;
  area_acres: number;
  perimeter_m: number;
  job?: number;
  created_at?: string;
  synced?: boolean;
  local_id?: string;
}

export interface FieldJob {
  id?: number;
  title: string;
  customer_name: string;
  location: string;
  description?: string;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  estimated_price?: number;
  actual_price?: number;
  scheduled_date?: string;
  completed_date?: string;
  land_plots?: LandPlot[];
  created_at?: string;
  updated_at?: string;
  synced?: boolean;
  local_id?: string;
}

export interface Invoice {
  id: number;
  job: number;
  invoice_number: string;
  customer_name: string;
  issued_date: string;
  due_date: string;
  total_amount: number;
  status: 'draft' | 'sent' | 'paid' | 'overdue';
  pdf_url?: string;
}

export interface SyncQueueItem {
  id?: number;
  operation: 'create' | 'update' | 'delete';
  entity_type: 'job' | 'plot' | 'invoice';
  entity_id?: string;
  data: string;
  status: 'pending' | 'syncing' | 'success' | 'failed';
  retry_count: number;
  created_at: string;
  updated_at: string;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
}

export interface PaginatedResponse<T> {
  count: number;
  next: string | null;
  previous: string | null;
  results: T[];
}

export interface ApiError {
  message: string;
  status?: number;
  errors?: Record<string, string[]>;
}
