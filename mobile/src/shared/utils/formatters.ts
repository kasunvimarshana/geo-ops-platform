import { format, parseISO, formatDistanceToNow, isValid } from 'date-fns';

export const formatDate = (date: string | Date, formatStr: string = 'PPP'): string => {
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date;
    return isValid(dateObj) ? format(dateObj, formatStr) : '';
  } catch (error) {
    console.error('Date formatting error:', error);
    return '';
  }
};

export const formatRelativeTime = (date: string | Date): string => {
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date;
    return isValid(dateObj) ? formatDistanceToNow(dateObj, { addSuffix: true }) : '';
  } catch (error) {
    console.error('Relative time formatting error:', error);
    return '';
  }
};

export const formatCurrency = (
  amount: number,
  currency: string = 'LKR'
): string => {
  try {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency,
      minimumFractionDigits: 2,
    }).format(amount);
  } catch (error) {
    console.error('Currency formatting error:', error);
    return `${currency} ${amount.toFixed(2)}`;
  }
};

export const formatArea = (area: number, unit: string = 'acres'): string => {
  return `${area.toFixed(2)} ${unit}`;
};

export const formatDistance = (distance: number, unit: string = 'km'): string => {
  if (unit === 'km' && distance < 1) {
    return `${(distance * 1000).toFixed(0)} m`;
  }
  return `${distance.toFixed(2)} ${unit}`;
};
