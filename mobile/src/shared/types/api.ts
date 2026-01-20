// API Request/Response Types matching backend

export interface ApiError {
  message: string;
  code?: string;
  details?: Record<string, unknown>;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  pagination: {
    page: number;
    limit: number;
    total: number;
    totalPages: number;
  };
}

// Auth Types
export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  phone?: string;
}

export interface AuthResponse {
  token: string;
  refreshToken: string;
  user: User;
}

export interface User {
  id: string;
  name: string;
  email: string;
  phone?: string;
  role: 'farmer' | 'admin' | 'contractor';
  createdAt: string;
  updatedAt: string;
}

// Land Types
export interface Land {
  id: string;
  name: string;
  area: number;
  location: {
    latitude: number;
    longitude: number;
  };
  boundaries?: Array<{
    latitude: number;
    longitude: number;
  }>;
  ownerId: string;
  createdAt: string;
  updatedAt: string;
}

export interface CreateLandRequest {
  name: string;
  area: number;
  location: {
    latitude: number;
    longitude: number;
  };
  boundaries?: Array<{
    latitude: number;
    longitude: number;
  }>;
}

// Measurement Types
export interface Measurement {
  id: string;
  landId: string;
  type: 'area' | 'perimeter' | 'distance';
  value: number;
  unit: string;
  coordinates: Array<{
    latitude: number;
    longitude: number;
  }>;
  notes?: string;
  createdAt: string;
  updatedAt: string;
}

// Job Types
export interface Job {
  id: string;
  title: string;
  description: string;
  landId: string;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  scheduledDate?: string;
  completedDate?: string;
  assignedTo?: string;
  createdAt: string;
  updatedAt: string;
}

// Invoice Types
export interface Invoice {
  id: string;
  invoiceNumber: string;
  jobId?: string;
  amount: number;
  currency: string;
  status: 'draft' | 'sent' | 'paid' | 'overdue' | 'cancelled';
  dueDate: string;
  paidDate?: string;
  createdAt: string;
  updatedAt: string;
}

// Payment Types
export interface Payment {
  id: string;
  invoiceId: string;
  amount: number;
  currency: string;
  method: 'cash' | 'bank_transfer' | 'mobile_payment';
  transactionId?: string;
  notes?: string;
  paidAt: string;
  createdAt: string;
}

// Expense Types
export interface Expense {
  id: string;
  category: string;
  amount: number;
  currency: string;
  description: string;
  date: string;
  receiptUrl?: string;
  createdAt: string;
  updatedAt: string;
}

// GPS Tracking Types
export interface LocationPoint {
  latitude: number;
  longitude: number;
  accuracy?: number;
  timestamp: string;
}

export interface TrackingSession {
  id: string;
  startTime: string;
  endTime?: string;
  points: LocationPoint[];
  distance?: number;
  duration?: number;
}
