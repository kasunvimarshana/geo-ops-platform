import { useEffect } from 'react';
import { useStore } from '../store/syncStore';
import { SyncService } from '../services/SyncService';

const useOfflineSync = () => {
    const { syncQueue, clearSyncQueue } = useStore();

    useEffect(() => {
        const syncData = async () => {
            if (syncQueue.length > 0) {
                try {
                    await SyncService.syncOfflineData(syncQueue);
                    clearSyncQueue();
                } catch (error) {
                    console.error('Error syncing offline data:', error);
                }
            }
        };

        syncData();
    }, [syncQueue]);

    return { syncQueue };
};

export default useOfflineSync;