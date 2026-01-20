import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import { v4 as uuidv4 } from 'uuid';
import { config } from '../config';
import { query } from '../config/database';
import { AppError } from '../utils/errors';
import { UserRole, SubscriptionPackage } from '../types';

export class AuthService {
  async register(data: {
    email: string;
    password: string;
    firstName: string;
    lastName: string;
    phone: string;
    role?: UserRole;
    organizationName?: string;
  }) {
    // Check if user exists
    const existingUser = await query(
      'SELECT id FROM users WHERE email = $1',
      [data.email]
    );

    if (existingUser.rows.length > 0) {
      throw new AppError(400, 'Email already registered');
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(data.password, 10);

    // Create organization for owner role
    let organizationId = null;
    if (data.role === UserRole.OWNER || !data.role) {
      const orgResult = await query(
        `INSERT INTO organizations (name, subscription_package, is_active)
         VALUES ($1, $2, TRUE)
         RETURNING id`,
        [data.organizationName || `${data.firstName}'s Organization`, SubscriptionPackage.FREE]
      );
      organizationId = orgResult.rows[0].id;
    }

    // Create user
    const result = await query(
      `INSERT INTO users (
        email, password, first_name, last_name, phone, role, 
        organization_id, subscription_package, is_active
      ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, TRUE)
      RETURNING id, email, first_name, last_name, phone, role, organization_id, subscription_package`,
      [
        data.email,
        hashedPassword,
        data.firstName,
        data.lastName,
        data.phone,
        data.role || UserRole.OWNER,
        organizationId,
        SubscriptionPackage.FREE,
      ]
    );

    const user = result.rows[0];

    // Generate token
    const token = this.generateToken({
      id: user.id,
      email: user.email,
      role: user.role,
      organizationId: user.organization_id,
    });

    return {
      user: {
        id: user.id,
        email: user.email,
        firstName: user.first_name,
        lastName: user.last_name,
        phone: user.phone,
        role: user.role,
        organizationId: user.organization_id,
        subscriptionPackage: user.subscription_package,
      },
      token,
    };
  }

  async login(email: string, password: string) {
    // Find user
    const result = await query(
      `SELECT id, email, password, first_name, last_name, phone, role, 
              organization_id, subscription_package, is_active
       FROM users WHERE email = $1`,
      [email]
    );

    if (result.rows.length === 0) {
      throw new AppError(401, 'Invalid credentials');
    }

    const user = result.rows[0];

    if (!user.is_active) {
      throw new AppError(401, 'Account is inactive');
    }

    // Verify password
    const isValidPassword = await bcrypt.compare(password, user.password);

    if (!isValidPassword) {
      throw new AppError(401, 'Invalid credentials');
    }

    // Generate token
    const token = this.generateToken({
      id: user.id,
      email: user.email,
      role: user.role,
      organizationId: user.organization_id,
    });

    return {
      user: {
        id: user.id,
        email: user.email,
        firstName: user.first_name,
        lastName: user.last_name,
        phone: user.phone,
        role: user.role,
        organizationId: user.organization_id,
        subscriptionPackage: user.subscription_package,
      },
      token,
    };
  }

  async getProfile(userId: string) {
    const result = await query(
      `SELECT id, email, first_name, last_name, phone, role, 
              organization_id, subscription_package, subscription_expiry, 
              is_active, created_at
       FROM users WHERE id = $1`,
      [userId]
    );

    if (result.rows.length === 0) {
      throw new AppError(404, 'User not found');
    }

    const user = result.rows[0];

    return {
      id: user.id,
      email: user.email,
      firstName: user.first_name,
      lastName: user.last_name,
      phone: user.phone,
      role: user.role,
      organizationId: user.organization_id,
      subscriptionPackage: user.subscription_package,
      subscriptionExpiry: user.subscription_expiry,
      isActive: user.is_active,
      createdAt: user.created_at,
    };
  }

  private generateToken(payload: any): string {
    return jwt.sign(payload, config.jwt.secret, {
      expiresIn: config.jwt.expiresIn,
    });
  }
}
