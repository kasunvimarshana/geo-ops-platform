export interface User {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  phone: string;
  role: string;
  organizationId: string;
  subscriptionPackage: string;
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
  unit: string;
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
  status: string;
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
  status: string;
  dueDate: Date;
  paidDate?: Date;
  pdfUrl?: string;
  notes?: string;
  createdAt: Date;
  updatedAt: Date;
}

export interface AuthResponse {
  user: User;
  token: string;
}
