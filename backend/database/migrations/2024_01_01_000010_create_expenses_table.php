<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('job_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('expense_type', ['fuel', 'maintenance', 'parts', 'labor', 'other']);
            $table->string('category', 100)->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('expense_date');
            $table->string('receipt_path', 500)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('sync_status', ['synced', 'pending', 'conflict'])->default('synced');
            $table->uuid('offline_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('machine_id');
            $table->index('driver_id');
            $table->index('expense_date');
            $table->index('expense_type');
            $table->index('offline_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
