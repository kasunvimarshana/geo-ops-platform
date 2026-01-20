import AsyncStorage from '@react-native-async-storage/async-storage';
import { SyncQueue } from '../database/models/SyncQueue';
import { apiClient } from '../api/client';
import { Job } from '../types/job';
import { Land } from '../types/land';

class SyncService {
    async syncOfflineData() {
        try {
            const syncQueue = await SyncQueue.findAll();

            for (const item of syncQueue) {
                await this.syncItem(item);
            }

            await SyncQueue.clear(); // Clear the sync queue after successful sync
        } catch (error) {
            console.error('Error syncing offline data:', error);
        }
    }

    async syncItem(item: any) {
        switch (item.type) {
            case 'job':
                await this.syncJob(item.data);
                break;
            case 'land':
                await this.syncLand(item.data);
                break;
            default:
                console.warn('Unknown sync item type:', item.type);
        }
    }

    async syncJob(jobData: Job) {
        try {
            const response = await apiClient.post('/api/v1/jobs', jobData);
            console.log('Job synced successfully:', response.data);
        } catch (error) {
            console.error('Error syncing job:', error);
        }
    }

    async syncLand(landData: Land) {
        try {
            const response = await apiClient.post('/api/v1/lands', landData);
            console.log('Land synced successfully:', response.data);
        } catch (error) {
            console.error('Error syncing land:', error);
        }
    }
}

export const syncService = new SyncService();