/**
 * Job Store
 * Manages job state using Zustand
 */

import { create } from 'zustand';
import { Job, JobCreateData } from '../../domain/entities/Job';
import JobUseCase from '../../application/usecases/JobUseCase';

interface JobStore {
  jobs: Job[];
  currentJob: Job | null;
  isLoading: boolean;
  error: string | null;
  
  // Actions
  fetchJobs: (filters?: {
    status?: string;
    assignee_id?: number;
    field_id?: number;
    priority?: string;
  }) => Promise<void>;
  fetchJob: (id: string) => Promise<void>;
  createJob: (data: JobCreateData) => Promise<Job>;
  updateJob: (id: string, data: Partial<JobCreateData>) => Promise<Job>;
  deleteJob: (id: string) => Promise<void>;
  updateJobStatus: (id: string, status: string) => Promise<Job>;
  setCurrentJob: (job: Job | null) => void;
  clearError: () => void;
}

export const useJobStore = create<JobStore>((set, get) => ({
  jobs: [],
  currentJob: null,
  isLoading: false,
  error: null,

  fetchJobs: async (filters) => {
    set({ isLoading: true, error: null });
    try {
      const response = await JobUseCase.getJobs(1, 100, filters);
      set({ jobs: response.data, isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  fetchJob: async (id) => {
    set({ isLoading: true, error: null });
    try {
      const job = await JobUseCase.getJob(id);
      set({ currentJob: job, isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  createJob: async (data) => {
    set({ isLoading: true, error: null });
    try {
      const newJob = await JobUseCase.createJob(data);
      set((state) => ({
        jobs: [newJob, ...state.jobs],
        isLoading: false,
      }));
      return newJob;
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  updateJob: async (id, data) => {
    set({ isLoading: true, error: null });
    try {
      const updatedJob = await JobUseCase.updateJob(id, data);
      set((state) => ({
        jobs: state.jobs.map((job) =>
          job.id.toString() === id ? updatedJob : job
        ),
        currentJob: state.currentJob?.id.toString() === id ? updatedJob : state.currentJob,
        isLoading: false,
      }));
      return updatedJob;
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  deleteJob: async (id) => {
    set({ isLoading: true, error: null });
    try {
      await JobUseCase.deleteJob(id);
      set((state) => ({
        jobs: state.jobs.filter((job) => job.id.toString() !== id),
        isLoading: false,
      }));
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  updateJobStatus: async (id, status) => {
    set({ isLoading: true, error: null });
    try {
      const updatedJob = await JobUseCase.updateJobStatus(id, status);
      set((state) => ({
        jobs: state.jobs.map((job) =>
          job.id.toString() === id ? updatedJob : job
        ),
        currentJob: state.currentJob?.id.toString() === id ? updatedJob : state.currentJob,
        isLoading: false,
      }));
      return updatedJob;
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  setCurrentJob: (job) => set({ currentJob: job }),

  clearError: () => set({ error: null }),
}));
