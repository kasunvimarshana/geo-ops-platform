import client from './client';
import { LandMeasurement } from './measurements';
import { Job } from './jobs';
import { Customer } from './customers';
import { Driver } from './drivers';
import { Machine } from './machines';
import { Invoice } from './invoices';

export interface SyncPushData {
  measurements?: Array<{
    id?: number;
    name: string;
    coordinates: number[][];
    area_acres: number;
    area_hectares: number;
    measured_at: string;
    client_id: string;
  }>;
  jobs?: Array<{
    id?: number;
    customer_id: number;
    land_measurement_id?: number;
    status: string;
    client_id: string;
  }>;
  tracking?: Array<{
    driver_id?: number;
    job_id?: number;
    latitude: number;
    longitude: number;
    accuracy?: number;
    speed?: number;
    heading?: number;
    recorded_at: string;
  }>;
  expenses?: Array<{
    id?: number;
    category: string;
    amount: number;
    date: string;
    client_id: string;
  }>;
}

export interface SyncPushResult {
  measurements: Array<{
    client_id: string;
    server_id: number;
    status: string;
  }>;
  jobs: Array<{
    client_id: string;
    server_id: number;
    status: string;
  }>;
  tracking: {
    count: number;
  };
  expenses: Array<{
    client_id: string;
    server_id: number;
    status: string;
  }>;
  conflicts: Array<{
    type: string;
    client_id: string;
    error: string;
  }>;
}

export interface SyncPullData {
  measurements?: LandMeasurement[];
  jobs?: Job[];
  customers?: Customer[];
  drivers?: Driver[];
  machines?: Machine[];
  invoices?: Invoice[];
}

export interface SyncPullParams {
  last_sync_at?: string;
  include?: Array<'measurements' | 'jobs' | 'customers' | 'drivers' | 'machines' | 'invoices'>;
}

export const syncApi = {
  /**
   * Push offline data to server
   * Implements last-write-wins conflict resolution
   */
  push: async (data: SyncPushData): Promise<{ data: SyncPushResult }> => {
    const response = await client.post('/sync/push', data);
    return response.data;
  },

  /**
   * Pull latest data from server
   * Only fetches data updated since last sync
   */
  pull: async (params?: SyncPullParams): Promise<{ 
    data: SyncPullData; 
    sync_timestamp: string;
  }> => {
    const response = await client.get('/sync/pull', { params });
    return response.data;
  },
};
