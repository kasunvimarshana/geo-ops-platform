/**
 * Field Use Case
 * Handles field management operations
 */

import ApiClient from '../../infrastructure/api/ApiClient';
import { Field, FieldCreateData, FieldMeasurement } from '../../domain/entities/Field';

interface FieldListResponse {
  data: Field[];
  total: number;
  current_page: number;
  per_page: number;
}

class FieldUseCase {
  /**
   * Get list of fields
   */
  async getFields(page: number = 1, perPage: number = 15): Promise<FieldListResponse> {
    try {
      const response = await ApiClient.get<FieldListResponse>('/fields', {
        page,
        per_page: perPage,
      });
      return response;
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Failed to fetch fields');
    }
  }

  /**
   * Get single field by ID
   */
  async getField(id: string): Promise<Field> {
    try {
      const response = await ApiClient.get<Field>(`/fields/${id}`);
      return response;
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Failed to fetch field');
    }
  }

  /**
   * Create new field
   */
  async createField(data: FieldCreateData): Promise<Field> {
    try {
      const response = await ApiClient.post<Field>('/fields', data);
      return response;
    } catch (error: any) {
      throw new Error(error.response?.data?.errors || 'Failed to create field');
    }
  }

  /**
   * Update existing field
   */
  async updateField(id: string, data: Partial<FieldCreateData>): Promise<Field> {
    try {
      const response = await ApiClient.put<Field>(`/fields/${id}`, data);
      return response;
    } catch (error: any) {
      throw new Error(error.response?.data?.errors || 'Failed to update field');
    }
  }

  /**
   * Delete field
   */
  async deleteField(id: string): Promise<void> {
    try {
      await ApiClient.delete(`/fields/${id}`);
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Failed to delete field');
    }
  }

  /**
   * Save field measurement
   */
  async saveMeasurement(measurement: FieldMeasurement): Promise<Field> {
    const fieldData: FieldCreateData = {
      name: `Field ${new Date().toLocaleDateString()}`,
      boundary: measurement.points,
      area: measurement.area,
      perimeter: measurement.perimeter,
    };

    return this.createField(fieldData);
  }
}

export default new FieldUseCase();
