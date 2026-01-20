import * as Location from 'expo-location';
import { Coordinates } from '../../types/api.types';
import { GPS_CONFIG } from '../../constants/config';

class LocationService {
  private watchId: Location.LocationSubscription | null = null;

  async requestPermissions(): Promise<boolean> {
    const { status } = await Location.requestForegroundPermissionsAsync();
    return status === 'granted';
  }

  async getCurrentLocation(): Promise<Coordinates | null> {
    try {
      const hasPermission = await this.requestPermissions();
      if (!hasPermission) {
        throw new Error('Location permission not granted');
      }

      const location = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.High,
      });

      return {
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
      };
    } catch (error) {
      console.error('Error getting current location:', error);
      return null;
    }
  }

  async startWatching(
    callback: (location: Coordinates) => void,
    errorCallback?: (error: Error) => void
  ): Promise<void> {
    try {
      const hasPermission = await this.requestPermissions();
      if (!hasPermission) {
        throw new Error('Location permission not granted');
      }

      this.watchId = await Location.watchPositionAsync(
        {
          accuracy: Location.Accuracy.High,
          timeInterval: GPS_CONFIG.UPDATE_INTERVAL,
          distanceInterval: GPS_CONFIG.DISTANCE_FILTER,
        },
        (location) => {
          if (location.coords.accuracy && location.coords.accuracy <= GPS_CONFIG.ACCURACY_THRESHOLD) {
            callback({
              latitude: location.coords.latitude,
              longitude: location.coords.longitude,
            });
          }
        }
      );
    } catch (error) {
      if (errorCallback) {
        errorCallback(error as Error);
      }
      console.error('Error watching location:', error);
    }
  }

  stopWatching(): void {
    if (this.watchId) {
      this.watchId.remove();
      this.watchId = null;
    }
  }

  calculateDistance(coord1: Coordinates, coord2: Coordinates): number {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = (coord1.latitude * Math.PI) / 180;
    const φ2 = (coord2.latitude * Math.PI) / 180;
    const Δφ = ((coord2.latitude - coord1.latitude) * Math.PI) / 180;
    const Δλ = ((coord2.longitude - coord1.longitude) * Math.PI) / 180;

    const a =
      Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
      Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
  }

  calculateArea(coordinates: Coordinates[]): number {
    if (coordinates.length < 3) return 0;

    const R = 6371e3;
    let area = 0;

    for (let i = 0; i < coordinates.length; i++) {
      const j = (i + 1) % coordinates.length;
      const lat1 = (coordinates[i].latitude * Math.PI) / 180;
      const lat2 = (coordinates[j].latitude * Math.PI) / 180;
      const lon1 = (coordinates[i].longitude * Math.PI) / 180;
      const lon2 = (coordinates[j].longitude * Math.PI) / 180;

      area += (lon2 - lon1) * (2 + Math.sin(lat1) + Math.sin(lat2));
    }

    area = (area * R * R) / 2;
    return Math.abs(area);
  }

  calculatePerimeter(coordinates: Coordinates[]): number {
    if (coordinates.length < 2) return 0;

    let perimeter = 0;
    for (let i = 0; i < coordinates.length; i++) {
      const j = (i + 1) % coordinates.length;
      perimeter += this.calculateDistance(coordinates[i], coordinates[j]);
    }

    return perimeter;
  }

  sqmToAcres(sqm: number): number {
    return sqm * 0.000247105;
  }
}

export const locationService = new LocationService();
