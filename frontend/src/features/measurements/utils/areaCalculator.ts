/**
 * Calculate polygon area using Shoelace formula (Gauss's area formula)
 * Matches backend implementation for consistency
 */

export interface Coordinate {
  latitude: number;
  longitude: number;
}

export interface AreaResult {
  areaSqm: number;
  areaAcres: number;
  areaHectares: number;
}

const EARTH_RADIUS = 6371000; // meters

/**
 * Calculate polygon area in square meters, acres, and hectares
 */
export function calculatePolygonArea(coordinates: Coordinate[]): AreaResult {
  if (coordinates.length < 3) {
    throw new Error('At least 3 coordinates are required to calculate area');
  }

  let area = 0;
  const numPoints = coordinates.length;

  for (let i = 0; i < numPoints; i++) {
    const p1 = coordinates[i];
    const p2 = coordinates[(i + 1) % numPoints];

    const lat1 = toRadians(p1.latitude);
    const lat2 = toRadians(p2.latitude);
    const lng1 = toRadians(p1.longitude);
    const lng2 = toRadians(p2.longitude);

    area += (lng2 - lng1) * (2 + Math.sin(lat1) + Math.sin(lat2));
  }

  area = Math.abs((area * EARTH_RADIUS * EARTH_RADIUS) / 2);

  return {
    areaSqm: Math.round(area * 100) / 100,
    areaAcres: Math.round(area * 0.000247105 * 10000) / 10000,
    areaHectares: Math.round(area * 0.0001 * 10000) / 10000,
  };
}

/**
 * Calculate distance between two points using Haversine formula
 */
export function calculateDistance(
  lat1: number,
  lng1: number,
  lat2: number,
  lng2: number
): number {
  const dLat = toRadians(lat2 - lat1);
  const dLng = toRadians(lng2 - lng1);

  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(toRadians(lat1)) *
      Math.cos(toRadians(lat2)) *
      Math.sin(dLng / 2) *
      Math.sin(dLng / 2);

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return EARTH_RADIUS * c;
}

/**
 * Get center point (centroid) of a polygon
 */
export function getCentroid(coordinates: Coordinate[]): Coordinate {
  let lat = 0;
  let lng = 0;

  coordinates.forEach((coord) => {
    lat += coord.latitude;
    lng += coord.longitude;
  });

  return {
    latitude: Math.round((lat / coordinates.length) * 1000000) / 1000000,
    longitude: Math.round((lng / coordinates.length) * 1000000) / 1000000,
  };
}

/**
 * Convert degrees to radians
 */
function toRadians(degrees: number): number {
  return (degrees * Math.PI) / 180;
}

/**
 * Format area for display
 */
export function formatArea(area: AreaResult, unit: 'acres' | 'hectares' = 'acres'): string {
  if (unit === 'acres') {
    return `${area.areaAcres.toFixed(4)} acres`;
  }
  return `${area.areaHectares.toFixed(4)} hectares`;
}

/**
 * Check if polygon is closed (first and last points are the same)
 * Uses epsilon comparison to handle floating point precision issues
 */
export function isPolygonClosed(coordinates: Coordinate[]): boolean {
  if (coordinates.length < 2) return false;

  const first = coordinates[0];
  const last = coordinates[coordinates.length - 1];

  const epsilon = 0.0000001; // ~1cm precision for GPS coordinates
  return (
    Math.abs(first.latitude - last.latitude) < epsilon &&
    Math.abs(first.longitude - last.longitude) < epsilon
  );
}

/**
 * Close polygon if not already closed
 */
export function closePolygon(coordinates: Coordinate[]): Coordinate[] {
  if (isPolygonClosed(coordinates)) {
    return coordinates;
  }
  return [...coordinates, coordinates[0]];
}
