import create from 'zustand';
import { Job } from '../types/job';
import { fetchJobs, createJob, updateJob, deleteJob } from '../api/jobs';

interface JobStore {
  jobs: Job[];
  fetchJobs: () => Promise<void>;
  addJob: (job: Job) => Promise<void>;
  editJob: (job: Job) => Promise<void>;
  removeJob: (id: string) => Promise<void>;
}

const useJobStore = create<JobStore>((set) => ({
  jobs: [],
  fetchJobs: async () => {
    const jobs = await fetchJobs();
    set({ jobs });
  },
  addJob: async (job) => {
    await createJob(job);
    set((state) => ({ jobs: [...state.jobs, job] }));
  },
  editJob: async (job) => {
    await updateJob(job);
    set((state) => ({
      jobs: state.jobs.map((j) => (j.id === job.id ? job : j)),
    }));
  },
  removeJob: async (id) => {
    await deleteJob(id);
    set((state) => ({ jobs: state.jobs.filter((job) => job.id !== id) }));
  },
}));

export default useJobStore;