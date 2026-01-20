<?php

namespace App\Console\Commands;

use App\Jobs\CheckSubscriptionExpiryJob;
use Illuminate\Console\Command;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired and expiring subscriptions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Dispatching subscription expiry check job...');
        
        CheckSubscriptionExpiryJob::dispatch();
        
        $this->info('Job dispatched successfully!');
        
        return Command::SUCCESS;
    }
}
