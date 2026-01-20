import { create } from 'zustand';
import type { User, LoginRequest, RegisterRequest } from '../shared/types/api';
import { authApi } from '../services/api/endpoints';
import { tokenStorage } from '../services/storage';

interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
  
  login: (credentials: LoginRequest) => Promise<void>;
  register: (data: RegisterRequest) => Promise<void>;
  logout: () => Promise<void>;
  loadUser: () => Promise<void>;
  updateUser: (data: Partial<User>) => Promise<void>;
  clearError: () => void;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  isAuthenticated: false,
  isLoading: false,
  error: null,

  login: async (credentials) => {
    try {
      set({ isLoading: true, error: null });
      const response = await authApi.login(credentials);
      const { token, refreshToken, user } = response.data.data;
      
      await tokenStorage.setTokens(token, refreshToken);
      set({ user, isAuthenticated: true, isLoading: false });
    } catch (error) {
      const message = error instanceof Error ? error.message : 'Login failed';
      set({ error: message, isLoading: false });
      throw error;
    }
  },

  register: async (data) => {
    try {
      set({ isLoading: true, error: null });
      const response = await authApi.register(data);
      const { token, refreshToken, user } = response.data.data;
      
      await tokenStorage.setTokens(token, refreshToken);
      set({ user, isAuthenticated: true, isLoading: false });
    } catch (error) {
      const message = error instanceof Error ? error.message : 'Registration failed';
      set({ error: message, isLoading: false });
      throw error;
    }
  },

  logout: async () => {
    try {
      await authApi.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      await tokenStorage.clearTokens();
      set({ user: null, isAuthenticated: false, error: null });
    }
  },

  loadUser: async () => {
    try {
      const token = await tokenStorage.getAccessToken();
      if (!token) {
        set({ isAuthenticated: false, isLoading: false });
        return;
      }

      set({ isLoading: true });
      const response = await authApi.getProfile();
      set({ user: response.data.data, isAuthenticated: true, isLoading: false });
    } catch (error) {
      console.error('Load user error:', error);
      await tokenStorage.clearTokens();
      set({ user: null, isAuthenticated: false, isLoading: false });
    }
  },

  updateUser: async (data) => {
    try {
      set({ isLoading: true, error: null });
      const response = await authApi.updateProfile(data);
      set({ user: response.data.data, isLoading: false });
    } catch (error) {
      const message = error instanceof Error ? error.message : 'Update failed';
      set({ error: message, isLoading: false });
      throw error;
    }
  },

  clearError: () => set({ error: null }),
}));
