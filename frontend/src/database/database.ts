/**
 * SQLite Database Configuration and Management
 *
 * This module handles the local SQLite database for offline-first functionality.
 * It provides CRUD operations for measurements, jobs, and sync queue.
 */

import * as SQLite from 'expo-sqlite';

const DB_NAME = 'geo-ops.db';
const DB_VERSION = 1;

let db: SQLite.WebSQLDatabase | null = null;

/**
 * Initialize the SQLite database and create tables
 */
export const initDatabase = async (): Promise<void> => {
  try {
    db = SQLite.openDatabase(DB_NAME);

    await executeSql(`
      CREATE TABLE IF NOT EXISTS measurements (
        id INTEGER PRIMARY KEY,
        server_id INTEGER,
        name TEXT NOT NULL,
        area_sqm REAL,
        area_acres REAL,
        area_hectares REAL,
        coordinates TEXT,
        measured_at TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
        synced INTEGER DEFAULT 0,
        deleted INTEGER DEFAULT 0
      )
    `);

    // Create index for sync queries
    await executeSql(`
      CREATE INDEX IF NOT EXISTS idx_measurements_sync_status 
      ON measurements (synced, deleted)
    `);

    await executeSql(`
      CREATE TABLE IF NOT EXISTS jobs (
        id INTEGER PRIMARY KEY,
        server_id INTEGER,
        customer_id INTEGER,
        land_measurement_id INTEGER,
        driver_id INTEGER,
        machine_id INTEGER,
        service_type TEXT NOT NULL,
        status TEXT DEFAULT 'pending',
        scheduled_at TEXT,
        started_at TEXT,
        completed_at TEXT,
        notes TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
        synced INTEGER DEFAULT 0,
        deleted INTEGER DEFAULT 0
      )
    `);

    // Create index for sync queries
    await executeSql(`
      CREATE INDEX IF NOT EXISTS idx_jobs_sync_status 
      ON jobs (synced, deleted)
    `);

    await executeSql(`
      CREATE TABLE IF NOT EXISTS sync_queue (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        entity_type TEXT NOT NULL,
        entity_id INTEGER NOT NULL,
        operation TEXT NOT NULL,
        data TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        retry_count INTEGER DEFAULT 0,
        last_error TEXT
      )
    `);

    await executeSql(`
      CREATE TABLE IF NOT EXISTS app_settings (
        key TEXT PRIMARY KEY,
        value TEXT,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    console.log('Database initialized successfully');
  } catch (error) {
    console.error('Error initializing database:', error);
    throw error;
  }
};

/**
 * Execute a SQL query with parameters
 */
const executeSql = (sql: string, params: any[] = []): Promise<SQLite.SQLResultSet> => {
  return new Promise((resolve, reject) => {
    if (!db) {
      reject(new Error('Database not initialized'));
      return;
    }

    db.transaction(
      (tx) => {
        tx.executeSql(
          sql,
          params,
          (_, result) => resolve(result),
          (_, error) => {
            console.error('SQL Error:', error);
            reject(error);
            return false;
          }
        );
      },
      (error) => {
        console.error('Transaction Error:', error);
        reject(error);
      }
    );
  });
};

/**
 * Close the database connection
 */
export const closeDatabase = (): void => {
  db = null;
};

/**
 * Clear all data from the database (for logout/reset)
 */
export const clearDatabase = async (): Promise<void> => {
  try {
    await executeSql('DELETE FROM measurements');
    await executeSql('DELETE FROM jobs');
    await executeSql('DELETE FROM sync_queue');
    console.log('Database cleared successfully');
  } catch (error) {
    console.error('Error clearing database:', error);
    throw error;
  }
};

/**
 * Get database statistics
 */
export const getDatabaseStats = async (): Promise<any> => {
  try {
    const measurements = await executeSql(
      'SELECT COUNT(*) as count FROM measurements WHERE deleted = 0'
    );
    const jobs = await executeSql('SELECT COUNT(*) as count FROM jobs WHERE deleted = 0');
    const syncQueue = await executeSql('SELECT COUNT(*) as count FROM sync_queue');

    return {
      measurements: measurements.rows.item(0).count,
      jobs: jobs.rows.item(0).count,
      syncQueue: syncQueue.rows.item(0).count,
    };
  } catch (error) {
    console.error('Error getting database stats:', error);
    return { measurements: 0, jobs: 0, syncQueue: 0 };
  }
};

export { executeSql };
export default db;
