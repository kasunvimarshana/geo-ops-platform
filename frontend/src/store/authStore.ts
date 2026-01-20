import { create } from 'zustand';
import AsyncStorage from '@react-native-async-storage/async-storage';
import * as SecureStore from 'expo-secure-store';
import { authApi } from '../services/api/auth';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  organization_id: number;
}

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
  login: (email: string, password: string) => Promise<void>;
  register: (data: RegisterData) => Promise<void>;
  logout: () => Promise<void>;
  loadUser: () => Promise<void>;
  clearError: () => void;
}

interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  phone: string;
  organization_name: string;
}

const TOKEN_KEY = 'auth_token';
const USER_KEY = 'auth_user';

export const useAuthStore = create<AuthState>((set, get) => ({
  user: null,
  token: null,
  isAuthenticated: false,
  isLoading: false,
  error: null,

  login: async (email: string, password: string) => {
    set({ isLoading: true, error: null });
    try {
      const response = await authApi.login(email, password);
      
      if (response.success) {
        const { user, token } = response.data;
        
        // Save token to secure storage
        await SecureStore.setItemAsync(TOKEN_KEY, token);
        
        // Save user to async storage
        await AsyncStorage.setItem(USER_KEY, JSON.stringify(user));
        
        set({
          user,
          token,
          isAuthenticated: true,
          isLoading: false,
        });
      } else {
        throw new Error(response.error?.message || 'Login failed');
      }
    } catch (error: any) {
      set({
        error: error.message || 'Login failed',
        isLoading: false,
      });
      throw error;
    }
  },

  register: async (data: RegisterData) => {
    set({ isLoading: true, error: null });
    try {
      const response = await authApi.register(data);
      
      if (response.success) {
        const { user, token } = response.data;
        
        // Save token to secure storage
        await SecureStore.setItemAsync(TOKEN_KEY, token);
        
        // Save user to async storage
        await AsyncStorage.setItem(USER_KEY, JSON.stringify(user));
        
        set({
          user,
          token,
          isAuthenticated: true,
          isLoading: false,
        });
      } else {
        throw new Error(response.error?.message || 'Registration failed');
      }
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
      // Call logout API
      if (get().token) {
        await authApi.logout();
      }
    } catch (error) {
      console.error('Logout API error:', error);
    } finally {
      // Clear local storage
      await SecureStore.deleteItemAsync(TOKEN_KEY);
      await AsyncStorage.removeItem(USER_KEY);
      
      set({
        user: null,
        token: null,
        isAuthenticated: false,
      });
    }
  },

  loadUser: async () => {
    try {
      const token = await SecureStore.getItemAsync(TOKEN_KEY);
      const userStr = await AsyncStorage.getItem(USER_KEY);
      
      if (token && userStr) {
        const user = JSON.parse(userStr);
        set({
          user,
          token,
          isAuthenticated: true,
        });
      }
    } catch (error) {
      console.error('Failed to load user:', error);
      // Clear invalid data
      await SecureStore.deleteItemAsync(TOKEN_KEY);
      await AsyncStorage.removeItem(USER_KEY);
    }
  },

  clearError: () => set({ error: null }),
}));
