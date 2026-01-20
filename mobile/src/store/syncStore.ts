import create from 'zustand';

interface SyncState {
  isSyncing: boolean;
  syncQueue: string[];
  addToQueue: (item: string) => void;
  removeFromQueue: (item: string) => void;
  startSync: () => void;
  stopSync: () => void;
}

const useSyncStore = create<SyncState>((set) => ({
  isSyncing: false,
  syncQueue: [],
  addToQueue: (item) => set((state) => ({ syncQueue: [...state.syncQueue, item] })),
  removeFromQueue: (item) => set((state) => ({
    syncQueue: state.syncQueue.filter((i) => i !== item),
  })),
  startSync: () => set({ isSyncing: true }),
  stopSync: () => set({ isSyncing: false }),
}));

export default useSyncStore;