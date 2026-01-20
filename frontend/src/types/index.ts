/**
 * Global type definitions for the GeoOps application
 */

// User types
export interface User {
  id: string;
  name: string;
  email: string;
  phone?: string;
  avatar?: string;
}

// Field types
export interface Field {
  id: string;
  name: string;
  location: Location;
  area: number;
  cropType?: string;
  userId: string;
  createdAt: Date;
  updatedAt: Date;
}

// Location types
export interface Location {
  latitude: number;
  longitude: number;
  accuracy?: number;
}

// Task types
export interface Task {
  id: string;
  title: string;
  description?: string;
  fieldId: string;
  status: 'pending' | 'in-progress' | 'completed';
  createdAt: Date;
  updatedAt: Date;
}

// API Response types
export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  error?: string;
  message?: string;
}

// Error types
export interface ApiError {
  code: string;
  message: string;
  details?: Record<string, any>;
}
