<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('land_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('area_acres', 10, 4);
            $table->decimal('area_hectares', 10, 4);
            $table->foreignId('measured_by')->constrained('users');
            $table->timestamp('measured_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('measured_by');
            $table->index('measured_at');
        });

        // Add spatial column for coordinates (polygon)
        // MySQL/MariaDB
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE land_measurements ADD coordinates POLYGON NOT NULL');
            DB::statement('CREATE SPATIAL INDEX idx_coordinates ON land_measurements(coordinates)');
        }
        // PostgreSQL with PostGIS
        elseif (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE land_measurements ADD coordinates GEOMETRY(POLYGON, 4326)');
            DB::statement('CREATE INDEX idx_coordinates ON land_measurements USING GIST(coordinates)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_measurements');
    }
};
