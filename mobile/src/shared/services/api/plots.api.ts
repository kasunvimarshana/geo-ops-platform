import { apiClient } from './apiClient';
import { LandPlot } from '../../types/api.types';

export const plotsApi = {
  getPlots: async (jobId?: number): Promise<LandPlot[]> => {
    const params = jobId ? { job: jobId } : undefined;
    return apiClient.get<LandPlot[]>('/land-plots', { params });
  },

  getPlot: async (id: number): Promise<LandPlot> => {
    return apiClient.get<LandPlot>(`/land-plots/${id}`);
  },

  createPlot: async (data: Partial<LandPlot>): Promise<LandPlot> => {
    return apiClient.post<LandPlot>('/land-plots', data);
  },

  updatePlot: async (id: number, data: Partial<LandPlot>): Promise<LandPlot> => {
    return apiClient.patch<LandPlot>(`/land-plots/${id}`, data);
  },

  deletePlot: async (id: number): Promise<void> => {
    return apiClient.delete<void>(`/land-plots/${id}`);
  },
};
