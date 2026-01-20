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
        Schema::create('tracking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained('field_jobs')->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->decimal('altitude_meters', 10, 2)->nullable();
            $table->decimal('speed_mps', 8, 2)->nullable(); // meters per second
            $table->decimal('heading_degrees', 5, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->string('device_id', 100)->nullable();
            $table->string('platform', 50)->nullable(); // iOS, Android
            $table->json('metadata')->nullable(); // Battery level, GPS mode, etc.
            $table->boolean('is_synced')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'recorded_at']);
            $table->index(['job_id', 'recorded_at']);
            $table->index(['organization_id']);
            $table->index(['is_synced']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_logs');
    }
};
