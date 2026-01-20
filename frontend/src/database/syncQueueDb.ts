/**
 * Sync Queue Operations
 * 
 * This module manages the sync queue for offline operations.
 */

import { executeSql } from './database';

export interface SyncQueueItem {
  id?: number;
  entity_type: 'measurement' | 'job' | 'expense' | 'payment';
  entity_id: number;
  operation: 'create' | 'update' | 'delete';
  data: string; // JSON string
  created_at?: string;
  retry_count: number;
  last_error?: string;
}

/**
 * Add an item to the sync queue
 */
export const addToSyncQueue = async (
  item: Omit<SyncQueueItem, 'id' | 'created_at' | 'retry_count'>
): Promise<number> => {
  try {
    const result = await executeSql(
      `INSERT INTO sync_queue 
       (entity_type, entity_id, operation, data, retry_count) 
       VALUES (?, ?, ?, ?, 0)`,
      [item.entity_type, item.entity_id, item.operation, item.data]
    );

    return result.insertId || 0;
  } catch (error) {
    console.error('Error adding to sync queue:', error);
    throw error;
  }
};

/**
 * Get all pending sync items
 */
export const getPendingSyncItems = async (): Promise<SyncQueueItem[]> => {
  try {
    const result = await executeSql(
      'SELECT * FROM sync_queue ORDER BY created_at ASC LIMIT 50'
    );

    const items: SyncQueueItem[] = [];
    for (let i = 0; i < result.rows.length; i++) {
      items.push(result.rows.item(i));
    }

    return items;
  } catch (error) {
    console.error('Error fetching sync queue:', error);
    throw error;
  }
};

/**
 * Remove an item from the sync queue
 */
export const removeFromSyncQueue = async (id: number): Promise<void> => {
  try {
    await executeSql('DELETE FROM sync_queue WHERE id = ?', [id]);
  } catch (error) {
    console.error('Error removing from sync queue:', error);
    throw error;
  }
};

/**
 * Update retry count and last error for a sync item
 */
export const updateSyncItemError = async (
  id: number,
  error: string
): Promise<void> => {
  try {
    await executeSql(
      'UPDATE sync_queue SET retry_count = retry_count + 1, last_error = ? WHERE id = ?',
      [error, id]
    );
  } catch (error) {
    console.error('Error updating sync item:', error);
    throw error;
  }
};

/**
 * Clear all items from the sync queue
 */
export const clearSyncQueue = async (): Promise<void> => {
  try {
    await executeSql('DELETE FROM sync_queue');
  } catch (error) {
    console.error('Error clearing sync queue:', error);
    throw error;
  }
};

/**
 * Get sync queue count
 */
export const getSyncQueueCount = async (): Promise<number> => {
  try {
    const result = await executeSql('SELECT COUNT(*) as count FROM sync_queue');
    return result.rows.item(0).count;
  } catch (error) {
    console.error('Error getting sync queue count:', error);
    return 0;
  }
};
