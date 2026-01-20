import { apiClient } from './apiClient';
import { FieldJob, PaginatedResponse } from '../../types/api.types';

export const jobsApi = {
  getJobs: async (params?: {
    status?: string;
    page?: number;
  }): Promise<PaginatedResponse<FieldJob>> => {
    return apiClient.get<PaginatedResponse<FieldJob>>('/field-jobs', { params });
  },

  getJob: async (id: number): Promise<FieldJob> => {
    return apiClient.get<FieldJob>(`/field-jobs/${id}`);
  },

  createJob: async (data: Partial<FieldJob>): Promise<FieldJob> => {
    return apiClient.post<FieldJob>('/field-jobs', data);
  },

  updateJob: async (id: number, data: Partial<FieldJob>): Promise<FieldJob> => {
    return apiClient.patch<FieldJob>(`/field-jobs/${id}`, data);
  },

  deleteJob: async (id: number): Promise<void> => {
    return apiClient.delete<void>(`/field-jobs/${id}`);
  },

  updateJobStatus: async (id: number, status: string): Promise<FieldJob> => {
    return apiClient.patch<FieldJob>(`/field-jobs/${id}`, { status });
  },
};
