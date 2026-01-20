/**
 * Jobs Database Operations
 * 
 * This module provides CRUD operations for jobs in the local SQLite database.
 */

import { executeSql } from './database';

export interface LocalJob {
  id?: number;
  server_id?: number;
  customer_id: number;
  land_measurement_id?: number;
  driver_id?: number;
  machine_id?: number;
  service_type: string;
  status: string;
  scheduled_at?: string;
  started_at?: string;
  completed_at?: string;
  notes?: string;
  created_at?: string;
  updated_at?: string;
  synced: number; // 0 = not synced, 1 = synced
  deleted: number; // 0 = active, 1 = deleted
}

/**
 * Get all jobs from local database
 */
export const getAllJobs = async (): Promise<LocalJob[]> => {
  try {
    const result = await executeSql(
      'SELECT * FROM jobs WHERE deleted = 0 ORDER BY created_at DESC'
    );

    const jobs: LocalJob[] = [];
    for (let i = 0; i < result.rows.length; i++) {
      jobs.push(result.rows.item(i));
    }

    return jobs;
  } catch (error) {
    console.error('Error fetching jobs:', error);
    throw error;
  }
};

/**
 * Get unsynced jobs
 */
export const getUnsyncedJobs = async (): Promise<LocalJob[]> => {
  try {
    const result = await executeSql(
      'SELECT * FROM jobs WHERE synced = 0 AND deleted = 0'
    );

    const jobs: LocalJob[] = [];
    for (let i = 0; i < result.rows.length; i++) {
      jobs.push(result.rows.item(i));
    }

    return jobs;
  } catch (error) {
    console.error('Error fetching unsynced jobs:', error);
    throw error;
  }
};

/**
 * Get a single job by ID
 */
export const getJobById = async (id: number): Promise<LocalJob | null> => {
  try {
    const result = await executeSql(
      'SELECT * FROM jobs WHERE id = ? AND deleted = 0',
      [id]
    );

    if (result.rows.length > 0) {
      return result.rows.item(0);
    }

    return null;
  } catch (error) {
    console.error('Error fetching job:', error);
    throw error;
  }
};

/**
 * Create a new job
 */
export const createJob = async (job: Omit<LocalJob, 'id'>): Promise<number> => {
  try {
    const result = await executeSql(
      `INSERT INTO jobs 
       (customer_id, land_measurement_id, driver_id, machine_id, service_type, status, 
        scheduled_at, notes, synced, deleted) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        job.customer_id,
        job.land_measurement_id || null,
        job.driver_id || null,
        job.machine_id || null,
        job.service_type,
        job.status || 'pending',
        job.scheduled_at || null,
        job.notes || null,
        job.synced || 0,
        job.deleted || 0,
      ]
    );

    return result.insertId || 0;
  } catch (error) {
    console.error('Error creating job:', error);
    throw error;
  }
};

/**
 * Update an existing job
 */
export const updateJob = async (
  id: number,
  job: Partial<LocalJob>
): Promise<void> => {
  try {
    const fields: string[] = [];
    const values: any[] = [];

    if (job.customer_id !== undefined) {
      fields.push('customer_id = ?');
      values.push(job.customer_id);
    }
    if (job.land_measurement_id !== undefined) {
      fields.push('land_measurement_id = ?');
      values.push(job.land_measurement_id);
    }
    if (job.driver_id !== undefined) {
      fields.push('driver_id = ?');
      values.push(job.driver_id);
    }
    if (job.machine_id !== undefined) {
      fields.push('machine_id = ?');
      values.push(job.machine_id);
    }
    if (job.service_type !== undefined) {
      fields.push('service_type = ?');
      values.push(job.service_type);
    }
    if (job.status !== undefined) {
      fields.push('status = ?');
      values.push(job.status);
    }
    if (job.scheduled_at !== undefined) {
      fields.push('scheduled_at = ?');
      values.push(job.scheduled_at);
    }
    if (job.started_at !== undefined) {
      fields.push('started_at = ?');
      values.push(job.started_at);
    }
    if (job.completed_at !== undefined) {
      fields.push('completed_at = ?');
      values.push(job.completed_at);
    }
    if (job.notes !== undefined) {
      fields.push('notes = ?');
      values.push(job.notes);
    }
    if (job.synced !== undefined) {
      fields.push('synced = ?');
      values.push(job.synced);
    }
    if (job.server_id !== undefined) {
      fields.push('server_id = ?');
      values.push(job.server_id);
    }

    fields.push('updated_at = ?');
    values.push(new Date().toISOString());

    values.push(id);

    await executeSql(`UPDATE jobs SET ${fields.join(', ')} WHERE id = ?`, values);
  } catch (error) {
    console.error('Error updating job:', error);
    throw error;
  }
};

/**
 * Soft delete a job
 */
export const deleteJob = async (id: number): Promise<void> => {
  try {
    await executeSql(
      'UPDATE jobs SET deleted = 1, updated_at = ? WHERE id = ?',
      [new Date().toISOString(), id]
    );
  } catch (error) {
    console.error('Error deleting job:', error);
    throw error;
  }
};

/**
 * Mark a job as synced
 */
export const markJobAsSynced = async (id: number, serverId: number): Promise<void> => {
  try {
    await executeSql(
      'UPDATE jobs SET synced = 1, server_id = ?, updated_at = ? WHERE id = ?',
      [serverId, new Date().toISOString(), id]
    );
  } catch (error) {
    console.error('Error marking job as synced:', error);
    throw error;
  }
};

/**
 * Upsert jobs from server (for sync)
 */
export const upsertJobsFromServer = async (jobs: any[]): Promise<void> => {
  try {
    for (const job of jobs) {
      const existing = await executeSql(
        'SELECT id FROM jobs WHERE server_id = ?',
        [job.id]
      );

      if (existing.rows.length > 0) {
        // Update existing
        await executeSql(
          `UPDATE jobs SET 
           customer_id = ?, 
           land_measurement_id = ?, 
           driver_id = ?, 
           machine_id = ?, 
           service_type = ?, 
           status = ?,
           scheduled_at = ?,
           started_at = ?,
           completed_at = ?,
           notes = ?,
           synced = 1,
           updated_at = ?
           WHERE server_id = ?`,
          [
            job.customer_id,
            job.land_measurement_id,
            job.driver_id,
            job.machine_id,
            job.service_type,
            job.status,
            job.scheduled_at,
            job.started_at,
            job.completed_at,
            job.notes,
            new Date().toISOString(),
            job.id,
          ]
        );
      } else {
        // Insert new
        await executeSql(
          `INSERT INTO jobs 
           (server_id, customer_id, land_measurement_id, driver_id, machine_id, 
            service_type, status, scheduled_at, started_at, completed_at, notes, synced, deleted) 
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)`,
          [
            job.id,
            job.customer_id,
            job.land_measurement_id,
            job.driver_id,
            job.machine_id,
            job.service_type,
            job.status,
            job.scheduled_at,
            job.started_at,
            job.completed_at,
            job.notes,
          ]
        );
      }
    }
  } catch (error) {
    console.error('Error upserting jobs from server:', error);
    throw error;
  }
};
