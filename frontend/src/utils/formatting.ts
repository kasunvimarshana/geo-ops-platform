/**
 * Formatting utility functions
 */

import { formatDistance, formatDate, parseISO } from 'date-fns';

export function formatDateString(date: Date | string): string {
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date;
    return formatDate(dateObj, 'yyyy-MM-dd');
  } catch {
    return 'Invalid date';
  }
}

export function formatTimeAgo(date: Date | string): string {
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date;
    return formatDistance(dateObj, new Date(), { addSuffix: true });
  } catch {
    return 'Unknown';
  }
}

export function formatCoordinates(
  latitude: number,
  longitude: number,
  decimals = 4
): string {
  return `${latitude.toFixed(decimals)}, ${longitude.toFixed(decimals)}`;
}

export function formatArea(areaInSquareMeters: number): string {
  if (areaInSquareMeters < 10000) {
    return `${(areaInSquareMeters / 100).toFixed(2)} centiares`;
  }
  return `${(areaInSquareMeters / 10000).toFixed(2)} hectares`;
}
