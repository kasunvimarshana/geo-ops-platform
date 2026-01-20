import { databaseService } from '../storage/database';
import { apiClient } from '../api/client';
import { useSyncStore } from '@/stores/syncStore';
import { SyncPayload, SyncResponse, SyncItem } from '@/types';
import { SYNC_CONFIG } from '@/constants';

/**
 * Sync Service
 * 
 * Handles background synchronization of offline data
 */
class SyncService {
  private syncInterval: NodeJS.Timeout | null = null;
  private isSyncing = false;

  async start(): Promise<void> {
    await databaseService.init();
    this.startAutoSync();
  }

  private startAutoSync(): void {
    if (this.syncInterval) {
      clearInterval(this.syncInterval);
    }

    this.syncInterval = setInterval(() => {
      this.syncAll().catch(error => {
        console.error('Auto-sync failed:', error);
      });
    }, SYNC_CONFIG.AUTO_SYNC_INTERVAL);
  }

  stop(): void {
    if (this.syncInterval) {
      clearInterval(this.syncInterval);
      this.syncInterval = null;
    }
  }

  async syncAll(): Promise<boolean> {
    if (this.isSyncing) {
      return false;
    }

    try {
      this.isSyncing = true;
      const { startSync, endSync, addSyncError } = useSyncStore.getState();
      
      startSync();

      const payload = await this.collectPendingItems();
      const pendingCount = this.countPendingItems(payload);

      if (pendingCount === 0) {
        endSync();
        return true;
      }

      const response = await apiClient.post<SyncResponse>('/sync', payload);

      if (response.success) {
        await this.handleSyncSuccess(response.data, payload);
      } else {
        addSyncError('all', 'Sync failed');
      }

      endSync();
      return true;
    } catch (error: any) {
      console.error('Sync error:', error);
      useSyncStore.getState().addSyncError('all', error.message);
      useSyncStore.getState().endSync();
      return false;
    } finally {
      this.isSyncing = false;
    }
  }

  private async collectPendingItems(): Promise<SyncPayload> {
    const payload: SyncPayload = {};

    const lands = await databaseService.query(
      'SELECT * FROM lands WHERE sync_status = ? ORDER BY updated_at ASC LIMIT ?',
      ['pending', SYNC_CONFIG.BATCH_SIZE]
    );

    if (lands.length > 0) {
      payload.lands = lands.map((land: any) => ({
        offline_id: land.offline_id,
        action: land.server_id ? 'update' : 'create',
        data: {
          ...land,
          polygon: JSON.parse(land.polygon),
        },
        updated_at: land.updated_at,
      }));
    }

    const jobs = await databaseService.query(
      'SELECT * FROM jobs WHERE sync_status = ? ORDER BY updated_at ASC LIMIT ?',
      ['pending', SYNC_CONFIG.BATCH_SIZE]
    );

    if (jobs.length > 0) {
      payload.jobs = jobs.map((job: any) => ({
        offline_id: job.offline_id,
        action: job.server_id ? 'update' : 'create',
        data: {
          ...job,
          location: {
            latitude: job.location_latitude,
            longitude: job.location_longitude,
          },
        },
        updated_at: job.updated_at,
      }));
    }

    const invoices = await databaseService.query(
      'SELECT * FROM invoices WHERE sync_status = ? ORDER BY updated_at ASC LIMIT ?',
      ['pending', SYNC_CONFIG.BATCH_SIZE]
    );

    if (invoices.length > 0) {
      payload.invoices = invoices.map((invoice: any) => ({
        offline_id: invoice.offline_id,
        action: invoice.server_id ? 'update' : 'create',
        data: invoice,
        updated_at: invoice.updated_at,
      }));
    }

    return payload;
  }

  private countPendingItems(payload: SyncPayload): number {
    let count = 0;
    if (payload.lands) count += payload.lands.length;
    if (payload.jobs) count += payload.jobs.length;
    if (payload.invoices) count += payload.invoices.length;
    return count;
  }

  private async handleSyncSuccess(data: SyncResponse['data'], payload: SyncPayload): Promise<void> {
    await databaseService.transaction(async () => {
      if (data.synced.lands > 0 && payload.lands) {
        const offlineIds = payload.lands.map(item => item.offline_id);
        const placeholders = offlineIds.map(() => '?').join(',');
        await databaseService.execute(
          `UPDATE lands SET sync_status = ? WHERE offline_id IN (${placeholders})`,
          ['synced', ...offlineIds]
        );
      }

      if (data.synced.jobs > 0 && payload.jobs) {
        const offlineIds = payload.jobs.map(item => item.offline_id);
        const placeholders = offlineIds.map(() => '?').join(',');
        await databaseService.execute(
          `UPDATE jobs SET sync_status = ? WHERE offline_id IN (${placeholders})`,
          ['synced', ...offlineIds]
        );
      }

      if (data.synced.invoices > 0 && payload.invoices) {
        const offlineIds = payload.invoices.map(item => item.offline_id);
        const placeholders = offlineIds.map(() => '?').join(',');
        await databaseService.execute(
          `UPDATE invoices SET sync_status = ? WHERE offline_id IN (${placeholders})`,
          ['synced', ...offlineIds]
        );
      }
    });

    if (data.conflicts.length > 0) {
      console.warn('Sync conflicts:', data.conflicts);
      data.conflicts.forEach((conflict) => {
        useSyncStore.getState().addSyncError(
          conflict.entity_type,
          `Conflict: ${conflict.reason}`
        );
      });
    }
  }

  async getPendingCount(): Promise<number> {
    const results = await Promise.all([
      databaseService.query('SELECT COUNT(*) as count FROM lands WHERE sync_status = ?', ['pending']),
      databaseService.query('SELECT COUNT(*) as count FROM jobs WHERE sync_status = ?', ['pending']),
      databaseService.query('SELECT COUNT(*) as count FROM invoices WHERE sync_status = ?', ['pending']),
    ]);

    return results.reduce((sum, result) => sum + (result[0] as any).count, 0);
  }
}

export const syncService = new SyncService();
export default syncService;
