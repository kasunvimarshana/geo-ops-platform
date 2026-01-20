<?php

namespace App\Console\Commands;

use App\Jobs\CleanupTrackingLogsJob;
use Illuminate\Console\Command;

class CleanupTrackingLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracking:cleanup {--days=90 : Number of days to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old tracking logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        
        $this->info("Dispatching tracking logs cleanup job (keeping last {$days} days)...");
        
        CleanupTrackingLogsJob::dispatch($days);
        
        $this->info('Job dispatched successfully!');
        
        return Command::SUCCESS;
    }
}
