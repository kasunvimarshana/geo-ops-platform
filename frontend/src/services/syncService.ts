/**
 * Offline Sync Service
 * 
 * This service handles background synchronization between local SQLite database
 * and the remote API server. It manages conflict resolution and retry logic.
 */

import NetInfo from '@react-native-community/netinfo';
import config from '../config';
import { measurementApi } from '../api/measurements';
import jobApi from '../api/jobs';
import {
  getAllMeasurements,
  getUnsyncedMeasurements,
  upsertMeasurementsFromServer,
  markMeasurementAsSynced,
} from '../../database/measurementsDb';
import {
  getAllJobs,
  getUnsyncedJobs,
  upsertJobsFromServer,
  markJobAsSynced,
} from '../../database/jobsDb';
import {
  getPendingSyncItems,
  removeFromSyncQueue,
  updateSyncItemError,
  addToSyncQueue,
} from '../../database/syncQueueDb';

// Sync configuration from centralized config file
const MAX_RETRY_COUNT = config.sync.maxRetryCount;
const SYNC_INTERVAL = config.sync.interval;

let syncInterval: NodeJS.Timeout | null = null;
let isSyncing = false;

/**
 * Check if device is online
 */
export const isOnline = async (): Promise<boolean> => {
  const state = await NetInfo.fetch();
  return state.isConnected === true && state.isInternetReachable === true;
};

/**
 * Sync measurements from local to server
 */
const syncMeasurementsToServer = async (): Promise<void> => {
  try {
    const unsynced = await getUnsyncedMeasurements();

    for (const measurement of unsynced) {
      try {
        if (measurement.deleted) {
          // Delete on server if has server_id
          if (measurement.server_id) {
            await measurementApi.delete(measurement.server_id);
          }
        } else if (measurement.server_id) {
          // Update existing
          const response = await measurementApi.update(measurement.server_id, {
            name: measurement.name,
            coordinates: JSON.parse(measurement.coordinates || '[]'),
          });
        } else {
          // Create new
          const response = await measurementApi.create({
            name: measurement.name,
            coordinates: JSON.parse(measurement.coordinates || '[]'),
          });
          
          if (response.data && response.data.id && measurement.id) {
            await markMeasurementAsSynced(measurement.id, response.data.id);
          }
        }
      } catch (error: any) {
        console.error(`Error syncing measurement ${measurement.id}:`, error);
        // Add to sync queue for retry
        if (measurement.id) {
          await addToSyncQueue({
            entity_type: 'measurement',
            entity_id: measurement.id,
            operation: measurement.deleted ? 'delete' : measurement.server_id ? 'update' : 'create',
            data: JSON.stringify(measurement),
          });
        }
      }
    }
  } catch (error) {
    console.error('Error syncing measurements to server:', error);
    throw error;
  }
};

/**
 * Sync measurements from server to local
 */
const syncMeasurementsFromServer = async (): Promise<void> => {
  try {
    const response = await measurementApi.getAll();
    const serverMeasurements = response.data.data || [];
    
    if (serverMeasurements.length > 0) {
      await upsertMeasurementsFromServer(serverMeasurements);
    }
  } catch (error) {
    console.error('Error syncing measurements from server:', error);
    throw error;
  }
};

/**
 * Sync jobs from local to server
 */
const syncJobsToServer = async (): Promise<void> => {
  try {
    const unsynced = await getUnsyncedJobs();

    for (const job of unsynced) {
      try {
        if (job.deleted) {
          // Delete on server if has server_id
          if (job.server_id) {
            await jobApi.delete(job.server_id);
          }
        } else if (job.server_id) {
          // Update existing
          await jobApi.update(job.server_id, {
            customer_id: job.customer_id,
            land_measurement_id: job.land_measurement_id,
            driver_id: job.driver_id,
            machine_id: job.machine_id,
            service_type: job.service_type,
            notes: job.notes,
          });

          // Update status if changed
          if (job.status) {
            await jobApi.updateStatus(job.server_id, { status: job.status });
          }
        } else {
          // Create new
          const response = await jobApi.create({
            customer_id: job.customer_id,
            land_measurement_id: job.land_measurement_id,
            driver_id: job.driver_id,
            machine_id: job.machine_id,
            service_type: job.service_type,
            notes: job.notes,
          });

          if (response.data && response.data.id && job.id) {
            await markJobAsSynced(job.id, response.data.id);
          }
        }
      } catch (error: any) {
        console.error(`Error syncing job ${job.id}:`, error);
        // Add to sync queue for retry
        if (job.id) {
          await addToSyncQueue({
            entity_type: 'job',
            entity_id: job.id,
            operation: job.deleted ? 'delete' : job.server_id ? 'update' : 'create',
            data: JSON.stringify(job),
          });
        }
      }
    }
  } catch (error) {
    console.error('Error syncing jobs to server:', error);
    throw error;
  }
};

