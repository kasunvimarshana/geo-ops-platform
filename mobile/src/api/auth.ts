import axios from 'axios';
import { API_BASE_URL } from '../constants/Config';
import { LoginRequest, RegisterRequest } from '../types/api';

const authClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Register a new user
export const registerUser = async (data: RegisterRequest) => {
  const response = await authClient.post('/api/v1/auth/register', data);
  return response.data;
};

// Login user
export const loginUser = async (data: LoginRequest) => {
  const response = await authClient.post('/api/v1/auth/login', data);
  return response.data;
};

// Logout user
export const logoutUser = async () => {
  const response = await authClient.post('/api/v1/auth/logout');
  return response.data;
};

// Get current user
export const getCurrentUser = async () => {
  const response = await authClient.get('/api/v1/auth/me');
  return response.data;
};