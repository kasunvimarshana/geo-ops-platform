import { create } from 'zustand';
import { syncService } from '../shared/services/sync/syncService';
import NetInfo from '@react-native-community/netinfo';
import { SyncStatus, NetworkStatus } from '../shared/types/common.types';

interface SyncState {
  syncStatus: SyncStatus;
  networkStatus: NetworkStatus;
  lastSyncTime: Date | null;
  pendingCount: number;

  initSync: () => void;
  syncNow: () => Promise<void>;
  updateNetworkStatus: (status: NetworkStatus) => void;
  setPendingCount: (count: number) => void;
}

export const useSyncStore = create<SyncState>((set) => ({
  syncStatus: 'idle',
  networkStatus: 'unknown',
  lastSyncTime: null,
  pendingCount: 0,

  initSync: () => {
    // Start auto sync
    syncService.startAutoSync();

    // Monitor network status
    NetInfo.addEventListener((state) => {
      set({
        networkStatus: state.isConnected ? 'online' : 'offline',
      });

      if (state.isConnected) {
        syncService.syncAll();
      }
    });
  },

  syncNow: async () => {
    set({ syncStatus: 'syncing' });
    try {
      await syncService.syncAll();
      set({
        syncStatus: 'success',
        lastSyncTime: new Date(),
      });
    } catch (error) {
      set({ syncStatus: 'error' });
      console.error('Sync error:', error);
    }
  },

  updateNetworkStatus: (status: NetworkStatus) => {
    set({ networkStatus: status });
  },

  setPendingCount: (count: number) => {
    set({ pendingCount: count });
  },
}));
