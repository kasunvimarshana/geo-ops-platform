import { create } from 'zustand';
import { Coordinate, calculatePolygonArea, AreaResult } from '../features/measurements/utils/areaCalculator';
import * as measurementsDb from '../database/measurementsDb';

export interface Measurement {
  id?: number;
  serverId?: number;
  fieldName: string;
  coordinates: Coordinate[];
  areaSqm: number;
  areaAcres: number;
  areaHectares: number;
  locationAddress?: string;
  notes?: string;
  synced: boolean;
  createdAt: string;
  updatedAt: string;
}

interface MeasurementState {
  measurements: Measurement[];
  currentMeasurement: Measurement | null;
  isRecording: boolean;
  recordedCoordinates: Coordinate[];
  
  // Actions
  loadMeasurements: () => Promise<void>;
  startRecording: () => void;
  stopRecording: () => void;
  addCoordinate: (coordinate: Coordinate) => void;
  removeLastCoordinate: () => void;
  clearCoordinates: () => void;
  saveMeasurement: (measurement: Omit<Measurement, 'id' | 'createdAt' | 'updatedAt' | 'synced' | 'areaSqm' | 'areaAcres' | 'areaHectares'>) => Promise<Measurement>;
  updateMeasurement: (id: number, updates: Partial<Measurement>) => Promise<void>;
  deleteMeasurement: (id: number) => Promise<void>;
  calculateCurrentArea: () => AreaResult | null;
  setCurrentMeasurement: (measurement: Measurement | null) => void;
}

export const useMeasurementStore = create<MeasurementState>((set, get) => ({
  measurements: [],
  currentMeasurement: null,
  isRecording: false,
  recordedCoordinates: [],

  loadMeasurements: async () => {
    const measurements = await measurementsDb.getAllMeasurements();
    set({ measurements: measurements.map(m => ({
      ...m,
      coordinates: JSON.parse(m.coordinates as string),
    })) });
  },

  startRecording: () => {
    set({ isRecording: true, recordedCoordinates: [] });
  },

  stopRecording: () => {
    set({ isRecording: false });
  },

  addCoordinate: (coordinate: Coordinate) => {
    set((state) => ({
      recordedCoordinates: [...state.recordedCoordinates, coordinate],
    }));
  },

  removeLastCoordinate: () => {
    set((state) => ({
      recordedCoordinates: state.recordedCoordinates.slice(0, -1),
    }));
  },

  clearCoordinates: () => {
    set({ recordedCoordinates: [] });
  },

  saveMeasurement: async (measurement) => {
    const { recordedCoordinates } = get();
    
    if (recordedCoordinates.length < 3) {
      throw new Error('At least 3 coordinates are required');
    }

    // Calculate area
    const area = calculatePolygonArea(recordedCoordinates);

    const newMeasurement = {
      ...measurement,
      coordinates: recordedCoordinates,
      areaSqm: area.areaSqm,
      areaAcres: area.areaAcres,
      areaHectares: area.areaHectares,
      synced: false,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    };

    const id = await measurementsDb.saveMeasurement({
      ...newMeasurement,
      coordinates: JSON.stringify(recordedCoordinates),
    });

    const savedMeasurement = { ...newMeasurement, id };

    set((state) => ({
      measurements: [savedMeasurement, ...state.measurements],
      recordedCoordinates: [],
      isRecording: false,
    }));

    return savedMeasurement;
  },

  updateMeasurement: async (id, updates) => {
    await measurementsDb.updateMeasurement(id, updates);
    
    set((state) => ({
      measurements: state.measurements.map((m) =>
        m.id === id ? { ...m, ...updates, updatedAt: new Date().toISOString() } : m
      ),
    }));
  },

  deleteMeasurement: async (id) => {
    await measurementsDb.deleteMeasurement(id);
    
    set((state) => ({
      measurements: state.measurements.filter((m) => m.id !== id),
    }));
  },

  calculateCurrentArea: () => {
    const { recordedCoordinates } = get();
    
    if (recordedCoordinates.length < 3) {
      return null;
    }

    return calculatePolygonArea(recordedCoordinates);
  },

  setCurrentMeasurement: (measurement) => {
    set({ currentMeasurement: measurement });
  },
}));
