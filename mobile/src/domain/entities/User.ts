/**
 * User Entity
 * Domain model for user authentication and profile
 */

export interface User {
  id: string;
  email: string;
  name: string;
  phone?: string;
  organizationId?: string;
  organization?: {
    id: string;
    name: string;
  };
  role: UserRole;
  createdAt: Date;
  updatedAt: Date;
}

export enum UserRole {
  ADMIN = 'admin',
  MANAGER = 'manager',
  DRIVER = 'driver',
  FIELD_WORKER = 'field_worker',
}

export interface AuthTokens {
  accessToken: string;
  refreshToken: string;
  expiresAt: Date;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  email: string;
  password: string;
  name: string;
  phone?: string;
  organization_name?: string;
}
