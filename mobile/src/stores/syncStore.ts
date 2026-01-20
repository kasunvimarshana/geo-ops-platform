import { create } from 'zustand';

interface SyncState {
  isSyncing: boolean;
  lastSyncTime: Date | null;
  pendingItemsCount: number;
  syncErrors: Array<{
    entityType: string;
    error: string;
    timestamp: Date;
  }>;
  
  // Actions
  startSync: () => void;
  endSync: () => void;
  setLastSyncTime: (time: Date) => void;
  setPendingItemsCount: (count: number) => void;
  addSyncError: (entityType: string, error: string) => void;
  clearSyncErrors: () => void;
}

/**
 * Sync Store
 * 
 * Manages offline synchronization state
 * Tracks sync status and pending items
 */
export const useSyncStore = create<SyncState>((set) => ({
  isSyncing: false,
  lastSyncTime: null,
  pendingItemsCount: 0,
  syncErrors: [],

  startSync: () => {
    set({ isSyncing: true });
  },

  endSync: () => {
    set({ isSyncing: false, lastSyncTime: new Date() });
  },

  setLastSyncTime: (time) => {
    set({ lastSyncTime: time });
  },

  setPendingItemsCount: (count) => {
    set({ pendingItemsCount: count });
  },

  addSyncError: (entityType, error) => {
    set((state) => ({
      syncErrors: [
        ...state.syncErrors,
        {
          entityType,
          error,
          timestamp: new Date(),
        },
      ],
    }));
  },

  clearSyncErrors: () => {
    set({ syncErrors: [] });
  },
}));
