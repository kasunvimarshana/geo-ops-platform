import * as SQLite from "expo-sqlite";

const DB_NAME = "geo-ops.db";

let db: SQLite.SQLiteDatabase | null = null;

export const initDatabase = async (): Promise<void> => {
  try {
    db = await SQLite.openDatabaseAsync(DB_NAME);

    await db.execAsync(`
      PRAGMA journal_mode = WAL;
      
      CREATE TABLE IF NOT EXISTS lands (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        area REAL NOT NULL,
        latitude REAL NOT NULL,
        longitude REAL NOT NULL,
        boundaries TEXT,
        ownerId TEXT NOT NULL,
        syncStatus TEXT DEFAULT 'synced',
        createdAt TEXT NOT NULL,
        updatedAt TEXT NOT NULL
      );
      
      CREATE TABLE IF NOT EXISTS measurements (
        id TEXT PRIMARY KEY,
        landId TEXT NOT NULL,
        type TEXT NOT NULL,
        value REAL NOT NULL,
        unit TEXT NOT NULL,
        coordinates TEXT NOT NULL,
        notes TEXT,
        syncStatus TEXT DEFAULT 'synced',
        createdAt TEXT NOT NULL,
        updatedAt TEXT NOT NULL,
        FOREIGN KEY (landId) REFERENCES lands (id)
      );
      
      CREATE TABLE IF NOT EXISTS jobs (
        id TEXT PRIMARY KEY,
        title TEXT NOT NULL,
        description TEXT,
        landId TEXT NOT NULL,
        status TEXT NOT NULL,
        scheduledDate TEXT,
        completedDate TEXT,
        assignedTo TEXT,
        syncStatus TEXT DEFAULT 'synced',
        createdAt TEXT NOT NULL,
        updatedAt TEXT NOT NULL,
        FOREIGN KEY (landId) REFERENCES lands (id)
      );
      
      CREATE TABLE IF NOT EXISTS invoices (
        id TEXT PRIMARY KEY,
        invoiceNumber TEXT NOT NULL,
        jobId TEXT,
        amount REAL NOT NULL,
        currency TEXT NOT NULL,
        status TEXT NOT NULL,
        dueDate TEXT NOT NULL,
        paidDate TEXT,
        syncStatus TEXT DEFAULT 'synced',
        createdAt TEXT NOT NULL,
        updatedAt TEXT NOT NULL
      );
      
      CREATE TABLE IF NOT EXISTS payments (
        id TEXT PRIMARY KEY,
        invoiceId TEXT NOT NULL,
        amount REAL NOT NULL,
        currency TEXT NOT NULL,
        method TEXT NOT NULL,
        transactionId TEXT,
        notes TEXT,
        paidAt TEXT NOT NULL,
        syncStatus TEXT DEFAULT 'synced',
        createdAt TEXT NOT NULL,
        FOREIGN KEY (invoiceId) REFERENCES invoices (id)
      );
      
      CREATE TABLE IF NOT EXISTS expenses (
        id TEXT PRIMARY KEY,
        category TEXT NOT NULL,
        amount REAL NOT NULL,
        currency TEXT NOT NULL,
        description TEXT NOT NULL,
        date TEXT NOT NULL,
        receiptUrl TEXT,
        syncStatus TEXT DEFAULT 'synced',
        createdAt TEXT NOT NULL,
        updatedAt TEXT NOT NULL
      );
      
      CREATE TABLE IF NOT EXISTS sync_queue (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        entityType TEXT NOT NULL,
        entityId TEXT NOT NULL,
        operation TEXT NOT NULL,
        data TEXT NOT NULL,
        createdAt TEXT NOT NULL
      );
      
      CREATE INDEX IF NOT EXISTS idx_lands_ownerId ON lands(ownerId);
      CREATE INDEX IF NOT EXISTS idx_measurements_landId ON measurements(landId);
      CREATE INDEX IF NOT EXISTS idx_jobs_landId ON jobs(landId);
      CREATE INDEX IF NOT EXISTS idx_jobs_status ON jobs(status);
      CREATE INDEX IF NOT EXISTS idx_invoices_status ON invoices(status);
      CREATE INDEX IF NOT EXISTS idx_payments_invoiceId ON payments(invoiceId);
      CREATE INDEX IF NOT EXISTS idx_sync_queue_entityType ON sync_queue(entityType);
    `);

    console.log("Database initialized successfully");
  } catch (error) {
    console.error("Error initializing database:", error);
    throw error;
  }
};

export const getDatabase = (): SQLite.SQLiteDatabase => {
  if (!db) {
    throw new Error(
      "Database not initialized. Call initDatabase() in App.tsx before using any database operations.",
    );
  }
  return db;
};

export const closeDatabase = async (): Promise<void> => {
  if (db) {
    await db.closeAsync();
    db = null;
  }
};
