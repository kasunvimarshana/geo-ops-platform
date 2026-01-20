import NetInfo from '@react-native-community/netinfo';
import { sqliteService } from '../storage/sqlite.service';
import { jobsApi } from '../api/jobs.api';
import { plotsApi } from '../api/plots.api';
import { SYNC_CONFIG } from '../../constants/config';
import { SyncQueueItem } from '../../types/api.types';

class SyncService {
  private isSyncing = false;
  private syncInterval: NodeJS.Timeout | null = null;

  async startAutoSync() {
    this.syncInterval = setInterval(() => {
      this.syncAll();
    }, SYNC_CONFIG.SYNC_INTERVAL);
  }

  stopAutoSync() {
    if (this.syncInterval) {
      clearInterval(this.syncInterval);
      this.syncInterval = null;
    }
  }

  async syncAll(): Promise<void> {
    if (this.isSyncing) return;

    const netState = await NetInfo.fetch();
    if (!netState.isConnected) return;

    this.isSyncing = true;

    try {
      const queue = await sqliteService.getSyncQueue();
      
      for (const item of queue.slice(0, SYNC_CONFIG.BATCH_SIZE)) {
        await this.processSyncItem(item);
      }
    } catch (error) {
      console.error('Sync error:', error);
    } finally {
      this.isSyncing = false;
    }
  }

  private async processSyncItem(item: SyncQueueItem): Promise<void> {
    try {
      if (item.retry_count >= SYNC_CONFIG.MAX_RETRY_ATTEMPTS) {
        await sqliteService.updateSyncQueueItem(item.id!, 'failed');
        return;
      }

      await sqliteService.updateSyncQueueItem(item.id!, 'syncing');

      const data = JSON.parse(item.data);

      if (item.entity_type === 'job') {
        await this.syncJob(item.operation, data, item.entity_id);
      } else if (item.entity_type === 'plot') {
        await this.syncPlot(item.operation, data, item.entity_id);
      }

      await sqliteService.deleteSyncQueueItem(item.id!);
    } catch (error) {
      console.error(`Sync item ${item.id} failed:`, error);
      await sqliteService.updateSyncQueueItem(
        item.id!,
        'failed',
        item.retry_count + 1
      );
    }
  }

  private async syncJob(operation: string, data: any, entityId?: string): Promise<void> {
    if (operation === 'create') {
      const result = await jobsApi.createJob(data);
      // Update local record with server ID
      if (entityId) {
        const job = await sqliteService.getJobs();
        const localJob = job.find((j) => j.local_id === entityId);
        if (localJob) {
          await sqliteService.saveJob({ ...localJob, id: result.id, synced: true });
        }
      }
    } else if (operation === 'update' && data.id) {
      await jobsApi.updateJob(data.id, data);
    } else if (operation === 'delete' && data.id) {
      await jobsApi.deleteJob(data.id);
    }
  }

  private async syncPlot(operation: string, data: any, entityId?: string): Promise<void> {
    if (operation === 'create') {
      const result = await plotsApi.createPlot(data);
      if (entityId) {
        const plots = await sqliteService.getPlots();
        const localPlot = plots.find((p) => p.local_id === entityId);
        if (localPlot) {
          await sqliteService.savePlot({ ...localPlot, id: result.id, synced: true });
        }
      }
    } else if (operation === 'update' && data.id) {
      await plotsApi.updatePlot(data.id, data);
    } else if (operation === 'delete' && data.id) {
      await plotsApi.deletePlot(data.id);
    }
  }

  async queueJob(operation: 'create' | 'update' | 'delete', data: any, entityId?: string): Promise<void> {
    await sqliteService.addToSyncQueue({
      operation,
      entity_type: 'job',
      entity_id: entityId,
      data: JSON.stringify(data),
      status: 'pending',
      retry_count: 0,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
    });

    // Try immediate sync if online
    const netState = await NetInfo.fetch();
    if (netState.isConnected) {
      await this.syncAll();
    }
  }

  async queuePlot(operation: 'create' | 'update' | 'delete', data: any, entityId?: string): Promise<void> {
    await sqliteService.addToSyncQueue({
      operation,
      entity_type: 'plot',
      entity_id: entityId,
      data: JSON.stringify(data),
      status: 'pending',
      retry_count: 0,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
    });

    const netState = await NetInfo.fetch();
    if (netState.isConnected) {
      await this.syncAll();
    }
  }
}

export const syncService = new SyncService();
