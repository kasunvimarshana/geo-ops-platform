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
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['walk_around', 'point_based'])->default('walk_around');
            $table->json('coordinates'); // Array of lat/lng points
            $table->decimal('area_square_meters', 15, 2);
            $table->decimal('area_acres', 15, 4);
            $table->decimal('area_hectares', 15, 4);
            $table->decimal('perimeter_meters', 15, 2)->nullable();
            $table->json('center_point')->nullable(); // {lat, lng}
            $table->integer('point_count');
            $table->decimal('accuracy_meters', 8, 2)->nullable(); // GPS accuracy
            $table->timestamp('measurement_started_at');
            $table->timestamp('measurement_completed_at');
            $table->integer('duration_seconds')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_synced')->default(false);
            $table->string('device_id', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->index(['organization_id', 'created_at']);
            $table->index(['land_id']);
            $table->index(['user_id']);
            $table->index(['is_synced']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
