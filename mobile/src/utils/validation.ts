/**
 * Form Validation Utilities
 * Reusable validation functions for forms
 */

export interface ValidationRule {
  required?: boolean;
  minLength?: number;
  maxLength?: number;
  pattern?: RegExp;
  email?: boolean;
  phone?: boolean;
  numeric?: boolean;
  min?: number;
  max?: number;
  custom?: (value: any) => boolean;
  message?: string;
}

export interface ValidationResult {
  isValid: boolean;
  errors: Record<string, string>;
}

export const validateField = (
  value: any,
  rules: ValidationRule
): string | null => {
  // Required check
  if (rules.required && (!value || value.toString().trim() === '')) {
    return rules.message || 'This field is required';
  }

  // Skip other validations if empty and not required
  if (!value || value.toString().trim() === '') {
    return null;
  }

  // Min length check
  if (rules.minLength && value.toString().length < rules.minLength) {
    return rules.message || `Minimum ${rules.minLength} characters required`;
  }

  // Max length check
  if (rules.maxLength && value.toString().length > rules.maxLength) {
    return rules.message || `Maximum ${rules.maxLength} characters allowed`;
  }

  // Email validation
  if (rules.email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      return rules.message || 'Invalid email address';
    }
  }

  // Phone validation
  if (rules.phone) {
    // Basic international phone number validation
    // For production, consider using libphonenumber-js library
    const phoneRegex = /^\+?[1-9]\d{1,14}$/;
    if (!phoneRegex.test(value.replace(/[\s\-().]/g, ''))) {
      return rules.message || 'Invalid phone number';
    }
  }

  // Numeric validation
  if (rules.numeric) {
    const numValue = value.toString().trim();
    if (numValue === '' || !/^-?\d+(\.\d+)?$/.test(numValue)) {
      return rules.message || 'Must be a number';
    }
  }

  // Min value check
  if (rules.min !== undefined && Number(value) < rules.min) {
    return rules.message || `Minimum value is ${rules.min}`;
  }

  // Max value check
  if (rules.max !== undefined && Number(value) > rules.max) {
    return rules.message || `Maximum value is ${rules.max}`;
  }

  // Pattern check
  if (rules.pattern && !rules.pattern.test(value)) {
    return rules.message || 'Invalid format';
  }

  // Custom validation
  if (rules.custom && !rules.custom(value)) {
    return rules.message || 'Validation failed';
  }

  return null;
};

export const validateForm = (
  data: Record<string, any>,
  rules: Record<string, ValidationRule>
): ValidationResult => {
  const errors: Record<string, string> = {};

  Object.keys(rules).forEach((field) => {
    const error = validateField(data[field], rules[field]);
    if (error) {
      errors[field] = error;
    }
  });

  return {
    isValid: Object.keys(errors).length === 0,
    errors,
  };
};

// Common validation rules
export const ValidationRules = {
  email: {
    required: true,
    email: true,
    message: 'Valid email address is required',
  },
  password: {
    required: true,
    minLength: 8,
    pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/,
    message:
      'Password must be at least 8 characters with uppercase, lowercase, and number',
  },
  phone: {
    phone: true,
    message: 'Valid phone number is required',
  },
  name: {
    required: true,
    minLength: 2,
    maxLength: 255,
    message: 'Name must be between 2 and 255 characters',
  },
  numeric: {
    numeric: true,
    min: 0,
    message: 'Must be a positive number',
  },
};
