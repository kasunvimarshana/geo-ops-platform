import { create } from 'zustand';
import { LandPlot, Coordinates } from '../shared/types/api.types';
import { plotsApi } from '../shared/services/api/plots.api';
import { sqliteService } from '../shared/services/storage/sqlite.service';
import { syncService } from '../shared/services/sync/syncService';
import { locationService } from '../shared/services/location/locationService';

interface PlotsState {
  plots: LandPlot[];
  currentPlot: LandPlot | null;
  currentMeasurement: Coordinates[];
  isTracking: boolean;
  isLoading: boolean;
  error: string | null;

  fetchPlots: (jobId?: number) => Promise<void>;
  createPlot: (data: Partial<LandPlot>) => Promise<void>;
  startMeasurement: () => void;
  addPoint: (coordinate: Coordinates) => void;
  removeLastPoint: () => void;
  clearMeasurement: () => void;
  saveMeasurement: (jobId?: number) => Promise<void>;
  loadLocalPlots: (jobId?: number) => Promise<void>;
  setCurrentPlot: (plot: LandPlot | null) => void;
}

export const usePlotsStore = create<PlotsState>((set, get) => ({
  plots: [],
  currentPlot: null,
  currentMeasurement: [],
  isTracking: false,
  isLoading: false,
  error: null,

  fetchPlots: async (jobId?: number) => {
    set({ isLoading: true, error: null });
    try {
      const plots = await plotsApi.getPlots(jobId);
      set({ plots, isLoading: false });
    } catch (error: any) {
      console.error('Error fetching plots:', error);
      await get().loadLocalPlots(jobId);
      set({ error: error.message, isLoading: false });
    }
  },

  createPlot: async (data: Partial<LandPlot>) => {
    set({ isLoading: true, error: null });
    try {
      const localId = `local_${Date.now()}`;
      const plotData = {
        ...data,
        local_id: localId,
        created_at: new Date().toISOString(),
        synced: false,
      };

      await sqliteService.savePlot(plotData as any);
      await syncService.queuePlot('create', data, localId);

      await get().loadLocalPlots(data.job);
      set({ isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  startMeasurement: () => {
    set({ currentMeasurement: [], isTracking: true });
  },

  addPoint: (coordinate: Coordinates) => {
    set((state) => ({
      currentMeasurement: [...state.currentMeasurement, coordinate],
    }));
  },

  removeLastPoint: () => {
    set((state) => ({
      currentMeasurement: state.currentMeasurement.slice(0, -1),
    }));
  },

  clearMeasurement: () => {
    set({ currentMeasurement: [], isTracking: false });
  },

  saveMeasurement: async (jobId?: number) => {
    const { currentMeasurement } = get();
    
    if (currentMeasurement.length < 3) {
      set({ error: 'Need at least 3 points to create a plot' });
      return;
    }

    const area_sqm = locationService.calculateArea(currentMeasurement);
    const area_acres = locationService.sqmToAcres(area_sqm);
    const perimeter_m = locationService.calculatePerimeter(currentMeasurement);

    const plotData: Partial<LandPlot> = {
      coordinates: currentMeasurement,
      area_sqm,
      area_acres,
      perimeter_m,
      job: jobId,
    };

    await get().createPlot(plotData);
    set({ currentMeasurement: [], isTracking: false });
  },

  loadLocalPlots: async (jobId?: number) => {
    try {
      const plots = await sqliteService.getPlots(jobId);
      set({ plots });
    } catch (error) {
      console.error('Error loading local plots:', error);
    }
  },

  setCurrentPlot: (plot: LandPlot | null) => {
    set({ currentPlot: plot });
  },
}));
