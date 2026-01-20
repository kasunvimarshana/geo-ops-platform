<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('measurements_count')->default(0);
            $table->integer('measurements_limit')->default(0);
            $table->integer('drivers_count')->default(0);
            $table->integer('drivers_limit')->default(0);
            $table->integer('exports_count')->default(0);
            $table->integer('exports_limit')->default(0);
            $table->timestamp('reset_at')->nullable();
            $table->timestamps();

            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_limits');
    }
};
