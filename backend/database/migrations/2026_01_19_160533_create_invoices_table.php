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
            $table->string('invoice_number')->unique();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('job_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type')->default('subscription'); // subscription, service, other
            $table->text('description')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pending'); // pending, paid, cancelled, overdue
            $table->string('payment_method')->nullable(); // card, bank_transfer, cash, mobile_money
            $table->timestamp('issued_at');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('items')->nullable(); // invoice line items
            $table->timestamps();
            
            $table->index(['organization_id', 'status', 'due_date']);
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
