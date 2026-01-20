import { GpsCoordinate } from '../types';
import { DEFAULT_MAP_REGION, MAP_CONSTANTS } from '../constants';

interface MapRegion {
  latitude: number;
  longitude: number;
  latitudeDelta: number;
  longitudeDelta: number;
}

/**
 * Calculate map region from an array of GPS coordinates
 * Returns a region that encompasses all coordinates with padding
 * Falls back to DEFAULT_MAP_REGION if no coordinates provided
 */
export function calculateMapRegion(coordinates: GpsCoordinate[]): MapRegion {
  if (coordinates.length === 0) {
    return DEFAULT_MAP_REGION;
  }

  const lats = coordinates.map(c => c.latitude);
  const lngs = coordinates.map(c => c.longitude);
  
  const minLat = Math.min(...lats);
  const maxLat = Math.max(...lats);
  const minLng = Math.min(...lngs);
  const maxLng = Math.max(...lngs);
  
  const centerLat = (minLat + maxLat) / 2;
  const centerLng = (minLng + maxLng) / 2;
  
  // Use Math.max to ensure minimum delta even for single points
  const latDelta = Math.max(
    (maxLat - minLat) * MAP_CONSTANTS.REGION_PADDING_MULTIPLIER,
    MAP_CONSTANTS.MIN_REGION_DELTA
  );
  const lngDelta = Math.max(
    (maxLng - minLng) * MAP_CONSTANTS.REGION_PADDING_MULTIPLIER,
    MAP_CONSTANTS.MIN_REGION_DELTA
  );

  return {
    latitude: centerLat,
    longitude: centerLng,
    latitudeDelta: latDelta,
    longitudeDelta: lngDelta,
  };
}
