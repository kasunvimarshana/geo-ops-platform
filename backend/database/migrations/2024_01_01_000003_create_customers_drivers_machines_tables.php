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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('phone');
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('license_number', 50)->nullable();
            $table->json('vehicle_info')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
            $table->index('organization_id');
            $table->index('status');
        });

        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['tractor', 'harvester', 'plough', 'seeder', 'sprayer', 'other']);
            $table->string('model')->nullable();
            $table->string('registration_number', 50)->nullable();
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('customers');
    }
};
