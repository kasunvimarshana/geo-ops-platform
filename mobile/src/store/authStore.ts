import { create } from 'zustand';
import { User } from '../shared/types/api.types';
import { authApi } from '../shared/services/api/auth.api';
import { storageService } from '../shared/services/storage/mmkv.service';
import { STORAGE_KEYS } from '../shared/constants/config';

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
  
  login: (username: string, password: string) => Promise<void>;
  register: (data: any) => Promise<void>;
  logout: () => Promise<void>;
  loadStoredAuth: () => Promise<void>;
  clearError: () => void;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token: null,
  isAuthenticated: false,
  isLoading: false,
  error: null,

  login: async (email: string, password: string) => {
    set({ isLoading: true, error: null });
    try {
      const result = await authApi.login({ email, password });
      
      await storageService.setItem(STORAGE_KEYS.AUTH_TOKEN, result.tokens.access);
      await storageService.setItem(STORAGE_KEYS.REFRESH_TOKEN, result.tokens.refresh);
      await storageService.setObject(STORAGE_KEYS.USER_DATA, result.user);

      set({
        user: result.user,
        token: result.tokens.access,
        isAuthenticated: true,
        isLoading: false,
      });
    } catch (error: any) {
      set({
        error: error.message || 'Login failed',
        isLoading: false,
      });
      throw error;
    }
  },

  register: async (data: any) => {
    set({ isLoading: true, error: null });
    try {
      const result = await authApi.register(data);
      
      await storageService.setItem(STORAGE_KEYS.AUTH_TOKEN, result.tokens.access);
      await storageService.setItem(STORAGE_KEYS.REFRESH_TOKEN, result.tokens.refresh);
      await storageService.setObject(STORAGE_KEYS.USER_DATA, result.user);

      set({
        user: result.user,
        token: result.tokens.access,
        isAuthenticated: true,
        isLoading: false,
      });
    } catch (error: any) {
      set({
        error: error.message || 'Registration failed',
        isLoading: false,
      });
      throw error;
    }
  },

  logout: async () => {
    try {
      await authApi.logout();
    } catch (error) {
      console.error('Logout API error:', error);
    } finally {
      await storageService.removeItem(STORAGE_KEYS.AUTH_TOKEN);
      await storageService.removeItem(STORAGE_KEYS.REFRESH_TOKEN);
      await storageService.removeItem(STORAGE_KEYS.USER_DATA);

      set({
        user: null,
        token: null,
        isAuthenticated: false,
      });
    }
  },

  loadStoredAuth: async () => {
    set({ isLoading: true });
    try {
      const token = await storageService.getItem(STORAGE_KEYS.AUTH_TOKEN);
      const user = await storageService.getObject<User>(STORAGE_KEYS.USER_DATA);

      if (token && user) {
        set({
          user,
          token,
          isAuthenticated: true,
          isLoading: false,
        });
      } else {
        set({ isLoading: false });
      }
    } catch (error) {
      console.error('Error loading stored auth:', error);
      set({ isLoading: false });
    }
  },

  clearError: () => set({ error: null }),
}));
