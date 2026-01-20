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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // creator
            $table->json('boundary'); // GeoJSON polygon
            $table->decimal('area', 15, 2); // in square meters
            $table->decimal('perimeter', 15, 2); // in meters
            $table->string('crop_type')->nullable();
            $table->text('notes')->nullable();
            $table->string('measurement_type')->default('walk_around'); // walk_around, polygon, manual
            $table->timestamp('measured_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['organization_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
