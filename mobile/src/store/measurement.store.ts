import { create } from 'zustand';
import { LandMeasurement } from '../types';

interface MeasurementState {
  measurements: LandMeasurement[];
  currentMeasurement: LandMeasurement | null;
  isRecording: boolean;
  setMeasurements: (measurements: LandMeasurement[]) => void;
  addMeasurement: (measurement: LandMeasurement) => void;
  setCurrentMeasurement: (measurement: LandMeasurement | null) => void;
  setIsRecording: (isRecording: boolean) => void;
}

export const useMeasurementStore = create<MeasurementState>((set) => ({
  measurements: [],
  currentMeasurement: null,
  isRecording: false,
  setMeasurements: (measurements) => set({ measurements }),
  addMeasurement: (measurement) => 
    set((state) => ({ measurements: [measurement, ...state.measurements] })),
  setCurrentMeasurement: (measurement) => set({ currentMeasurement: measurement }),
  setIsRecording: (isRecording) => set({ isRecording }),
}));
