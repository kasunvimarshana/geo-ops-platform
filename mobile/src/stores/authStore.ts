import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { User, Organization } from '@/types';

interface AuthState {
  isAuthenticated: boolean;
  user: User | null;
  organization: Organization | null;
  accessToken: string | null;
  refreshToken: string | null;
  
  // Actions
  setAuth: (user: User, organization: Organization, accessToken: string, refreshToken: string) => void;
  clearAuth: () => void;
  updateUser: (user: Partial<User>) => void;
}

/**
 * Authentication Store
 * 
 * Manages authentication state using Zustand
 * Persists data to AsyncStorage for offline access
 */
export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      isAuthenticated: false,
      user: null,
      organization: null,
      accessToken: null,
      refreshToken: null,

      setAuth: (user, organization, accessToken, refreshToken) => {
        set({
          isAuthenticated: true,
          user,
          organization,
          accessToken,
          refreshToken,
        });
      },

      clearAuth: () => {
        set({
          isAuthenticated: false,
          user: null,
          organization: null,
          accessToken: null,
          refreshToken: null,
        });
      },

      updateUser: (userData) => {
        set((state) => ({
          user: state.user ? { ...state.user, ...userData } : null,
        }));
      },
    }),
    {
      name: 'auth-storage',
      storage: createJSONStorage(() => AsyncStorage),
    }
  )
);
