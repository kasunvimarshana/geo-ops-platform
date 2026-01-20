import { FieldJob } from '../../shared/types/api.types';

export interface JobFormData {
  title: string;
  customer_name: string;
  location: string;
  description?: string;
  estimated_price?: number;
  scheduled_date?: string;
}

export interface JobFilters {
  status?: string;
  dateRange?: {
    start: string;
    end: string;
  };
}

export type JobStatus = 'pending' | 'in_progress' | 'completed' | 'cancelled';
