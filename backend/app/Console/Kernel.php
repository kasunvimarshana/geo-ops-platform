<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Define the command schedule here
        // Example: $schedule->command('inspire')->hourly();
    }

    protected function commands()
    {
        // Load the commands from the app/Console/Commands directory
        $this->load(__DIR__.'/Commands');

        // Register your commands here
        // Example: $this->command('your:command', YourCommand::class);
    }
}