/**
 * Job Use Case
 * Handles job management operations
 */

import ApiClient from '../../infrastructure/api/ApiClient';
import { Job, JobCreateData } from '../../domain/entities/Job';

interface JobListResponse {
  data: Job[];
  total: number;
  current_page: number;
  per_page: number;
}

class JobUseCase {
  /**
   * Get list of jobs
   */
  async getJobs(page: number = 1, perPage: number = 15, filters?: {
    status?: string;
    assignee_id?: number;
    field_id?: number;
    priority?: string;
  }): Promise<JobListResponse> {
    try {
      const response = await ApiClient.get<JobListResponse>('/jobs', {
        page,
        per_page: perPage,
        ...filters,
      });
      return response;
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Failed to fetch jobs');
    }
  }

  /**
   * Get single job by ID
   */
  async getJob(id: string): Promise<Job> {
    try {
      const response = await ApiClient.get<Job>(`/jobs/${id}`);
      return response;
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Failed to fetch job');
    }
  }

  /**
   * Create new job
   */
  async createJob(data: JobCreateData): Promise<Job> {
    try {
      const response = await ApiClient.post<Job>('/jobs', data);
      return response;
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Failed to create job');
    }
  }

  /**
   * Update existing job
   */
  async updateJob(id: string, data: Partial<JobCreateData>): Promise<Job> {
    try {
      const response = await ApiClient.put<Job>(`/jobs/${id}`, data);
      return response;
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Failed to update job');
    }
  }

  /**
   * Delete job
   */
  async deleteJob(id: string): Promise<void> {
    try {
      await ApiClient.delete(`/jobs/${id}`);
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Failed to delete job');
    }
  }

  /**
   * Update job status
   */
  async updateJobStatus(id: string, status: string): Promise<Job> {
    try {
      const response = await ApiClient.patch<Job>(`/jobs/${id}/status`, { status });
      return response;
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Failed to update job status');
    }
  }
}

export default new JobUseCase();
