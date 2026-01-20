import create from 'zustand';
import { Land } from '../types/land';
import { fetchLands, createLand, updateLand, deleteLand } from '../api/lands';

interface LandStore {
  lands: Land[];
  fetchLands: () => Promise<void>;
  addLand: (land: Land) => Promise<void>;
  editLand: (land: Land) => Promise<void>;
  removeLand: (id: string) => Promise<void>;
}

const useLandStore = create<LandStore>((set) => ({
  lands: [],
  fetchLands: async () => {
    const lands = await fetchLands();
    set({ lands });
  },
  addLand: async (land) => {
    await createLand(land);
    set((state) => ({ lands: [...state.lands, land] }));
  },
  editLand: async (land) => {
    await updateLand(land);
    set((state) => ({
      lands: state.lands.map((l) => (l.id === land.id ? land : l)),
    }));
  },
  removeLand: async (id) => {
    await deleteLand(id);
    set((state) => ({ lands: state.lands.filter((land) => land.id !== id) }));
  },
}));

export default useLandStore;