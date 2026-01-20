import client from './client';

export interface Customer {
  id: number;
  organization_id: number;
  name: string;
  phone: string;
  email?: string;
  address?: string;
  balance: number;
  created_at: string;
  updated_at: string;
}

export interface CustomerStatistics {
  total_jobs: number;
  completed_jobs: number;
  total_invoices: number;
  paid_invoices: number;
  total_invoiced: number;
  total_paid: number;
  current_balance: number;
}

export interface CreateCustomerData {
  name: string;
  phone: string;
  email?: string;
  address?: string;
}

export interface UpdateCustomerData {
  name?: string;
  phone?: string;
  email?: string;
  address?: string;
}

export interface CustomerListResponse {
  data: {
    data: Customer[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export const customerApi = {
  /**
   * Get list of customers
   */
  getCustomers: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
  }): Promise<CustomerListResponse> => {
    const response = await client.get('/customers', { params });
    return response.data;
  },

  /**
   * Get single customer by ID
   */
  getCustomer: async (id: number): Promise<{ data: Customer }> => {
    const response = await client.get(`/customers/${id}`);
    return response.data;
  },

  /**
   * Create new customer
   */
  createCustomer: async (data: CreateCustomerData): Promise<{ data: Customer }> => {
    const response = await client.post('/customers', data);
    return response.data;
  },

  /**
   * Update customer
   */
  updateCustomer: async (id: number, data: UpdateCustomerData): Promise<{ data: Customer }> => {
    const response = await client.put(`/customers/${id}`, data);
    return response.data;
  },

  /**
   * Delete customer
   */
  deleteCustomer: async (id: number): Promise<void> => {
    await client.delete(`/customers/${id}`);
  },

  /**
   * Get customer statistics
   */
  getCustomerStatistics: async (id: number): Promise<{ data: CustomerStatistics }> => {
    const response = await client.get(`/customers/${id}/statistics`);
    return response.data;
  },
};
