<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('land_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('machine_id')->constrained()->onDelete('restrict');
            $table->foreignId('driver_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->date('job_date');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->string('location_name', 255)->nullable();
            $table->text('notes')->nullable();
            $table->enum('sync_status', ['synced', 'pending', 'conflict'])->default('synced');
            $table->uuid('offline_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('driver_id');
            $table->index('status');
            $table->index('job_date');
            $table->index('offline_id');
        });

        // Add spatial column for location point
        DB::statement('ALTER TABLE jobs ADD COLUMN location GEOMETRY(POINT, 4326) NULL');
        DB::statement('CREATE SPATIAL INDEX idx_jobs_location ON jobs(location)');
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
