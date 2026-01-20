import * as Location from 'expo-location';
import { gpsService } from '../gps/gpsService';

/**
 * Enhanced Location Service
 * 
 * Extends the GPS service with additional location utilities
 */
class LocationService {
  async getAddressFromCoordinates(
    latitude: number,
    longitude: number
  ): Promise<string | null> {
    try {
      const addresses = await Location.reverseGeocodeAsync({
        latitude,
        longitude,
      });

      if (addresses && addresses.length > 0) {
        const address = addresses[0];
        const parts = [
          address.street,
          address.city,
          address.region,
          address.country,
        ].filter(Boolean);
        
        return parts.join(', ');
      }

      return null;
    } catch (error) {
      console.error('Reverse geocode error:', error);
      return null;
    }
  }

  async searchLocation(query: string): Promise<Array<{
    address: string;
    latitude: number;
    longitude: number;
  }>> {
    try {
      const results = await Location.geocodeAsync(query);
      
      return results.map((result) => ({
        address: query,
        latitude: result.latitude,
        longitude: result.longitude,
      }));
    } catch (error) {
      console.error('Geocode error:', error);
      return [];
    }
  }

  async getCurrentLocationWithAddress(): Promise<{
    latitude: number;
    longitude: number;
    address: string | null;
  } | null> {
    const location = await gpsService.getCurrentLocation();
    
    if (!location) return null;

    const address = await this.getAddressFromCoordinates(
      location.latitude,
      location.longitude
    );

    return {
      latitude: location.latitude,
      longitude: location.longitude,
      address,
    };
  }

  formatCoordinates(latitude: number, longitude: number): string {
    const latDirection = latitude >= 0 ? 'N' : 'S';
    const lonDirection = longitude >= 0 ? 'E' : 'W';
    
    return `${Math.abs(latitude).toFixed(6)}°${latDirection}, ${Math.abs(longitude).toFixed(6)}°${lonDirection}`;
  }

  async openInMaps(latitude: number, longitude: number, label?: string): Promise<void> {
    const url = `geo:${latitude},${longitude}?q=${latitude},${longitude}${label ? `(${label})` : ''}`;
    
    try {
      const { Linking } = await import('react-native');
      const canOpen = await Linking.canOpenURL(url);
      
      if (canOpen) {
        await Linking.openURL(url);
      }
    } catch (error) {
      console.error('Open maps error:', error);
    }
  }
}

export const locationService = new LocationService();
export default locationService;
