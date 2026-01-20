import { format, parseISO } from 'date-fns';

export const formatDate = (dateString: string, dateFormat: string = 'dd/MM/yyyy'): string => {
    const date = parseISO(dateString);
    return format(date, dateFormat);
};

export const getCurrentDate = (): string => {
    return format(new Date(), 'yyyy-MM-dd');
};

export const isDateValid = (dateString: string): boolean => {
    const date = parseISO(dateString);
    return !isNaN(date.getTime());
};