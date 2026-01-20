import AsyncStorage from '@react-native-async-storage/async-storage';
import api from './api';
import { STORAGE_KEYS } from '../constants';
import { AuthResponse } from '../types';

export const authService = {
  async register(data: {
    email: string;
    password: string;
    firstName: string;
    lastName: string;
    phone: string;
    organizationName?: string;
  }): Promise<AuthResponse> {
    const response = await api.post('/auth/register', data);
    const { token, user } = response.data.data;
    
    await AsyncStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token);
    await AsyncStorage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(user));
    
    return { token, user };
  },

  async login(email: string, password: string): Promise<AuthResponse> {
    const response = await api.post('/auth/login', { email, password });
    const { token, user } = response.data.data;
    
    await AsyncStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token);
    await AsyncStorage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(user));
    
    return { token, user };
  },

  async logout(): Promise<void> {
    await AsyncStorage.multiRemove([
      STORAGE_KEYS.AUTH_TOKEN,
      STORAGE_KEYS.USER_DATA,
    ]);
  },

  async getProfile() {
    const response = await api.get('/auth/profile');
    return response.data.data;
  },

  async getCurrentUser() {
    const userData = await AsyncStorage.getItem(STORAGE_KEYS.USER_DATA);
    return userData ? JSON.parse(userData) : null;
  },

  async isAuthenticated(): Promise<boolean> {
    const token = await AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);
    return !!token;
  },
};
