import axios from 'axios';
import { Land } from '../types/land';

const API_URL = 'https://your-api-url.com/api/v1/lands';

export const getLands = async (): Promise<Land[]> => {
    const response = await axios.get(API_URL);
    return response.data;
};

export const getLandById = async (id: string): Promise<Land> => {
    const response = await axios.get(`${API_URL}/${id}`);
    return response.data;
};

export const createLand = async (landData: Land): Promise<Land> => {
    const response = await axios.post(API_URL, landData);
    return response.data;
};

export const updateLand = async (id: string, landData: Land): Promise<Land> => {
    const response = await axios.put(`${API_URL}/${id}`, landData);
    return response.data;
};

export const deleteLand = async (id: string): Promise<void> => {
    await axios.delete(`${API_URL}/${id}`);
};