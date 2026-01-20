import * as SQLite from "expo-sqlite";
import { LandPlot, FieldJob, SyncQueueItem } from "../../types/api.types";

class SQLiteService {
  private db: SQLite.SQLiteDatabase | null = null;

  async init() {
    if (this.db) return;

    this.db = await SQLite.openDatabaseAsync("geo-ops.db");
    await this.createTables();
  }

  private async createTables() {
    if (!this.db) return;

    await this.db.execAsync(`
      CREATE TABLE IF NOT EXISTS land_plots (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        local_id TEXT UNIQUE NOT NULL,
        server_id INTEGER,
        coordinates TEXT NOT NULL,
        area_sqm REAL NOT NULL,
        area_acres REAL NOT NULL,
        perimeter_m REAL NOT NULL,
        job_id INTEGER,
        created_at TEXT NOT NULL,
        synced INTEGER DEFAULT 0
      );

      CREATE TABLE IF NOT EXISTS field_jobs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        local_id TEXT UNIQUE NOT NULL,
        server_id INTEGER,
        title TEXT NOT NULL,
        customer_name TEXT NOT NULL,
        location TEXT NOT NULL,
        description TEXT,
        status TEXT NOT NULL,
        estimated_price REAL,
        actual_price REAL,
        scheduled_date TEXT,
        completed_date TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        synced INTEGER DEFAULT 0
      );

      CREATE TABLE IF NOT EXISTS sync_queue (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        operation TEXT NOT NULL,
        entity_type TEXT NOT NULL,
        entity_id TEXT,
        data TEXT NOT NULL,
        status TEXT DEFAULT 'pending',
        retry_count INTEGER DEFAULT 0,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
      );

      CREATE INDEX IF NOT EXISTS idx_jobs_status ON field_jobs(status);
      CREATE INDEX IF NOT EXISTS idx_jobs_synced ON field_jobs(synced);
      CREATE INDEX IF NOT EXISTS idx_plots_synced ON land_plots(synced);
      CREATE INDEX IF NOT EXISTS idx_sync_queue_status ON sync_queue(status);
    `);
  }

  async savePlot(plot: LandPlot & { local_id: string }): Promise<void> {
    if (!this.db) await this.init();

    await this.db!.runAsync(
      `INSERT OR REPLACE INTO land_plots 
       (local_id, server_id, coordinates, area_sqm, area_acres, perimeter_m, job_id, created_at, synced)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        plot.local_id,
        plot.id || null,
        JSON.stringify(plot.coordinates),
        plot.area_sqm,
        plot.area_acres,
        plot.perimeter_m,
        plot.job || null,
        plot.created_at || new Date().toISOString(),
        plot.synced ? 1 : 0,
      ],
    );
  }

  async getPlots(jobId?: number): Promise<LandPlot[]> {
    if (!this.db) await this.init();

    const query = jobId
      ? "SELECT * FROM land_plots WHERE job_id = ? ORDER BY created_at DESC"
      : "SELECT * FROM land_plots ORDER BY created_at DESC";

    const params = jobId ? [jobId] : [];
    const result = await this.db!.getAllAsync(query, params);

    return result.map((row: any) => ({
      id: row.server_id,
      local_id: row.local_id,
      coordinates: JSON.parse(row.coordinates),
      area_sqm: row.area_sqm,
      area_acres: row.area_acres,
      perimeter_m: row.perimeter_m,
      job: row.job_id,
      created_at: row.created_at,
      synced: row.synced === 1,
    }));
  }

  async saveJob(job: FieldJob & { local_id: string }): Promise<void> {
    if (!this.db) await this.init();

    await this.db!.runAsync(
      `INSERT OR REPLACE INTO field_jobs 
       (local_id, server_id, title, customer_name, location, description, status, 
        estimated_price, actual_price, scheduled_date, completed_date, created_at, updated_at, synced)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        job.local_id,
        job.id || null,
        job.title,
        job.customer_name,
        job.location,
        job.description || null,
        job.status,
        job.estimated_price || null,
        job.actual_price || null,
        job.scheduled_date || null,
        job.completed_date || null,
        job.created_at || new Date().toISOString(),
        job.updated_at || new Date().toISOString(),
        job.synced ? 1 : 0,
      ],
    );
  }

  async getJobs(status?: string): Promise<FieldJob[]> {
    if (!this.db) await this.init();

    const query = status
      ? "SELECT * FROM field_jobs WHERE status = ? ORDER BY created_at DESC"
      : "SELECT * FROM field_jobs ORDER BY created_at DESC";

    const params = status ? [status] : [];
    const result = await this.db!.getAllAsync(query, params);

    return result.map((row: any) => ({
      id: row.server_id,
      local_id: row.local_id,
      title: row.title,
      customer_name: row.customer_name,
      location: row.location,
      description: row.description,
      status: row.status,
      estimated_price: row.estimated_price,
      actual_price: row.actual_price,
      scheduled_date: row.scheduled_date,
      completed_date: row.completed_date,
      created_at: row.created_at,
      updated_at: row.updated_at,
      synced: row.synced === 1,
    }));
  }

  async addToSyncQueue(item: Omit<SyncQueueItem, "id">): Promise<void> {
    if (!this.db) await this.init();

    await this.db!.runAsync(
      `INSERT INTO sync_queue 
       (operation, entity_type, entity_id, data, status, retry_count, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        item.operation,
        item.entity_type,
        item.entity_id || null,
        item.data,
        item.status,
        item.retry_count,
        item.created_at,
        item.updated_at,
      ],
    );
  }

  async getSyncQueue(): Promise<SyncQueueItem[]> {
    if (!this.db) await this.init();

    const result = await this.db!.getAllAsync(
      "SELECT * FROM sync_queue WHERE status IN (?, ?) ORDER BY created_at ASC",
      ["pending", "failed"],
    );

    return result.map((row: any) => ({
      id: row.id,
      operation: row.operation,
      entity_type: row.entity_type,
      entity_id: row.entity_id,
      data: row.data,
      status: row.status,
      retry_count: row.retry_count,
      created_at: row.created_at,
      updated_at: row.updated_at,
    }));
  }

  async updateSyncQueueItem(
    id: number,
    status: string,
    retryCount?: number,
  ): Promise<void> {
    if (!this.db) await this.init();

    if (retryCount !== undefined) {
      await this.db!.runAsync(
        "UPDATE sync_queue SET status = ?, retry_count = ?, updated_at = ? WHERE id = ?",
        [status, retryCount, new Date().toISOString(), id],
      );
    } else {
      await this.db!.runAsync(
        "UPDATE sync_queue SET status = ?, updated_at = ? WHERE id = ?",
        [status, new Date().toISOString(), id],
      );
    }
  }

  async deleteSyncQueueItem(id: number): Promise<void> {
    if (!this.db) await this.init();
    await this.db!.runAsync("DELETE FROM sync_queue WHERE id = ?", [id]);
  }
}

export const sqliteService = new SQLiteService();
