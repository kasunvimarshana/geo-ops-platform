export const API_URL = process.env.EXPO_PUBLIC_API_URL || 'http://localhost:3000/api/v1';

export const MEASUREMENT_UNITS = {
  ACRES: 'acres',
  HECTARES: 'hectares',
  SQUARE_METERS: 'square_meters',
} as const;

export const USER_ROLES = {
  ADMIN: 'admin',
  OWNER: 'owner',
  DRIVER: 'driver',
  BROKER: 'broker',
  ACCOUNTANT: 'accountant',
} as const;

export const JOB_STATUS = {
  PENDING: 'pending',
  IN_PROGRESS: 'in_progress',
  COMPLETED: 'completed',
  CANCELLED: 'cancelled',
} as const;

export const INVOICE_STATUS = {
  DRAFT: 'draft',
  SENT: 'sent',
  PAID: 'paid',
  OVERDUE: 'overdue',
  CANCELLED: 'cancelled',
} as const;

export const COLORS = {
  primary: '#2196F3',
  secondary: '#4CAF50',
  error: '#F44336',
  warning: '#FF9800',
  success: '#4CAF50',
  info: '#2196F3',
  background: '#F5F5F5',
  surface: '#FFFFFF',
  text: '#212121',
  textSecondary: '#757575',
  border: '#E0E0E0',
  polygonFill: 'rgba(33, 150, 243, 0.2)',
  polygonStroke: '#2196F3',
} as const;

// Default map region centered on Sri Lanka
// Coordinates: 7.8731°N, 80.7718°E (approximate center of Sri Lanka)
// Delta values control zoom level: smaller = more zoomed in
// latitudeDelta/longitudeDelta of 0.05 shows ~5.5km view
export const DEFAULT_MAP_REGION = {
  latitude: 7.8731,
  longitude: 80.7718,
  latitudeDelta: 0.05,
  longitudeDelta: 0.05,
} as const;

export const MAP_CONSTANTS = {
  REGION_PADDING_MULTIPLIER: 1.5,
  MIN_REGION_DELTA: 0.01,
  MIN_POLYGON_POINTS: 3,
  MAP_EDGE_PADDING: 50,
  INITIAL_LATITUDE_DELTA: 0.005,
  INITIAL_LONGITUDE_DELTA: 0.005,
} as const;

export const STORAGE_KEYS = {
  AUTH_TOKEN: '@auth_token',
  USER_DATA: '@user_data',
  OFFLINE_DATA: '@offline_data',
} as const;
