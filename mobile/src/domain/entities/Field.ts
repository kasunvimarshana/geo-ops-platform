/**
 * Field Entity
 * Domain model for agricultural fields/land
 */

export interface Field {
  id: string;
  name: string;
  organizationId?: string;
  organization_id?: number;
  location?: string;
  boundary: string | GeoPoint[]; // Can be JSON string or array
  area: number; // in square meters
  perimeter: number; // in meters
  cropType?: string;
  crop_type?: string;
  notes?: string;
  measurement_type?: string;
  measured_at?: string;
  createdAt?: Date;
  created_at?: string;
  updatedAt?: Date;
  updated_at?: string;
}

export interface GeoPoint {
  latitude: number;
  longitude: number;
  altitude?: number;
  accuracy?: number;
  timestamp?: Date;
}

export interface FieldMeasurement {
  id?: string;
  fieldId?: string;
  points: GeoPoint[];
  area: number;
  perimeter: number;
  measurementType: MeasurementType;
  startTime: Date;
  endTime?: Date;
  isSynced: boolean;
}

export enum MeasurementType {
  WALK_AROUND = 'walk_around',
  POLYGON = 'polygon',
  MANUAL = 'manual',
}

export interface FieldCreateData {
  name: string;
  boundary: GeoPoint[];
  area: number;
  perimeter: number;
  cropType?: string;
  notes?: string;
}
