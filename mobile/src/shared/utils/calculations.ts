import { Coordinates } from '../types/api.types';

export const calculatePolygonArea = (coordinates: Coordinates[]): number => {
  if (coordinates.length < 3) return 0;

  const R = 6371e3; // Earth's radius in meters
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
};

export const calculateDistance = (coord1: Coordinates, coord2: Coordinates): number => {
  const R = 6371e3;
  const φ1 = (coord1.latitude * Math.PI) / 180;
  const φ2 = (coord2.latitude * Math.PI) / 180;
  const Δφ = ((coord2.latitude - coord1.latitude) * Math.PI) / 180;
  const Δλ = ((coord2.longitude - coord1.longitude) * Math.PI) / 180;

  const a =
    Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
    Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return R * c;
};

export const sqmToAcres = (sqm: number): number => {
  return sqm * 0.000247105;
};

export const acresToSqm = (acres: number): number => {
  return acres / 0.000247105;
};
