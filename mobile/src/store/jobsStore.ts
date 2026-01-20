import { create } from 'zustand';
import { FieldJob } from '../shared/types/api.types';
import { jobsApi } from '../shared/services/api/jobs.api';
import { sqliteService } from '../shared/services/storage/sqlite.service';
import { syncService } from '../shared/services/sync/syncService';

interface JobsState {
  jobs: FieldJob[];
  currentJob: FieldJob | null;
  isLoading: boolean;
  error: string | null;
  statusFilter: string | null;

  fetchJobs: (status?: string) => Promise<void>;
  fetchJob: (id: number) => Promise<void>;
  createJob: (data: Partial<FieldJob>) => Promise<void>;
  updateJob: (id: number, data: Partial<FieldJob>) => Promise<void>;
  updateJobStatus: (id: number, status: string) => Promise<void>;
  deleteJob: (id: number) => Promise<void>;
  loadLocalJobs: (status?: string) => Promise<void>;
  setStatusFilter: (status: string | null) => void;
  setCurrentJob: (job: FieldJob | null) => void;
}

export const useJobsStore = create<JobsState>((set, get) => ({
  jobs: [],
  currentJob: null,
  isLoading: false,
  error: null,
  statusFilter: null,

  fetchJobs: async (status?: string) => {
    set({ isLoading: true, error: null });
    try {
      const response = await jobsApi.getJobs({ status });
      set({ jobs: response.results, isLoading: false });
    } catch (error: any) {
      console.error('Error fetching jobs:', error);
      await get().loadLocalJobs(status);
      set({ error: error.message, isLoading: false });
    }
  },

  fetchJob: async (id: number) => {
    set({ isLoading: true, error: null });
    try {
      const job = await jobsApi.getJob(id);
      set({ currentJob: job, isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
    }
  },

  createJob: async (data: Partial<FieldJob>) => {
    set({ isLoading: true, error: null });
    try {
      const localId = `local_${Date.now()}`;
      const jobData = {
        ...data,
        local_id: localId,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        synced: false,
      };

      await sqliteService.saveJob(jobData as any);
      await syncService.queueJob('create', data, localId);

      await get().loadLocalJobs();
      set({ isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  updateJob: async (id: number, data: Partial<FieldJob>) => {
    set({ isLoading: true, error: null });
    try {
      await syncService.queueJob('update', { id, ...data });
      await get().fetchJobs();
      set({ isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  updateJobStatus: async (id: number, status: string) => {
    set({ isLoading: true, error: null });
    try {
      await syncService.queueJob('update', { id, status });
      await get().fetchJobs();
      set({ isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  deleteJob: async (id: number) => {
    set({ isLoading: true, error: null });
    try {
      await syncService.queueJob('delete', { id });
      await get().fetchJobs();
      set({ isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  loadLocalJobs: async (status?: string) => {
    try {
      const jobs = await sqliteService.getJobs(status);
      set({ jobs });
    } catch (error) {
      console.error('Error loading local jobs:', error);
    }
  },

  setStatusFilter: (status: string | null) => {
    set({ statusFilter: status });
  },

  setCurrentJob: (job: FieldJob | null) => {
    set({ currentJob: job });
  },
}));
