<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->string('machine_type', 100);
            $table->string('registration_number', 50)->nullable();
            $table->text('description')->nullable();
            $table->decimal('rate_per_acre', 10, 2)->default(0);
            $table->decimal('rate_per_hectare', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
