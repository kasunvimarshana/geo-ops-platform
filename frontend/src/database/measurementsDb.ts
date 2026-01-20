/**
 * Measurements Database Operations
 * 
 * This module provides CRUD operations for land measurements in the local SQLite database.
 */

import { executeSql } from './database';

export interface LocalMeasurement {
  id?: number;
  server_id?: number;
  name: string;
  area_sqm?: number;
  area_acres?: number;
  area_hectares?: number;
  coordinates: string; // JSON string
  measured_at?: string;
  created_at?: string;
  updated_at?: string;
  synced: number; // 0 = not synced, 1 = synced
  deleted: number; // 0 = active, 1 = deleted
}

/**
 * Get all measurements from local database
 */
export const getAllMeasurements = async (): Promise<LocalMeasurement[]> => {
  try {
    const result = await executeSql(
      'SELECT * FROM measurements WHERE deleted = 0 ORDER BY created_at DESC'
    );

    const measurements: LocalMeasurement[] = [];
    for (let i = 0; i < result.rows.length; i++) {
      measurements.push(result.rows.item(i));
    }

    return measurements;
  } catch (error) {
    console.error('Error fetching measurements:', error);
    throw error;
  }
};

/**
 * Get unsynced measurements
 */
export const getUnsyncedMeasurements = async (): Promise<LocalMeasurement[]> => {
  try {
    const result = await executeSql(
      'SELECT * FROM measurements WHERE synced = 0 AND deleted = 0'
    );

    const measurements: LocalMeasurement[] = [];
    for (let i = 0; i < result.rows.length; i++) {
      measurements.push(result.rows.item(i));
    }

    return measurements;
  } catch (error) {
    console.error('Error fetching unsynced measurements:', error);
    throw error;
  }
};

/**
 * Get a single measurement by ID
 */
export const getMeasurementById = async (id: number): Promise<LocalMeasurement | null> => {
  try {
    const result = await executeSql(
      'SELECT * FROM measurements WHERE id = ? AND deleted = 0',
      [id]
    );

    if (result.rows.length > 0) {
      return result.rows.item(0);
    }

    return null;
  } catch (error) {
    console.error('Error fetching measurement:', error);
    throw error;
  }
};

/**
 * Create a new measurement
 */
export const createMeasurement = async (
  measurement: Omit<LocalMeasurement, 'id'>
): Promise<number> => {
  try {
    const result = await executeSql(
      `INSERT INTO measurements 
       (name, area_sqm, area_acres, area_hectares, coordinates, measured_at, synced, deleted) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        measurement.name,
        measurement.area_sqm || null,
        measurement.area_acres || null,
        measurement.area_hectares || null,
        measurement.coordinates,
        measurement.measured_at || new Date().toISOString(),
        measurement.synced || 0,
        measurement.deleted || 0,
      ]
    );

    return result.insertId || 0;
  } catch (error) {
    console.error('Error creating measurement:', error);
    throw error;
  }
};

/**
 * Update an existing measurement
 */
export const updateMeasurement = async (
  id: number,
  measurement: Partial<LocalMeasurement>
): Promise<void> => {
  try {
    const fields: string[] = [];
    const values: any[] = [];

    if (measurement.name !== undefined) {
      fields.push('name = ?');
      values.push(measurement.name);
    }
    if (measurement.area_sqm !== undefined) {
      fields.push('area_sqm = ?');
      values.push(measurement.area_sqm);
    }
    if (measurement.area_acres !== undefined) {
      fields.push('area_acres = ?');
      values.push(measurement.area_acres);
    }
    if (measurement.area_hectares !== undefined) {
      fields.push('area_hectares = ?');
      values.push(measurement.area_hectares);
    }
    if (measurement.coordinates !== undefined) {
      fields.push('coordinates = ?');
      values.push(measurement.coordinates);
    }
    if (measurement.synced !== undefined) {
      fields.push('synced = ?');
      values.push(measurement.synced);
    }
    if (measurement.server_id !== undefined) {
      fields.push('server_id = ?');
      values.push(measurement.server_id);
    }

    fields.push('updated_at = ?');
    values.push(new Date().toISOString());

    values.push(id);

    await executeSql(
      `UPDATE measurements SET ${fields.join(', ')} WHERE id = ?`,
      values
    );
  } catch (error) {
    console.error('Error updating measurement:', error);
    throw error;
  }
};

/**
 * Soft delete a measurement
 */
export const deleteMeasurement = async (id: number): Promise<void> => {
  try {
    await executeSql(
      'UPDATE measurements SET deleted = 1, updated_at = ? WHERE id = ?',
      [new Date().toISOString(), id]
    );
  } catch (error) {
    console.error('Error deleting measurement:', error);
    throw error;
  }
};

/**
 * Mark a measurement as synced
 */
export const markMeasurementAsSynced = async (
  id: number,
  serverId: number
): Promise<void> => {
  try {
    await executeSql(
      'UPDATE measurements SET synced = 1, server_id = ?, updated_at = ? WHERE id = ?',
      [serverId, new Date().toISOString(), id]
    );
  } catch (error) {
    console.error('Error marking measurement as synced:', error);
    throw error;
  }
};

/**
 * Upsert measurements from server (for sync)
 */
export const upsertMeasurementsFromServer = async (
  measurements: any[]
): Promise<void> => {
  try {
    for (const measurement of measurements) {
      const existing = await executeSql(
        'SELECT id FROM measurements WHERE server_id = ?',
        [measurement.id]
      );

      if (existing.rows.length > 0) {
        // Update existing
        await executeSql(
          `UPDATE measurements SET 
           name = ?, 
           area_sqm = ?, 
           area_acres = ?, 
           area_hectares = ?, 
           coordinates = ?, 
           measured_at = ?,
           synced = 1,
           updated_at = ?
           WHERE server_id = ?`,
          [
            measurement.name,
            measurement.area_sqm,
            measurement.area_acres,
            measurement.area_hectares,
            JSON.stringify(measurement.coordinates || []),
            measurement.measured_at || measurement.created_at,
            new Date().toISOString(),
            measurement.id,
          ]
        );
      } else {
        // Insert new
        await executeSql(
          `INSERT INTO measurements 
           (server_id, name, area_sqm, area_acres, area_hectares, coordinates, measured_at, synced, deleted) 
           VALUES (?, ?, ?, ?, ?, ?, ?, 1, 0)`,
          [
            measurement.id,
            measurement.name,
            measurement.area_sqm,
            measurement.area_acres,
            measurement.area_hectares,
            JSON.stringify(measurement.coordinates || []),
            measurement.measured_at || measurement.created_at,
          ]
        );
      }
    }
  } catch (error) {
    console.error('Error upserting measurements from server:', error);
    throw error;
  }
};
