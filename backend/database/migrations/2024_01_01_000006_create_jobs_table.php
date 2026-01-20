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
        Schema::create('field_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('land_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('job_number', 50)->unique();
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('service_type', ['plowing', 'harvesting', 'spraying', 'seeding', 'other'])->default('plowing');
            $table->string('customer_name', 200);
            $table->string('customer_phone', 50)->nullable();
            $table->text('customer_address')->nullable();
            $table->json('location_coordinates')->nullable(); // {lat, lng}
            $table->decimal('area_acres', 15, 4)->nullable();
            $table->decimal('area_hectares', 15, 4)->nullable();
            $table->decimal('rate_per_unit', 10, 2)->nullable(); // Rate per acre/hectare
            $table->enum('rate_unit', ['acre', 'hectare', 'hour', 'fixed'])->default('acre');
            $table->decimal('estimated_amount', 10, 2)->nullable();
            $table->decimal('actual_amount', 10, 2)->nullable();
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('completion_notes')->nullable();
            $table->json('attachments')->nullable(); // Array of file URLs
            $table->boolean('is_synced')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->index(['organization_id', 'status']);
            $table->index(['driver_id', 'status']);
            $table->index(['customer_id']);
            $table->index(['scheduled_date']);
            $table->index(['job_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_jobs');
    }
};
