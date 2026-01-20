import apiClient from './client';
import type {
  LoginRequest,
  RegisterRequest,
  AuthResponse,
  User,
  Land,
  CreateLandRequest,
  Measurement,
  Job,
  Invoice,
  Payment,
  Expense,
  ApiResponse,
  PaginatedResponse,
} from '../../shared/types/api';

export const authApi = {
  login: (data: LoginRequest) =>
    apiClient.post<ApiResponse<AuthResponse>>('/auth/login', data),
  
  register: (data: RegisterRequest) =>
    apiClient.post<ApiResponse<AuthResponse>>('/auth/register', data),
  
  logout: () =>
    apiClient.post('/auth/logout'),
  
  refreshToken: (refreshToken: string) =>
    apiClient.post<ApiResponse<{ token: string; refreshToken: string }>>('/auth/refresh', {
      refreshToken,
    }),
  
  getProfile: () =>
    apiClient.get<ApiResponse<User>>('/auth/profile'),
  
  updateProfile: (data: Partial<User>) =>
    apiClient.put<ApiResponse<User>>('/auth/profile', data),
};

export const landsApi = {
  getAll: (params?: { page?: number; limit?: number }) =>
    apiClient.get<PaginatedResponse<Land>>('/lands', { params }),
  
  getById: (id: string) =>
    apiClient.get<ApiResponse<Land>>(`/lands/${id}`),
  
  create: (data: CreateLandRequest) =>
    apiClient.post<ApiResponse<Land>>('/lands', data),
  
  update: (id: string, data: Partial<CreateLandRequest>) =>
    apiClient.put<ApiResponse<Land>>(`/lands/${id}`, data),
  
  delete: (id: string) =>
    apiClient.delete(`/lands/${id}`),
};

export const measurementsApi = {
  getAll: (params?: { landId?: string; page?: number; limit?: number }) =>
    apiClient.get<PaginatedResponse<Measurement>>('/measurements', { params }),
  
  getById: (id: string) =>
    apiClient.get<ApiResponse<Measurement>>(`/measurements/${id}`),
  
  create: (data: Omit<Measurement, 'id' | 'createdAt' | 'updatedAt'>) =>
    apiClient.post<ApiResponse<Measurement>>('/measurements', data),
  
  delete: (id: string) =>
    apiClient.delete(`/measurements/${id}`),
};

export const jobsApi = {
  getAll: (params?: { page?: number; limit?: number; status?: string }) =>
    apiClient.get<PaginatedResponse<Job>>('/jobs', { params }),
  
  getById: (id: string) =>
    apiClient.get<ApiResponse<Job>>(`/jobs/${id}`),
  
  create: (data: Omit<Job, 'id' | 'createdAt' | 'updatedAt'>) =>
    apiClient.post<ApiResponse<Job>>('/jobs', data),
  
  update: (id: string, data: Partial<Job>) =>
    apiClient.put<ApiResponse<Job>>(`/jobs/${id}`, data),
  
  delete: (id: string) =>
    apiClient.delete(`/jobs/${id}`),
};

export const invoicesApi = {
  getAll: (params?: { page?: number; limit?: number; status?: string }) =>
    apiClient.get<PaginatedResponse<Invoice>>('/invoices', { params }),
  
  getById: (id: string) =>
    apiClient.get<ApiResponse<Invoice>>(`/invoices/${id}`),
  
  create: (data: Omit<Invoice, 'id' | 'createdAt' | 'updatedAt'>) =>
    apiClient.post<ApiResponse<Invoice>>('/invoices', data),
  
  update: (id: string, data: Partial<Invoice>) =>
    apiClient.put<ApiResponse<Invoice>>(`/invoices/${id}`, data),
  
  delete: (id: string) =>
    apiClient.delete(`/invoices/${id}`),
};

export const paymentsApi = {
  getAll: (params?: { page?: number; limit?: number; invoiceId?: string }) =>
    apiClient.get<PaginatedResponse<Payment>>('/payments', { params }),
  
  getById: (id: string) =>
    apiClient.get<ApiResponse<Payment>>(`/payments/${id}`),
  
  create: (data: Omit<Payment, 'id' | 'createdAt'>) =>
    apiClient.post<ApiResponse<Payment>>('/payments', data),
};

export const expensesApi = {
  getAll: (params?: { page?: number; limit?: number; category?: string }) =>
    apiClient.get<PaginatedResponse<Expense>>('/expenses', { params }),
  
  getById: (id: string) =>
    apiClient.get<ApiResponse<Expense>>(`/expenses/${id}`),
  
  create: (data: Omit<Expense, 'id' | 'createdAt' | 'updatedAt'>) =>
    apiClient.post<ApiResponse<Expense>>('/expenses', data),
  
  update: (id: string, data: Partial<Expense>) =>
    apiClient.put<ApiResponse<Expense>>(`/expenses/${id}`, data),
  
  delete: (id: string) =>
    apiClient.delete(`/expenses/${id}`),
};
