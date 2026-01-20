import { query } from '../config/database';
import { AppError } from '../utils/errors';
import { GpsCoordinate, MeasurementUnit } from '../types';

export class LandMeasurementService {
  async create(userId: string, data: {
    name: string;
    description?: string;
    coordinates: GpsCoordinate[];
    unit: MeasurementUnit;
    address?: string;
    metadata?: Record<string, any>;
  }) {
    // Calculate area from coordinates
    const area = this.calculateArea(data.coordinates, data.unit);

    const result = await query(
      `INSERT INTO land_measurements (
        user_id, name, description, coordinates, area, unit, address, metadata
      ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
      RETURNING *`,
      [
        userId,
        data.name,
        data.description || null,
        JSON.stringify(data.coordinates),
        area,
        data.unit,
        data.address || null,
        data.metadata ? JSON.stringify(data.metadata) : null,
      ]
    );

    return this.formatMeasurement(result.rows[0]);
  }

  async getById(id: string, userId: string) {
    const result = await query(
      'SELECT * FROM land_measurements WHERE id = $1 AND user_id = $2',
      [id, userId]
    );

    if (result.rows.length === 0) {
      throw new AppError(404, 'Land measurement not found');
    }

    return this.formatMeasurement(result.rows[0]);
  }

  async getAll(userId: string, filters?: {
    limit?: number;
    offset?: number;
    search?: string;
  }) {
    let queryText = 'SELECT * FROM land_measurements WHERE user_id = $1';
    const params: any[] = [userId];
    let paramIndex = 2;

    if (filters?.search) {
      queryText += ` AND (name ILIKE $${paramIndex} OR address ILIKE $${paramIndex})`;
      params.push(`%${filters.search}%`);
      paramIndex++;
    }

    queryText += ' ORDER BY created_at DESC';

    if (filters?.limit) {
      queryText += ` LIMIT $${paramIndex}`;
      params.push(filters.limit);
      paramIndex++;
    }

    if (filters?.offset) {
      queryText += ` OFFSET $${paramIndex}`;
      params.push(filters.offset);
    }

    const result = await query(queryText, params);

    return result.rows.map(row => this.formatMeasurement(row));
  }

  async update(id: string, userId: string, data: {
    name?: string;
    description?: string;
    address?: string;
    metadata?: Record<string, any>;
  }) {
    const updates: string[] = [];
    const params: any[] = [];
    let paramIndex = 1;

    if (data.name !== undefined) {
      updates.push(`name = $${paramIndex++}`);
      params.push(data.name);
    }

    if (data.description !== undefined) {
      updates.push(`description = $${paramIndex++}`);
      params.push(data.description);
    }

    if (data.address !== undefined) {
      updates.push(`address = $${paramIndex++}`);
      params.push(data.address);
    }

    if (data.metadata !== undefined) {
      updates.push(`metadata = $${paramIndex++}`);
      params.push(JSON.stringify(data.metadata));
    }

    if (updates.length === 0) {
      throw new AppError(400, 'No fields to update');
    }

    updates.push(`updated_at = CURRENT_TIMESTAMP`);
    params.push(id, userId);

    const result = await query(
      `UPDATE land_measurements 
       SET ${updates.join(', ')}
       WHERE id = $${paramIndex} AND user_id = $${paramIndex + 1}
       RETURNING *`,
      params
    );

    if (result.rows.length === 0) {
      throw new AppError(404, 'Land measurement not found');
    }

    return this.formatMeasurement(result.rows[0]);
  }

  async delete(id: string, userId: string) {
    const result = await query(
      'DELETE FROM land_measurements WHERE id = $1 AND user_id = $2 RETURNING id',
      [id, userId]
    );

    if (result.rows.length === 0) {
      throw new AppError(404, 'Land measurement not found');
    }

    return { message: 'Land measurement deleted successfully' };
  }

  private formatMeasurement(row: any) {
    return {
      id: row.id,
      userId: row.user_id,
      name: row.name,
      description: row.description,
      coordinates: typeof row.coordinates === 'string' 
        ? JSON.parse(row.coordinates) 
        : row.coordinates,
      area: parseFloat(row.area),
      unit: row.unit,
      address: row.address,
      metadata: row.metadata ? 
        (typeof row.metadata === 'string' ? JSON.parse(row.metadata) : row.metadata) 
        : null,
      createdAt: row.created_at,
      updatedAt: row.updated_at,
    };
  }

  private calculateArea(coordinates: GpsCoordinate[], unit: MeasurementUnit): number {
    if (coordinates.length < 3) {
      throw new AppError(400, 'At least 3 coordinates required to calculate area');
    }

    // Calculate area using spherical excess formula for better accuracy
    // This is a simplified version - for production, consider using turf.js
    let area = 0;
    const n = coordinates.length;
    const earthRadiusMeters = 6378137; // WGS84 semi-major axis

    // Convert degrees to radians
    const toRad = (deg: number) => (deg * Math.PI) / 180;

    for (let i = 0; i < n; i++) {
      const j = (i + 1) % n;
      const lat1 = toRad(coordinates[i].latitude);
      const lat2 = toRad(coordinates[j].latitude);
      const lon1 = toRad(coordinates[i].longitude);
      const lon2 = toRad(coordinates[j].longitude);

      area += (lon2 - lon1) * (2 + Math.sin(lat1) + Math.sin(lat2));
    }

    // Area in square meters
    const areaInSqMeters = Math.abs(area * earthRadiusMeters * earthRadiusMeters / 2);

    // Convert to requested unit
    switch (unit) {
      case MeasurementUnit.SQUARE_METERS:
        return Math.round(areaInSqMeters * 100) / 100;
      case MeasurementUnit.HECTARES:
        return Math.round((areaInSqMeters / 10000) * 10000) / 10000;
      case MeasurementUnit.ACRES:
        return Math.round((areaInSqMeters / 4046.86) * 10000) / 10000;
      default:
        return Math.round(areaInSqMeters * 100) / 100;
    }
  }
}
