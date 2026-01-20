/**
 * Expo App Configuration
 * 
 * This file allows us to dynamically inject environment variables into the app.
 * Environment variables are accessed via Constants.expoConfig.extra in the app.
 */

export default ({ config }) => {
  return {
    ...config,
    extra: {
      ...config.extra,
      // API Configuration
      apiUrl: process.env.EXPO_PUBLIC_API_URL || 'http://localhost:8000/api',
      apiTimeout: parseInt(process.env.EXPO_PUBLIC_API_TIMEOUT || '30000', 10),
      
      // Google Maps API Key
      googleMapsApiKey: process.env.EXPO_PUBLIC_GOOGLE_MAPS_API_KEY || '',
      
      // Mapbox Access Token
      mapboxAccessToken: process.env.EXPO_PUBLIC_MAPBOX_ACCESS_TOKEN || '',
      
      // App Environment
      appEnv: process.env.EXPO_PUBLIC_APP_ENV || 'development',
      
      // Feature Flags
      enableTracking: process.env.EXPO_PUBLIC_ENABLE_TRACKING === 'true',
      enableOffline: process.env.EXPO_PUBLIC_ENABLE_OFFLINE === 'true',
      debugMode: process.env.EXPO_PUBLIC_DEBUG_MODE === 'true',
      
      // Sync Configuration
      syncInterval: parseInt(process.env.EXPO_PUBLIC_SYNC_INTERVAL || '300000', 10),
      syncMaxRetryCount: parseInt(process.env.EXPO_PUBLIC_SYNC_MAX_RETRY_COUNT || '3', 10),
      syncRetryDelay: parseInt(process.env.EXPO_PUBLIC_SYNC_RETRY_DELAY || '2000', 10),
      
      // GPS Configuration
      gpsAccuracyThreshold: parseInt(process.env.EXPO_PUBLIC_GPS_ACCURACY_THRESHOLD || '20', 10),
      trackingInterval: parseInt(process.env.EXPO_PUBLIC_TRACKING_INTERVAL || '60000', 10),
      gpsDistanceFilter: parseInt(process.env.EXPO_PUBLIC_GPS_DISTANCE_FILTER || '10', 10),
    },
  };
};
