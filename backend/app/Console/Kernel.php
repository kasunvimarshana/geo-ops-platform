<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check subscription expiry daily at 2 AM
        $schedule->command('subscriptions:check-expiry')
            ->daily()
            ->at('02:00')
            ->onOneServer();

        // Cleanup old tracking logs weekly on Sunday at 3 AM
        $schedule->command('tracking:cleanup --days=90')
            ->weekly()
            ->sundays()
            ->at('03:00')
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
