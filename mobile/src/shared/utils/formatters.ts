import { format, formatDistance, parseISO } from 'date-fns';

export const formatDate = (date: string | Date): string => {
  if (typeof date === 'string') {
    return format(parseISO(date), 'MMM dd, yyyy');
  }
  return format(date, 'MMM dd, yyyy');
};

export const formatDateTime = (date: string | Date): string => {
  if (typeof date === 'string') {
    return format(parseISO(date), 'MMM dd, yyyy HH:mm');
  }
  return format(date, 'MMM dd, yyyy HH:mm');
};

export const formatRelativeTime = (date: string | Date): string => {
  const parsedDate = typeof date === 'string' ? parseISO(date) : date;
  return formatDistance(parsedDate, new Date(), { addSuffix: true });
};

export const formatCurrency = (amount: number, currency: string = 'LKR'): string => {
  return new Intl.NumberFormat('en-LK', {
    style: 'currency',
    currency,
  }).format(amount);
};

export const formatArea = (sqm: number): string => {
  return `${sqm.toFixed(2)} sq.m`;
};

export const formatAreaAcres = (acres: number): string => {
  return `${acres.toFixed(3)} acres`;
};

export const formatPerimeter = (meters: number): string => {
  return `${meters.toFixed(2)} m`;
};
