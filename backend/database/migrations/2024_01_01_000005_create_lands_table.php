<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('area_acres', 10, 4)->default(0);
            $table->decimal('area_hectares', 10, 4)->default(0);
            $table->enum('measurement_type', ['walk-around', 'point-based'])->default('walk-around');
            $table->string('location_name', 255)->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->foreignId('measured_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('measured_at')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'archived'])->default('draft');
            $table->enum('sync_status', ['synced', 'pending', 'conflict'])->default('synced');
            $table->uuid('offline_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('measured_by');
            $table->index('sync_status');
            $table->index('status');
            $table->index('offline_id');
        });

        // Add spatial column for polygon (works for both MySQL and PostgreSQL)
        DB::statement('ALTER TABLE lands ADD COLUMN polygon GEOMETRY(POLYGON, 4326) NULL');
        DB::statement('CREATE SPATIAL INDEX idx_lands_polygon ON lands(polygon)');
    }

    public function down(): void
    {
        Schema::dropIfExists('lands');
    }
};
