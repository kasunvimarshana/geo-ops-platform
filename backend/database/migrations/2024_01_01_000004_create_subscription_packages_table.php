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
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            
            // Limits
            $table->integer('max_measurements')->unsigned();
            $table->integer('max_drivers')->unsigned();
            $table->integer('max_jobs')->unsigned();
            $table->integer('max_lands')->unsigned();
            $table->integer('max_storage_mb')->unsigned();
            
            // Pricing
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2)->nullable();
            
            // Features as JSON array
            $table->json('features')->nullable();
            
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();

            $table->index('name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_packages');
    }
};
