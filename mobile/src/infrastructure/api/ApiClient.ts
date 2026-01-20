/**
 * API Client
 * Axios-based HTTP client with JWT auth interceptors
 */

import axios, { AxiosInstance, AxiosError, InternalAxiosRequestConfig } from 'axios';
import API_CONFIG from '../../config/api';
import { getToken, saveToken, clearToken } from '../storage/TokenStorage';

class ApiClient {
  private client: AxiosInstance;

  constructor() {
    this.client = axios.create({
      baseURL: `${API_CONFIG.BASE_URL}/api/${API_CONFIG.API_VERSION}`,
      timeout: API_CONFIG.TIMEOUT,
      headers: API_CONFIG.HEADERS,
    });

    this.setupInterceptors();
  }

  private setupInterceptors() {
    // Request interceptor - add JWT token
    this.client.interceptors.request.use(
      async (config: InternalAxiosRequestConfig) => {
        const token = await getToken();
        if (token && config.headers) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor - handle auth errors
    this.client.interceptors.response.use(
      (response) => response,
      async (error: AxiosError) => {
        if (error.response?.status === 401) {
          // Token expired or invalid - clear stored token
          await clearToken();
          // You might want to redirect to login here
        }
        return Promise.reject(error);
      }
    );
  }

  public getClient(): AxiosInstance {
    return this.client;
  }

  // Convenience methods
  public async get<T>(url: string, params?: any): Promise<T> {
    const response = await this.client.get<T>(url, { params });
    return response.data;
  }

  public async post<T>(url: string, data?: any): Promise<T> {
    const response = await this.client.post<T>(url, data);
    return response.data;
  }

  public async put<T>(url: string, data?: any): Promise<T> {
    const response = await this.client.put<T>(url, data);
    return response.data;
  }

  public async delete<T>(url: string): Promise<T> {
    const response = await this.client.delete<T>(url);
    return response.data;
  }
}

export default new ApiClient();
