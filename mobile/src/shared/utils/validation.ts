export const validateEmail = (email: string): boolean => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

// Validates phone numbers (Sri Lankan format preferred)
// Expected formats: +94771234567, 0771234567
// Note: For production, consider using libphonenumber-js for comprehensive validation
export const validatePhone = (phone: string): boolean => {
  const cleaned = phone.replace(/[\s-()]/g, '');
  // Sri Lankan: +94XXXXXXXXX or 0XXXXXXXXX (9-10 digits)
  const sriLankaRegex = /^(\+94[1-9][0-9]{8}|0[1-9][0-9]{8})$/;
  // General international: at least 10 digits
  const generalRegex = /^\+?[1-9][0-9]{9,14}$/;
  return sriLankaRegex.test(cleaned) || generalRegex.test(cleaned);
};

export const validatePassword = (password: string): {
  isValid: boolean;
  errors: string[];
} => {
  const errors: string[] = [];
  
  if (password.length < 8) {
    errors.push('Password must be at least 8 characters long');
  }
  
  if (!/[A-Z]/.test(password)) {
    errors.push('Password must contain at least one uppercase letter');
  }
  
  if (!/[a-z]/.test(password)) {
    errors.push('Password must contain at least one lowercase letter');
  }
  
  if (!/\d/.test(password)) {
    errors.push('Password must contain at least one number');
  }
  
  return {
    isValid: errors.length === 0,
    errors,
  };
};

export const validateRequired = (value: string): boolean => {
  return value.trim().length > 0;
};

export const validateNumber = (value: string): boolean => {
  return !isNaN(Number(value)) && value.trim().length > 0;
};

export const validatePositiveNumber = (value: string): boolean => {
  const num = Number(value);
  return !isNaN(num) && num > 0;
};
