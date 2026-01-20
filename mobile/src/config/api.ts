/**
 * API Configuration
 * Base URL for the backend API
 */

const API_CONFIG = {
  // Update this with your Laravel backend URL
  BASE_URL: process.env.EXPO_PUBLIC_API_URL || 'http://localhost:8000',
  API_VERSION: 'v1',
  TIMEOUT: 30000,
  HEADERS: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
};

export default API_CONFIG;
