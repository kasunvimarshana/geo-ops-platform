<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ValidateProductionConfig extends Command
{
    protected $signature = 'geo-ops:validate-config {--show-warnings}';
    protected $description = 'Validate production-readiness of configuration';

    public function handle()
    {
        $this->info('Validating GeoOps Production Configuration...');
        $errors = [];
        $warnings = [];
        $passed = [];

        // Check APP_DEBUG
        if (config('app.debug') === true) {
            $errors[] = 'APP_DEBUG is enabled - MUST be false in production';
        } else {
            $passed[] = 'APP_DEBUG is disabled';
        }
        
        // Check APP_KEY
        if (empty(config('app.key'))) {
            $errors[] = 'APP_KEY is not set';
        } else {
            $passed[] = 'APP_KEY is set';
        }
        
        // Check JWT_SECRET
        if (empty(config('jwt.secret'))) {
            $errors[] = 'JWT_SECRET is not set';
        } else {
            $passed[] = 'JWT_SECRET is configured';
        }

        // Display results
        $this->newLine();
        $this->info('Passed: ' . count($passed));
        
        if (count($errors) > 0) {
            $this->error('Errors: ' . count($errors));
            foreach ($errors as $error) {
                $this->error('  - ' . $error);
            }
            return Command::FAILURE;
        }

        $this->info('Configuration is production-ready!');
        return Command::SUCCESS;
    }
}
