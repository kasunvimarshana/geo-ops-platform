export const API_CONFIG = {
  BASE_URL: process.env.EXPO_PUBLIC_API_URL || 'http://localhost:8000/api',
  TIMEOUT: 30000,
  RETRY_ATTEMPTS: 3,
  RETRY_DELAY: 1000,
};

export const STORAGE_KEYS = {
  AUTH_TOKEN: 'auth_token',
  REFRESH_TOKEN: 'refresh_token',
  USER_DATA: 'user_data',
  LANGUAGE: 'language',
};

export const SYNC_CONFIG = {
  BATCH_SIZE: 10,
  SYNC_INTERVAL: 300000, // 5 minutes
  MAX_RETRY_ATTEMPTS: 5,
};

export const GPS_CONFIG = {
  ACCURACY_THRESHOLD: 10, // meters
  UPDATE_INTERVAL: 5000, // 5 seconds - balanced for battery life
  DISTANCE_FILTER: 5, // meters
};

export const APP_CONFIG = {
  DEFAULT_LANGUAGE: 'en',
  SUPPORTED_LANGUAGES: ['en', 'si'],
};
