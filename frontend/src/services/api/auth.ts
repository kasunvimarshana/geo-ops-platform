import apiClient from './client';

export const authApi = {
  login: async (email: string, password: string) => {
    return apiClient.post('/auth/login', { email, password });
  },

  register: async (data: any) => {
    return apiClient.post('/auth/register', data);
  },

  logout: async () => {
    return apiClient.post('/auth/logout');
  },

  me: async () => {
    return apiClient.get('/auth/me');
  },

  refresh: async () => {
    return apiClient.post('/auth/refresh');
  },
};
