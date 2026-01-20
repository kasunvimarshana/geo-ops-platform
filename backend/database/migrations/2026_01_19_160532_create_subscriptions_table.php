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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('plan_name'); // basic, pro, enterprise
            $table->string('plan_type')->default('monthly'); // monthly, yearly
            $table->decimal('price', 10, 2);
            $table->string('status')->default('active'); // active, cancelled, expired, suspended
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->json('features')->nullable(); // allowed features/limits
            $table->timestamps();
            
            $table->index(['organization_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
