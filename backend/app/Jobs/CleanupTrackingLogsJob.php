<?php

namespace App\Jobs;

use App\Models\TrackingLog;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupTrackingLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $daysToKeep = 90
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $cutoffDate = Carbon::now()->subDays($this->daysToKeep);

            Log::info('Starting tracking logs cleanup', [
                'cutoff_date' => $cutoffDate->toDateString(),
                'days_to_keep' => $this->daysToKeep,
            ]);

            $deletedCount = TrackingLog::where('created_at', '<', $cutoffDate)->delete();

            Log::info('Tracking logs cleanup completed', [
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to cleanup tracking logs', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupTrackingLogsJob failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}
