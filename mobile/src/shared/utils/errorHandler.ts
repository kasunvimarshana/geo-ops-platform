import { AxiosError } from 'axios';
import type { ApiError } from '../types/api';

export const handleApiError = (error: unknown): string => {
  if (error instanceof AxiosError) {
    if (error.response?.data) {
      const apiError = error.response.data as ApiError;
      return apiError.message || 'An error occurred';
    }
    
    if (error.code === 'ECONNABORTED') {
      return 'Request timeout. Please try again.';
    }
    
    if (error.message === 'Network Error') {
      return 'Network error. Please check your connection.';
    }
    
    return error.message || 'An error occurred';
  }
  
  if (error instanceof Error) {
    return error.message;
  }
  
  return 'An unknown error occurred';
};

export const isNetworkError = (error: unknown): boolean => {
  if (error instanceof AxiosError) {
    return error.message === 'Network Error' || error.code === 'ECONNABORTED';
  }
  return false;
};
