import { useState, useEffect, useCallback, useRef } from 'react';
import * as Location from 'expo-location';
import { Alert } from 'react-native';

/**
 * GPS Tracking Hook
 * 
 * Handles GPS location tracking for land measurement.
 * Optimized for battery usage and accuracy.
 */

export interface GPSPoint {
  latitude: number;
  longitude: number;
  altitude: number | null;
  accuracy: number | null;
  timestamp: string;
}

interface UseGPSTrackingOptions {
  accuracy?: Location.LocationAccuracy;
  timeInterval?: number;
  distanceInterval?: number;
}

interface UseGPSTrackingReturn {
  isTracking: boolean;
  currentLocation: GPSPoint | null;
  points: GPSPoint[];
  error: string | null;
  startTracking: () => Promise<void>;
  stopTracking: () => void;
  addPoint: (point: GPSPoint) => void;
  clearPoints: () => void;
}

export const useGPSTracking = (
  options: UseGPSTrackingOptions = {}
): UseGPSTrackingReturn => {
  const {
    accuracy = Location.LocationAccuracy.High,
    timeInterval = 1000, // 1 second
    distanceInterval = 1, // 1 meter
  } = options;

  const [isTracking, setIsTracking] = useState(false);
  const [currentLocation, setCurrentLocation] = useState<GPSPoint | null>(null);
  const [points, setPoints] = useState<GPSPoint[]>([]);
  const [error, setError] = useState<string | null>(null);

  const locationSubscription = useRef<Location.LocationSubscription | null>(null);

  /**
   * Request location permissions
   */
  const requestPermissions = async (): Promise<boolean> => {
    try {
      const { status: foregroundStatus } =
        await Location.requestForegroundPermissionsAsync();

      if (foregroundStatus !== 'granted') {
        setError('Location permission denied');
        Alert.alert(
          'Permission Required',
          'This app needs location permission to measure land areas.',
          [{ text: 'OK' }]
        );
        return false;
      }

      return true;
    } catch (err) {
      setError('Failed to request permissions');
      return false;
    }
  };

  /**
   * Start GPS tracking
   */
  const startTracking = async (): Promise<void> => {
    const hasPermission = await requestPermissions();
    if (!hasPermission) return;

    try {
      setIsTracking(true);
      setError(null);

      // Start watching position
      locationSubscription.current = await Location.watchPositionAsync(
        {
          accuracy,
          timeInterval,
          distanceInterval,
        },
        (location) => {
          const point: GPSPoint = {
            latitude: location.coords.latitude,
            longitude: location.coords.longitude,
            altitude: location.coords.altitude,
            accuracy: location.coords.accuracy,
            timestamp: new Date(location.timestamp).toISOString(),
          };

          setCurrentLocation(point);
          
          // Auto-add point if tracking
          if (isTracking) {
            setPoints((prev) => [...prev, point]);
          }
        }
      );
    } catch (err: any) {
      setError(err.message || 'Failed to start tracking');
      setIsTracking(false);
    }
  };

  /**
   * Stop GPS tracking
   */
  const stopTracking = useCallback((): void => {
    if (locationSubscription.current) {
      locationSubscription.current.remove();
      locationSubscription.current = null;
    }
    setIsTracking(false);
  }, []);

  /**
   * Manually add a point (for point-based measurement)
   */
  const addPoint = useCallback((point: GPSPoint): void => {
    setPoints((prev) => [...prev, point]);
  }, []);

  /**
   * Clear all points
   */
  const clearPoints = useCallback((): void => {
    setPoints([]);
  }, []);

  /**
   * Cleanup on unmount
   */
  useEffect(() => {
    return () => {
      if (locationSubscription.current) {
        locationSubscription.current.remove();
      }
    };
  }, []);

  return {
    isTracking,
    currentLocation,
    points,
    error,
    startTracking,
    stopTracking,
    addPoint,
    clearPoints,
  };
};

/**
 * Area Calculation Hook
 * 
 * Calculates area from GPS polygon points.
 */

const EARTH_RADIUS_METERS = 6371000;
const SQUARE_METERS_PER_ACRE = 4046.86;
const SQUARE_METERS_PER_HECTARE = 10000;

export interface AreaCalculation {
  areaAcres: number;
  areaHectares: number;
  perimeterMeters: number;
}

export const useAreaCalculation = () => {
  const calculate = useCallback((points: GPSPoint[]): AreaCalculation | null => {
    if (points.length < 3) {
      return null;
    }

    // Calculate area using Shoelace formula with spherical correction
    const areaSquareMeters = calculateAreaInSquareMeters(points);
    const perimeterMeters = calculatePerimeter(points);

    return {
      areaAcres: areaSquareMeters / SQUARE_METERS_PER_ACRE,
      areaHectares: areaSquareMeters / SQUARE_METERS_PER_HECTARE,
      perimeterMeters,
    };
  }, []);

  return { calculate };
};

/**
 * Calculate area using Shoelace formula
 */
function calculateAreaInSquareMeters(points: GPSPoint[]): number {
  const n = points.length;
  let area = 0;

  for (let i = 0; i < n; i++) {
    const p1 = points[i];
    const p2 = points[(i + 1) % n];

    const lat1 = (p1.latitude * Math.PI) / 180;
    const lon1 = (p1.longitude * Math.PI) / 180;
    const lat2 = (p2.latitude * Math.PI) / 180;
    const lon2 = (p2.longitude * Math.PI) / 180;

    area += (lon2 - lon1) * (2 + Math.sin(lat1) + Math.sin(lat2));
  }

  area = Math.abs((area * EARTH_RADIUS_METERS * EARTH_RADIUS_METERS) / 2.0);

  return area;
}

/**
 * Calculate perimeter using Haversine distance
 */
function calculatePerimeter(points: GPSPoint[]): number {
  const n = points.length;
  let perimeter = 0;

  for (let i = 0; i < n; i++) {
    const p1 = points[i];
    const p2 = points[(i + 1) % n];
    perimeter += haversineDistance(p1, p2);
  }

  return perimeter;
}

/**
 * Haversine distance between two points
 */
function haversineDistance(p1: GPSPoint, p2: GPSPoint): number {
  const lat1 = (p1.latitude * Math.PI) / 180;
  const lon1 = (p1.longitude * Math.PI) / 180;
  const lat2 = (p2.latitude * Math.PI) / 180;
  const lon2 = (p2.longitude * Math.PI) / 180;

  const dLat = lat2 - lat1;
  const dLon = lon2 - lon1;

  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLon / 2) * Math.sin(dLon / 2);

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return EARTH_RADIUS_METERS * c;
}
