import { create } from 'zustand';
import { measurementAPI, Measurement, CreateMeasurementDTO } from '@/api/measurement.api';
import { database } from '@/services/storage/database';

/**
 * Measurement Store
 * 
 * Manages measurement state with offline support.
 */

interface MeasurementState {
  measurements: Measurement[];
  currentMeasurement: Measurement | null;
  isLoading: boolean;
  error: string | null;
  
  // Actions
  fetchMeasurements: (params?: any) => Promise<void>;
  getMeasurementById: (id: number) => Promise<void>;
  createMeasurement: (data: CreateMeasurementDTO) => Promise<Measurement>;
  updateMeasurement: (id: number, data: Partial<CreateMeasurementDTO>) => Promise<void>;
  deleteMeasurement: (id: number) => Promise<void>;
  clearError: () => void;
  
  // Offline methods
  saveMeasurementOffline: (data: CreateMeasurementDTO) => Promise<number>;
  getOfflineMeasurements: () => Promise<Measurement[]>;
}

export const useMeasurementStore = create<MeasurementState>((set, get) => ({
  measurements: [],
  currentMeasurement: null,
  isLoading: false,
  error: null,

  fetchMeasurements: async (params?: any) => {
    set({ isLoading: true, error: null });
    
    try {
      const response = await measurementAPI.list(params);
      
      set({
        measurements: response.data,
        isLoading: false,
      });

      // Cache in local database for offline access
      await database.measurements.replaceAll(response.data);
    } catch (error: any) {
      // If offline, load from local database
      if (error.status === 0) {
        const offlineMeasurements = await database.measurements.getAll();
        set({
          measurements: offlineMeasurements,
          isLoading: false,
        });
      } else {
        set({
          error: error.message || 'Failed to fetch measurements',
          isLoading: false,
        });
      }
    }
  },

  getMeasurementById: async (id: number) => {
    set({ isLoading: true, error: null });
    
    try {
      const response = await measurementAPI.getById(id);
      
      set({
        currentMeasurement: response.data,
        isLoading: false,
      });
    } catch (error: any) {
      // Try to load from offline database
      const offlineMeasurement = await database.measurements.getById(id);
      
      if (offlineMeasurement) {
        set({
          currentMeasurement: offlineMeasurement,
          isLoading: false,
        });
      } else {
        set({
          error: error.message || 'Failed to fetch measurement',
          isLoading: false,
        });
      }
    }
  },

  createMeasurement: async (data: CreateMeasurementDTO) => {
    set({ isLoading: true, error: null });
    
    try {
      const response = await measurementAPI.create(data);
      
      // Add to measurements list
      set((state) => ({
        measurements: [response.data, ...state.measurements],
        isLoading: false,
      }));

      // Save to offline database
      await database.measurements.insert(response.data);

      return response.data;
    } catch (error: any) {
      // If offline, save to local database and queue for sync
      if (error.status === 0) {
        const offlineId = await get().saveMeasurementOffline(data);
        
        // Return temporary measurement object
        return {
          id: offlineId,
          ...data,
          status: 'pending_sync',
        } as Measurement;
      }

      set({
        error: error.message || 'Failed to create measurement',
        isLoading: false,
      });
      throw error;
    }
  },

  updateMeasurement: async (id: number, data: Partial<CreateMeasurementDTO>) => {
    set({ isLoading: true, error: null });
    
    try {
      const response = await measurementAPI.update(id, data);
      
      // Update in measurements list
      set((state) => ({
        measurements: state.measurements.map((m) =>
          m.id === id ? response.data : m
        ),
        currentMeasurement: response.data,
        isLoading: false,
      }));

      // Update in offline database
      await database.measurements.update(id, response.data);
    } catch (error: any) {
      set({
        error: error.message || 'Failed to update measurement',
        isLoading: false,
      });
      throw error;
    }
  },

  deleteMeasurement: async (id: number) => {
    set({ isLoading: true, error: null });
    
    try {
      await measurementAPI.delete(id);
      
      // Remove from measurements list
      set((state) => ({
        measurements: state.measurements.filter((m) => m.id !== id),
        isLoading: false,
      }));

      // Remove from offline database
      await database.measurements.delete(id);
    } catch (error: any) {
      set({
        error: error.message || 'Failed to delete measurement',
        isLoading: false,
      });
      throw error;
    }
  },

  clearError: () => {
    set({ error: null });
  },

  // Offline methods

  saveMeasurementOffline: async (data: CreateMeasurementDTO) => {
    // Save to offline database
    const id = await database.measurements.insertPending(data);

    // Add to sync queue
    await database.syncQueue.add({
      entity_type: 'measurement',
      action: 'create',
      payload: data,
      created_at: new Date().toISOString(),
    });

    return id;
  },

  getOfflineMeasurements: async () => {
    return database.measurements.getPending();
  },
}));
