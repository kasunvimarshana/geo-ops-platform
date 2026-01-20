/**
 * Application Constants
 * 
 * Central location for all app-wide constants
 */

// Default Map Coordinates (Sri Lanka center)
export const DEFAULT_MAP_COORDINATES = {
  latitude: 7.8731,
  longitude: 80.7718,
  latitudeDelta: 2.0,
  longitudeDelta: 2.0,
};

// API Configuration
export const API_CONFIG = {
  TIMEOUT: 30000,
  MAX_RETRIES: 3,
  RETRY_DELAY: 1000,
};

// GPS Configuration
export const GPS_CONFIG = {
  HIGH_ACCURACY: true,
  DISTANCE_FILTER: 3, // meters
  TIME_INTERVAL: 2000, // milliseconds
  MIN_ACCURACY: 20, // meters - reject points with lower accuracy
  BACKGROUND_DISTANCE_FILTER: 50,
  BACKGROUND_TIME_INTERVAL: 30000,
};

// Measurement Configuration
export const MEASUREMENT_CONFIG = {
  MIN_POINTS: 3,
  MAX_POINTS: 1000,
  SQUARE_METERS_PER_ACRE: 4046.86,
  SQUARE_METERS_PER_HECTARE: 10000,
};

// Subscription Limits
export const SUBSCRIPTION_LIMITS = {
  FREE: {
    measurements: 10,
    drivers: 1,
    exports: 5,
  },
  BASIC: {
    measurements: 100,
    drivers: 5,
    exports: 50,
  },
  PRO: {
    measurements: 1000,
    drivers: 50,
    exports: 500,
  },
};

// Sync Configuration
export const SYNC_CONFIG = {
  AUTO_SYNC_INTERVAL: 300000, // 5 minutes
  RETRY_ATTEMPTS: 3,
  RETRY_DELAY: 5000,
  BATCH_SIZE: 50,
};

// Storage Keys
export const STORAGE_KEYS = {
  ACCESS_TOKEN: 'access_token',
  REFRESH_TOKEN: 'refresh_token',
  USER_ID: 'user_id',
  ORGANIZATION_ID: 'organization_id',
  LANGUAGE: 'language',
  THEME: 'theme',
  LAST_SYNC: 'last_sync',
};

// Role IDs (should match backend)
export const ROLES = {
  ADMIN: 1,
  OWNER: 2,
  DRIVER: 3,
  BROKER: 4,
  ACCOUNTANT: 5,
};

// Status Constants
export const JOB_STATUS = {
  PENDING: 'pending',
  IN_PROGRESS: 'in_progress',
  COMPLETED: 'completed',
  CANCELLED: 'cancelled',
};

export const INVOICE_STATUS = {
  DRAFT: 'draft',
  SENT: 'sent',
  PAID: 'paid',
  OVERDUE: 'overdue',
  CANCELLED: 'cancelled',
};

export const PAYMENT_METHODS = {
  CASH: 'cash',
  BANK: 'bank',
  DIGITAL: 'digital',
  CHECK: 'check',
};

export const EXPENSE_TYPES = {
  FUEL: 'fuel',
  MAINTENANCE: 'maintenance',
  PARTS: 'parts',
  LABOR: 'labor',
  OTHER: 'other',
};

// Map Configuration
export const MAP_CONFIG = {
  DEFAULT_LATITUDE: 7.8731,
  DEFAULT_LONGITUDE: 80.7718,
  DEFAULT_ZOOM: 13,
  MARKER_COLORS: {
    land: '#4CAF50',
    job_pending: '#FFC107',
    job_in_progress: '#2196F3',
    job_completed: '#8BC34A',
    driver_active: '#FF5722',
  },
};

// UI Constants
export const UI_CONFIG = {
  DEBOUNCE_DELAY: 300,
  TOAST_DURATION: 3000,
  ANIMATION_DURATION: 300,
};

// Validation Rules
export const VALIDATION = {
  MIN_NAME_LENGTH: 2,
  MAX_NAME_LENGTH: 255,
  MIN_PASSWORD_LENGTH: 8,
  PHONE_REGEX: /^\+94[0-9]{9}$/,
  EMAIL_REGEX: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
};

// Error Messages
export const ERROR_MESSAGES = {
  NETWORK_ERROR: 'Network error. Please check your internet connection.',
  SERVER_ERROR: 'Server error. Please try again later.',
  UNAUTHORIZED: 'You are not authorized to perform this action.',
  VALIDATION_ERROR: 'Please check your input and try again.',
  GPS_PERMISSION_DENIED: 'GPS permission is required for land measurement.',
  GPS_NOT_AVAILABLE: 'GPS is not available on this device.',
  BLUETOOTH_NOT_AVAILABLE: 'Bluetooth is not available on this device.',
};

// Success Messages
export const SUCCESS_MESSAGES = {
  MEASUREMENT_SAVED: 'Land measurement saved successfully',
  JOB_CREATED: 'Job created successfully',
  INVOICE_GENERATED: 'Invoice generated successfully',
  PAYMENT_RECORDED: 'Payment recorded successfully',
  SYNC_COMPLETED: 'Data synchronized successfully',
};
