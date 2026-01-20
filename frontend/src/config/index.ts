/**
 * Application Configuration
 *
 * Centralized configuration file that reads from environment variables.
 * This allows different configurations for development, staging, and production.
 */

import Constants from 'expo-constants';

/**
 * Get environment variable with fallback
 */
const getEnvVar = (key: string, fallback: string): string => {
  return Constants.expoConfig?.extra?.[key] || fallback;
};

/**
 * Get numeric environment variable with fallback
 */
const getNumericEnvVar = (key: string, fallback: number): number => {
  const value = Constants.expoConfig?.extra?.[key];
  return value ? parseInt(value, 10) : fallback;
};

/**
 * Get boolean environment variable with fallback
 */
const getBooleanEnvVar = (key: string, fallback: boolean): boolean => {
  const value = Constants.expoConfig?.extra?.[key];
  if (value === undefined) return fallback;
  return value === 'true' || value === true;
};

/**
 * Application configuration object
 */
export const config = {
  /**
   * API Configuration
   */
  api: {
    baseUrl: getEnvVar('apiUrl', 'http://localhost:8000/api'),
    timeout: getNumericEnvVar('apiTimeout', 30000), // 30 seconds
  },

  /**
   * Sync Configuration
   */
  sync: {
    interval: getNumericEnvVar('syncInterval', 5 * 60 * 1000), // Default: 5 minutes
    maxRetryCount: getNumericEnvVar('syncMaxRetryCount', 3),
    retryDelay: getNumericEnvVar('syncRetryDelay', 2000), // 2 seconds
  },

  /**
   * GPS Configuration
   */
  gps: {
    accuracyThreshold: getNumericEnvVar('gpsAccuracyThreshold', 20), // meters
    trackingInterval: getNumericEnvVar('trackingInterval', 60000), // Default: 1 minute
    distanceFilter: getNumericEnvVar('gpsDistanceFilter', 10), // meters - minimum distance before location update
  },

  /**
   * Map Configuration
   */
  maps: {
    googleApiKey: getEnvVar('googleMapsApiKey', ''),
    mapboxToken: getEnvVar('mapboxAccessToken', ''),
    defaultLatitude: 7.8731, // Sri Lanka center
    defaultLongitude: 80.7718,
    defaultZoom: 8,
  },

  /**
   * App Configuration
   */
  app: {
    name: 'GeoOps',
    version: Constants.expoConfig?.version || '1.0.0',
    environment: getEnvVar('appEnv', 'development') as 'development' | 'staging' | 'production',
  },

  /**
   * Feature Flags
   */
  features: {
    enableTracking: getBooleanEnvVar('enableTracking', true),
    enableOffline: getBooleanEnvVar('enableOffline', true),
    debugMode: getBooleanEnvVar('debugMode', false),
  },

  /**
   * Storage Keys
   */
  storage: {
    authToken: 'authToken',
    refreshToken: 'refreshToken',
    userData: 'userData',
    language: 'app_language',
  },
};

/**
 * Helper function to check if app is in production
 */
export const isProduction = (): boolean => {
  return config.app.environment === 'production';
};

/**
 * Helper function to check if app is in development
 */
export const isDevelopment = (): boolean => {
  return config.app.environment === 'development';
};

/**
 * Helper function to check if debug mode is enabled
 */
export const isDebugMode = (): boolean => {
  return config.features.debugMode;
};

export default config;
