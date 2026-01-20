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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->enum('subscription_package', ['free', 'basic', 'pro'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index('owner_id');
            $table->index('subscription_package');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
