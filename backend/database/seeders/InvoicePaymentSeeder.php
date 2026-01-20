<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\FieldJob;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;

class InvoicePaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first organization and user for testing
        $organization = Organization::first();
        $user = User::where('organization_id', $organization->id)->first();
        
        if (!$organization || !$user) {
            $this->command->error('No organization or user found. Please run UserSeeder first.');
            return;
        }

        // Get completed jobs for creating invoices
        $completedJobs = FieldJob::where('organization_id', $organization->id)
            ->where('status', 'completed')
            ->limit(3)
            ->get();

        if ($completedJobs->isEmpty()) {
            $this->command->warn('No completed jobs found. Creating sample invoices without jobs.');
        }

        $this->command->info('Creating sample invoices...');

        // Create invoices
        $invoices = [];

        // Invoice 1: Draft invoice
        $invoices[] = Invoice::create([
            'organization_id' => $organization->id,
            'customer_id' => $user->id,
            'invoice_number' => 'INV-' . Carbon::now()->format('Ymd') . '-0001',
            'status' => 'draft',
            'customer_name' => 'John Doe Farms',
            'customer_phone' => '+1234567890',
            'customer_address' => '123 Farm Road, Rural County',
            'invoice_date' => Carbon::now()->toDateString(),
            'due_date' => Carbon::now()->addDays(30)->toDateString(),
            'line_items' => [
                [
                    'description' => 'Land plowing service - 50 acres',
                    'quantity' => 50,
                    'unit' => 'acre',
                    'rate' => 25.00,
                    'amount' => 1250.00,
                ],
                [
                    'description' => 'Fertilizer application',
                    'quantity' => 1,
                    'unit' => 'fixed',
                    'rate' => 150.00,
                    'amount' => 150.00,
                ],
            ],
            'subtotal' => 1400.00,
            'tax_amount' => 112.00,
            'discount_amount' => 50.00,
            'total_amount' => 1462.00,
            'paid_amount' => 0.00,
            'balance_amount' => 1462.00,
            'currency' => 'USD',
            'notes' => 'Thank you for your business',
            'terms' => 'Payment due within 30 days',
            'is_synced' => false,
            'created_by' => $user->id,
        ]);

        // Invoice 2: Pending invoice (partially paid)
        $invoice2 = Invoice::create([
            'organization_id' => $organization->id,
            'job_id' => $completedJobs->isNotEmpty() ? $completedJobs[0]->id : null,
            'customer_id' => $user->id,
            'invoice_number' => 'INV-' . Carbon::now()->format('Ymd') . '-0002',
            'status' => 'pending',
            'customer_name' => 'Jane Smith Agriculture',
            'customer_phone' => '+1234567891',
            'customer_address' => '456 Harvest Lane, Farm City',
            'invoice_date' => Carbon::now()->subDays(10)->toDateString(),
            'due_date' => Carbon::now()->addDays(20)->toDateString(),
            'line_items' => [
                [
                    'description' => 'Harvesting service - 75 acres',
                    'quantity' => 75,
                    'unit' => 'acre',
                    'rate' => 35.00,
                    'amount' => 2625.00,
                ],
            ],
            'subtotal' => 2625.00,
            'tax_amount' => 210.00,
            'discount_amount' => 0.00,
            'total_amount' => 2835.00,
            'paid_amount' => 1000.00,
            'balance_amount' => 1835.00,
            'currency' => 'USD',
            'notes' => 'Partial payment received',
            'terms' => 'Payment due within 30 days',
            'is_synced' => false,
            'created_by' => $user->id,
        ]);
        $invoices[] = $invoice2;

        // Invoice 3: Paid invoice
        $invoice3 = Invoice::create([
            'organization_id' => $organization->id,
            'job_id' => $completedJobs->count() > 1 ? $completedJobs[1]->id : null,
            'customer_id' => $user->id,
            'invoice_number' => 'INV-' . Carbon::now()->format('Ymd') . '-0003',
            'status' => 'paid',
            'customer_name' => 'Bob Johnson Crops',
            'customer_phone' => '+1234567892',
            'customer_address' => '789 Crop Circle, Farming Town',
            'invoice_date' => Carbon::now()->subDays(20)->toDateString(),
            'due_date' => Carbon::now()->subDays(5)->toDateString(),
            'line_items' => [
                [
                    'description' => 'Seeding service - 40 acres',
                    'quantity' => 40,
                    'unit' => 'acre',
                    'rate' => 20.00,
                    'amount' => 800.00,
                ],
            ],
            'subtotal' => 800.00,
            'tax_amount' => 64.00,
            'discount_amount' => 0.00,
            'total_amount' => 864.00,
            'paid_amount' => 864.00,
            'balance_amount' => 0.00,
            'currency' => 'USD',
            'notes' => 'Paid in full',
            'terms' => 'Payment due within 30 days',
            'is_synced' => false,
            'created_by' => $user->id,
        ]);
        $invoices[] = $invoice3;

        // Invoice 4: Overdue invoice
        $invoices[] = Invoice::create([
            'organization_id' => $organization->id,
            'customer_id' => $user->id,
            'invoice_number' => 'INV-' . Carbon::now()->format('Ymd') . '-0004',
            'status' => 'overdue',
            'customer_name' => 'Mike Wilson Farm',
            'customer_phone' => '+1234567893',
            'customer_address' => '321 Wheat Field, Agricultural County',
            'invoice_date' => Carbon::now()->subDays(60)->toDateString(),
            'due_date' => Carbon::now()->subDays(30)->toDateString(),
            'line_items' => [
                [
                    'description' => 'Spraying service - 100 acres',
                    'quantity' => 100,
                    'unit' => 'acre',
                    'rate' => 15.00,
                    'amount' => 1500.00,
                ],
            ],
            'subtotal' => 1500.00,
            'tax_amount' => 120.00,
            'discount_amount' => 0.00,
            'total_amount' => 1620.00,
            'paid_amount' => 0.00,
            'balance_amount' => 1620.00,
            'currency' => 'USD',
            'notes' => 'Payment overdue',
            'terms' => 'Payment due within 30 days',
            'is_synced' => false,
            'created_by' => $user->id,
        ]);

        $this->command->info('Created ' . count($invoices) . ' sample invoices.');

        // Create payments for some invoices
        $this->command->info('Creating sample payments...');

        // Payment 1: For invoice 2 (partial payment)
        Payment::create([
            'organization_id' => $organization->id,
            'invoice_id' => $invoice2->id,
            'customer_id' => $user->id,
            'payment_number' => 'PAY-' . Carbon::now()->format('Ymd') . '-0001',
            'payment_method' => 'bank_transfer',
            'amount' => 1000.00,
            'currency' => 'USD',
            'payment_date' => Carbon::now()->subDays(5)->toDateString(),
            'reference_number' => 'TRF-20240115-001',
            'notes' => 'Partial payment received via bank transfer',
            'is_synced' => false,
            'created_by' => $user->id,
        ]);

        // Payment 2: For invoice 3 (full payment)
        Payment::create([
            'organization_id' => $organization->id,
            'invoice_id' => $invoice3->id,
            'customer_id' => $user->id,
            'payment_number' => 'PAY-' . Carbon::now()->format('Ymd') . '-0002',
            'payment_method' => 'cash',
            'amount' => 864.00,
            'currency' => 'USD',
            'payment_date' => Carbon::now()->subDays(10)->toDateString(),
            'reference_number' => null,
            'notes' => 'Full payment in cash',
            'is_synced' => false,
            'created_by' => $user->id,
        ]);

        $this->command->info('Created 2 sample payments.');
        $this->command->info('Invoice and Payment seeding completed successfully!');
    }
}
