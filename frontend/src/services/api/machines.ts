import client from './client';

export interface Machine {
  id: number;
  organization_id: number;
  name: string;
  type: 'tractor' | 'harvester' | 'rotavator' | 'planter' | 'sprayer' | 'other';
  make?: string;
  model?: string;
  year?: number;
  registration?: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface MachineStatistics {
  total_jobs: number;
  completed_jobs: number;
  in_progress_jobs: number;
  total_expenses: number;
  fuel_expenses: number;
  parts_expenses: number;
  maintenance_expenses: number;
  last_service_date?: string;
}

export interface CreateMachineData {
  name: string;
  type: 'tractor' | 'harvester' | 'rotavator' | 'planter' | 'sprayer' | 'other';
  make?: string;
  model?: string;
  year?: number;
  registration?: string;
  is_active?: boolean;
}

export interface UpdateMachineData {
  name?: string;
  type?: 'tractor' | 'harvester' | 'rotavator' | 'planter' | 'sprayer' | 'other';
  make?: string;
  model?: string;
  year?: number;
  registration?: string;
  is_active?: boolean;
}

export interface MachineListResponse {
  data: {
    data: Machine[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export const machineApi = {
  /**
   * Get list of machines
   */
  getMachines: async (params?: {
    page?: number;
    per_page?: number;
    type?: string;
    is_active?: boolean;
  }): Promise<MachineListResponse> => {
    const response = await client.get('/machines', { params });
    return response.data;
  },

  /**
   * Get single machine by ID
   */
  getMachine: async (id: number): Promise<{ data: Machine }> => {
    const response = await client.get(`/machines/${id}`);
    return response.data;
  },

  /**
   * Create new machine
   */
  createMachine: async (data: CreateMachineData): Promise<{ data: Machine }> => {
    const response = await client.post('/machines', data);
    return response.data;
  },

  /**
   * Update machine
   */
  updateMachine: async (id: number, data: UpdateMachineData): Promise<{ data: Machine }> => {
    const response = await client.put(`/machines/${id}`, data);
    return response.data;
  },

  /**
   * Delete machine
   */
  deleteMachine: async (id: number): Promise<void> => {
    await client.delete(`/machines/${id}`);
  },

  /**
   * Get machine statistics
   */
  getMachineStatistics: async (id: number): Promise<{ data: MachineStatistics }> => {
    const response = await client.get(`/machines/${id}/statistics`);
    return response.data;
  },

  /**
   * Toggle machine active status
   */
  toggleMachineStatus: async (id: number): Promise<{ data: Machine }> => {
    const response = await client.post(`/machines/${id}/toggle-status`);
    return response.data;
  },

  /**
   * Get available machine types
   */
  getMachineTypes: async (): Promise<{ data: string[] }> => {
    const response = await client.get('/machines/types/list');
    return response.data;
  },
};
