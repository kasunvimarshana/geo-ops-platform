import * as Location from 'expo-location';
import * as TaskManager from 'expo-task-manager';

const LOCATION_TRACKING_TASK = 'background-location-tracking';

export interface GPSPoint {
  latitude: number;
  longitude: number;
  altitude?: number;
  accuracy: number;
  speed?: number;
  heading?: number;
  timestamp: number;
}

export interface PolygonArea {
  acres: number;
  hectares: number;
  squareMeters: number;
}

/**
 * GPS Service
 * 
 * Handles GPS location tracking, area calculation, and background tracking
 * Optimized for battery usage
 */
class GPSService {
  private isTracking: boolean = false;
  private currentPoints: GPSPoint[] = [];
  private locationSubscription: Location.LocationSubscription | null = null;

  /**
   * Request location permissions
   */
  async requestPermissions(): Promise<boolean> {
    try {
      const { status: foregroundStatus } = await Location.requestForegroundPermissionsAsync();
      
      if (foregroundStatus !== 'granted') {
        return false;
      }

      // Request background permission for job tracking
      const { status: backgroundStatus } = await Location.requestBackgroundPermissionsAsync();
      
      return backgroundStatus === 'granted';
    } catch (error) {
      console.error('Error requesting location permissions:', error);
      return false;
    }
  }

  /**
   * Check if location services are enabled
   */
  async isLocationEnabled(): Promise<boolean> {
    return await Location.hasServicesEnabledAsync();
  }

  /**
   * Get current location
   */
  async getCurrentLocation(): Promise<GPSPoint | null> {
    try {
      const location = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.High,
      });

      return {
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
        altitude: location.coords.altitude || undefined,
        accuracy: location.coords.accuracy || 0,
        speed: location.coords.speed || undefined,
        heading: location.coords.heading || undefined,
        timestamp: location.timestamp,
      };
    } catch (error) {
      console.error('Error getting current location:', error);
      return null;
    }
  }

  /**
   * Start tracking for land measurement
   */
  async startMeasurementTracking(
    onLocationUpdate: (point: GPSPoint) => void
  ): Promise<boolean> {
    try {
      this.currentPoints = [];
      this.isTracking = true;

      this.locationSubscription = await Location.watchPositionAsync(
        {
          accuracy: Location.Accuracy.High,
          distanceInterval: 3, // Update every 3 meters
          timeInterval: 2000, // Or every 2 seconds
        },
        (location) => {
          if (!this.isTracking) return;

          const point: GPSPoint = {
            latitude: location.coords.latitude,
            longitude: location.coords.longitude,
            altitude: location.coords.altitude || undefined,
            accuracy: location.coords.accuracy || 0,
            speed: location.coords.speed || undefined,
            heading: location.coords.heading || undefined,
            timestamp: location.timestamp,
          };

          this.currentPoints.push(point);
          onLocationUpdate(point);
        }
      );

      return true;
    } catch (error) {
      console.error('Error starting measurement tracking:', error);
      return false;
    }
  }

  /**
   * Stop tracking
   */
  stopMeasurementTracking(): GPSPoint[] {
    this.isTracking = false;
    
    if (this.locationSubscription) {
      this.locationSubscription.remove();
      this.locationSubscription = null;
    }

    return this.currentPoints;
  }

  /**
   * Calculate area of polygon in different units
   * Using Shoelace formula
   */
  calculatePolygonArea(points: GPSPoint[]): PolygonArea {
    if (points.length < 3) {
      return { acres: 0, hectares: 0, squareMeters: 0 };
    }

    // Calculate area in square meters
    let area = 0;
    const n = points.length;

    for (let i = 0; i < n; i++) {
      const j = (i + 1) % n;
      
      const lat1 = this.toRadians(points[i].latitude);
      const lon1 = this.toRadians(points[i].longitude);
      const lat2 = this.toRadians(points[j].latitude);
      const lon2 = this.toRadians(points[j].longitude);

      const x1 = lon1 * Math.cos(lat1);
      const y1 = lat1;
      const x2 = lon2 * Math.cos(lat2);
      const y2 = lat2;

      area += (x1 * y2 - x2 * y1);
    }

    area = Math.abs(area / 2);

    // Convert to square meters (Earth radius = 6371000 m)
    const earthRadius = 6371000;
    const squareMeters = area * earthRadius * earthRadius;

    // Convert to acres and hectares
    const acres = squareMeters / 4046.86;
    const hectares = squareMeters / 10000;

    return {
      acres: parseFloat(acres.toFixed(4)),
      hectares: parseFloat(hectares.toFixed(4)),
      squareMeters: parseFloat(squareMeters.toFixed(2)),
    };
  }

  /**
   * Calculate distance between two points using Haversine formula
   * Returns distance in meters
   */
  calculateDistance(point1: GPSPoint, point2: GPSPoint): number {
    const R = 6371000; // Earth radius in meters

    const lat1 = this.toRadians(point1.latitude);
    const lat2 = this.toRadians(point2.latitude);
    const deltaLat = this.toRadians(point2.latitude - point1.latitude);
    const deltaLon = this.toRadians(point2.longitude - point1.longitude);

    const a =
      Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
      Math.cos(lat1) * Math.cos(lat2) *
      Math.sin(deltaLon / 2) * Math.sin(deltaLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
  }

  /**
   * Start background location tracking for jobs
   */
  async startBackgroundTracking(): Promise<boolean> {
    try {
      await Location.startLocationUpdatesAsync(LOCATION_TRACKING_TASK, {
        accuracy: Location.Accuracy.Balanced,
        distanceInterval: 50, // Update every 50 meters
        timeInterval: 30000, // Or every 30 seconds
        foregroundService: {
          notificationTitle: 'GeoOps Tracking',
          notificationBody: 'Tracking your location for the current job',
        },
      });

      return true;
    } catch (error) {
      console.error('Error starting background tracking:', error);
      return false;
    }
  }

  /**
   * Stop background location tracking
   */
  async stopBackgroundTracking(): Promise<void> {
    try {
      await Location.stopLocationUpdatesAsync(LOCATION_TRACKING_TASK);
    } catch (error) {
      console.error('Error stopping background tracking:', error);
    }
  }

  /**
   * Convert degrees to radians
   */
  private toRadians(degrees: number): number {
    return degrees * (Math.PI / 180);
  }
}

// Define background task for location tracking
TaskManager.defineTask(LOCATION_TRACKING_TASK, async ({ data, error }) => {
  if (error) {
    console.error('Background location task error:', error);
    return;
  }

  if (data) {
    const { locations } = data as any;
    // Store locations in local database for later sync
    // This will be handled by the sync service
    console.log('Background locations:', locations);
  }
});

// Export singleton instance
export const gpsService = new GPSService();
export default gpsService;
