import * as SQLite from "expo-sqlite";

const db = SQLite.openDatabase("geo-ops_service.db");

export const createTables = async () => {
  await new Promise((resolve, reject) => {
    db.transaction((tx) => {
      tx.executeSql(
        `CREATE TABLE IF NOT EXISTS lands (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          name TEXT NOT NULL,
          area REAL NOT NULL,
          coordinates TEXT NOT NULL,
          created_at TEXT NOT NULL,
          updated_at TEXT NOT NULL
        );`,
        [],
        () => resolve(),
        (_, error) => {
          reject(error);
          return false;
        },
      );

      tx.executeSql(
        `CREATE TABLE IF NOT EXISTS jobs (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          land_id INTEGER NOT NULL,
          status TEXT NOT NULL,
          created_at TEXT NOT NULL,
          updated_at TEXT NOT NULL,
          FOREIGN KEY (land_id) REFERENCES lands (id) ON DELETE CASCADE
        );`,
        [],
        () => resolve(),
        (_, error) => {
          reject(error);
          return false;
        },
      );

      tx.executeSql(
        `CREATE TABLE IF NOT EXISTS sync_queue (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          table_name TEXT NOT NULL,
          action TEXT NOT NULL,
          data TEXT NOT NULL,
          created_at TEXT NOT NULL
        );`,
        [],
        () => resolve(),
        (_, error) => {
          reject(error);
          return false;
        },
      );
    });
  });
};

export const insertLand = async (name, area, coordinates) => {
  return new Promise((resolve, reject) => {
    db.transaction((tx) => {
      tx.executeSql(
        `INSERT INTO lands (name, area, coordinates, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'));`,
        [name, area, coordinates],
        (_, result) => resolve(result),
        (_, error) => {
          reject(error);
          return false;
        },
      );
    });
  });
};

export const getLands = async () => {
  return new Promise((resolve, reject) => {
    db.transaction((tx) => {
      tx.executeSql(
        `SELECT * FROM lands;`,
        [],
        (_, { rows }) => resolve(rows._array),
        (_, error) => {
          reject(error);
          return false;
        },
      );
    });
  });
};

// Additional functions for jobs and sync_queue can be added here.
