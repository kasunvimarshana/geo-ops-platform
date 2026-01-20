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
            $table->foreignId('job_id')->nullable()->constrained('field_jobs')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('invoice_number', 50)->unique();
            $table->enum('status', ['draft', 'pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->string('customer_name', 200);
            $table->string('customer_phone', 50)->nullable();
            $table->text('customer_address')->nullable();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->json('line_items'); // Array of {description, quantity, rate, amount}
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2);
            $table->string('currency', 10)->default('LKR');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('pdf_generated_at')->nullable();
            $table->boolean('is_synced')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->index(['organization_id', 'status']);
            $table->index(['customer_id']);
            $table->index(['invoice_number']);
            $table->index(['invoice_date']);
            $table->index(['due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
