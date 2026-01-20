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
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('land_measurement_id')->nullable()->constrained();
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->foreignId('machine_id')->nullable()->constrained('machines');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'billed', 'paid'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('customer_id');
            $table->index('driver_id');
            $table->index('machine_id');
            $table->index('status');
            $table->index('scheduled_at');
        });

        Schema::create('tracking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 6, 2)->nullable();
            $table->decimal('speed', 6, 2)->nullable();
            $table->decimal('heading', 5, 2)->nullable();
            $table->timestamp('recorded_at');

            $table->index('driver_id');
            $table->index('job_id');
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_logs');
        Schema::dropIfExists('jobs');
    }
};
