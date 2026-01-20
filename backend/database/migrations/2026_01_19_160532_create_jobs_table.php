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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->timestamp('due_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('location')->nullable(); // GPS coordinates if applicable
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['organization_id', 'status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
