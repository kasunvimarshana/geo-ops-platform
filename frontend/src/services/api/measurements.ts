import apiClient from './client';

interface Coordinate {
  latitude: number;
  longitude: number;
}

interface CreateMeasurementData {
  name: string;
  coordinates: Coordinate[];
  measured_at?: string;
}

export const measurementApi = {
  getAll: async () => {
    return apiClient.get('/measurements');
  },

  getById: async (id: number) => {
    return apiClient.get(`/measurements/${id}`);
  },

  create: async (data: CreateMeasurementData) => {
    return apiClient.post('/measurements', data);
  },

  update: async (id: number, data: Partial<CreateMeasurementData>) => {
    return apiClient.put(`/measurements/${id}`, data);
  },

  delete: async (id: number) => {
    return apiClient.delete(`/measurements/${id}`);
  },
};
