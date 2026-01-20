import { apiClient } from './apiClient';
import { Invoice, PaginatedResponse } from '../../types/api.types';

export const invoicesApi = {
  getInvoices: async (params?: { page?: number }): Promise<PaginatedResponse<Invoice>> => {
    return apiClient.get<PaginatedResponse<Invoice>>('/invoices', { params });
  },

  getInvoice: async (id: number): Promise<Invoice> => {
    return apiClient.get<Invoice>(`/invoices/${id}`);
  },

  downloadInvoicePDF: async (id: number): Promise<string> => {
    const response = await apiClient.get<{ pdf_url: string }>(`/invoices/${id}/download`);
    return response.pdf_url;
  },
};
