<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->string('invoice_number', 50)->unique();
            $table->decimal('amount', 12, 2);
            $table->decimal('tax', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2);
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->string('pdf_path', 500)->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('organization_id');
            $table->index('job_id');
            $table->index('customer_id');
            $table->index('invoice_number');
            $table->index('status');
            $table->index('issued_at');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mobile_payment', 'credit']);
            $table->string('reference_number', 100)->nullable();
            $table->timestamp('payment_date');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('organization_id');
            $table->index('invoice_id');
            $table->index('customer_id');
            $table->index('payment_date');
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained();
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('machine_id')->nullable()->constrained('machines');
            $table->enum('category', ['fuel', 'spare_parts', 'maintenance', 'labor', 'other']);
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->string('receipt_path', 500)->nullable();
            $table->date('expense_date');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('job_id');
            $table->index('driver_id');
            $table->index('machine_id');
            $table->index('category');
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
