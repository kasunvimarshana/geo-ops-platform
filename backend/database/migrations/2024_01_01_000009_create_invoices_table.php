<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('land_id')->nullable()->constrained()->onDelete('set null');
            $table->string('invoice_number', 50)->unique();
            $table->string('customer_name', 255);
            $table->string('customer_phone', 20)->nullable();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('area_acres', 10, 4)->default(0);
            $table->decimal('area_hectares', 10, 4)->default(0);
            $table->decimal('rate_per_unit', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->string('pdf_path', 500)->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->enum('sync_status', ['synced', 'pending', 'conflict'])->default('synced');
            $table->uuid('offline_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('invoice_number');
            $table->index('status');
            $table->index('invoice_date');
            $table->index('offline_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
