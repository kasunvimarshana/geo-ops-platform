import client from './client';

export interface Invoice {
  id: number;
  organization_id: number;
  customer_id: number;
  job_id?: number;
  invoice_number: string;
  subtotal: number;
  tax: number;
  total: number;
  status: 'draft' | 'sent' | 'paid' | 'overdue' | 'cancelled';
  issued_at: string;
  due_at: string;
  paid_at?: string;
  created_at: string;
  updated_at: string;
  customer?: any;
  job?: any;
}

export interface CreateInvoiceData {
  customer_id: number;
  job_id?: number;
  subtotal: number;
  tax?: number;
  total: number;
  issued_at?: string;
  due_at?: string;
}

export interface GenerateInvoiceFromJobData {
  rate_per_unit?: number;
  tax_percentage?: number;
  issued_at?: string;
  due_at?: string;
}

const invoiceApi = {
  /**
   * Get all invoices
   */
  getAll: async (params?: any) => {
    const response = await client.get('/invoices', { params });
    return response.data;
  },

  /**
   * Get a single invoice by ID
   */
  getById: async (id: number) => {
    const response = await client.get(`/invoices/${id}`);
    return response.data;
  },

  /**
   * Create a new invoice manually
   */
  create: async (data: CreateInvoiceData) => {
    const response = await client.post('/invoices', data);
    return response.data;
  },

  /**
   * Generate invoice from a job
   */
  generateFromJob: async (jobId: number, data?: GenerateInvoiceFromJobData) => {
    const response = await client.post(`/jobs/${jobId}/invoice`, data);
    return response.data;
  },

  /**
   * Update an existing invoice
   */
  update: async (id: number, data: Partial<CreateInvoiceData>) => {
    const response = await client.put(`/invoices/${id}`, data);
    return response.data;
  },

  /**
   * Update invoice status
   */
  updateStatus: async (id: number, status: string) => {
    const response = await client.post(`/invoices/${id}/status`, { status });
    return response.data;
  },

  /**
   * Mark invoice as paid
   */
  markAsPaid: async (id: number) => {
    const response = await client.post(`/invoices/${id}/paid`);
    return response.data;
  },

  /**
   * Download invoice PDF
   */
  downloadPdf: async (id: number) => {
    const response = await client.get(`/invoices/${id}/pdf`, {
      responseType: 'blob',
    });
    return response.data;
  },

  /**
   * Send invoice via email
   */
  sendEmail: async (id: number) => {
    const response = await client.post(`/invoices/${id}/email`);
    return response.data;
  },

  /**
   * Delete an invoice
   */
  delete: async (id: number) => {
    const response = await client.delete(`/invoices/${id}`);
    return response.data;
  },

  /**
   * Get invoice summary
   */
  getSummary: async () => {
    const response = await client.get('/invoices-summary');
    return response.data;
  },
};

export default invoiceApi;
