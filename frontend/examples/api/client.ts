import axios, { AxiosInstance, AxiosError } from 'axios';
import { Platform } from 'react-native';
import * as SecureStore from 'expo-secure-store';
import { useAuthStore } from '@/stores/authStore';

const API_URL = process.env.EXPO_PUBLIC_API_URL || 'http://localhost:8000/api/v1';
const TIMEOUT = parseInt(process.env.EXPO_PUBLIC_API_TIMEOUT || '30000');

/**
 * API Client
 * 
 * Centralized HTTP client with interceptors for authentication,
 * error handling, and offline support.
 */
class APIClient {
  private client: AxiosInstance;

  constructor() {
    this.client = axios.create({
      baseURL: API_URL,
      timeout: TIMEOUT,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Platform': Platform.OS,
        'X-App-Version': process.env.EXPO_PUBLIC_APP_VERSION || '1.0.0',
      },
    });

    this.setupInterceptors();
  }

  /**
   * Setup request and response interceptors
   */
  private setupInterceptors(): void {
    // Request interceptor - add auth token
    this.client.interceptors.request.use(
      async (config) => {
        const token = await this.getAccessToken();
        
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    // Response interceptor - handle errors and token refresh
    this.client.interceptors.response.use(
      (response) => {
        return response.data; // Return only data
      },
      async (error: AxiosError) => {
        const originalRequest = error.config as any;

        // Handle 401 Unauthorized - token expired
        if (error.response?.status === 401 && !originalRequest._retry) {
          originalRequest._retry = true;

          try {
            // Attempt to refresh token
            const newToken = await this.refreshAccessToken();
            
            if (newToken) {
              originalRequest.headers.Authorization = `Bearer ${newToken}`;
              return this.client(originalRequest);
            }
          } catch (refreshError) {
            // Refresh failed, logout user
            useAuthStore.getState().logout();
            return Promise.reject(refreshError);
          }
        }

        // Handle other errors
        return this.handleError(error);
      }
    );
  }

  /**
   * Get access token from secure storage
   */
  private async getAccessToken(): Promise<string | null> {
    try {
      return await SecureStore.getItemAsync('access_token');
    } catch (error) {
      console.error('Error getting access token:', error);
      return null;
    }
  }

  /**
   * Get refresh token from secure storage
   */
  private async getRefreshToken(): Promise<string | null> {
    try {
      return await SecureStore.getItemAsync('refresh_token');
    } catch (error) {
      console.error('Error getting refresh token:', error);
      return null;
    }
  }

  /**
   * Refresh access token
   */
  private async refreshAccessToken(): Promise<string | null> {
    const refreshToken = await this.getRefreshToken();
    
    if (!refreshToken) {
      throw new Error('No refresh token available');
    }

    try {
      const response = await axios.post(`${API_URL}/auth/refresh`, {
        refresh_token: refreshToken,
      });

      const { access_token } = response.data.data.tokens;
      
      // Store new access token
      await SecureStore.setItemAsync('access_token', access_token);
      
      return access_token;
    } catch (error) {
      throw new Error('Failed to refresh token');
    }
  }

  /**
   * Handle API errors
   */
  private handleError(error: AxiosError): Promise<never> {
    if (error.response) {
      // Server responded with error
      const { status, data } = error.response;
      
      const errorMessage = (data as any)?.message || 'An error occurred';
      const errors = (data as any)?.errors || {};

      throw {
        status,
        message: errorMessage,
        errors,
      };
    } else if (error.request) {
      // Request made but no response (network error)
      throw {
        status: 0,
        message: 'Network error. Please check your internet connection.',
        errors: {},
      };
    } else {
      // Something else happened
      throw {
        status: 0,
        message: error.message || 'An unexpected error occurred',
        errors: {},
      };
    }
  }

  /**
   * HTTP Methods
   */

  public get<T = any>(url: string, config?: any): Promise<T> {
    return this.client.get(url, config);
  }

  public post<T = any>(url: string, data?: any, config?: any): Promise<T> {
    return this.client.post(url, data, config);
  }

  public put<T = any>(url: string, data?: any, config?: any): Promise<T> {
    return this.client.put(url, data, config);
  }

  public patch<T = any>(url: string, data?: any, config?: any): Promise<T> {
    return this.client.patch(url, data, config);
  }

  public delete<T = any>(url: string, config?: any): Promise<T> {
    return this.client.delete(url, config);
  }
}

// Export singleton instance
export const apiClient = new APIClient();
