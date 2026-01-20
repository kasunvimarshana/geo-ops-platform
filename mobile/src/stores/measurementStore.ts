import { create } from 'zustand';
import { Land, GPSPoint } from '@/types';

interface MeasurementState {
  isTracking: boolean;
  currentPoints: GPSPoint[];
  currentLand: Partial<Land> | null;
  savedLands: Land[];
  
  // Actions
  startTracking: () => void;
  stopTracking: () => void;
  addPoint: (point: GPSPoint) => void;
  clearPoints: () => void;
  setCurrentLand: (land: Partial<Land>) => void;
  saveLand: (land: Land) => void;
  updateLand: (id: number, land: Partial<Land>) => void;
  deleteLand: (id: number) => void;
  setSavedLands: (lands: Land[]) => void;
}

/**
 * Measurement Store
 * 
 * Manages land measurement state
 * Tracks GPS points and measurement progress
 */
export const useMeasurementStore = create<MeasurementState>((set) => ({
  isTracking: false,
  currentPoints: [],
  currentLand: null,
  savedLands: [],

  startTracking: () => {
    set({ isTracking: true, currentPoints: [] });
  },

  stopTracking: () => {
    set({ isTracking: false });
  },

  addPoint: (point) => {
    set((state) => ({
      currentPoints: [...state.currentPoints, point],
    }));
  },

  clearPoints: () => {
    set({ currentPoints: [], currentLand: null });
  },

  setCurrentLand: (land) => {
    set({ currentLand: land });
  },

  saveLand: (land) => {
    set((state) => ({
      savedLands: [...state.savedLands, land],
      currentPoints: [],
      currentLand: null,
    }));
  },

  updateLand: (id, landData) => {
    set((state) => ({
      savedLands: state.savedLands.map((land) =>
        land.id === id ? { ...land, ...landData } : land
      ),
    }));
  },

  deleteLand: (id) => {
    set((state) => ({
      savedLands: state.savedLands.filter((land) => land.id !== id),
    }));
  },

  setSavedLands: (lands) => {
    set({ savedLands: lands });
  },
}));
