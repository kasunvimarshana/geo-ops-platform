import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';
import AsyncStorage from '@react-native-async-storage/async-storage';
import * as SecureStore from 'expo-secure-store';
import { authAPI, LoginDTO, RegisterDTO } from '@/api/auth.api';

/**
 * Authentication Store
 * 
 * Manages authentication state using Zustand.
 */

interface User {
  id: number;
  name: string;
  email: string;
  phone: string;
  role: string;
  organization_id: number;
  profile_photo?: string;
}

interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
  
  // Actions
  login: (credentials: LoginDTO) => Promise<void>;
  register: (data: RegisterDTO) => Promise<void>;
  logout: () => Promise<void>;
  setUser: (user: User | null) => void;
  clearError: () => void;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      isAuthenticated: false,
      isLoading: false,
      error: null,

      login: async (credentials: LoginDTO) => {
        set({ isLoading: true, error: null });
        
        try {
          const response = await authAPI.login(credentials);
          const { user, tokens } = response.data;

          // Store tokens securely
          await SecureStore.setItemAsync('access_token', tokens.access_token);
          await SecureStore.setItemAsync('refresh_token', tokens.refresh_token);

          set({
            user,
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

      register: async (data: RegisterDTO) => {
        set({ isLoading: true, error: null });
        
        try {
          const response = await authAPI.register(data);
          const { user, tokens } = response.data;

          // Store tokens securely
          await SecureStore.setItemAsync('access_token', tokens.access_token);
          await SecureStore.setItemAsync('refresh_token', tokens.refresh_token);

          set({
            user,
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
          // Call logout API
          await authAPI.logout();
        } catch (error) {
          console.error('Logout API error:', error);
        } finally {
          // Clear tokens
          await SecureStore.deleteItemAsync('access_token');
          await SecureStore.deleteItemAsync('refresh_token');

          set({
            user: null,
            isAuthenticated: false,
            error: null,
          });
        }
      },

      setUser: (user: User | null) => {
        set({
          user,
          isAuthenticated: !!user,
        });
      },

      clearError: () => {
        set({ error: null });
      },
    }),
    {
      name: 'auth-storage',
      storage: createJSONStorage(() => AsyncStorage),
      // Only persist user data, not tokens (stored securely)
      partialize: (state) => ({
        user: state.user,
        isAuthenticated: state.isAuthenticated,
      }),
    }
  )
);
