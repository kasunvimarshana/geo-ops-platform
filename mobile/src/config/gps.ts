/**
 * GPS Configuration
 * Settings for GPS tracking and measurement
 */

export const GPS_CONFIG = {
  // Accuracy settings (in meters)
  ACCURACY: {
    HIGH: 10,
    MEDIUM: 50,
    LOW: 100,
  },
  
  // Update intervals (in milliseconds)
  UPDATE_INTERVAL: {
    ACTIVE: 1000,      // 1 second for active tracking
    BACKGROUND: 5000,  // 5 seconds for background tracking
  },
  
  // Distance filter (in meters) - minimum distance before update
  DISTANCE_FILTER: 5,
  
  // Battery optimization
  BATTERY_SAVER: {
    ENABLED: true,
    ACCURACY_THRESHOLD: 50, // Use lower accuracy when battery is low
  },
  
  // Polygon measurement
  POLYGON: {
    MIN_POINTS: 3,
    MAX_POINTS: 1000,
    AUTO_CLOSE_DISTANCE: 10, // meters - auto-close polygon if within this distance
  },
};

export default GPS_CONFIG;