/**
 * Sync jobs from server to local
 */
const syncJobsFromServer = async (): Promise<void> => {
  try {
    const response = await jobApi.getAll();
    const serverJobs = response.data || [];

    if (serverJobs.length > 0) {
      await upsertJobsFromServer(serverJobs);
    }
  } catch (error) {
    console.error('Error syncing jobs from server:', error);
    throw error;
  }
};

/**
 * Process sync queue items
 */
const processSyncQueue = async (): Promise<void> => {
  try {
    const queueItems = await getPendingSyncItems();

    for (const item of queueItems) {
      if (item.retry_count >= MAX_RETRY_COUNT) {
        // Skip items that have failed too many times
        console.warn(`Skipping sync item ${item.id} after ${MAX_RETRY_COUNT} retries`);
        continue;
      }

      try {
        const data = JSON.parse(item.data);

        switch (item.entity_type) {
          case 'measurement':
            if (item.operation === 'create') {
              const response = await measurementApi.create({
                name: data.name,
                coordinates: JSON.parse(data.coordinates || '[]'),
              });
              if (response.data && response.data.id) {
                await markMeasurementAsSynced(item.entity_id, response.data.id);
              }
            } else if (item.operation === 'update' && data.server_id) {
              await measurementApi.update(data.server_id, {
                name: data.name,
                coordinates: JSON.parse(data.coordinates || '[]'),
              });
            } else if (item.operation === 'delete' && data.server_id) {
              await measurementApi.delete(data.server_id);
            }
            break;

          case 'job':
            if (item.operation === 'create') {
              const response = await jobApi.create({
                customer_id: data.customer_id,
                land_measurement_id: data.land_measurement_id,
                driver_id: data.driver_id,
                machine_id: data.machine_id,
                service_type: data.service_type,
                notes: data.notes,
              });
              if (response.data && response.data.id) {
                await markJobAsSynced(item.entity_id, response.data.id);
              }
            } else if (item.operation === 'update' && data.server_id) {
              await jobApi.update(data.server_id, {
                customer_id: data.customer_id,
                land_measurement_id: data.land_measurement_id,
                driver_id: data.driver_id,
                machine_id: data.machine_id,
                service_type: data.service_type,
                notes: data.notes,
              });
            } else if (item.operation === 'delete' && data.server_id) {
              await jobApi.delete(data.server_id);
            }
            break;
        }

        // Remove from queue on success
        if (item.id) {
          await removeFromSyncQueue(item.id);
        }
      } catch (error: any) {
        console.error(`Error processing sync queue item ${item.id}:`, error);
        if (item.id) {
          await updateSyncItemError(
            item.id,
            error.message || 'Unknown error'
          );
        }
      }
    }
  } catch (error) {
    console.error('Error processing sync queue:', error);
    throw error;
  }
};

/**
 * Perform full sync (upload local changes, then download server changes)
 */
export const performSync = async (): Promise<{
  success: boolean;
  message: string;
}> => {
  if (isSyncing) {
    return { success: false, message: 'Sync already in progress' };
  }

  try {
    isSyncing = true;

    // Check if online
    const online = await isOnline();
    if (!online) {
      return { success: false, message: 'No internet connection' };
    }

    console.log('Starting sync...');

    // 1. Upload local changes to server
    await syncMeasurementsToServer();
    await syncJobsToServer();

    // 2. Process sync queue for failed items
    await processSyncQueue();

    // 3. Download server changes to local
    await syncMeasurementsFromServer();
    await syncJobsFromServer();

    console.log('Sync completed successfully');

    return { success: true, message: 'Sync completed successfully' };
  } catch (error: any) {
    console.error('Sync error:', error);
    return {
      success: false,
      message: error.message || 'Sync failed',
    };
  } finally {
    isSyncing = false;
  }
};

/**
 * Start automatic background sync
 */
export const startBackgroundSync = (): void => {
  if (syncInterval) {
    return; // Already running
  }

  console.log('Starting background sync...');

  syncInterval = setInterval(async () => {
    const online = await isOnline();
    if (online && !isSyncing) {
      console.log('Running background sync...');
      await performSync();
    }
  }, SYNC_INTERVAL);
};

/**
 * Stop automatic background sync
 */
export const stopBackgroundSync = (): void => {
  if (syncInterval) {
    clearInterval(syncInterval);
    syncInterval = null;
    console.log('Background sync stopped');
  }
};

/**
 * Get sync status
 */
export const getSyncStatus = (): {
  isRunning: boolean;
  isSyncing: boolean;
} => {
  return {
    isRunning: syncInterval !== null,
    isSyncing,
  };
};
