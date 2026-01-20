import client from './client';

export interface Expense {
  id: number;
  organization_id: number;
  machine_id?: number;
  driver_id?: number;
  category: 'fuel' | 'parts' | 'maintenance' | 'labor' | 'other';
  amount: number;
  description?: string;
  receipt_path?: string;
  expense_date: string;
  status: 'pending' | 'approved' | 'rejected';
  recorded_by?: number;
  approved_by?: number;
  approved_at?: string;
  created_at: string;
  updated_at: string;
  machine?: any;
  driver?: any;
}

export interface CreateExpenseData {
  machine_id?: number;
  driver_id?: number;
  category: 'fuel' | 'parts' | 'maintenance' | 'labor' | 'other';
  amount: number;
  description?: string;
  expense_date?: string;
}

const expenseApi = {
  /**
   * Get all expenses
   */
  getAll: async (params?: any) => {
    const response = await client.get('/expenses', { params });
    return response.data;
  },

  /**
   * Get a single expense by ID
   */
  getById: async (id: number) => {
    const response = await client.get(`/expenses/${id}`);
    return response.data;
  },

  /**
   * Create a new expense
   */
  create: async (data: CreateExpenseData) => {
    const response = await client.post('/expenses', data);
    return response.data;
  },

  /**
   * Update an existing expense
   */
  update: async (id: number, data: Partial<CreateExpenseData>) => {
    const response = await client.put(`/expenses/${id}`, data);
    return response.data;
  },

  /**
   * Upload receipt for expense
   */
  uploadReceipt: async (id: number, file: any) => {
    const formData = new FormData();
    formData.append('receipt', file);
    const response = await client.post(`/expenses/${id}/receipt`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  /**
   * Approve expense
   */
  approve: async (id: number) => {
    const response = await client.post(`/expenses/${id}/approve`);
    return response.data;
  },

  /**
   * Reject expense
   */
  reject: async (id: number) => {
    const response = await client.post(`/expenses/${id}/reject`);
    return response.data;
  },

  /**
   * Delete an expense
   */
  delete: async (id: number) => {
    const response = await client.delete(`/expenses/${id}`);
    return response.data;
  },

  /**
   * Get expense summary
   */
  getSummary: async (period?: string) => {
    const response = await client.get('/expenses-summary', { params: { period } });
    return response.data;
  },

  /**
   * Get machine expenses
   */
  getMachineExpenses: async (machineId: number) => {
    const response = await client.get(`/machines/${machineId}/expenses`);
    return response.data;
  },

  /**
   * Get driver expenses
   */
  getDriverExpenses: async (driverId: number) => {
    const response = await client.get(`/drivers/${driverId}/expenses`);
    return response.data;
  },
};

export default expenseApi;
