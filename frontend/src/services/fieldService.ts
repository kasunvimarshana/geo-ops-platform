import { Field, ApiResponse } from '@types/index';
import { apiService } from './api';

export const fieldService = {
  async getFields(): Promise<ApiResponse<Field[]>> {
    try {
      const response = await apiService.instance.get<Field[]>('/fields');
      return {
        success: true,
        data: response.data,
      };
    } catch (error) {
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Failed to fetch fields',
      };
    }
  },

  async getField(id: string): Promise<ApiResponse<Field>> {
    try {
      const response = await apiService.instance.get<Field>(`/fields/${id}`);
      return {
        success: true,
        data: response.data,
      };
    } catch (error) {
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Failed to fetch field',
      };
    }
  },

  async createField(field: Omit<Field, 'id' | 'createdAt' | 'updatedAt'>): Promise<
    ApiResponse<Field>
  > {
    try {
      const response = await apiService.instance.post<Field>('/fields', field);
      return {
        success: true,
        data: response.data,
      };
    } catch (error) {
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Failed to create field',
      };
    }
  },

  async updateField(id: string, field: Partial<Field>): Promise<ApiResponse<Field>> {
    try {
      const response = await apiService.instance.put<Field>(`/fields/${id}`, field);
      return {
        success: true,
        data: response.data,
      };
    } catch (error) {
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Failed to update field',
      };
    }
  },

  async deleteField(id: string): Promise<ApiResponse<void>> {
    try {
      await apiService.instance.delete(`/fields/${id}`);
      return {
        success: true,
      };
    } catch (error) {
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Failed to delete field',
      };
    }
  },
};
