import axios, { AxiosInstance, AxiosError } from 'axios';
import * as SecureStore from 'expo-secure-store';
import config from '../config';

class ApiService {
  private axiosInstance: AxiosInstance;

  constructor() {
    this.axiosInstance = axios.create({
      baseURL: config.api.baseUrl,
      timeout: config.api.timeout,
    });

    // Add request interceptor to include auth token
    this.axiosInstance.interceptors.request.use(
      async (config) => {
        try {
          const token = await SecureStore.getItemAsync('authToken');
          if (token) {
            config.headers.Authorization = `Bearer ${token}`;
          }
        } catch (error) {
          console.error('Error retrieving auth token:', error);
        }
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    // Add response interceptor for error handling
    this.axiosInstance.interceptors.response.use(
      (response) => response,
      (error: AxiosError) => {
        if (error.response?.status === 401) {
          // Handle unauthorized - redirect to login
          SecureStore.deleteItemAsync('authToken').catch(console.error);
        }
        return Promise.reject(error);
      }
    );
  }

  get instance() {
    return this.axiosInstance;
  }
}

export const apiService = new ApiService();
