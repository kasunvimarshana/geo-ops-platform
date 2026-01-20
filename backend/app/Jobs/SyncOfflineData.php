<?php

namespace App\Jobs;

use App\Models\Job;
use App\Models\Land;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SyncOfflineData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $offlineData;

    /**
     * Create a new job instance.
     *
     * @param array $offlineData
     * @return void
     */
    public function __construct(array $offlineData)
    {
        $this->offlineData = $offlineData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            foreach ($this->offlineData['jobs'] as $jobData) {
                Job::updateOrCreate(['id' => $jobData['id']], $jobData);
            }

            foreach ($this->offlineData['lands'] as $landData) {
                Land::updateOrCreate(['id' => $landData['id']], $landData);
            }

            foreach ($this->offlineData['invoices'] as $invoiceData) {
                Invoice::updateOrCreate(['id' => $invoiceData['id']], $invoiceData);
            }

            foreach ($this->offlineData['payments'] as $paymentData) {
                Payment::updateOrCreate(['id' => $paymentData['id']], $paymentData);
            }
        });
    }
}