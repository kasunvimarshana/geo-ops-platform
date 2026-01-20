import * as SecureStore from 'expo-secure-store';
import { User, ApiResponse } from '@types/index';
import { apiService } from './api';

interface LoginRequest {
  email: string;
  password: string;
}

interface LoginResponse {
  user: User;
  token: string;
}

export const authService = {
  async login(credentials: LoginRequest): Promise<ApiResponse<LoginResponse>> {
    try {
      const response = await apiService.instance.post<LoginResponse>(
        '/auth/login',
        credentials
      );
      const data = response.data;

      // Store auth token securely
      if (data.token) {
        await SecureStore.setItemAsync('authToken', data.token);
      }

      return {
        success: true,
        data,
      };
    } catch (error) {
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Login failed',
      };
    }
  },

  async logout(): Promise<void> {
    try {
      await apiService.instance.post('/auth/logout');
      await SecureStore.deleteItemAsync('authToken');
    } catch (error) {
      console.error('Logout error:', error);
      await SecureStore.deleteItemAsync('authToken');
    }
  },

  async getCurrentUser(): Promise<ApiResponse<User>> {
    try {
      const response = await apiService.instance.get<User>('/auth/me');
      return {
        success: true,
        data: response.data,
      };
    } catch (error) {
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Failed to fetch user',
      };
    }
  },

  async isAuthenticated(): Promise<boolean> {
    try {
      const token = await SecureStore.getItemAsync('authToken');
      return !!token;
    } catch {
      return false;
    }
  },
};
