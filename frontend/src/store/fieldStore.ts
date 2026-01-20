import { create } from 'zustand';
import { Field } from '@types/index';

interface FieldStore {
  fields: Field[];
  selectedField: Field | null;
  isLoading: boolean;
  error: string | null;
  setFields: (fields: Field[]) => void;
  setSelectedField: (field: Field | null) => void;
  setLoading: (loading: boolean) => void;
  setError: (error: string | null) => void;
  addField: (field: Field) => void;
  updateField: (field: Field) => void;
  removeField: (fieldId: string) => void;
}

export const useFieldStore = create<FieldStore>((set) => ({
  fields: [],
  selectedField: null,
  isLoading: false,
  error: null,
  setFields: (fields) => set({ fields }),
  setSelectedField: (field) => set({ selectedField: field }),
  setLoading: (isLoading) => set({ isLoading }),
  setError: (error) => set({ error }),
  addField: (field) =>
    set((state) => ({
      fields: [...state.fields, field],
    })),
  updateField: (field) =>
    set((state) => ({
      fields: state.fields.map((f) => (f.id === field.id ? field : f)),
    })),
  removeField: (fieldId) =>
    set((state) => ({
      fields: state.fields.filter((f) => f.id !== fieldId),
    })),
}));
