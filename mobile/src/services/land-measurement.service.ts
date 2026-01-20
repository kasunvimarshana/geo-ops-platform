import api from './api';
import { LandMeasurement, GpsCoordinate } from '../types';

export const landMeasurementService = {
  async create(data: {
    name: string;
    description?: string;
    coordinates: GpsCoordinate[];
    unit: string;
    address?: string;
    metadata?: Record<string, any>;
  }): Promise<LandMeasurement> {
    const response = await api.post('/land-measurements', data);
    return response.data.data;
  },

  async getAll(params?: {
    limit?: number;
    offset?: number;
    search?: string;
  }): Promise<LandMeasurement[]> {
    const response = await api.get('/land-measurements', { params });
    return response.data.data;
  },

  async getById(id: string): Promise<LandMeasurement> {
    const response = await api.get(`/land-measurements/${id}`);
    return response.data.data;
  },

  async update(id: string, data: {
    name?: string;
    description?: string;
    address?: string;
    metadata?: Record<string, any>;
  }): Promise<LandMeasurement> {
    const response = await api.patch(`/land-measurements/${id}`, data);
    return response.data.data;
  },

  async delete(id: string): Promise<void> {
    await api.delete(`/land-measurements/${id}`);
  },
};
