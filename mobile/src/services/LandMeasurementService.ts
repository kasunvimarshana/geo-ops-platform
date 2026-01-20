import { API } from '../api/client';
import { Land } from '../types/land';

class LandMeasurementService {
    async measureLand(data: Land): Promise<Land> {
        try {
            const response = await API.post('/lands', data);
            return response.data;
        } catch (error) {
            throw new Error('Error measuring land: ' + error.message);
        }
    }

    async getLandById(id: string): Promise<Land> {
        try {
            const response = await API.get(`/lands/${id}`);
            return response.data;
        } catch (error) {
            throw new Error('Error fetching land data: ' + error.message);
        }
    }

    async updateLand(id: string, data: Land): Promise<Land> {
        try {
            const response = await API.put(`/lands/${id}`, data);
            return response.data;
        } catch (error) {
            throw new Error('Error updating land data: ' + error.message);
        }
    }

    async deleteLand(id: string): Promise<void> {
        try {
            await API.delete(`/lands/${id}`);
        } catch (error) {
            throw new Error('Error deleting land: ' + error.message);
        }
    }
}

export default new LandMeasurementService();