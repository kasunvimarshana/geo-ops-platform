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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained('field_jobs')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('category', ['fuel', 'maintenance', 'parts', 'salary', 'transport', 'food', 'other'])->default('fuel');
            $table->string('expense_number', 50)->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('LKR');
            $table->date('expense_date');
            $table->string('vendor_name', 200)->nullable();
            $table->text('description');
            $table->string('receipt_path')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('is_synced')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->index(['organization_id', 'expense_date']);
            $table->index(['job_id']);
            $table->index(['driver_id']);
            $table->index(['category']);
            $table->index(['expense_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
