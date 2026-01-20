import client from './client';

export interface TrackingLog {
  id: number;
  driver_id?: number;
  job_id?: number;
  latitude: number;
  longitude: number;
  accuracy?: number;
  speed?: number;
  heading?: number;
  recorded_at: string;
}

export interface TrackingLogBatch {
  driver_id?: number;
  job_id?: number;
  locations: Array<{
    latitude: number;
    longitude: number;
    accuracy?: number;
    speed?: number;
    heading?: number;
    recorded_at: string;
  }>;
}

export interface ActiveDriver {
  driver_id: number;
  driver_name: string;
  latest_location: {
    latitude: number;
    longitude: number;
    recorded_at: string;
  };
  job_id?: number;
  job_status?: string;
}

export interface DriverHistoryParams {
  from_date?: string;
  to_date?: string;
  page?: number;
  per_page?: number;
}

export interface TrackingListResponse {
  data: {
    data: TrackingLog[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export const trackingApi = {
  /**
   * Batch upload location data
   */
  uploadLocations: async (data: TrackingLogBatch): Promise<{ data: { created: number } }> => {
    const response = await client.post('/tracking', data);
    return response.data;
  },

  /**
   * Get driver tracking history
   */
  getDriverHistory: async (
    driverId: number,
    params?: DriverHistoryParams
  ): Promise<TrackingListResponse> => {
    const response = await client.get(`/tracking/drivers/${driverId}`, { params });
    return response.data;
  },

  /**
   * Get job tracking history
   */
  getJobHistory: async (
    jobId: number,
    params?: { page?: number; per_page?: number }
  ): Promise<TrackingListResponse> => {
    const response = await client.get(`/tracking/jobs/${jobId}`, { params });
    return response.data;
  },

  /**
   * Get active drivers (currently tracking)
   */
  getActiveDrivers: async (): Promise<{ data: ActiveDriver[] }> => {
    const response = await client.get('/tracking/active');
    return response.data;
  },
};
