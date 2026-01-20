import client from './client';

export interface Job {
  id: number;
  organization_id: number;
  customer_id: number;
  land_measurement_id?: number;
  driver_id?: number;
  machine_id?: number;
  service_type: string;
  status: 'pending' | 'assigned' | 'in_progress' | 'completed' | 'billed' | 'paid';
  invoice_generated: boolean;
  scheduled_at?: string;
  started_at?: string;
  completed_at?: string;
  notes?: string;
  created_by: number;
  created_at: string;
  updated_at: string;
  customer?: any;
  driver?: any;
  machine?: any;
  landMeasurement?: any;
}

export interface CreateJobData {
  customer_id: number;
  land_measurement_id?: number;
  driver_id?: number;
  machine_id?: number;
  service_type: string;
  scheduled_at?: string;
  notes?: string;
}

export interface UpdateJobStatusData {
  status: string;
}

export interface AssignJobData {
  driver_id?: number;
  machine_id?: number;
}

const jobApi = {
  /**
   * Get all jobs
   */
  getAll: async (params?: any) => {
    const response = await client.get('/jobs', { params });
    return response.data;
  },

  /**
   * Get a single job by ID
   */
  getById: async (id: number) => {
    const response = await client.get(`/jobs/${id}`);
    return response.data;
  },

  /**
   * Create a new job
   */
  create: async (data: CreateJobData) => {
    const response = await client.post('/jobs', data);
    return response.data;
  },

  /**
   * Update an existing job
   */
  update: async (id: number, data: Partial<CreateJobData>) => {
    const response = await client.put(`/jobs/${id}`, data);
    return response.data;
  },

  /**
   * Update job status
   */
  updateStatus: async (id: number, data: UpdateJobStatusData) => {
    const response = await client.post(`/jobs/${id}/status`, data);
    return response.data;
  },

  /**
   * Assign driver/machine to job
   */
  assign: async (id: number, data: AssignJobData) => {
    const response = await client.post(`/jobs/${id}/assign`, data);
    return response.data;
  },

  /**
   * Delete a job
   */
  delete: async (id: number) => {
    const response = await client.delete(`/jobs/${id}`);
    return response.data;
  },
};

export default jobApi;
