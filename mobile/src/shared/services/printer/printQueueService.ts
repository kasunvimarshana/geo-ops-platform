/**
 * Print Queue Service
 * Manages offline print queue with retry logic and persistence
 */

import { SQLiteService } from '../storage/sqliteService';
import { bluetoothPrinterService } from './bluetoothPrinterService';
import type { PrintJob } from './types';

class PrintQueueService {
  private isProcessing: boolean = false;
  private maxRetries: number = 3;
  private retryDelay: number = 5000; // 5 seconds

  /**
   * Initialize print queue table in SQLite
   */
  async initialize(): Promise<void> {
    const db = await SQLiteService.getDatabase();

    await db.execAsync(`
      CREATE TABLE IF NOT EXISTS print_queue (
        id TEXT PRIMARY KEY,
        type TEXT NOT NULL,
        data TEXT NOT NULL,
        status TEXT NOT NULL,
        attempts INTEGER DEFAULT 0,
        created_at INTEGER NOT NULL,
        error TEXT,
        synced INTEGER DEFAULT 0
      );
    `);

    console.log('Print queue initialized');
  }

  /**
   * Add print job to queue
   */
  async addJob(job: Omit<PrintJob, 'id' | 'status' | 'attempts' | 'createdAt'>): Promise<string> {
    const db = await SQLiteService.getDatabase();
    const id = `print_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    const createdAt = Date.now();

    await db.runAsync(
      'INSERT INTO print_queue (id, type, data, status, attempts, created_at) VALUES (?, ?, ?, ?, ?, ?)',
      [id, job.type, JSON.stringify(job.data), 'pending', 0, createdAt]
    );

    console.log('Print job added to queue:', id);

    // Try to process immediately if printer is connected
    if (bluetoothPrinterService.isConnected()) {
      this.processQueue();
    }

    return id;
  }

  /**
   * Get all pending print jobs
   */
  async getPendingJobs(): Promise<PrintJob[]> {
    const db = await SQLiteService.getDatabase();

    const result = await db.getAllAsync<{
      id: string;
      type: 'invoice' | 'receipt' | 'job_summary';
      data: string;
      status: 'pending' | 'printing' | 'completed' | 'failed';
      attempts: number;
      created_at: number;
      error: string | null;
    }>('SELECT * FROM print_queue WHERE status IN (?, ?) ORDER BY created_at ASC', [
      'pending',
      'failed',
    ]);

    return result.map((row) => ({
      id: row.id,
      type: row.type,
      data: JSON.parse(row.data),
      status: row.status,
      attempts: row.attempts,
      createdAt: new Date(row.created_at),
      error: row.error || undefined,
    }));
  }

  /**
   * Get all jobs (for history)
   */
  async getAllJobs(): Promise<PrintJob[]> {
    const db = await SQLiteService.getDatabase();

    const result = await db.getAllAsync<{
      id: string;
      type: 'invoice' | 'receipt' | 'job_summary';
      data: string;
      status: 'pending' | 'printing' | 'completed' | 'failed';
      attempts: number;
      created_at: number;
      error: string | null;
    }>('SELECT * FROM print_queue ORDER BY created_at DESC LIMIT 50');

    return result.map((row) => ({
      id: row.id,
      type: row.type,
      data: JSON.parse(row.data),
      status: row.status,
      attempts: row.attempts,
      createdAt: new Date(row.created_at),
      error: row.error || undefined,
    }));
  }

  /**
   * Update job status
   */
  private async updateJobStatus(
    jobId: string,
    status: PrintJob['status'],
    error?: string
  ): Promise<void> {
    const db = await SQLiteService.getDatabase();

    await db.runAsync(
      'UPDATE print_queue SET status = ?, error = ? WHERE id = ?',
      [status, error || null, jobId]
    );
  }

  /**
   * Increment job attempts
   */
  private async incrementAttempts(jobId: string): Promise<void> {
    const db = await SQLiteService.getDatabase();

    await db.runAsync('UPDATE print_queue SET attempts = attempts + 1 WHERE id = ?', [jobId]);
  }

  /**
   * Process a single print job
   */
  private async processJob(job: PrintJob): Promise<boolean> {
    try {
      console.log(`Processing print job: ${job.id} (attempt ${job.attempts + 1})`);

      // Increment attempts first
      await this.incrementAttempts(job.id);
      const currentAttempts = job.attempts + 1;

      // Update status to printing
      await this.updateJobStatus(job.id, 'printing');

      // Print using Bluetooth service
      await bluetoothPrinterService.print(job);

      // Mark as completed
      await this.updateJobStatus(job.id, 'completed');
      console.log(`Print job completed: ${job.id}`);

      return true;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Unknown error';
      console.error(`Print job failed: ${job.id}`, errorMessage);

      const currentAttempts = job.attempts + 1; // Attempts were incremented above

      // Check if we should retry
      if (currentAttempts >= this.maxRetries) {
        await this.updateJobStatus(job.id, 'failed', errorMessage);
        console.log(`Print job marked as failed after ${this.maxRetries} attempts: ${job.id}`);
      } else {
        // Revert to pending for retry
        await this.updateJobStatus(job.id, 'pending', errorMessage);
      }

      return false;
    }
  }

  /**
   * Process all pending jobs in queue
   */
  async processQueue(): Promise<void> {
    if (this.isProcessing) {
      console.log('Queue is already being processed');
      return;
    }

    if (!bluetoothPrinterService.isConnected()) {
      console.log('No printer connected, skipping queue processing');
      return;
    }

    this.isProcessing = true;

    try {
      const pendingJobs = await this.getPendingJobs();
      console.log(`Processing ${pendingJobs.length} pending print jobs`);

      for (const job of pendingJobs) {
        const success = await this.processJob(job);

        // Add delay between jobs
        if (!success) {
          await new Promise((resolve) => setTimeout(resolve, this.retryDelay));
        } else {
          await new Promise((resolve) => setTimeout(resolve, 1000)); // 1 second between successful prints
        }
      }

      console.log('Queue processing completed');
    } catch (error) {
      console.error('Error processing queue:', error);
    } finally {
      this.isProcessing = false;
    }
  }

  /**
   * Retry failed job
   */
  async retryJob(jobId: string): Promise<void> {
    const db = await SQLiteService.getDatabase();

    // Reset attempts and status
    await db.runAsync(
      'UPDATE print_queue SET status = ?, attempts = 0, error = NULL WHERE id = ?',
      ['pending', jobId]
    );

    console.log(`Job ${jobId} reset for retry`);

    // Process queue
    await this.processQueue();
  }

  /**
   * Delete a print job
   */
  async deleteJob(jobId: string): Promise<void> {
    const db = await SQLiteService.getDatabase();

    await db.runAsync('DELETE FROM print_queue WHERE id = ?', [jobId]);
    console.log(`Job ${jobId} deleted`);
  }

  /**
   * Clear all completed jobs
   */
  async clearCompleted(): Promise<void> {
    const db = await SQLiteService.getDatabase();

    await db.runAsync('DELETE FROM print_queue WHERE status = ?', ['completed']);
    console.log('Completed jobs cleared');
  }

  /**
   * Get queue stats
   */
  async getStats(): Promise<{
    total: number;
    pending: number;
    completed: number;
    failed: number;
  }> {
    const db = await SQLiteService.getDatabase();

    const result = await db.getAllAsync<{ status: string; count: number }>(
      'SELECT status, COUNT(*) as count FROM print_queue GROUP BY status'
    );

    const stats = {
      total: 0,
      pending: 0,
      completed: 0,
      failed: 0,
    };

    result.forEach((row) => {
      stats.total += row.count;
      if (row.status === 'pending') stats.pending = row.count;
      if (row.status === 'completed') stats.completed = row.count;
      if (row.status === 'failed') stats.failed = row.count;
    });

    return stats;
  }
}

// Export singleton instance
export const printQueueService = new PrintQueueService();
