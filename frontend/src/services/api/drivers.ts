import client from './client';

export interface Driver {
  id: number;
  organization_id: number;
  user_id: number;
  license_number: string;
  license_expiry: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  user?: {
    id: number;
    name: string;
    email: string;
    phone: string;
  };
}

export interface DriverStatistics {
  total_jobs: number;
  completed_jobs: number;
  in_progress_jobs: number;
  total_tracking_points: number;
  total_expenses: number;
  approved_expenses: number;
}

export interface CreateDriverData {
  name: string;
  email: string;
  password: string;
  phone: string;
  license_number: string;
  license_expiry: string;
  is_active?: boolean;
}

export interface UpdateDriverData {
  name?: string;
  phone?: string;
  license_number?: string;
  license_expiry?: string;
  is_active?: boolean;
}

export interface DriverListResponse {
  data: {
    data: Driver[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export const driverApi = {
  /**
   * Get list of drivers
   */
  getDrivers: async (params?: {
    page?: number;
    per_page?: number;
    is_active?: boolean;
  }): Promise<DriverListResponse> => {
    const response = await client.get('/drivers', { params });
    return response.data;
  },

  /**
   * Get single driver by ID
   */
  getDriver: async (id: number): Promise<{ data: Driver }> => {
    const response = await client.get(`/drivers/${id}`);
    return response.data;
  },

  /**
   * Create new driver
   */
  createDriver: async (data: CreateDriverData): Promise<{ data: Driver }> => {
    const response = await client.post('/drivers', data);
    return response.data;
  },

  /**
   * Update driver
   */
  updateDriver: async (id: number, data: UpdateDriverData): Promise<{ data: Driver }> => {
    const response = await client.put(`/drivers/${id}`, data);
    return response.data;
  },

  /**
   * Delete driver
   */
  deleteDriver: async (id: number): Promise<void> => {
    await client.delete(`/drivers/${id}`);
  },

  /**
   * Get driver statistics
   */
  getDriverStatistics: async (id: number): Promise<{ data: DriverStatistics }> => {
    const response = await client.get(`/drivers/${id}/statistics`);
    return response.data;
  },

  /**
   * Toggle driver active status
   */
  toggleDriverStatus: async (id: number): Promise<{ data: Driver }> => {
    const response = await client.post(`/drivers/${id}/toggle-status`);
    return response.data;
  },
};
