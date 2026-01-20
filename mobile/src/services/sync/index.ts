import { getDatabase } from '../storage/database';
import { config } from '../../config';

interface SyncQueueItem {
  id?: number;
  entityType: string;
  entityId: string;
  operation: 'create' | 'update' | 'delete';
  data: string;
  createdAt: string;
}

export class SyncService {
  private syncInterval: NodeJS.Timeout | null = null;

  async addToQueue(
    entityType: string,
    entityId: string,
    operation: 'create' | 'update' | 'delete',
    data: Record<string, unknown>
  ): Promise<void> {
    try {
      const db = getDatabase();
      await db.runAsync(
        `INSERT INTO sync_queue (entityType, entityId, operation, data, createdAt) 
         VALUES (?, ?, ?, ?, ?)`,
        [entityType, entityId, operation, JSON.stringify(data), new Date().toISOString()]
      );
    } catch (error) {
      console.error('Error adding to sync queue:', error);
      throw error;
    }
  }

  async getPendingItems(): Promise<SyncQueueItem[]> {
    try {
      const db = getDatabase();
      const result = await db.getAllAsync<SyncQueueItem>(
        'SELECT * FROM sync_queue ORDER BY createdAt ASC'
      );
      return result;
    } catch (error) {
      console.error('Error getting pending items:', error);
      return [];
    }
  }

  async removeFromQueue(id: number): Promise<void> {
    try {
      const db = getDatabase();
      await db.runAsync('DELETE FROM sync_queue WHERE id = ?', [id]);
    } catch (error) {
      console.error('Error removing from sync queue:', error);
      throw error;
    }
  }

  async syncAll(): Promise<void> {
    try {
      const pendingItems = await this.getPendingItems();
      
      for (const item of pendingItems) {
        try {
          await this.syncItem(item);
          if (item.id) {
            await this.removeFromQueue(item.id);
          }
        } catch (error) {
          console.error(`Error syncing item ${item.id}:`, error);
        }
      }
    } catch (error) {
      console.error('Error syncing all items:', error);
    }
  }

  private async syncItem(item: SyncQueueItem): Promise<void> {
    console.log(`Syncing ${item.operation} ${item.entityType} ${item.entityId}`);
  }

  startAutoSync(): void {
    if (this.syncInterval) {
      return;
    }

    this.syncInterval = setInterval(() => {
      this.syncAll();
    }, config.syncInterval);
  }

  stopAutoSync(): void {
    if (this.syncInterval) {
      clearInterval(this.syncInterval);
      this.syncInterval = null;
    }
  }
}

export const syncService = new SyncService();
