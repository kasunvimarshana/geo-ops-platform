import { apiClient } from './client';

/**
 * Measurement API
 * 
 * All measurement-related API calls.
 */

export interface GPSPoint {
  latitude: number;
  longitude: number;
  altitude?: number;
  accuracy?: number;
  timestamp: string;
}

export interface CreateMeasurementDTO {
  customer_name: string;
  customer_phone: string;
  location_name: string;
  location_address?: string;
  measurement_method: 'walk_around' | 'point_based';
  measurement_date: string;
  polygon_points: GPSPoint[];
  notes?: string;
}

export interface Measurement {
  id: number;
  customer_name: string;
  customer_phone: string;
  location_name: string;
  area_acres: number;
  area_hectares: number;
  perimeter_meters: number;
  center_latitude: number;
  center_longitude: number;
  measurement_method: string;
  measurement_date: string;
  status: string;
  measured_by: {
    id: number;
    name: string;
  };
  polygon_points?: GPSPoint[];
  created_at: string;
}

export interface MeasurementListResponse {
  success: boolean;
  data: Measurement[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

class MeasurementAPI {
  /**
   * Get list of measurements
   */
  async list(params?: {
    customer_phone?: string;
    measured_by?: number;
    status?: string;
    from_date?: string;
    to_date?: string;
    page?: number;
    per_page?: number;
  }): Promise<MeasurementListResponse> {
    return apiClient.get('/measurements', { params });
  }

  /**
   * Get measurement by ID
   */
  async getById(id: number): Promise<{ success: boolean; data: Measurement }> {
    return apiClient.get(`/measurements/${id}`);
  }

  /**
   * Create new measurement
   */
  async create(data: CreateMeasurementDTO): Promise<{ success: boolean; data: Measurement }> {
    return apiClient.post('/measurements', data);
  }

  /**
   * Update measurement
   */
  async update(
    id: number,
    data: Partial<CreateMeasurementDTO>
  ): Promise<{ success: boolean; data: Measurement }> {
    return apiClient.put(`/measurements/${id}`, data);
  }

  /**
   * Delete measurement
   */
  async delete(id: number): Promise<{ success: boolean; message: string }> {
    return apiClient.delete(`/measurements/${id}`);
  }
}

export const measurementAPI = new MeasurementAPI();
