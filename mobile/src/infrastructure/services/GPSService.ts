/**
 * GPS Service
 * Optimized GPS location tracking with battery management
 */

import * as Location from "expo-location";
import * as TaskManager from "expo-task-manager";
import { GPS_CONFIG } from "../../config/gps";
import { GeoPoint } from "../../domain/entities/Field";

const LOCATION_TASK_NAME = "background-location-task";

class GPSService {
  private isTracking: boolean = false;
  private locationSubscription: Location.LocationSubscription | null = null;
  private onLocationUpdate?: (location: GeoPoint) => void;

  /**
   * Request location permissions
   */
  async requestPermissions(): Promise<boolean> {
    try {
      const { status: foregroundStatus } =
        await Location.requestForegroundPermissionsAsync();

      if (foregroundStatus !== "granted") {
        return false;
      }

      const { status: backgroundStatus } =
        await Location.requestBackgroundPermissionsAsync();

      return foregroundStatus === "granted";
    } catch (error) {
      console.error("Error requesting permissions:", error);
      return false;
    }
  }

  /**
   * Check if location services are enabled
   */
  async isLocationEnabled(): Promise<boolean> {
    try {
      return await Location.hasServicesEnabledAsync();
    } catch (error) {
      console.error("Error checking location services:", error);
      return false;
    }
  }

  /**
   * Get current location
   */
  async getCurrentLocation(): Promise<GeoPoint | null> {
    try {
      const location = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.High,
      });

      return this.convertToGeoPoint(location);
    } catch (error) {
      console.error("Error getting current location:", error);
      return null;
    }
  }

  /**
   * Start tracking location with optimized settings
   */
  async startTracking(
    callback: (location: GeoPoint) => void,
    useHighAccuracy: boolean = true,
  ): Promise<boolean> {
    try {
      if (this.isTracking) {
        return true;
      }

      const hasPermission = await this.requestPermissions();
      if (!hasPermission) {
        return false;
      }

      this.onLocationUpdate = callback;

      // Configure accuracy based on battery optimization
      const accuracy = useHighAccuracy
        ? Location.Accuracy.High
        : Location.Accuracy.Balanced;

      this.locationSubscription = await Location.watchPositionAsync(
        {
          accuracy,
          timeInterval: GPS_CONFIG.UPDATE_INTERVAL.ACTIVE,
          distanceInterval: GPS_CONFIG.DISTANCE_FILTER,
        },
        (location) => {
          const geoPoint = this.convertToGeoPoint(location);
          if (geoPoint && this.onLocationUpdate) {
            this.onLocationUpdate(geoPoint);
          }
        },
      );

      this.isTracking = true;
      return true;
    } catch (error) {
      console.error("Error starting location tracking:", error);
      return false;
    }
  }

  /**
   * Stop tracking location
   */
  async stopTracking(): Promise<void> {
    try {
      if (this.locationSubscription) {
        this.locationSubscription.remove();
        this.locationSubscription = null;
      }
      this.isTracking = false;
      this.onLocationUpdate = undefined;
    } catch (error) {
      console.error("Error stopping location tracking:", error);
    }
  }

  /**
   * Start background location tracking
   */
  async startBackgroundTracking(): Promise<boolean> {
    try {
      const hasPermission = await this.requestPermissions();
      if (!hasPermission) {
        return false;
      }

      await Location.startLocationUpdatesAsync(LOCATION_TASK_NAME, {
        accuracy: Location.Accuracy.Balanced,
        timeInterval: GPS_CONFIG.UPDATE_INTERVAL.BACKGROUND,
        distanceInterval: GPS_CONFIG.DISTANCE_FILTER,
        foregroundService: {
          notificationTitle: "GeoOps Tracking",
          notificationBody: "GPS location tracking is active",
        },
      });

      return true;
    } catch (error) {
      console.error("Error starting background tracking:", error);
      return false;
    }
  }

  /**
   * Stop background location tracking
   */
  async stopBackgroundTracking(): Promise<void> {
    try {
      await Location.stopLocationUpdatesAsync(LOCATION_TASK_NAME);
    } catch (error) {
      console.error("Error stopping background tracking:", error);
    }
  }

  /**
   * Calculate distance between two points (Haversine formula)
   */
  calculateDistance(point1: GeoPoint, point2: GeoPoint): number {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = (point1.latitude * Math.PI) / 180;
    const φ2 = (point2.latitude * Math.PI) / 180;
    const Δφ = ((point2.latitude - point1.latitude) * Math.PI) / 180;
    const Δλ = ((point2.longitude - point1.longitude) * Math.PI) / 180;

    const a =
      Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
      Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
  }

  /**
   * Calculate area of polygon using Shoelace formula
   */
  calculatePolygonArea(points: GeoPoint[]): number {
    if (points.length < 3) {
      return 0;
    }

    const R = 6371e3; // Earth's radius in meters
    let area = 0;

    for (let i = 0; i < points.length; i++) {
      const j = (i + 1) % points.length;
      const p1 = points[i];
      const p2 = points[j];

      const lat1 = (p1.latitude * Math.PI) / 180;
      const lat2 = (p2.latitude * Math.PI) / 180;
      const lon1 = (p1.longitude * Math.PI) / 180;
      const lon2 = (p2.longitude * Math.PI) / 180;

      area += (lon2 - lon1) * (2 + Math.sin(lat1) + Math.sin(lat2));
    }

    area = Math.abs((area * R * R) / 2);
    return area;
  }

  /**
   * Calculate perimeter of polygon
   */
  calculatePolygonPerimeter(points: GeoPoint[]): number {
    if (points.length < 2) {
      return 0;
    }

    let perimeter = 0;

    for (let i = 0; i < points.length; i++) {
      const j = (i + 1) % points.length;
      perimeter += this.calculateDistance(points[i], points[j]);
    }

    return perimeter;
  }

  /**
   * Convert Expo Location to GeoPoint
   */
  private convertToGeoPoint(location: Location.LocationObject): GeoPoint {
    return {
      latitude: location.coords.latitude,
      longitude: location.coords.longitude,
      altitude: location.coords.altitude || undefined,
      accuracy: location.coords.accuracy || undefined,
      timestamp: new Date(location.timestamp),
    };
  }
}

// Define background location task
TaskManager.defineTask(LOCATION_TASK_NAME, ({ data, error }: any) => {
  if (error) {
    console.error("Background location error:", error);
    return;
  }
  if (data) {
    const { locations } = data;
    // Store locations for later sync
    console.log("Background locations:", locations);
  }
});

export default new GPSService();
