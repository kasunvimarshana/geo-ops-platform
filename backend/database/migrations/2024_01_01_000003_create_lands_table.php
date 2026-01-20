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
        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('owner_user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            
            // Spatial data - JSON array of coordinates
            $table->json('coordinates');
            
            // Calculated areas
            $table->decimal('area_acres', 12, 4)->nullable();
            $table->decimal('area_hectares', 12, 4)->nullable();
            $table->decimal('area_square_meters', 12, 2)->nullable();
            
            // Center point for map display
            $table->decimal('center_latitude', 10, 7)->nullable();
            $table->decimal('center_longitude', 10, 7)->nullable();
            
            // Location information
            $table->text('location_address')->nullable();
            $table->string('location_district', 100)->nullable();
            $table->string('location_province', 100)->nullable();
            
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->index('organization_id');
            $table->index('owner_user_id');
            $table->index('status');
            $table->index(['location_district', 'location_province']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lands');
    }
};
