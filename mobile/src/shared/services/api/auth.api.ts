import { apiClient } from './apiClient';
import { User, AuthTokens, LoginCredentials, RegisterData } from '../../types/api.types';

export const authApi = {
  login: async (credentials: LoginCredentials): Promise<{ user: User; tokens: AuthTokens }> => {
    const response = await apiClient.post<{ user?: User; token: string }>(
      '/auth/login',
      credentials
    );
    
    // If backend doesn't return user, fetch it separately
    let user = response.user;
    if (!user) {
      user = await apiClient.get<User>('/auth/me');
    }
    
    return {
      user,
      tokens: {
        access: response.token,
        refresh: response.token, // Backend uses single token
      },
    };
  },

  register: async (data: RegisterData): Promise<{ user: User; tokens: AuthTokens }> => {
    const response = await apiClient.post<{ user?: User; token: string }>(
      '/auth/register',
      data
    );
    
    // If backend doesn't return user, fetch it separately
    let user = response.user;
    if (!user) {
      user = await apiClient.get<User>('/auth/me');
    }
    
    return {
      user,
      tokens: {
        access: response.token,
        refresh: response.token,
      },
    };
  },

  getCurrentUser: async (): Promise<User> => {
    return apiClient.get<User>('/auth/me');
  },

  logout: async (): Promise<void> => {
    await apiClient.post('/auth/logout');
  },

  refreshToken: async (refreshToken: string): Promise<{ access: string }> => {
    return apiClient.post<{ access: string }>('/auth/refresh', { token: refreshToken });
  },
};
