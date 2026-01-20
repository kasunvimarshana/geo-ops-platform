import client from './client';

export interface Payment {
  id: number;
  organization_id: number;
  customer_id: number;
  invoice_id?: number;
  amount: number;
  method: 'cash' | 'bank' | 'mobile' | 'credit';
  reference?: string;
  notes?: string;
  paid_at: string;
  recorded_by?: number;
  created_at: string;
  updated_at: string;
  customer?: any;
  invoice?: any;
}

export interface CreatePaymentData {
  customer_id: number;
  invoice_id?: number;
  amount: number;
  method: 'cash' | 'bank' | 'mobile' | 'credit';
  reference?: string;
  notes?: string;
  paid_at?: string;
}

const paymentApi = {
  /**
   * Get all payments
   */
  getAll: async (params?: any) => {
    const response = await client.get('/payments', { params });
    return response.data;
  },

  /**
   * Get a single payment by ID
   */
  getById: async (id: number) => {
    const response = await client.get(`/payments/${id}`);
    return response.data;
  },

  /**
   * Record a new payment
   */
  create: async (data: CreatePaymentData) => {
    const response = await client.post('/payments', data);
    return response.data;
  },

  /**
   * Update an existing payment
   */
  update: async (id: number, data: Partial<CreatePaymentData>) => {
    const response = await client.put(`/payments/${id}`, data);
    return response.data;
  },

  /**
   * Delete a payment
   */
  delete: async (id: number) => {
    const response = await client.delete(`/payments/${id}`);
    return response.data;
  },

  /**
   * Get payment summary
   */
  getSummary: async (period?: string) => {
    const response = await client.get('/payments-summary', { params: { period } });
    return response.data;
  },

  /**
   * Get customer payment history
   */
  getCustomerHistory: async (customerId: number) => {
    const response = await client.get(`/customers/${customerId}/payments`);
    return response.data;
  },
};

export default paymentApi;
