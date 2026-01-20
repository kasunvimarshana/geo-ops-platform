import { useEffect } from 'react';
import { useSyncStore } from '../../store/syncStore';
import { useNetworkStatus } from './useNetworkStatus';

export const useOfflineSync = () => {
  const { syncNow, syncStatus, lastSyncTime, pendingCount } = useSyncStore();
  const { isConnected } = useNetworkStatus();

  useEffect(() => {
    if (isConnected && pendingCount > 0) {
      syncNow();
    }
  }, [isConnected]);

  return {
    syncNow,
    syncStatus,
    lastSyncTime,
    pendingCount,
    isConnected,
  };
};
