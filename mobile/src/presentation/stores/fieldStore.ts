/**
 * Field Store
 * Global state management for fields
 */

import { create } from 'zustand';
import { Field, FieldMeasurement, FieldCreateData } from '../../domain/entities/Field';
import FieldUseCase from '../../application/usecases/FieldUseCase';

interface FieldState {
  fields: Field[];
  currentField: Field | null;
  currentMeasurement: FieldMeasurement | null;
  isLoading: boolean;
  error: string | null;
  
  // Actions
  fetchFields: (page?: number) => Promise<void>;
  fetchField: (id: string) => Promise<void>;
  createField: (field: FieldCreateData) => Promise<void>;
  updateField: (id: string, data: Partial<FieldCreateData>) => Promise<void>;
  deleteField: (id: string) => Promise<void>;
  setCurrentMeasurement: (measurement: FieldMeasurement) => void;
  saveMeasurement: () => Promise<void>;
  clearError: () => void;
}

export const useFieldStore = create<FieldState>((set, get) => ({
  fields: [],
  currentField: null,
  currentMeasurement: null,
  isLoading: false,
  error: null,

  fetchFields: async (page = 1) => {
    set({ isLoading: true, error: null });
    try {
      const response = await FieldUseCase.getFields(page);
      set({ fields: response.data, isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
    }
  },

  fetchField: async (id: string) => {
    set({ isLoading: true, error: null });
    try {
      const field = await FieldUseCase.getField(id);
      set({ currentField: field, isLoading: false });
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
    }
  },

  createField: async (field: FieldCreateData) => {
    set({ isLoading: true, error: null });
    try {
      await FieldUseCase.createField(field);
      set({ isLoading: false });
      // Refresh fields list
      get().fetchFields();
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  updateField: async (id: string, data: Partial<FieldCreateData>) => {
    set({ isLoading: true, error: null });
    try {
      await FieldUseCase.updateField(id, data);
      set({ isLoading: false });
      // Refresh fields list
      get().fetchFields();
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  deleteField: async (id: string) => {
    set({ isLoading: true, error: null });
    try {
      await FieldUseCase.deleteField(id);
      set({ isLoading: false });
      // Refresh fields list
      get().fetchFields();
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  setCurrentMeasurement: (measurement: FieldMeasurement) => {
    set({ currentMeasurement: measurement });
  },

  saveMeasurement: async () => {
    const { currentMeasurement } = get();
    if (!currentMeasurement) {
      throw new Error('No measurement to save');
    }

    set({ isLoading: true, error: null });
    try {
      const field = await FieldUseCase.saveMeasurement(currentMeasurement);
      set({ 
        currentField: field, 
        currentMeasurement: null, 
        isLoading: false 
      });
      // Refresh fields list
      get().fetchFields();
    } catch (error: any) {
      set({ error: error.message, isLoading: false });
      throw error;
    }
  },

  clearError: () => set({ error: null }),
}));
