import axios from 'axios';
import { Job } from '../types/job';
import { API_URL } from '../constants/Config';

// Create a new job
export const createJob = async (jobData: Job): Promise<Job> => {
    const response = await axios.post(`${API_URL}/jobs`, jobData);
    return response.data;
};

// Get all jobs
export const getJobs = async (): Promise<Job[]> => {
    const response = await axios.get(`${API_URL}/jobs`);
    return response.data;
};

// Get a job by ID
export const getJobById = async (id: string): Promise<Job> => {
    const response = await axios.get(`${API_URL}/jobs/${id}`);
    return response.data;
};

// Update a job
export const updateJob = async (id: string, jobData: Job): Promise<Job> => {
    const response = await axios.put(`${API_URL}/jobs/${id}`, jobData);
    return response.data;
};

// Delete a job
export const deleteJob = async (id: string): Promise<void> => {
    await axios.delete(`${API_URL}/jobs/${id}`);
};