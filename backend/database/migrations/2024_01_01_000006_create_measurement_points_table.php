<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude', 8, 2)->nullable();
            $table->decimal('accuracy', 5, 2);
            $table->integer('sequence');
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['land_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurement_points');
    }
};
