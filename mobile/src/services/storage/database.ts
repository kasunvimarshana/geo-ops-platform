import * as SQLite from 'expo-sqlite';

/**
 * SQLite Database Service
 * 
 * Manages local database for offline data storage
 */
class DatabaseService {
  private db: SQLite.SQLiteDatabase | null = null;

  async init(): Promise<void> {
    try {
      this.db = await SQLite.openDatabaseAsync('geoops.db');
      await this.createTables();
    } catch (error) {
      console.error('Database initialization error:', error);
      throw error;
    }
  }

  private async createTables(): Promise<void> {
    if (!this.db) throw new Error('Database not initialized');

    await this.db.execAsync(`
      PRAGMA journal_mode = WAL;

      CREATE TABLE IF NOT EXISTS lands (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        offline_id TEXT UNIQUE NOT NULL,
        organization_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        description TEXT,
        polygon TEXT NOT NULL,
        area_acres REAL NOT NULL,
        area_hectares REAL NOT NULL,
        measurement_type TEXT NOT NULL,
        location_name TEXT,
        customer_name TEXT,
        customer_phone TEXT,
        measured_by INTEGER NOT NULL,
        measured_at TEXT NOT NULL,
        status TEXT NOT NULL,
        sync_status TEXT NOT NULL DEFAULT 'pending',
        server_id INTEGER,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
      );

      CREATE TABLE IF NOT EXISTS jobs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        offline_id TEXT UNIQUE NOT NULL,
        organization_id INTEGER NOT NULL,
        land_id INTEGER,
        machine_id INTEGER NOT NULL,
        driver_id INTEGER NOT NULL,
        assigned_by INTEGER NOT NULL,
        title TEXT NOT NULL,
        description TEXT,
        job_date TEXT NOT NULL,
        status TEXT NOT NULL,
        start_time TEXT,
        end_time TEXT,
        duration_minutes INTEGER,
        customer_name TEXT NOT NULL,
        customer_phone TEXT NOT NULL,
        location_latitude REAL NOT NULL,
        location_longitude REAL NOT NULL,
        location_name TEXT NOT NULL,
        notes TEXT,
        sync_status TEXT NOT NULL DEFAULT 'pending',
        server_id INTEGER,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
      );

      CREATE TABLE IF NOT EXISTS invoices (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        offline_id TEXT UNIQUE NOT NULL,
        organization_id INTEGER NOT NULL,
        job_id INTEGER,
        land_id INTEGER,
        invoice_number TEXT NOT NULL,
        customer_name TEXT NOT NULL,
        customer_phone TEXT NOT NULL,
        invoice_date TEXT NOT NULL,
        due_date TEXT NOT NULL,
        area_acres REAL NOT NULL,
        area_hectares REAL NOT NULL,
        rate_per_unit REAL NOT NULL,
        subtotal REAL NOT NULL,
        tax_rate REAL NOT NULL,
        tax_amount REAL NOT NULL,
        total_amount REAL NOT NULL,
        paid_amount REAL NOT NULL DEFAULT 0,
        balance REAL NOT NULL,
        status TEXT NOT NULL,
        notes TEXT,
        pdf_path TEXT,
        printed_at TEXT,
        sync_status TEXT NOT NULL DEFAULT 'pending',
        server_id INTEGER,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
      );

      CREATE TABLE IF NOT EXISTS sync_queue (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        entity_type TEXT NOT NULL,
        entity_id TEXT NOT NULL,
        action TEXT NOT NULL,
        payload TEXT NOT NULL,
        attempts INTEGER DEFAULT 0,
        last_error TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
      );

      CREATE INDEX IF NOT EXISTS idx_lands_sync ON lands(sync_status);
      CREATE INDEX IF NOT EXISTS idx_jobs_sync ON jobs(sync_status);
      CREATE INDEX IF NOT EXISTS idx_invoices_sync ON invoices(sync_status);
      CREATE INDEX IF NOT EXISTS idx_sync_queue_retry ON sync_queue(entity_type, attempts, created_at);
    `);
  }

  async query<T = any>(sql: string, params: any[] = []): Promise<T[]> {
    if (!this.db) throw new Error('Database not initialized');
    
    try {
      const result = await this.db.getAllAsync(sql, params);
      return result as T[];
    } catch (error) {
      console.error('Query error:', error);
      throw error;
    }
  }

  async execute(sql: string, params: any[] = []): Promise<SQLite.SQLiteRunResult> {
    if (!this.db) throw new Error('Database not initialized');
    
    try {
      return await this.db.runAsync(sql, params);
    } catch (error) {
      console.error('Execute error:', error);
      throw error;
    }
  }

  async transaction(callback: () => Promise<void>): Promise<void> {
    if (!this.db) throw new Error('Database not initialized');
    
    try {
      await this.db.withTransactionAsync(callback);
    } catch (error) {
      console.error('Transaction error:', error);
      throw error;
    }
  }

  async close(): Promise<void> {
    if (this.db) {
      await this.db.closeAsync();
      this.db = null;
    }
  }
}

export const databaseService = new DatabaseService();
export default databaseService;
