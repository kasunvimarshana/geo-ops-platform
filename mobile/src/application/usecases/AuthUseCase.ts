/**
 * Authentication Use Case
 * Handles user login, registration, and logout
 */

import ApiClient from '../../infrastructure/api/ApiClient';
import { saveToken, saveRefreshToken, clearToken } from '../../infrastructure/storage/TokenStorage';
import { User, LoginCredentials, RegisterData, AuthTokens } from '../../domain/entities/User';

interface AuthResponse {
  user: User;
  token: string;
  token_type: string;
  expires_in: number;
}

class AuthUseCase {
  /**
   * Login user with email and password
   */
  async login(credentials: LoginCredentials): Promise<User> {
    try {
      const response = await ApiClient.post<AuthResponse>('/auth/login', credentials);
      
      // Save tokens
      await saveToken(response.token);
      
      return response.user;
    } catch (error: any) {
      throw new Error(error.response?.data?.error || 'Login failed');
    }
  }

  /**
   * Register new user
   */
  async register(data: RegisterData): Promise<User> {
    try {
      const response = await ApiClient.post<AuthResponse>('/auth/register', {
        ...data,
        password_confirmation: data.password,
      });
      
      // Save tokens
      await saveToken(response.token);
      
      return response.user;
    } catch (error: any) {
      throw new Error(error.response?.data?.errors || 'Registration failed');
    }
  }

  /**
   * Get current authenticated user
   */
  async getCurrentUser(): Promise<User> {
    try {
      const response = await ApiClient.get<User>('/auth/me');
      return response;
    } catch (error: any) {
      throw new Error('Failed to get user data');
    }
  }

  /**
   * Logout user
   */
  async logout(): Promise<void> {
    try {
      await ApiClient.post('/auth/logout');
      await clearToken();
    } catch (error) {
      // Clear tokens even if API call fails
      await clearToken();
    }
  }

  /**
   * Refresh authentication token
   */
  async refreshToken(): Promise<string> {
    try {
      const response = await ApiClient.post<AuthResponse>('/auth/refresh');
      await saveToken(response.token);
      return response.token;
    } catch (error: any) {
      throw new Error('Failed to refresh token');
    }
  }
}

export default new AuthUseCase();
