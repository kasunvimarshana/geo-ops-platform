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
        Schema::create('land_plots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('area_acres', 12, 4);
            $table->decimal('area_hectares', 12, 4);
            $table->decimal('area_square_meters', 12, 2);
            $table->decimal('perimeter_meters', 12, 2)->nullable();
            $table->json('coordinates');
            $table->decimal('center_latitude', 10, 8);
            $table->decimal('center_longitude', 11, 8);
            $table->geometry('location')->nullable();
            $table->enum('measurement_method', ['walk_around', 'manual_points']);
            $table->decimal('accuracy_meters', 6, 2)->nullable();
            $table->timestamp('measured_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('organization_id');
            $table->index('user_id');
            $table->spatialIndex('location');
            $table->index('measured_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_plots');
    }
};
